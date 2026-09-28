<?php
/**
 * Template Name: Cafe Menu
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>
<section class="section section--first">
  <div class="container medium">
    <div class="section-head fade-up">
      <span class="eyebrow">Cafe</span>
      <h1>Sip, nibble, paint.</h1>
      <p class="muted large">A small cafe inside the studio. Filter coffee, masala chai, fresh sandwiches, and warm brownies. All vegetarian, with vegan and Jain options where you see the labels.</p>
    </div>
    <div class="fade-up">
      <?php echo do_shortcode('[fb_cafe_menu]'); ?>
    </div>
  </div>
</section>

<section class="section--tight section--cream">
  <div class="container medium text-center fade-up">
    <p class="muted">Add a drink or snack to your booking and we will have it ready when you sit down.</p>
    <a href="<?php echo esc_url( home_url('/booking/') ); ?>" class="btn btn--primary">Book a seat</a>
  </div>
</section>

<?php get_footer();
