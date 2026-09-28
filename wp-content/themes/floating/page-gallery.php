<?php
/**
 * Template Name: Gallery & Pieces
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$attach = get_posts( [ 'post_type' => 'attachment', 'numberposts' => 80, 'post_mime_type' => 'image' ] );
$ig_imgs = [];
foreach ( $attach as $a ) {
    if ( strpos( $a->post_name, 'profile' ) !== false ) continue;
    if ( ! preg_match( '/^\d{3}_/', $a->post_name ) ) continue;
    $ig_imgs[] = wp_get_attachment_image_url( $a->ID, 'large' );
}
$ig_imgs = array_values( array_unique( $ig_imgs ) );
?>
<section class="section section--first">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Pottery pieces</span>
      <h1>Every piece, ready to paint.</h1>
      <p class="muted large">From a small chai cup to a tall cylinder vase. Pick your favorite, and we will have it on the table for you.</p>
    </div>
    <?php echo do_shortcode('[fb_pottery_grid]'); ?>
  </div>
</section>

<section class="section section--cream">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Inspiration</span>
      <h2>Painted by visitors like you.</h2>
      <p class="muted large">Madhubani, Warli, Mandala, Floral Mughal, Rajasthani patterns. A look at what came out of the kiln.</p>
    </div>
    <div class="masonry">
      <?php $count = 0; foreach ( $ig_imgs as $url ) { if ( $count++ >= 24 ) break; echo '<div class="masonry-item fade-up"><img src="' . esc_url( $url ) . '" alt="Painted pottery" loading="lazy"></div>'; } ?>
    </div>
  </div>
</section>

<section class="section--tight section--clay">
  <div class="container text-center fade-up">
    <h2 style="max-width: 600px; margin: 0 auto 24px">Found one you love?</h2>
    <a href="<?php echo esc_url( home_url('/booking/') ); ?>" class="btn btn--lg" style="background: white; color: var(--c-clay-deep)">Book a seat to paint it</a>
  </div>
</section>

<?php get_footer();
