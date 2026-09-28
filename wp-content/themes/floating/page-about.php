<?php
/**
 * Template Name: About
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$attach = get_posts( [ 'post_type' => 'attachment', 'numberposts' => 30, 'post_mime_type' => 'image' ] );
$imgs = [];
foreach ( $attach as $a ) {
    if ( strpos( $a->post_name, 'profile' ) !== false ) $imgs[] = wp_get_attachment_image_url( $a->ID, 'full' );
    elseif ( preg_match( '/^\d{3}_.*_cover/', $a->post_name ) ) $imgs[] = wp_get_attachment_image_url( $a->ID, 'large' );
}
?>
<section class="section section--first">
  <div class="container medium">
    <div class="section-head section-head--left fade-up" style="text-align:left">
      <span class="eyebrow">Our story</span>
      <h1>A studio for slow afternoons.</h1>
    </div>
    <div class="split fade-up">
      <div>
        <p class="large" style="color: var(--c-ink-soft)">Floating By Everyday started with a wheel and a few brushes. We wanted a place in Raipur where you could sit, slow down, and make something with your hands without needing any experience.</p>
        <p class="muted">Today there are two studios. The painting studio at Samta Colony is where most people start. You walk in, pick a piece, and paint it your way. The making studio at Anand Nagar is for those who want the full pottery experience, throwing pieces from raw clay.</p>
        <p class="muted">Both studios share the same idea. Slower work, better afternoons, and a piece you take home that only you could have made.</p>
      </div>
      <div>
        <?php if ( ! empty( $imgs[1] ) ) echo '<img src="' . esc_url( $imgs[1] ) . '" alt="Floating By Everyday studio">'; ?>
      </div>
    </div>
  </div>
</section>

<section class="section section--cream">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Why people come back</span>
      <h2>What you can expect.</h2>
    </div>
    <div class="grid grid-3">
      <div class="feature-card fade-up">
        <div class="feature-card__num">01</div>
        <h3>No experience required</h3>
        <p>Most of our visitors have never painted ceramics before. We will set you up and stay near if you have questions.</p>
      </div>
      <div class="feature-card fade-up">
        <div class="feature-card__num">02</div>
        <h3>Real pieces, fired and finished</h3>
        <p>Whatever you paint goes into a kiln overnight. It comes out food-safe, glossy, and yours forever.</p>
      </div>
      <div class="feature-card fade-up">
        <div class="feature-card__num">03</div>
        <h3>Quietly social</h3>
        <p>Friends, families, dates, and solo visitors share the same space. Music is soft, conversation is welcome.</p>
      </div>
    </div>
  </div>
</section>

<section class="section section--ink">
  <div class="container medium text-center fade-up">
    <p class="quote" style="color: var(--c-cream)">If you are looking for a break from your own routine, try a Paint Your Own Pottery cafe. Creating something with your own hands is surprisingly relaxing and so much fun.</p>
    <p class="quote-author">From a visitor's note, in our Instagram</p>
  </div>
</section>

<?php get_footer();
