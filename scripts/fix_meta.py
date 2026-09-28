#!/usr/bin/env python3
"""Set post meta on every fb_piece, fb_package, fb_event, fb_menu_item via WP-CLI inside the container."""
import json, subprocess, shlex

CONTAINER = 'wordpress_app'

def wp(args):
    cmd = ['docker', 'exec', CONTAINER, 'bash', '-c', f"cd /var/www/html && ./wp-cli.phar {args} --allow-root"]
    r = subprocess.run(cmd, capture_output=True, text=True)
    if r.returncode != 0:
        print(f'  ERR: {r.stderr.strip()[:200]}')
        return None
    return r.stdout.strip()

def find_id(post_type, slug):
    out = wp(f"post list --post_type={post_type} --name={slug} --field=ID")
    return int(out) if out and out.isdigit() else None

def set_meta(post_id, key, value):
    if isinstance(value, bool):
        value = 1 if value else 0
    val_str = str(value).replace("'", "\\'")
    wp(f"post meta update {post_id} {key} '{val_str}'")

# Pottery pieces
catalog = json.load(open('data/raw/catalog.json'))
print('Setting pottery piece meta...')
for piece in catalog:
    pid = find_id('fb_piece', piece['slug'])
    if not pid: continue
    set_meta(pid, '_fb_price', piece['price'])
    set_meta(pid, '_fb_difficulty', piece['difficulty'])
    set_meta(pid, '_fb_category', piece['category'])
print(f"  {len(catalog)} pieces")

# Packages
PACKAGES = [
    {'slug':'solo-creative', 'price':599, 'seats':1, 'featured':0, 'includes':'1 studio seat\n1 medium pottery piece\nAll standard paints and tools\n1 chai or coffee from the cafe\nKiln firing and packaging'},
    {'slug':'date-night', 'price':1199, 'seats':2, 'featured':1, 'includes':'2 studio seats\n2 pottery pieces\nPremium paint set included\n2 drinks from the cafe\nKiln firing and packaging\nReserved corner table'},
    {'slug':'family-pack', 'price':2199, 'seats':4, 'featured':0, 'includes':'4 studio seats\n4 pottery pieces of choice\nAll standard and premium paints\n4 drinks from the cafe\nKiln firing and packaging\nOne free dessert to share'},
    {'slug':'kitty-party', 'price':4499, 'seats':8, 'featured':0, 'includes':'8 studio seats\n8 pottery pieces\nSnack platter for the table\nDrinks for everyone\nReserved studio space\nPhoto of your group'},
    {'slug':'corporate', 'price':5999, 'seats':10, 'featured':0, 'includes':'10 studio seats\n10 pottery pieces\nTea and coffee station\nLight snacks\nCertificate of completion\nOptional facilitated activity'},
    {'slug':'kids-birthday', 'price':3499, 'seats':6, 'featured':0, 'includes':"6 kids seats\n6 pottery pieces\nAll paints and aprons\nCake table and decor\nReturn gift packaging\nGroup photo"},
]
print('Setting package meta...')
for p in PACKAGES:
    pid = find_id('fb_package', p['slug'])
    if not pid: continue
    set_meta(pid, '_fb_price', p['price'])
    set_meta(pid, '_fb_seats', p['seats'])
    set_meta(pid, '_fb_featured', p['featured'])
    # writing newlines via shell is tricky — write to temp file
    inc = p['includes']
    cmd = ['docker', 'exec', '-i', CONTAINER, 'bash', '-c', f"cd /var/www/html && ./wp-cli.phar post meta update {pid} _fb_includes --allow-root"]
    subprocess.run(cmd, input=inc, capture_output=True, text=True)
print(f"  {len(PACKAGES)} packages")

# Events
EVENTS = [
    {'slug':'kids-birthday-party', 'audience':'Ages 5 to 12', 'min_group':6, 'duration':'2 hours', 'includes':'Pieces, paints, cake table, return gifts'},
    {'slug':'teen-adult-birthday', 'audience':'Ages 13 and up', 'min_group':4, 'duration':'2 to 3 hours', 'includes':'Pieces, paints, cafe credit, your playlist'},
    {'slug':'corporate', 'audience':'Office groups', 'min_group':10, 'duration':'2 to 3 hours', 'includes':'Pieces, paints, tea and coffee, optional facilitator, certificates'},
    {'slug':'kitty-party', 'audience':"Women's groups", 'min_group':8, 'duration':'2 to 3 hours', 'includes':'Pieces, paints, snack platter, reserved space'},
    {'slug':'bridal-baby-shower', 'audience':'Adults', 'min_group':8, 'duration':'2 to 3 hours', 'includes':'Themed pieces, soft decor, drinks, photo moments'},
    {'slug':'date-night-event', 'audience':'Couples 18 plus', 'min_group':2, 'duration':'2 hours', 'includes':'2 pieces, paints, coffee, dessert'},
    {'slug':'kids-camp', 'audience':'Ages 5 to 12', 'min_group':6, 'duration':'Half or full day', 'includes':'Multiple projects, snacks, supervision, group photo'},
    {'slug':'school-trip', 'audience':'Ages 5 to 16', 'min_group':15, 'duration':'1.5 hours', 'includes':'Curriculum-aligned activity, all supplies, teachers free'},
    {'slug':'festival-special', 'audience':'All ages', 'min_group':1, 'duration':'2 hours', 'includes':'Festival pieces, themed paints, photo backdrop'},
]
print('Setting event meta...')
for e in EVENTS:
    pid = find_id('fb_event', e['slug'])
    if not pid: continue
    for k,v in e.items():
        if k == 'slug': continue
        set_meta(pid, f'_fb_{k}', v)
