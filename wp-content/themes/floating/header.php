<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
<meta name="theme-color" content="#0a0a09" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main">Skip to content</a>

<header class="site-header" id="site-header">
  <div class="site-header__inner container">
    <div class="site-header__brand"><?php echo floating_logo(); ?></div>
    <nav class="site-nav" aria-label="Primary">
      <?php
      if ( has_nav_menu( 'primary' ) ) {
          wp_nav_menu( [ 'theme_location' => 'primary', 'container' => false, 'menu_class' => 'primary-menu', 'depth' => 2 ] );
      } else {
          floating_default_menu();
      }
      ?>
    </nav>
    <div class="site-header__cta">
      <a href="<?php echo esc_url( home_url('/booking/') ); ?>" class="btn btn--primary">Book a Seat</a>
    </div>
    <button class="hamburger" aria-controls="mobile-menu" aria-expanded="false" aria-label="Open menu">
      <span></span><span></span><span></span>
    </button>
  </div>
  <div class="site-header__mobile" id="mobile-menu" aria-hidden="true">
    <?php
    if ( has_nav_menu( 'primary' ) ) {
        wp_nav_menu( [ 'theme_location' => 'primary', 'container' => false, 'menu_class' => 'mobile-menu', 'depth' => 2 ] );
    } else {
        floating_default_menu();
    }
    ?>
    <a href="<?php echo esc_url( home_url('/booking/') ); ?>" class="btn btn--primary btn--block">Book a Seat</a>
  </div>
</header>

<main id="main" class="site-main">
