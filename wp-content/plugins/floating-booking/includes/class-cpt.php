<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class FB_CPT {

    public static function register() {

        register_post_type( 'fb_piece', [
            'label' => 'Pottery Pieces',
            'labels' => [
                'name' => 'Pottery Pieces',
                'singular_name' => 'Pottery Piece',
                'add_new_item' => 'Add Pottery Piece',
                'edit_item' => 'Edit Pottery Piece',
            ],
            'public' => true,
            'show_in_rest' => true,
            'rest_base' => 'pieces',
            'menu_icon' => 'dashicons-art',
            'has_archive' => false,
            'rewrite' => [ 'slug' => 'pottery-piece' ],
            'supports' => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
            'menu_position' => 22,
        ] );

        register_taxonomy( 'fb_piece_category', 'fb_piece', [
            'label' => 'Categories',
            'public' => true,
            'show_in_rest' => true,
            'hierarchical' => true,
            'rewrite' => [ 'slug' => 'pottery-category' ],
        ] );

        register_post_type( 'fb_package', [
            'label' => 'Packages',
            'public' => false,
            'show_ui' => true,
            'show_in_rest' => true,
            'rest_base' => 'packages',
            'menu_icon' => 'dashicons-tickets-alt',
            'supports' => [ 'title', 'editor', 'thumbnail' ],
            'menu_position' => 23,
        ] );

        register_post_type( 'fb_event', [
            'label' => 'Event Types',
            'public' => false,
            'show_ui' => true,
            'show_in_rest' => true,
            'rest_base' => 'events',
            'menu_icon' => 'dashicons-buddicons-buddypress-logo',
            'supports' => [ 'title', 'editor', 'thumbnail' ],
            'menu_position' => 24,
        ] );

        register_post_type( 'fb_menu_item', [
            'label' => 'Cafe Menu',
            'public' => false,
            'show_ui' => true,
            'show_in_rest' => true,
            'rest_base' => 'menu',
            'menu_icon' => 'dashicons-coffee',
            'supports' => [ 'title', 'editor' ],
            'menu_position' => 25,
        ] );

        register_taxonomy( 'fb_menu_section', 'fb_menu_item', [
            'label' => 'Menu Section',
            'public' => false,
            'show_ui' => true,
            'show_in_rest' => true,
            'hierarchical' => true,
        ] );

        register_post_type( 'fb_booking', [
            'label' => 'Bookings',
            'public' => false,
            'show_ui' => true,
            'show_in_rest' => false,
            'menu_icon' => 'dashicons-calendar-alt',
            'supports' => [ 'title' ],
            'menu_position' => 21,
            'capabilities' => [ 'create_posts' => 'do_not_allow' ],
            'map_meta_cap' => true,
        ] );

        register_post_type( 'fb_coupon', [
            'label' => 'Coupons',
            'public' => false,
            'show_ui' => true,
            'show_in_rest' => false,
            'menu_icon' => 'dashicons-tag',
            'supports' => [ 'title' ],
            'menu_position' => 26,
        ] );

        register_post_meta( 'fb_piece', '_fb_price', [
            'type' => 'number', 'single' => true, 'show_in_rest' => true,
        ] );
        register_post_meta( 'fb_piece', '_fb_difficulty', [
            'type' => 'string', 'single' => true, 'show_in_rest' => true,
        ] );
        register_post_meta( 'fb_piece', '_fb_category', [
            'type' => 'string', 'single' => true, 'show_in_rest' => true,
        ] );

        register_post_meta( 'fb_package', '_fb_price', [ 'type' => 'number', 'single' => true, 'show_in_rest' => true ] );
        register_post_meta( 'fb_package', '_fb_includes', [ 'type' => 'string', 'single' => true, 'show_in_rest' => true ] );
        register_post_meta( 'fb_package', '_fb_seats', [ 'type' => 'number', 'single' => true, 'show_in_rest' => true ] );
        register_post_meta( 'fb_package', '_fb_featured', [ 'type' => 'boolean', 'single' => true, 'show_in_rest' => true ] );

        register_post_meta( 'fb_menu_item', '_fb_price', [ 'type' => 'number', 'single' => true, 'show_in_rest' => true ] );
        register_post_meta( 'fb_menu_item', '_fb_section', [ 'type' => 'string', 'single' => true, 'show_in_rest' => true ] );
        register_post_meta( 'fb_menu_item', '_fb_diet', [ 'type' => 'string', 'single' => true, 'show_in_rest' => true ] );
        register_post_meta( 'fb_menu_item', '_fb_subtitle', [ 'type' => 'string', 'single' => true, 'show_in_rest' => true ] );

        register_post_meta( 'fb_event', '_fb_min_group', [ 'type' => 'number', 'single' => true, 'show_in_rest' => true ] );
        register_post_meta( 'fb_event', '_fb_duration', [ 'type' => 'string', 'single' => true, 'show_in_rest' => true ] );
        register_post_meta( 'fb_event', '_fb_includes', [ 'type' => 'string', 'single' => true, 'show_in_rest' => true ] );
        register_post_meta( 'fb_event', '_fb_audience', [ 'type' => 'string', 'single' => true, 'show_in_rest' => true ] );
    }
}
