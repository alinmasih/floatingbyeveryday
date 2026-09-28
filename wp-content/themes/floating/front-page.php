<?php
/**
 * Front page — Home
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

$brand = floating_brand();

// Pull a curated set of post covers for the hero and gallery
$gallery_imgs = [];
$attach = get_posts( [ 'post_type' => 'attachment', 'numberposts' => 50, 'post_mime_type' => 'image', 'orderby' => 'date', 'order' => 'DESC' ] );
foreach ( $attach as $a ) {
    if ( strpos( $a->post_name, 'profile' ) !== false ) continue;
    if ( strpos( $a->post_name, 'cover' ) !== false || strpos( $a->post_name, 'child' ) !== false ) {
        $gallery_imgs[] = wp_get_attachment_image_url( $a->ID, 'large' );
    }
}
$gallery_imgs = array_values( array_unique( $gallery_imgs ) );

// Hero image: prefer first post cover
$hero_img = $gallery_imgs[0] ?? '';

// Featured pieces: 6 most varied
$featured_pieces = fb_get_pieces( [ 'posts_per_page' => 6, 'orderby' => 'rand' ] );
?>

<!-- HERO -->
<section class="hero">
  <div class="container">
    <div class="hero__grid">
      <div class="hero__copy">
        <span class="eyebrow">Paint Your Own Pottery · Raipur</span>
        <h1 class="hero__title">Slow afternoons,<br><em>handmade memories.</em></h1>
        <p class="hero__lede">A studio in Samta Colony where you sit, sip something warm, and paint a piece of pottery that comes home with you. Walk in or book ahead.</p>
        <div class="hero__cta">
          <a href="<?php echo esc_url( home_url('/booking/') ); ?>" class="btn btn--primary btn--lg">Book a seat</a>
          <a href="<?php echo esc_url( home_url('/gallery/') ); ?>" class="btn btn--ghost btn--lg">See pottery pieces</a>
        </div>
        <div class="stats">
          <div class="stat"><div class="stat__num">4,800+</div><div class="stat__label">Followers</div></div>
          <div class="stat"><div class="stat__num">2</div><div class="stat__label">Studios in Raipur</div></div>
          <div class="stat"><div class="stat__num">18</div><div class="stat__label">Pieces to paint</div></div>
          <div class="stat"><div class="stat__num">All ages</div><div class="stat__label">Welcome</div></div>
        </div>
      </div>
      <div class="hero__media">
        <?php if ( $hero_img ) : ?><img src="<?php echo esc_url( $hero_img ); ?>" alt="Inside Floating By Everyday studio" loading="eager" fetchpriority="high"><?php endif; ?>
        <div class="hero__meta">
          <span class="hero__chip">📍 Samta Colony, Raipur</span>
          <span class="hero__chip"><strong>Open today</strong> · 11am to 8pm</span>
        </div>
      </div>
    </div>
  </div>

  <div class="hero__marquee" aria-hidden="true">
    <div class="hero__marquee-track">
      <span>Paint your own pottery</span><span>Handmade in Raipur</span><span>Birthday parties</span><span>Date nights</span><span>Kids welcome</span><span>Walk in or book ahead</span><span>Paint your own pottery</span><span>Handmade in Raipur</span><span>Birthday parties</span><span>Date nights</span><span>Kids welcome</span><span>Walk in or book ahead</span>
    </div>
  </div>
</section>

<!-- VALUE / HOW IT WORKS -->
<section class="section section--cream">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">How it works</span>
      <h2>Four steps. One piece. Yours forever.</h2>
      <p class="muted large">No experience needed. We hand you a brush, a piece, and an apron. You leave a few hours later with something only you could have made.</p>
    </div>
    <div class="grid grid-4">
      <div class="feature-card fade-up">
        <div class="feature-card__num">01</div>
        <h3>Pick your piece</h3>
        <p>Chai cups, thali plates, diyas, Ganesh figurines, planters, vases. From ₹150 to ₹1,500.</p>
      </div>
      <div class="feature-card fade-up">
        <div class="feature-card__num">02</div>
        <h3>Choose your colors</h3>
        <p>Standard paints are included. Add metallic, pearl, or detail tools for the look you want.</p>
      </div>
      <div class="feature-card fade-up">
        <div class="feature-card__num">03</div>
        <h3>Paint at your pace</h3>
        <p>An hour, ninety minutes, or two. Sip something from the cafe while you work.</p>
      </div>
      <div class="feature-card fade-up">
        <div class="feature-card__num">04</div>
        <h3>Take it home</h3>
        <p>We fire and finish your piece, then pack it for you to carry home the same evening.</p>
      </div>
    </div>
  </div>
</section>

<!-- PIECES PREVIEW -->
<section class="section">
  <div class="container">
    <div class="between fade-up" style="margin-bottom: 56px">
      <div>
        <span class="eyebrow">Pottery pieces</span>
        <h2 style="max-width: 540px">Pick a piece that feels like you.</h2>
      </div>
      <a href="<?php echo esc_url( home_url('/gallery/') ); ?>" class="btn btn--ghost">See all pieces</a>
    </div>
    <div class="grid grid-3">
      <?php foreach ( $featured_pieces as $p ) : ?>
        <a class="piece-card fade-up" href="<?php echo esc_url( home_url('/booking/') ); ?>">
          <div class="piece-card__image">
            <?php if ( $p['image'] ) : ?><img src="<?php echo esc_url( $p['image'] ); ?>" alt="<?php echo esc_attr( $p['name'] ); ?>" loading="lazy"><?php endif; ?>
          </div>
          <div class="piece-card__body">
            <div class="piece-card__cat"><?php echo esc_html( $p['category'] ); ?></div>
            <h3 class="piece-card__name"><?php echo esc_html( $p['name'] ); ?></h3>
            <div class="piece-card__row">
              <div class="piece-card__price"><?php echo fb_format_price( $p['price'] ); ?></div>
              <span class="piece-card__diff <?php echo esc_attr( $p['difficulty'] ); ?>"><?php echo esc_html( $p['difficulty'] ); ?></span>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- PACKAGES -->
<section class="section section--cream">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Packages</span>
      <h2>Made for the way you visit.</h2>
      <p class="muted large">A solo afternoon, a date night, or a kitty party for eight. We put together everything you need so you only have to show up.</p>
    </div>
    <?php echo do_shortcode('[fb_packages]'); ?>
  </div>
</section>

<!-- ABOUT / STORY -->
<section class="section">
  <div class="container">
    <div class="split">
      <div class="fade-up">
        <span class="eyebrow">Our studio</span>
        <h2>Two studios. One slow practice.</h2>
        <p class="muted large">Floating By Everyday is a pottery painting studio at Samta Colony and a pottery making studio at Anand Nagar, both in Raipur. We bring people together over clay, color, and good light.</p>
        <p class="muted">It started small, with a wheel and a few brushes. Today it is the place in Raipur where birthdays, first dates, and quiet Sundays happen, and where people leave with something they made themselves.</p>
        <a href="<?php echo esc_url( home_url('/about/') ); ?>" class="link">Read our story</a>
      </div>
      <div class="fade-up">
        <?php if ( ! empty( $gallery_imgs[2] ) ) : ?>
          <img src="<?php echo esc_url( $gallery_imgs[2] ); ?>" alt="Floating By Everyday studio interior" loading="lazy">
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- GALLERY (Instagram) -->
<section class="section section--ink">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">From the studio</span>
      <h2>Quietly painted, brightly remembered.</h2>
      <p class="muted large">A glimpse of work made by people just like you. Plates, diyas, glasses holders, mandalas, marbled glaze.</p>
    </div>
    <div class="masonry">
      <?php
      $count = 0;
      foreach ( $gallery_imgs as $url ) {
          if ( $count++ >= 12 ) break;
          echo '<div class="masonry-item fade-up"><img src="' . esc_url( $url ) . '" alt="Studio work" loading="lazy"></div>';
      }
      ?>
    </div>
    <div class="text-center fade-up" style="margin-top: 56px">
      <a href="<?php echo esc_url( $brand['instagram'] ); ?>" target="_blank" rel="noopener" class="btn btn--ghost" style="border-color: rgba(251,246,236,0.4); color: var(--c-cream)">Follow on Instagram</a>
    </div>
  </div>
</section>

<!-- VISIT -->
<section class="section">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Visit</span>
      <h2>Find us in Raipur.</h2>
    </div>
    <div class="grid grid-2">
      <div class="studio-card fade-up">
        <span class="pin">⬤ Painting Studio</span>
        <h3>Paint Your Own Pottery</h3>
        <address><?php echo esc_html( $brand['address_paint'] ); ?></address>
        <p class="muted small">Walk in any day except Monday. Booking ahead means a guaranteed seat at your time.</p>
        <a href="<?php echo esc_url( $brand['maps_link'] ); ?>" target="_blank" rel="noopener" class="link">Open in Google Maps</a>
      </div>
      <div class="studio-card fade-up">
        <span class="pin">⬤ Making Studio</span>
        <h3>Pottery Making</h3>
        <address><?php echo esc_html( $brand['address_make'] ); ?></address>
        <p class="muted small">Throw pieces from scratch, the full pottery experience. Saturdays and Sundays.</p>
        <a href="<?php echo esc_url( $brand['maps_link'] ); ?>" target="_blank" rel="noopener" class="link">Open in Google Maps</a>
      </div>
    </div>
  </div>
</section>

<!-- CTA BAND -->
<section class="section--tight section--clay">
  <div class="container text-center fade-up">
    <h2 style="max-width: 700px; margin: 0 auto 18px">Ready to pick up a brush?</h2>
    <p class="large" style="max-width: 540px; margin: 0 auto 32px; color: rgba(255,255,255,0.85)">Book a seat in two minutes. Come in. Make something only you would make.</p>
    <a href="<?php echo esc_url( home_url('/booking/') ); ?>" class="btn btn--lg" style="background: white; color: var(--c-clay-deep)">Book your seat</a>
  </div>
</section>

<!-- Floating CTA on mobile -->
<div class="float-bar">
  <p>Walk in or book a seat</p>
  <a href="<?php echo esc_url( home_url('/booking/') ); ?>" class="btn btn--clay btn--sm">Book</a>
</div>

<?php get_footer();
