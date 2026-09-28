#!/usr/bin/env python3
"""Create pages, set front page, build nav menu."""
import json, base64, urllib.request, urllib.error, subprocess

env = {l.split('=',1)[0]: l.split('=',1)[1].strip() for l in open('.env.local') if '=' in l and not l.startswith('#')}
WP_URL = env['WP_URL']
AUTH = base64.b64encode(f"{env['WP_USER']}:{env['WP_APP_PASS']}".encode()).decode()
HEADERS = {'Authorization': f'Basic {AUTH}', 'Content-Type': 'application/json'}

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

def upsert_page(slug, title, template):
    existing = api('GET', f'/wp/v2/pages?slug={slug}&status=any')
    payload = { 'title': title, 'status': 'publish', 'slug': slug, 'template': template, 'content': '' }
    if existing:
        return api('POST', f'/wp/v2/pages/{existing[0]["id"]}', payload)
    return api('POST', '/wp/v2/pages', payload)

PAGES = [
    ('home',     'Home',     'front-page.php'),  # but front-page.php is auto-used; just need a page named home
    ('gallery',  'Pottery Pieces',  'page-gallery.php'),
    ('pricing',  'Pricing',     'page-pricing.php'),
    ('cafe',     'Cafe Menu',   'page-cafe.php'),
    ('events',   'Events',      'page-events.php'),
    ('booking',  'Book a Seat', 'page-booking.php'),
    ('about',    'Our Story',   'page-about.php'),
    ('contact',  'Contact',     'page-contact.php'),
]

print('Creating pages...')
created = {}
for slug, title, tpl in PAGES:
    res = upsert_page(slug, title, tpl)
    if res:
        created[slug] = res['id']
        print(f"  {slug} (id={res['id']})")

# Set front page
print('Setting front page...')
home_id = created.get('home')
if home_id:
    cmds = [
        ['docker','exec','wordpress_app','bash','-c', f"cd /var/www/html && ./wp-cli.phar option update show_on_front page --allow-root && ./wp-cli.phar option update page_on_front {home_id} --allow-root"],
    ]
    for c in cmds:
        subprocess.run(c, capture_output=True)

# Build menu via WP-CLI
print('Building menu...')
def wp(args):
    r = subprocess.run(['docker','exec','wordpress_app','bash','-c', f"cd /var/www/html && ./wp-cli.phar {args} --allow-root"], capture_output=True, text=True)
    return r.stdout.strip(), r.returncode

# Delete existing menu if present, then create fresh
wp('menu delete primary')
wp('menu create "Primary"')
menu_items = [
    ('Studio', 'about'),
    ('Pottery Pieces', 'gallery'),
    ('Pricing', 'pricing'),
    ('Cafe', 'cafe'),
    ('Events', 'events'),
    ('Contact', 'contact'),
]
for name, slug in menu_items:
    pid = created.get(slug)
    if pid:
        wp(f'menu item add-post primary {pid} --title="{name}"')
wp('menu location assign primary primary')
print('Done.')

# Save brand option
brand = {
    'tagline': 'Paint Your Own Pottery in Raipur',
    'phone': '+91 9999999999',
    'whatsapp': '919999999999',
    'email': 'hialinmasih@gmail.com',
    'address_paint': 'Pottery Painting Studio, Samta Colony, Raipur, Chhattisgarh',
    'address_make':  'Pottery Making Studio, Anand Nagar, Raipur, Chhattisgarh',
    'maps_link': 'https://maps.app.goo.gl/BQ2voBeQHDuctzh69',
    'instagram': 'https://www.instagram.com/floatingbyeveryday/',
    'hours': 'Open Tuesday to Sunday, 11am to 8pm. Closed Mondays.',
}
brand_json = json.dumps(brand).replace("'", "'\\''")
subprocess.run(['docker','exec','wordpress_app','bash','-c', f"cd /var/www/html && ./wp-cli.phar option update floating_brand '{brand_json}' --format=json --allow-root"], capture_output=True)

# Save fb_settings
fb_settings = {
    'studio_fee': 250,
    'gst_rate': 18,
    'group_threshold': 6,
    'group_discount_pct': 10,
    'sender_email': 'hialinmasih@gmail.com',
    'sender_name': 'Floating By Everyday',
}
fb_json = json.dumps(fb_settings).replace("'", "'\\''")
subprocess.run(['docker','exec','wordpress_app','bash','-c', f"cd /var/www/html && ./wp-cli.phar option update fb_settings '{fb_json}' --format=json --allow-root"], capture_output=True)

print('Brand and settings saved.')
