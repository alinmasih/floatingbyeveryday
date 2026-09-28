#!/usr/bin/env python3
"""Seed pottery pieces, packages, menu items, events, and coupons."""
import json, base64, urllib.request, urllib.error

env = {l.split('=',1)[0]: l.split('=',1)[1].strip() for l in open('.env.local') if '=' in l and not l.startswith('#')}
WP_URL = env['WP_URL']
AUTH = base64.b64encode(f"{env['WP_USER']}:{env['WP_APP_PASS']}".encode()).decode()
HEADERS = {'Authorization': f'Basic {AUTH}', 'Content-Type': 'application/json'}

media_map = json.load(open('data/raw/media-map.json'))
catalog = json.load(open('data/raw/catalog.json'))

def api(method, path, data=None):
    url = f"{WP_URL}/wp-json{path}"
    body = json.dumps(data).encode() if data else None
    req = urllib.request.Request(url, data=body, headers=HEADERS, method=method)
    try:
        with urllib.request.urlopen(req, timeout=60) as r:
            return json.loads(r.read())
    except urllib.error.HTTPError as e:
        print(f'  HTTP {e.code} on {method} {path}: {e.read()[:300].decode("utf8","ignore")}')
        return None

def upsert(post_type, slug, title, **fields):
    """Create or update by slug."""
    existing = api('GET', f'/wp/v2/{post_type}?slug={slug}&status=any')
    if existing:
        post_id = existing[0]['id']
        api('POST', f'/wp/v2/{post_type}/{post_id}', {'title': title, 'status': 'publish', **fields})
        return post_id
    res = api('POST', f'/wp/v2/{post_type}', {'title': title, 'slug': slug, 'status': 'publish', **fields})
    return res['id'] if res else None

def set_meta(post_id, post_type, meta):
    """Update meta fields directly via REST (works for registered meta)."""
    api('POST', f'/wp/v2/{post_type}/{post_id}', {'meta': meta})

# =====================================================================
# 1. POTTERY PIECES
# =====================================================================
print('Seeding pottery pieces...')
pieces_created = 0
for piece in catalog:
    media = media_map['generated'].get(f"catalog:{piece['slug']}")
    if not media:
        print(f"  WARN no media for {piece['slug']}"); continue
    pid = upsert('pieces', piece['slug'], piece['name'],
                 content=f"Hand-thrown {piece['name'].lower()} for paint-your-own pottery sessions at Floating By Everyday. " + piece['desc'].capitalize() + '.',
                 excerpt=piece['desc'].capitalize() + '.',
                 featured_media=media['id'],
                 menu_order={'beginner':1,'intermediate':2,'advanced':3}[piece['difficulty']] * 100 + piece['price'],
                 meta={
                     '_fb_price': piece['price'],
                     '_fb_difficulty': piece['difficulty'],
                     '_fb_category': piece['category'],
                 })
    if pid:
        pieces_created += 1
        # Belt and suspenders: ensure meta is saved
        set_meta(pid, 'pieces', {
            '_fb_price': piece['price'],
            '_fb_difficulty': piece['difficulty'],
            '_fb_category': piece['category'],
        })
print(f'  {pieces_created} pieces')

# =====================================================================
# 2. PACKAGES
# =====================================================================
print('Seeding packages...')
PACKAGES = [
    {'slug':'solo-creative', 'name':'Solo Creative', 'price':599,
     'desc':'A perfect afternoon for one.',
     'seats':1, 'featured': False,
     'includes':['1 studio seat','1 medium pottery piece','All standard paints and tools','1 chai or coffee from the cafe','Kiln firing and packaging']},
    {'slug':'date-night', 'name':'Date Night', 'price':1199,
     'desc':'Slow down together with paint and stories.',
     'seats':2, 'featured': True,
     'includes':['2 studio seats','2 pottery pieces','Premium paint set included','2 drinks from the cafe','Kiln firing and packaging','Reserved corner table']},
    {'slug':'family-pack', 'name':'Family Pack of 4', 'price':2199,
     'desc':'Made for the family that creates together.',
     'seats':4, 'featured': False,
     'includes':['4 studio seats','4 pottery pieces of choice','All standard and premium paints','4 drinks from the cafe','Kiln firing and packaging','One free dessert to share']},
    {'slug':'kitty-party', 'name':'Kitty Party for 8', 'price':4499,
     'desc':'Your group, your pace, your music.',
     'seats':8, 'featured': False,
     'includes':['8 studio seats','8 pottery pieces','Snack platter for the table','Drinks for everyone','Reserved studio space','Photo of your group']},
    {'slug':'corporate', 'name':'Corporate Day Out', 'price':5999,
     'desc':'A team-building afternoon that people actually enjoy.',
     'seats':10, 'featured': False,
     'includes':['10 studio seats','10 pottery pieces','Tea and coffee station','Light snacks','Certificate of completion','Optional facilitated activity']},
    {'slug':'kids-birthday', 'name':"Kids' Birthday", 'price':3499,
     'desc':'A clean, creative party with no mess at home.',
     'seats':6, 'featured': False,
     'includes':['6 kids seats','6 pottery pieces','All paints and aprons','Cake table and decor','Return gift packaging','Group photo']},
]
for p in PACKAGES:
    pid = upsert('packages', p['slug'], p['name'],
                 content=p['desc'],
                 menu_order=PACKAGES.index(p),
                 meta={
                     '_fb_price': p['price'],
                     '_fb_seats': p['seats'],
                     '_fb_includes': '\n'.join(p['includes']),
                     '_fb_featured': p['featured'],
                 })
    if pid:
        set_meta(pid, 'packages', {
            '_fb_price': p['price'],
            '_fb_seats': p['seats'],
            '_fb_includes': '\n'.join(p['includes']),
            '_fb_featured': 1 if p['featured'] else 0,
        })
