<?php
/**
 * Theme: Floating
 * Description: Premium custom theme for Floating By Everyday pottery studio.
 * Version: 1.0.0
 * Author: Built for Floating By Everyday
 * Text Domain: floating
 * Requires PHP: 7.4
 *
 * @package Floating
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'FLOATING_VERSION', '1.0.0' );
define( 'FLOATING_DIR', get_template_directory() );
define( 'FLOATING_URI', get_template_directory_uri() );

function floating_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo', [ 'height' => 80, 'width' => 240, 'flex-height' => true, 'flex-width' => true ] );
    add_theme_support( 'html5', [ 'search-form','comment-form','comment-list','gallery','caption','script','style' ] );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'editor-styles' );
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );

    register_nav_menus( [
        'primary' => __( 'Primary Menu', 'floating' ),
        'footer'  => __( 'Footer Menu', 'floating' ),
    ] );

    add_image_size( 'floating-hero', 1920, 1280, true );
    add_image_size( 'floating-card', 800, 1000, true );
    add_image_size( 'floating-square', 800, 800, true );
}
add_action( 'after_setup_theme', 'floating_setup' );

function floating_assets() {
    wp_enqueue_style( 'floating-fonts', 'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300;9..144,400;9..144,500;9..144,600&family=Inter:wght@300;400;500;600;700&display=swap', [], null );
    wp_enqueue_style( 'floating-style', FLOATING_URI . '/assets/css/style.css', [], FLOATING_VERSION );
    wp_enqueue_script( 'floating-script', FLOATING_URI . '/assets/js/main.js', [], FLOATING_VERSION, true );
    wp_localize_script( 'floating-script', 'FloatingData', [
        'restUrl' => esc_url_raw( rest_url() ),
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'wp_rest' ),
        'home'    => home_url('/'),
    ] );
}
add_action( 'wp_enqueue_scripts', 'floating_assets' );

function floating_brand() {
    return wp_parse_args( get_option( 'floating_brand', [] ), [
        'tagline'      => 'Paint Your Own Pottery in Raipur',
        'phone'        => '+91 9999999999',
        'whatsapp'     => '919999999999',
        'email'        => 'hialinmasih@gmail.com',
        'address_paint'=> 'Pottery Painting Studio, Samta Colony, Raipur, Chhattisgarh',
        'address_make' => 'Pottery Making Studio, Anand Nagar, Raipur, Chhattisgarh',
        'maps_link'    => 'https://maps.app.goo.gl/BQ2voBeQHDuctzh69',
        'instagram'    => 'https://www.instagram.com/floatingbyeveryday/',
        'hours'        => 'Open Tue to Sun, 11am to 8pm. Closed Mondays.',
    ] );
}

function floating_logo() {
    if ( has_custom_logo() ) {
        return get_custom_logo();
    }
    return '<a href="' . esc_url( home_url('/') ) . '" class="brand-mark"><span class="brand-mark__icon" aria-hidden="true"></span><span class="brand-mark__text"><strong>Floating</strong><em>by everyday</em></span></a>';
}

function floating_excerpt( $content, $length = 28 ) {
    $text = wp_strip_all_tags( $content );
    $words = preg_split( '/\s+/', trim( $text ) );
    if ( count( $words ) <= $length ) return implode( ' ', $words );
    return implode( ' ', array_slice( $words, 0, $length ) ) . '…';
}

function floating_body_classes( $classes ) {
    $classes[] = 'floating-theme';
    if ( is_front_page() ) $classes[] = 'is-front';
    return $classes;
}
add_filter( 'body_class', 'floating_body_classes' );

add_filter( 'woocommerce_currency_symbol', function( $sym, $cur ) {
    if ( $cur === 'INR' ) return '₹';
    return $sym;
}, 10, 2 );

require_once FLOATING_DIR . '/inc/menu-fallback.php';
