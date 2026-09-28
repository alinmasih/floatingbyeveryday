<?php
/**
 * Template Name: Booking
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>
<section class="section section--first">
  <div class="container medium">
    <div class="section-head section-head--left fade-up" style="text-align:left; margin-left:0">
      <span class="eyebrow">Book a seat</span>
      <h1 style="margin-bottom: 18px">Reserve your time at the studio.</h1>
      <p class="muted large" style="max-width: 620px">Pick a date and time, choose your pieces and paints, tell us who is coming, and you are set. We will email a confirmation immediately.</p>
    </div>
    <?php echo do_shortcode('[fb_booking_flow]'); ?>
  </div>
</section>
<?php get_footer();