print(f"  {len(PACKAGES)} packages")

# =====================================================================
# 3. EVENTS
# =====================================================================
print('Seeding events...')
EVENTS = [
    {'slug':'kids-birthday-party', 'name':"Kids' Birthday Party", 'audience':'Ages 5 to 12', 'min_group':6, 'duration':'2 hours',
     'desc':'A bright, hands-on celebration where every child takes home something they made themselves.',
     'includes':'Pieces, paints, cake table, return gifts'},
    {'slug':'teen-adult-birthday', 'name':'Teen and Adult Birthday', 'audience':'Ages 13 and up', 'min_group':4, 'duration':'2 to 3 hours',
     'desc':'A warm, mellow birthday with your music and your people.',
     'includes':'Pieces, paints, cafe credit, your playlist'},
    {'slug':'corporate', 'name':'Corporate Team Building', 'audience':'Office groups', 'min_group':10, 'duration':'2 to 3 hours',
     'desc':'Work that does not feel like work. A facilitated session that gets quiet teams talking.',
     'includes':'Pieces, paints, tea and coffee, optional facilitator, certificates'},
    {'slug':'kitty-party', 'name':'Kitty Party', 'audience':"Women's groups", 'min_group':8, 'duration':'2 to 3 hours',
     'desc':'Bring the conversation, we will bring the clay and the chai.',
     'includes':'Pieces, paints, snack platter, reserved space'},
    {'slug':'bridal-baby-shower', 'name':'Bridal or Baby Shower', 'audience':'Adults', 'min_group':8, 'duration':'2 to 3 hours',
     'desc':'A sweet, slow celebration that lets the guest of honor sit and be made of.',
     'includes':'Themed pieces, soft decor, drinks, photo moments'},
    {'slug':'date-night-event', 'name':'Date Night', 'audience':'Couples 18 plus', 'min_group':2, 'duration':'2 hours',
     'desc':'Reserved table, two pieces, two drinks, and a piece of dessert.',
     'includes':'2 pieces, paints, coffee, dessert'},
    {'slug':'kids-camp', 'name':"Kids' Camp", 'audience':'Ages 5 to 12', 'min_group':6, 'duration':'Half or full day',
     'desc':'Multiple projects across the day. Snacks, water, and supervised play between sessions.',
     'includes':'Multiple projects, snacks, supervision, group photo'},
    {'slug':'school-trip', 'name':'School Field Trip', 'audience':'Ages 5 to 16', 'min_group':15, 'duration':'1.5 hours',
     'desc':'Curriculum-aligned and easy on teachers. We handle every supply.',
     'includes':'Curriculum-aligned activity, all supplies, teachers free'},
    {'slug':'festival-special', 'name':'Diwali, Holi or Rakhi Special', 'audience':'All ages', 'min_group':1, 'duration':'2 hours',
     'desc':'Festival-themed pieces and motifs. Diyas, rang plates, rakhi holders, and more.',
     'includes':'Festival pieces, themed paints, photo backdrop'},
]
for ev in EVENTS:
    pid = upsert('events', ev['slug'], ev['name'],
                 content=ev['desc'],
                 menu_order=EVENTS.index(ev),
                 meta={
                     '_fb_audience': ev['audience'],
                     '_fb_min_group': ev['min_group'],
                     '_fb_duration': ev['duration'],
                     '_fb_includes': ev['includes'],
                 })
    if pid:
        set_meta(pid, 'events', {
            '_fb_audience': ev['audience'],
            '_fb_min_group': ev['min_group'],
            '_fb_duration': ev['duration'],
            '_fb_includes': ev['includes'],
        })
