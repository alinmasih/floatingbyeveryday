<?php
/**
 * Template Name: Contact
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
$brand = floating_brand();
?>
<section class="section section--first">
  <div class="container">
    <div class="section-head fade-up">
      <span class="eyebrow">Contact</span>
      <h1>Come say hello.</h1>
      <p class="muted large">Walk in, message, or send us a note. We reply quickly.</p>
    </div>

    <div class="grid grid-2 fade-up">
      <div class="studio-card">
        <span class="pin">⬤ Pottery Painting</span>
        <h3>Samta Colony</h3>
        <address><?php echo esc_html( $brand['address_paint'] ); ?></address>
        <p class="muted small"><?php echo esc_html( $brand['hours'] ); ?></p>
        <a href="<?php echo esc_url( $brand['maps_link'] ); ?>" target="_blank" rel="noopener" class="link">Open in Maps</a>
      </div>
      <div class="studio-card">
        <span class="pin">⬤ Pottery Making</span>
        <h3>Anand Nagar</h3>
        <address><?php echo esc_html( $brand['address_make'] ); ?></address>
        <p class="muted small">Saturdays and Sundays. Booking required.</p>
        <a href="<?php echo esc_url( $brand['maps_link'] ); ?>" target="_blank" rel="noopener" class="link">Open in Maps</a>
      </div>
    </div>
  </div>
</section>

<section class="section section--cream">
  <div class="container medium">
    <div class="grid grid-2 fade-up" style="align-items:start">
      <div>
        <span class="eyebrow">Get in touch</span>
        <h2>Have a question?</h2>
        <p class="muted">For booking, walk-ins, parties, or anything else. We will reply within a working day.</p>
        <div class="stack-md" style="margin-top: 32px">
          <p>📧 <a href="mailto:<?php echo esc_attr( $brand['email'] ); ?>"><?php echo esc_html( $brand['email'] ); ?></a></p>
          <p>📞 <a href="tel:<?php echo esc_attr( $brand['phone'] ); ?>"><?php echo esc_html( $brand['phone'] ); ?></a></p>
          <p>📷 <a href="<?php echo esc_url( $brand['instagram'] ); ?>" target="_blank" rel="noopener">@floatingbyeveryday</a></p>
        </div>
      </div>
      <form id="fb-contact-form" class="booking-shell">
        <div class="field"><label>Your name</label><input type="text" name="name" required></div>
        <div class="field"><label>Email</label><input type="email" name="email" required></div>
        <div class="field"><label>Phone (optional)</label><input type="tel" name="phone"></div>
        <div class="field"><label>What's on your mind?</label><textarea name="message" rows="5" required></textarea></div>
        <button type="submit" class="btn btn--primary btn--block">Send message</button>
        <p id="fb-contact-msg" class="muted small" style="margin-top: 14px"></p>
      </form>
    </div>
  </div>
</section>

<script>
document.getElementById('fb-contact-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  const f = e.target;
  const data = Object.fromEntries(new FormData(f));
  const msg = document.getElementById('fb-contact-msg');
  msg.textContent = 'Sending...';
  try {
    const r = await fetch('<?php echo esc_url_raw( rest_url('fb/v1/event-enquiry') ); ?>', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ event_type: 'General contact', name: data.name, email: data.email, phone: data.phone || 'not provided', group_size: 1, theme: data.message }),
    });
    const j = await r.json();
    if (j.ok) { f.innerHTML = '<div class="confirm-card"><div class="seal">✓</div><h2>Got it</h2><p class="muted">Thanks for writing. We will reply within a working day.</p></div>'; }
    else msg.textContent = 'Something went wrong. Please try emailing us directly.';
  } catch(e) { msg.textContent = 'Network error. Please try again.'; }
});
</script>

<?php get_footer();
