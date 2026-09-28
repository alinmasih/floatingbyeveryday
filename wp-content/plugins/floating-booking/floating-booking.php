<?php
/**
 * Plugin Name: Floating Booking
 * Description: Multi-step pre-booking flow, pottery piece catalog, packages, coupons, GST, slots, group discounts, email confirmation, and Razorpay handoff for Floating By Everyday.
 * Version: 1.0.0
 * Author: Floating By Everyday
 * Text Domain: floating-booking
 * Requires at least: 6.0
 * Requires PHP: 7.4
 *
 * @package FloatingBooking
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'FB_VERSION', '1.0.0' );
define( 'FB_FILE', __FILE__ );
define( 'FB_DIR', plugin_dir_path( __FILE__ ) );
define( 'FB_URI', plugin_dir_url( __FILE__ ) );

require_once FB_DIR . 'includes/class-cpt.php';
require_once FB_DIR . 'includes/class-rest.php';
require_once FB_DIR . 'includes/class-booking.php';
require_once FB_DIR . 'includes/class-shortcodes.php';
require_once FB_DIR . 'includes/class-admin.php';
require_once FB_DIR . 'includes/class-email.php';
require_once FB_DIR . 'includes/helpers.php';

register_activation_hook( __FILE__, function() {
    FB_CPT::register();
    flush_rewrite_rules();
    if ( ! get_option( 'fb_settings' ) ) {
        update_option( 'fb_settings', [
            'studio_fee'   => 250,
            'gst_rate'     => 18,
            'group_threshold' => 6,
            'group_discount_pct' => 10,
            'razorpay_key' => '',
            'sender_email' => 'hialinmasih@gmail.com',
            'sender_name'  => 'Floating By Everyday',
        ] );
    }
} );

add_action( 'init', [ 'FB_CPT', 'register' ] );
add_action( 'rest_api_init', [ 'FB_REST', 'register_routes' ] );
add_action( 'init', [ 'FB_Shortcodes', 'register' ] );

if ( is_admin() ) {
    add_action( 'admin_menu', [ 'FB_Admin', 'menu' ] );
    add_action( 'admin_init', [ 'FB_Admin', 'register_settings' ] );
    add_action( 'admin_enqueue_scripts', [ 'FB_Admin', 'assets' ] );
    add_action( 'add_meta_boxes', [ 'FB_Admin', 'add_meta_boxes' ] );
    add_action( 'save_post', [ 'FB_Admin', 'save_meta_boxes' ], 10, 2 );
}

add_action( 'wp_enqueue_scripts', function() {
    if ( is_page() ) {
        $tpl = get_post_meta( get_the_ID(), '_wp_page_template', true );
        $slug = get_post_field( 'post_name', get_the_ID() );
        if ( $slug === 'booking' || strpos( $tpl ?: '', 'booking' ) !== false ) {
            wp_enqueue_style( 'fb-booking', FB_URI . 'assets/booking.css', [], FB_VERSION );
            wp_enqueue_script( 'fb-booking', FB_URI . 'assets/booking.js', [ 'wp-api-fetch' ], FB_VERSION, true );
            wp_localize_script( 'fb-booking', 'FBData', [
                'restUrl' => esc_url_raw( rest_url( 'fb/v1' ) ),
                'nonce'   => wp_create_nonce( 'wp_rest' ),
                'gst'     => floatval( fb_setting( 'gst_rate', 18 ) ),
                'studioFee' => floatval( fb_setting( 'studio_fee', 250 ) ),
                'groupThreshold' => intval( fb_setting( 'group_threshold', 6 ) ),
                'groupDiscountPct' => floatval( fb_setting( 'group_discount_pct', 10 ) ),
            ] );
        }
    }
} );