print(f"  {len(EVENTS)} events")

# =====================================================================
# 4. CAFE MENU
# =====================================================================
print('Seeding cafe menu...')
MENU = [
    # Hot Drinks
    ('Hot Drinks', 'Masala Chai', 80, 'Cardamom, ginger, fresh milk', 'Veg'),
    ('Hot Drinks', 'Filter Coffee', 90, 'South Indian, frothed', 'Veg'),
    ('Hot Drinks', 'Ginger Tea', 70, 'Strong, no sugar by default', 'Veg, Vegan'),
    ('Hot Drinks', 'Espresso', 110, 'A small, dark moment', 'Veg'),
    ('Hot Drinks', 'Cappuccino', 140, 'Foamed milk, cocoa dust', 'Veg'),
    ('Hot Drinks', 'Cafe Latte', 150, 'Smooth and milky', 'Veg'),
    ('Hot Drinks', 'Hot Chocolate', 160, 'Classic or hazelnut', 'Veg'),
    ('Hot Drinks', 'Turmeric Latte', 140, 'Golden milk with cinnamon', 'Veg, Vegan option'),
    # Cold Drinks
    ('Cold Drinks', 'Cold Coffee', 150, 'Slow brewed, lightly sweet', 'Veg'),
    ('Cold Drinks', 'Cold Brew', 160, 'Steeped 16 hours', 'Veg, Vegan'),
    ('Cold Drinks', 'Mango Lassi', 140, 'Seasonal Alphonso', 'Veg'),
    ('Cold Drinks', 'Spiced Buttermilk', 90, 'Cumin, mint, salt', 'Veg'),
    ('Cold Drinks', 'Fresh Lime Soda', 80, 'Sweet, salty or mixed', 'Veg, Vegan'),
    ('Cold Drinks', 'Kokum Sharbat', 90, 'A Konkan classic', 'Veg, Vegan'),
    ('Cold Drinks', 'Iced Tea', 120, 'Peach or lemon', 'Veg, Vegan'),
    ('Cold Drinks', 'Banana Date Smoothie', 170, 'Naturally sweet', 'Veg, Vegan'),
    # Snacks
    ('Snacks', 'Samosa', 60, 'Two pieces with chutney', 'Veg, Jain option'),
    ('Snacks', 'Vada Pav', 70, 'Mumbai street style', 'Veg'),
    ('Snacks', 'Bread Pakora', 80, 'Aloo or paneer', 'Veg'),
    ('Snacks', 'Grilled Paneer Sandwich', 180, 'Mint chutney, masala', 'Veg'),
    ('Snacks', 'Veg Club Sandwich', 200, 'Triple decker, fries on the side', 'Veg'),
    ('Snacks', 'Fresh Cookies', 60, 'Choc chip or oat raisin', 'Veg'),
    ('Snacks', 'Brownie', 110, 'Warm, with optional ice cream', 'Veg'),
    ('Snacks', 'Chocolate Cake Slice', 150, 'Dense, rich', 'Veg'),
    # Light Meals
    ('Light Meals', 'Maggi Bowl', 120, 'Classic or masala', 'Veg'),
    ('Light Meals', 'Pasta in Red Sauce', 220, 'Tomato, basil, parmesan', 'Veg'),
    ('Light Meals', 'Pasta in White Sauce', 240, 'Garlic cream, herbs', 'Veg'),
    ('Light Meals', 'Garlic Bread', 140, 'Cheese topped', 'Veg'),
    ('Light Meals', 'Bruschetta', 180, 'Tomato, basil, olive oil', 'Veg, Vegan'),
    ('Light Meals', 'Chips and Dips Platter', 220, 'Hummus, salsa, sour cream', 'Veg'),
]
for section, name, price, sub, diet in MENU:
    slug = name.lower().replace(' ', '-').replace(',','').replace("'",'')
    pid = upsert('menu', slug, name,
                 content=sub,
                 menu_order=MENU.index((section, name, price, sub, diet)),
                 meta={
                     '_fb_section': section,
                     '_fb_price': price,
                     '_fb_subtitle': sub,
                     '_fb_diet': diet,
                 })
    if pid:
        set_meta(pid, 'menu', {
            '_fb_section': section,
            '_fb_price': price,
            '_fb_subtitle': sub,
            '_fb_diet': diet,
        })
print(f"  {len(MENU)} menu items")

# =====================================================================
# 5. COUPONS (custom slug — not a public REST type, use admin endpoint)
# =====================================================================
# We'll seed a couple via WP-CLI from outside since fb_coupon isn't show_in_rest
# Skipping here — admin can add through dashboard
print('Coupons: admin can create via dashboard.')

print('\nDone seeding.')
