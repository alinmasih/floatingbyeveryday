<?php
/**
 * Template Name: Pricing
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>
<section class="section section--first">
  <div class="container medium">
    <div class="section-head fade-up">
      <span class="eyebrow">Pricing</span>
      <h1>Honest, simple pricing.</h1>
      <p class="muted large">A studio fee per person, your choice of pottery piece, and any add-ons you want. Group of 6 or more saves 10% automatically. GST is included on every quote.</p>
    </div>
    <div class="fade-up">
      <?php echo do_shortcode('[fb_pricing_table]'); ?>
    </div>
  </div>
</section>

<section class="section section--cream">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Packages</span>
      <h2>All in one. From solo to a kitty party.</h2>
      <p class="muted large">Bundles that take the math out of planning. Studio fee, pieces, paints, and drinks already calculated.</p>
    </div>
    <?php echo do_shortcode('[fb_packages]'); ?>
  </div>
</section>

<section class="section">
  <div class="container medium">
    <div class="section-head fade-up">
      <span class="eyebrow">FAQ</span>
      <h2 style="text-align:left">Common questions about price.</h2>
    </div>
    <div class="stack-lg">
      <div class="fade-up">
        <h3 style="font-family: var(--ff-serif); font-size: 22px">Is the studio fee in addition to the piece?</h3>
        <p class="muted">Yes. The studio fee covers your seat, apron, all standard paints, brushes, sponges, kiln firing, and packaging. The piece price is what you take home.</p>
      </div>
      <div class="fade-up">
        <h3 style="font-family: var(--ff-serif); font-size: 22px">Do I need to pay anything extra for tools?</h3>
        <p class="muted">No, the basics are included. Some special tools and technique kits are optional add-ons that you can pick during booking.</p>
      </div>
      <div class="fade-up">
        <h3 style="font-family: var(--ff-serif); font-size: 22px">Can I cancel or change my booking?</h3>
        <p class="muted">Bookings are non-refundable but can be rescheduled with 24 hours notice. Just call or email us.</p>
      </div>
      <div class="fade-up">
        <h3 style="font-family: var(--ff-serif); font-size: 22px">Do you have group discounts?</h3>
        <p class="muted">Yes. 10% off the subtotal automatically when you book for 6 or more guests.</p>
      </div>
    </div>
  </div>
</section>

<section class="section--tight section--ink">
  <div class="container text-center fade-up">
    <h2 style="max-width: 600px; margin: 0 auto 24px; color: var(--c-cream)">No surprises. Book in two minutes.</h2>
    <a href="<?php echo esc_url( home_url('/booking/') ); ?>" class="btn btn--clay btn--lg">Book a seat</a>
  </div>
</section>

<?php get_footer();
