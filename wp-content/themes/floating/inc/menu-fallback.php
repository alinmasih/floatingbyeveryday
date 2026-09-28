<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function floating_default_menu() {
    $items = [
        [ 'Studio',   home_url('/about/') ],
        [ 'Gallery',  home_url('/gallery/') ],
        [ 'Pricing',  home_url('/pricing/') ],
        [ 'Cafe',     home_url('/cafe/') ],
        [ 'Events',   home_url('/events/') ],
        [ 'Contact',  home_url('/contact/') ],
    ];
    echo '<ul class="primary-menu">';
    foreach ( $items as $i ) {
        printf( '<li><a href="%s">%s</a></li>', esc_url($i[1]), esc_html($i[0]) );
    }
    echo '</ul>';
}
