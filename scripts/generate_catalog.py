#!/usr/bin/env python3
"""Generate AI pottery catalog images via OpenRouter (Gemini 2.5 Flash Image)."""
import os, sys, json, base64, urllib.request, urllib.error, ssl
from concurrent.futures import ThreadPoolExecutor, as_completed

API_KEY = os.environ.get('OPENROUTER_API_KEY') or open('.env.local').read().split('OPENROUTER_API_KEY=')[1].split('\n')[0]
DEST = 'downloaded-media/generated'
os.makedirs(DEST, exist_ok=True)

STYLE = ("Studio product photo on a soft cream linen backdrop with gentle warm sunlight from the upper left, "
         "minimal soft shadow, photorealistic, 4k, magazine-quality, no text, no watermark, no people, no logos, "
         "centered composition, the piece is UNPAINTED bisque-fired terracotta ceramic in natural warm beige clay color, "
         "fine artisan handmade texture visible.")

CATALOG = [
    {'slug':'chai-cup','name':'Chai Cup','category':'Cups & Mugs','difficulty':'beginner','price':350,
     'desc':'a small handleless Indian chai cup, gently tapered, about 8cm tall'},
    {'slug':'coffee-mug','name':'Coffee Mug','category':'Cups & Mugs','difficulty':'beginner','price':450,
     'desc':'a classic ceramic coffee mug with a sturdy looped handle, straight cylindrical body'},
    {'slug':'espresso-cup','name':'Espresso Cup with Saucer','category':'Cups & Mugs','difficulty':'intermediate','price':550,
     'desc':'a small espresso cup with matching round saucer, both unpainted'},
    {'slug':'six-inch-plate','name':'6 Inch Plate','category':'Plates & Bowls','difficulty':'beginner','price':400,
     'desc':'a 6 inch round dinner plate with a slight raised rim, viewed from a 30 degree angle'},
    {'slug':'thali-plate','name':'Thali Plate','category':'Plates & Bowls','difficulty':'intermediate','price':650,
     'desc':'a large round Indian thali plate, flat with a low rim, about 10 inches across'},
    {'slug':'pasta-bowl','name':'Pasta Bowl','category':'Plates & Bowls','difficulty':'intermediate','price':600,
     'desc':'a wide shallow pasta bowl with a curved rim, about 9 inches across'},
    {'slug':'snack-bowl','name':'Snack Bowl','category':'Plates & Bowls','difficulty':'beginner','price':300,
     'desc':'a small round snack bowl, about 4 inches across, smooth interior'},
    {'slug':'traditional-diya','name':'Traditional Diya','category':'Diyas & Lamps','difficulty':'beginner','price':150,
     'desc':'a small traditional Indian diya oil lamp, oval shape with a pinched spout for a wick'},
    {'slug':'decorative-diya','name':'Decorative Lotus Diya','category':'Diyas & Lamps','difficulty':'intermediate','price':350,
     'desc':'a larger ornate diya shaped like an open lotus flower with petal details around the rim'},
    {'slug':'ganesh-figurine','name':'Ganesh Figurine','category':'Figurines','difficulty':'advanced','price':1200,
     'desc':'a small seated Ganesh figurine sculpture, about 6 inches tall, traditional pose with detailed features'},
    {'slug':'buddha-figurine','name':'Buddha Figurine','category':'Figurines','difficulty':'advanced','price':1100,
     'desc':'a small seated Buddha figurine in meditation pose, about 6 inches tall, serene expression'},
    {'slug':'owl-figurine','name':'Owl Figurine','category':'Figurines','difficulty':'intermediate','price':500,
     'desc':'a stylized round owl figurine about 4 inches tall, simple charming form with feather texture'},
    {'slug':'bud-vase','name':'Bud Vase','category':'Vases','difficulty':'intermediate','price':650,
     'desc':'a small narrow bud vase about 5 inches tall, slim neck, slightly bulbous base'},
    {'slug':'tall-vase','name':'Tall Cylinder Vase','category':'Vases','difficulty':'advanced','price':1500,
     'desc':'a tall cylindrical ceramic vase about 10 inches tall, smooth straight sides'},
    {'slug':'coaster','name':'Round Coaster','category':'Tiles & Coasters','difficulty':'beginner','price':150,
     'desc':'a single round flat ceramic coaster about 4 inches across, viewed from above'},
    {'slug':'decorative-tile','name':'Square Decorative Tile','category':'Tiles & Coasters','difficulty':'beginner','price':250,
     'desc':'a single square flat ceramic tile about 4 inches on a side, viewed slightly angled from above'},
    {'slug':'small-planter','name':'Succulent Planter','category':'Planters','difficulty':'intermediate','price':550,
     'desc':'a small round ceramic planter pot about 4 inches tall, gently tapered with a smooth rim'},
    {'slug':'hanging-planter','name':'Hanging Planter','category':'Planters','difficulty':'advanced','price':900,
     'desc':'a hanging ceramic planter, rounded bowl shape with three holes near the rim for a rope (rope not shown), about 5 inches across'},
]

def generate(piece):
    fn = os.path.join(DEST, f"{piece['slug']}.png")
    if os.path.exists(fn) and os.path.getsize(fn) > 50000:
        return ('SKIP', piece['slug'], 0)
    prompt = f"{STYLE} The piece is {piece['desc']}. Single piece only, isolated on the backdrop."
    body = json.dumps({
        'model': 'google/gemini-2.5-flash-image',
        'messages': [{'role':'user','content': prompt}],
        'modalities': ['image','text'],
    }).encode()
    req = urllib.request.Request(
        'https://openrouter.ai/api/v1/chat/completions',
        data=body,
        headers={'Authorization': f'Bearer {API_KEY}', 'Content-Type': 'application/json'},
        method='POST',
    )
    try:
        with urllib.request.urlopen(req, timeout=180) as r:
            d = json.loads(r.read())
        msg = d.get('choices',[{}])[0].get('message',{})
        imgs = msg.get('images') or []
        if not imgs:
            return ('FAIL', piece['slug'], 'no images returned: ' + str(d)[:200])
        url = imgs[0]['image_url']['url']
        if url.startswith('data:'):
            b64 = url.split(',',1)[1]
            data = base64.b64decode(b64)
        else:
            data = urllib.request.urlopen(url, timeout=60).read()
        with open(fn,'wb') as f:
            f.write(data)
        return ('OK', piece['slug'], len(data))
    except urllib.error.HTTPError as e:
        return ('FAIL', piece['slug'], f'HTTP {e.code}: {e.read()[:200].decode("utf8","ignore")}')
    except Exception as e:
        return ('FAIL', piece['slug'], str(e))

if __name__ == '__main__':
    print(f'Generating {len(CATALOG)} pottery catalog images...')
    with ThreadPoolExecutor(max_workers=6) as ex:
        futs = {ex.submit(generate, p): p for p in CATALOG}
        ok = fail = skip = 0
        for fut in as_completed(futs):
            status, slug, info = fut.result()
            if status == 'OK':
                ok += 1; print(f'  OK   {slug} ({info} bytes)')
            elif status == 'SKIP':
                skip += 1; print(f'  SKIP {slug}')
            else:
                fail += 1; print(f'  FAIL {slug}: {info}', file=sys.stderr)
    with open('data/raw/catalog.json','w') as f:
        json.dump(CATALOG, f, indent=2)
    print(f'Summary: OK={ok} SKIP={skip} FAIL={fail}')
