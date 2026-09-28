<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$brand = floating_brand();
?>
</main><!-- /#main -->

<footer class="site-footer">
  <div class="container site-footer__grid">
    <div class="site-footer__brand">
      <h3 class="site-footer__logo">Floating <em>by Everyday</em></h3>
      <p class="muted"><?php echo esc_html( $brand['tagline'] ); ?></p>
      <p class="muted small"><?php echo esc_html( $brand['hours'] ); ?></p>
      <div class="socials">
        <a href="<?php echo esc_url($brand['instagram']); ?>" target="_blank" rel="noopener" aria-label="Instagram">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg>
        </a>
      </div>
    </div>
    <div class="site-footer__col">
      <h4>Visit</h4>
      <p><strong>Pottery Painting</strong><br/><?php echo esc_html( $brand['address_paint'] ); ?></p>
      <p><strong>Pottery Making</strong><br/><?php echo esc_html( $brand['address_make'] ); ?></p>
      <p><a href="<?php echo esc_url($brand['maps_link']); ?>" target="_blank" rel="noopener" class="link">Open in Maps</a></p>
    </div>
    <div class="site-footer__col">
      <h4>Connect</h4>
      <p><a href="mailto:<?php echo esc_attr($brand['email']); ?>"><?php echo esc_html($brand['email']); ?></a></p>
      <p><a href="tel:<?php echo esc_attr($brand['phone']); ?>"><?php echo esc_html($brand['phone']); ?></a></p>
    </div>
    <div class="site-footer__col">
      <h4>Explore</h4>
      <ul class="link-list">
        <li><a href="<?php echo esc_url(home_url('/booking/')); ?>">Book a Seat</a></li>
        <li><a href="<?php echo esc_url(home_url('/gallery/')); ?>">Pottery Pieces</a></li>
        <li><a href="<?php echo esc_url(home_url('/cafe/')); ?>">Cafe Menu</a></li>
        <li><a href="<?php echo esc_url(home_url('/events/')); ?>">Events &amp; Parties</a></li>
        <li><a href="<?php echo esc_url(home_url('/about/')); ?>">Our Story</a></li>
      </ul>
    </div>
  </div>
  <div class="site-footer__bottom container">
    <p class="muted small">© <?php echo date('Y'); ?> Floating By Everyday. Handcrafted in Raipur, Chhattisgarh.</p>
    <p class="muted small">Made by <a href="https://alinmasih.free.nf" target="_blank" rel="noopener">Alin 💜</a></p>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
