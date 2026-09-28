<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function fb_setting( $key, $default = '' ) {
    $s = get_option( 'fb_settings', [] );
    return $s[ $key ] ?? $default;
}

function fb_format_price( $amount ) {
    return '₹' . number_format( floatval( $amount ), 0, '.', ',' );
}

function fb_get_pieces( $args = [] ) {
    $defaults = [
        'post_type' => 'fb_piece',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'menu_order title',
        'order' => 'ASC',
    ];
    $q = new WP_Query( wp_parse_args( $args, $defaults ) );
    $out = [];
    foreach ( $q->posts as $p ) {
        $out[] = [
            'id' => $p->ID,
            'name' => get_the_title( $p ),
            'slug' => $p->post_name,
            'category' => get_post_meta( $p->ID, '_fb_category', true ),
            'difficulty' => get_post_meta( $p->ID, '_fb_difficulty', true ) ?: 'beginner',
            'price' => floatval( get_post_meta( $p->ID, '_fb_price', true ) ),
            'image' => get_the_post_thumbnail_url( $p, 'floating-card' ) ?: get_the_post_thumbnail_url( $p, 'large' ),
            'excerpt' => $p->post_excerpt ?: floating_excerpt( $p->post_content, 18 ),
        ];
    }
    return $out;
}

function fb_get_packages() {
    $q = new WP_Query( [
        'post_type' => 'fb_package',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC',
    ] );
    $out = [];
    foreach ( $q->posts as $p ) {
        $out[] = [
            'id' => $p->ID,
            'name' => get_the_title( $p ),
            'description' => $p->post_content,
            'price' => floatval( get_post_meta( $p->ID, '_fb_price', true ) ),
            'seats' => intval( get_post_meta( $p->ID, '_fb_seats', true ) ),
            'includes' => array_filter( array_map( 'trim', explode( "\n", get_post_meta( $p->ID, '_fb_includes', true ) ) ) ),
            'featured' => (bool) get_post_meta( $p->ID, '_fb_featured', true ),
        ];
    }
    return $out;
}

function fb_get_menu_items_grouped() {
    $q = new WP_Query( [
        'post_type' => 'fb_menu_item',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'menu_order title',
        'order' => 'ASC',
    ] );
    $sections = [];
    foreach ( $q->posts as $p ) {
        $section = get_post_meta( $p->ID, '_fb_section', true ) ?: 'Other';
        $sections[ $section ][] = [
            'name' => get_the_title( $p ),
            'subtitle' => get_post_meta( $p->ID, '_fb_subtitle', true ),
            'price' => floatval( get_post_meta( $p->ID, '_fb_price', true ) ),
            'diet' => get_post_meta( $p->ID, '_fb_diet', true ),
        ];
    }
    return $sections;
}

function fb_get_events() {
    $q = new WP_Query( [
        'post_type' => 'fb_event',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC',
    ] );
    $out = [];
    foreach ( $q->posts as $p ) {
        $out[] = [
            'id' => $p->ID,
            'name' => get_the_title( $p ),
            'description' => $p->post_content,
            'audience' => get_post_meta( $p->ID, '_fb_audience', true ),
            'min_group' => intval( get_post_meta( $p->ID, '_fb_min_group', true ) ),
            'duration' => get_post_meta( $p->ID, '_fb_duration', true ),
            'includes' => get_post_meta( $p->ID, '_fb_includes', true ),
            'image' => get_the_post_thumbnail_url( $p, 'floating-card' ),
        ];
    }
    return $out;
}

function fb_default_slots( $date ) {
    $dow = (int) date( 'w', strtotime( $date ) );
    if ( $dow === 1 ) return [];
    return [
        [ 'time' => '11:00', 'label' => '11:00 AM', 'capacity' => 8 ],
        [ 'time' => '13:00', 'label' => '1:00 PM',  'capacity' => 8 ],
        [ 'time' => '15:00', 'label' => '3:00 PM',  'capacity' => 8 ],
        [ 'time' => '17:00', 'label' => '5:00 PM',  'capacity' => 8 ],
        [ 'time' => '19:00', 'label' => '7:00 PM',  'capacity' => 6 ],
    ];
}

function fb_slot_remaining( $date, $time ) {
    $slots = fb_default_slots( $date );
    $cap = 8;
    foreach ( $slots as $s ) if ( $s['time'] === $time ) { $cap = $s['capacity']; break; }
    $q = new WP_Query( [
        'post_type' => 'fb_booking',
        'post_status' => [ 'publish', 'pending' ],
        'posts_per_page' => -1,
        'meta_query' => [
            'relation' => 'AND',
            [ 'key' => '_fb_date', 'value' => $date ],
            [ 'key' => '_fb_time', 'value' => $time ],
        ],
    ] );
    $taken = 0;
    foreach ( $q->posts as $b ) {
        $taken += intval( get_post_meta( $b->ID, '_fb_guests', true ) );
    }
    return max( 0, $cap - $taken );
}

function fb_validate_coupon( $code, $subtotal ) {
    if ( ! $code ) return null;
    $q = new WP_Query( [
        'post_type' => 'fb_coupon',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'title' => $code,
    ] );
    if ( ! $q->posts ) return null;
    $c = $q->posts[0];
    $type = get_post_meta( $c->ID, '_fb_disc_type', true ) ?: 'percent';
    $val  = floatval( get_post_meta( $c->ID, '_fb_disc_value', true ) );
    $exp  = get_post_meta( $c->ID, '_fb_expiry', true );
    $min  = floatval( get_post_meta( $c->ID, '_fb_min_subtotal', true ) );
    if ( $exp && strtotime( $exp ) < strtotime( current_time('Y-m-d') ) ) return null;
    if ( $min && $subtotal < $min ) return null;
    $disc = $type === 'percent' ? round( $subtotal * $val / 100, 0 ) : $val;
    return [ 'code' => $code, 'amount' => $disc, 'type' => $type, 'value' => $val ];
}
