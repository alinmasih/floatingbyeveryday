#!/usr/bin/env python3
"""Download all Instagram media from scraped JSON before URLs expire."""
import json, os, sys, urllib.request, urllib.error, hashlib
from concurrent.futures import ThreadPoolExecutor, as_completed

DEST = 'downloaded-media/instagram'
os.makedirs(DEST, exist_ok=True)

HEADERS = {
    'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
    'Referer': 'https://www.instagram.com/',
    'Accept': 'image/avif,image/webp,image/*,*/*;q=0.8',
}

def download(url, filename):
    path = os.path.join(DEST, filename)
    if os.path.exists(path) and os.path.getsize(path) > 0:
        return ('SKIP', filename, 0)
    try:
        req = urllib.request.Request(url, headers=HEADERS)
        with urllib.request.urlopen(req, timeout=60) as r:
            data = r.read()
        with open(path, 'wb') as f:
            f.write(data)
        return ('OK', filename, len(data))
    except Exception as e:
        return ('FAIL', filename, str(e))

def gather_media(profile_path, posts_path):
    items = []
    with open(profile_path) as f:
        prof = json.load(f)
    p = prof[0] if isinstance(prof, list) else prof
    if p.get('profilePicUrlHD'):
        items.append((p['profilePicUrlHD'], 'profile_hd.jpg', 'profile', p.get('id'), 0))
    elif p.get('profilePicUrl'):
        items.append((p['profilePicUrl'], 'profile.jpg', 'profile', p.get('id'), 0))

    with open(posts_path) as f:
        posts = json.load(f)
    for idx, post in enumerate(posts):
        sc = post.get('shortCode', f'post{idx}')
        ptype = post.get('type', 'Image')
        if post.get('displayUrl'):
            ext = '.jpg'
            items.append((post['displayUrl'], f'{idx:03d}_{sc}_cover{ext}', 'post-cover', sc, idx))
        if post.get('videoUrl'):
            items.append((post['videoUrl'], f'{idx:03d}_{sc}_video.mp4', 'post-video', sc, idx))
        for ci, child in enumerate(post.get('childPosts', [])):
            if child.get('displayUrl'):
                items.append((child['displayUrl'], f'{idx:03d}_{sc}_child{ci}.jpg', 'post-child', sc, idx))
            if child.get('videoUrl'):
                items.append((child['videoUrl'], f'{idx:03d}_{sc}_child{ci}.mp4', 'post-child-video', sc, idx))
    return items, posts, p

if __name__ == '__main__':
    items, posts, profile = gather_media('data/raw/profile.json', 'data/raw/posts.json')
    print(f'Total media items to download: {len(items)}')
    ok, fail, skip = 0, 0, 0
    results = []
    with ThreadPoolExecutor(max_workers=8) as ex:
        futures = {ex.submit(download, url, fn): (url, fn, kind, sc, idx) for url, fn, kind, sc, idx in items}
        for fut in as_completed(futures):
            url, fn, kind, sc, idx = futures[fut]
            status, name, info = fut.result()
            results.append({'url': url, 'filename': fn, 'kind': kind, 'shortcode': sc, 'post_index': idx, 'status': status})
            if status == 'OK':
                ok += 1; print(f'  OK  {fn} ({info} bytes)')
            elif status == 'SKIP':
                skip += 1
            else:
                fail += 1; print(f'  FAIL {fn}: {info}', file=sys.stderr)
    print(f'\nSummary: OK={ok} SKIP={skip} FAIL={fail}')
    with open('data/raw/download-results.json', 'w') as f:
        json.dump(results, f, indent=2)
