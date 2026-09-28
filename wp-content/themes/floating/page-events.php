<?php
/**
 * Template Name: Events & Parties
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>
<section class="section section--first">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Events &amp; Parties</span>
      <h1>The clean, creative party.</h1>
      <p class="muted large">Birthdays, kitty parties, baby showers, corporate days. We host the people, the paints, and the snacks. You bring the playlist.</p>
    </div>
    <?php echo do_shortcode('[fb_event_grid]'); ?>
  </div>
</section>

<section class="section section--cream">
  <div class="container medium">
    <div class="section-head section-head--left fade-up" style="text-align:left">
      <span class="eyebrow">Plan your event</span>
      <h2>Tell us a little, we will reply quickly.</h2>
      <p class="muted">A working day or sooner. We will share availability, a quote, and a few sample themes that fit your group.</p>
    </div>
    <?php echo do_shortcode('[fb_event_form]'); ?>
  </div>
</section>

<?php get_footer();