print(f"  {len(EVENTS)} events")

# Menu
MENU = [
    ('Hot Drinks', 'masala-chai', 80, 'Cardamom, ginger, fresh milk', 'Veg'),
    ('Hot Drinks', 'filter-coffee', 90, 'South Indian, frothed', 'Veg'),
    ('Hot Drinks', 'ginger-tea', 70, 'Strong, no sugar by default', 'Veg, Vegan'),
    ('Hot Drinks', 'espresso', 110, 'A small, dark moment', 'Veg'),
    ('Hot Drinks', 'cappuccino', 140, 'Foamed milk, cocoa dust', 'Veg'),
    ('Hot Drinks', 'cafe-latte', 150, 'Smooth and milky', 'Veg'),
    ('Hot Drinks', 'hot-chocolate', 160, 'Classic or hazelnut', 'Veg'),
    ('Hot Drinks', 'turmeric-latte', 140, 'Golden milk with cinnamon', 'Veg, Vegan option'),
    ('Cold Drinks', 'cold-coffee', 150, 'Slow brewed, lightly sweet', 'Veg'),
    ('Cold Drinks', 'cold-brew', 160, 'Steeped 16 hours', 'Veg, Vegan'),
    ('Cold Drinks', 'mango-lassi', 140, 'Seasonal Alphonso', 'Veg'),
    ('Cold Drinks', 'spiced-buttermilk', 90, 'Cumin, mint, salt', 'Veg'),
    ('Cold Drinks', 'fresh-lime-soda', 80, 'Sweet, salty or mixed', 'Veg, Vegan'),
    ('Cold Drinks', 'kokum-sharbat', 90, 'A Konkan classic', 'Veg, Vegan'),
    ('Cold Drinks', 'iced-tea', 120, 'Peach or lemon', 'Veg, Vegan'),
    ('Cold Drinks', 'banana-date-smoothie', 170, 'Naturally sweet', 'Veg, Vegan'),
    ('Snacks', 'samosa', 60, 'Two pieces with chutney', 'Veg, Jain option'),
    ('Snacks', 'vada-pav', 70, 'Mumbai street style', 'Veg'),
    ('Snacks', 'bread-pakora', 80, 'Aloo or paneer', 'Veg'),
    ('Snacks', 'grilled-paneer-sandwich', 180, 'Mint chutney, masala', 'Veg'),
    ('Snacks', 'veg-club-sandwich', 200, 'Triple decker, fries on the side', 'Veg'),
    ('Snacks', 'fresh-cookies', 60, 'Choc chip or oat raisin', 'Veg'),
    ('Snacks', 'brownie', 110, 'Warm, with optional ice cream', 'Veg'),
    ('Snacks', 'chocolate-cake-slice', 150, 'Dense, rich', 'Veg'),
    ('Light Meals', 'maggi-bowl', 120, 'Classic or masala', 'Veg'),
    ('Light Meals', 'pasta-in-red-sauce', 220, 'Tomato, basil, parmesan', 'Veg'),
    ('Light Meals', 'pasta-in-white-sauce', 240, 'Garlic cream, herbs', 'Veg'),
    ('Light Meals', 'garlic-bread', 140, 'Cheese topped', 'Veg'),
    ('Light Meals', 'bruschetta', 180, 'Tomato, basil, olive oil', 'Veg, Vegan'),
    ('Light Meals', 'chips-and-dips-platter', 220, 'Hummus, salsa, sour cream', 'Veg'),
]
print('Setting menu meta...')
for section, slug, price, sub, diet in MENU:
    pid = find_id('fb_menu_item', slug)
    if not pid: continue
    set_meta(pid, '_fb_section', section)
    set_meta(pid, '_fb_price', price)
    set_meta(pid, '_fb_subtitle', sub)
    set_meta(pid, '_fb_diet', diet)
print(f"  {len(MENU)} items")

print('Done.')
