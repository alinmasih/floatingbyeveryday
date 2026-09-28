#!/usr/bin/env python3
"""Upload all downloaded + generated media to WordPress and build a media map."""
import os, json, mimetypes, base64, urllib.request, sys
from concurrent.futures import ThreadPoolExecutor, as_completed

env = {l.split('=',1)[0]: l.split('=',1)[1].strip() for l in open('.env.local') if '=' in l and not l.startswith('#')}
WP_URL = env['WP_URL']
AUTH = base64.b64encode(f"{env['WP_USER']}:{env['WP_APP_PASS']}".encode()).decode()

def upload(path, alt='', caption='', kind=''):
    fn = os.path.basename(path)
    mime = mimetypes.guess_type(fn)[0] or 'application/octet-stream'
    if mime.startswith('video/') and not mime == 'video/mp4':
        mime = 'video/mp4'
    with open(path,'rb') as f: data = f.read()
    req = urllib.request.Request(
        f'{WP_URL}/wp-json/wp/v2/media',
        data=data,
        headers={
            'Authorization': f'Basic {AUTH}',
            'Content-Type': mime,
            'Content-Disposition': f'attachment; filename="{fn}"',
        },
        method='POST',
    )
    try:
        with urllib.request.urlopen(req, timeout=180) as r:
            d = json.loads(r.read())
        mid = d['id']
        if alt or caption:
            patch = json.dumps({'alt_text': alt, 'caption': caption, 'description': caption}).encode()
            preq = urllib.request.Request(
                f'{WP_URL}/wp-json/wp/v2/media/{mid}',
                data=patch,
                headers={'Authorization': f'Basic {AUTH}','Content-Type':'application/json'},
                method='POST',
            )
            urllib.request.urlopen(preq, timeout=60).read()
        return ('OK', fn, {'id': mid, 'url': d['source_url'], 'kind': kind})
    except urllib.error.HTTPError as e:
        return ('FAIL', fn, f'HTTP {e.code}: {e.read()[:200].decode("utf8","ignore")}')
    except Exception as e:
        return ('FAIL', fn, str(e))

def main():
    media_map = {'instagram': {}, 'generated': {}, 'by_post_index': {}}
    tasks = []

    # Instagram media (only images and small videos for now to keep upload manageable)
    posts = json.load(open('data/raw/posts.json'))
    profile = json.load(open('data/raw/profile.json'))
    profile = profile[0] if isinstance(profile, list) else profile

    # Profile pic
    for fn in ('profile_hd.jpg','profile.jpg'):
        path = os.path.join('downloaded-media/instagram', fn)
        if os.path.exists(path):
            tasks.append((path, 'Floating By Everyday studio', 'Pottery studio in Raipur, Chhattisgarh.', f'profile:{fn}'))
            break

    for idx, post in enumerate(posts):
        sc = post.get('shortCode', f'post{idx}')
        cap = (post.get('caption') or '').strip()
        alt_short = cap.split('\n')[0][:120] if cap else f'Floating By Everyday post {idx}'
        cover_path = f"downloaded-media/instagram/{idx:03d}_{sc}_cover.jpg"
        if os.path.exists(cover_path):
            tasks.append((cover_path, alt_short, '', f'post:{sc}:cover'))
        for ci in range(20):
            cp = f"downloaded-media/instagram/{idx:03d}_{sc}_child{ci}.jpg"
            if os.path.exists(cp):
                tasks.append((cp, alt_short, '', f'post:{sc}:child{ci}'))
            else:
                break

    # AI catalog images
    catalog = json.load(open('data/raw/catalog.json'))
    for piece in catalog:
        path = os.path.join('downloaded-media/generated', f"{piece['slug']}.png")
        if os.path.exists(path):
            tasks.append((path, f"Unpainted {piece['name']} pottery piece", f"{piece['name']} bisque pottery ready for painting at Floating By Everyday.", f'catalog:{piece["slug"]}'))

    print(f'Uploading {len(tasks)} files...')
    ok = fail = 0
    with ThreadPoolExecutor(max_workers=4) as ex:
        futs = {ex.submit(upload, *t): t[3] for t in tasks}
        for fut in as_completed(futs):
            kind = futs[fut]
            status, fn, info = fut.result()
            if status == 'OK':
                ok += 1
                bucket = 'generated' if kind.startswith('catalog:') else 'instagram'
                media_map[bucket][kind] = info
                if ok % 20 == 0:
                    print(f'  {ok} uploaded...')
            else:
                fail += 1
                print(f'  FAIL {fn}: {info}', file=sys.stderr)
    print(f'Summary: OK={ok} FAIL={fail}')
    with open('data/raw/media-map.json','w') as f:
        json.dump(media_map, f, indent=2)

if __name__ == '__main__':
    main()
