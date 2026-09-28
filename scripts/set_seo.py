#!/usr/bin/env python3
"""Set SEO meta titles and descriptions via AIOSEO post meta."""
import subprocess

CONTAINER = 'wordpress_app'

def wp(args):
    cmd = ['docker', 'exec', CONTAINER, 'bash', '-c', f"cd /var/www/html && ./wp-cli.phar {args} --allow-root"]
    r = subprocess.run(cmd, capture_output=True, text=True)
    return r.stdout.strip()

def find_id(slug):
    return wp(f'post list --post_type=page --name={slug} --field=ID')

PAGES = {
    'home': {
        'title': 'Floating By Everyday — Paint Your Own Pottery in Raipur',
        'desc': 'A pottery painting and making studio in Samta Colony and Anand Nagar, Raipur. Walk in, paint a piece, take it home. Birthday parties, date nights, kids camps and more.',
    },
    'gallery': {
        'title': 'Pottery Pieces to Paint — Floating By Everyday',
        'desc': 'Browse 18 hand-thrown pottery pieces ready to paint at our Raipur studio. Chai cups, thali plates, diyas, Ganesh figurines, vases, planters and more from ₹150.',
    },
    'pricing': {
        'title': 'Pricing — Studio Fee, Pieces, Packages | Floating By Everyday',
        'desc': 'Honest, simple pricing. Studio fee from ₹250 per person plus your choice of pottery piece. Group discount on six or more. Packages from ₹599. GST included.',
    },
    'cafe': {
        'title': 'Cafe Menu — Floating By Everyday Pottery Studio Raipur',
        'desc': 'Filter coffee, masala chai, fresh sandwiches and warm brownies inside the studio. All vegetarian, with vegan and Jain options.',
    },
    'events': {
        'title': 'Birthday Parties, Corporate, Kitty Parties — Floating By Everyday',
        'desc': 'Host your kids birthday, corporate team building, kitty party, or bridal shower at our pottery studio in Raipur. Send an enquiry, we will get back the same day.',
    },
    'booking': {
        'title': 'Book a Seat at Floating By Everyday — Raipur Pottery Studio',
        'desc': 'Reserve your seat to paint pottery in Raipur. Pick a date, time, pieces and paints in two minutes. Email confirmation sent immediately.',
    },
    'about': {
        'title': 'Our Story — Floating By Everyday Pottery Studio',
        'desc': 'Two studios in Raipur, one slow practice. Pottery painting at Samta Colony and pottery making at Anand Nagar. No experience required.',
    },
    'contact': {
        'title': 'Contact and Visit — Floating By Everyday',
        'desc': 'Find us in Samta Colony and Anand Nagar, Raipur. Open Tuesday to Sunday, 11am to 8pm. Walk in, message us, or call.',
    },
}

for slug, m in PAGES.items():
    pid = find_id(slug)
    if not pid: continue
    # AIOSEO uses postmeta keys _aioseo_title and _aioseo_description
    title = m['title'].replace("'", "\\'")
    desc = m['desc'].replace("'", "\\'")
    wp(f"post meta update {pid} _aioseo_title '{title}'")
    wp(f"post meta update {pid} _aioseo_description '{desc}'")
    print(f'  {slug} ({pid}): {m["title"][:50]}...')

print('SEO meta set.')
