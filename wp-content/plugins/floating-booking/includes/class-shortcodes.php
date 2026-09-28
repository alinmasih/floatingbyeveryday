<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class FB_Shortcodes {

    public static function register() {
        add_shortcode( 'fb_booking_flow', [ __CLASS__, 'booking_flow' ] );
        add_shortcode( 'fb_pottery_grid', [ __CLASS__, 'pottery_grid' ] );
        add_shortcode( 'fb_packages',     [ __CLASS__, 'packages' ] );
        add_shortcode( 'fb_pricing_table',[ __CLASS__, 'pricing_table' ] );
        add_shortcode( 'fb_cafe_menu',    [ __CLASS__, 'cafe_menu' ] );
        add_shortcode( 'fb_event_grid',   [ __CLASS__, 'event_grid' ] );
        add_shortcode( 'fb_event_form',   [ __CLASS__, 'event_form' ] );
    }

    public static function booking_flow( $atts = [] ) {
        ob_start(); ?>
        <div id="fb-booking-app" class="booking-shell">
          <div class="booking-steps" role="tablist">
            <div class="booking-step active" data-step="1"><span class="booking-step__num">1</span><span class="booking-step__label">When</span></div>
            <div class="booking-step" data-step="2"><span class="booking-step__num">2</span><span class="booking-step__label">Pieces</span></div>
            <div class="booking-step" data-step="3"><span class="booking-step__num">3</span><span class="booking-step__label">Paints</span></div>
            <div class="booking-step" data-step="4"><span class="booking-step__num">4</span><span class="booking-step__label">Guests</span></div>
            <div class="booking-step" data-step="5"><span class="booking-step__num">5</span><span class="booking-step__label">Review</span></div>
            <div class="booking-step" data-step="6"><span class="booking-step__num">6</span><span class="booking-step__label">Pay</span></div>
          </div>

          <!-- STEP 1 -->
          <div class="booking-pane active" data-pane="1">
            <span class="eyebrow">Step 1</span>
            <h2>Choose your day and time</h2>
            <p class="muted">Pick an open date, then a time slot. Each slot shows seats remaining.</p>
            <div class="calendar-controls">
              <button type="button" id="fb-cal-prev" aria-label="Previous month">←</button>
              <h3 id="fb-cal-title">Month</h3>
              <button type="button" id="fb-cal-next" aria-label="Next month">→</button>
            </div>
            <div class="calendar-head">
              <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
            </div>
            <div class="calendar-grid" id="fb-cal-grid"></div>
            <div id="fb-slots-wrap" style="display:none">
              <h3 style="margin-top:32px">Time slots</h3>
              <div class="slot-grid" id="fb-slot-grid"></div>
              <h3 style="margin-top:32px">Session length</h3>
              <div class="duration-grid" id="fb-duration">
                <label class="slot"><input type="radio" name="fb-duration" value="1 hour" hidden><span class="slot__time">1 hour</span><span class="slot__seats">Quick visit</span></label>
                <label class="slot"><input type="radio" name="fb-duration" value="1.5 hours" hidden checked><span class="slot__time">1.5 hours</span><span class="slot__seats">Most popular</span></label>
                <label class="slot"><input type="radio" name="fb-duration" value="2 hours" hidden><span class="slot__time">2 hours</span><span class="slot__seats">Take your time</span></label>
              </div>
            </div>
            <div class="booking-nav">
              <span></span>
              <button type="button" class="btn btn--primary" id="fb-next-1" disabled>Continue</button>
            </div>
          </div>

          <!-- STEP 2 -->
          <div class="booking-pane" data-pane="2">
            <span class="eyebrow">Step 2</span>
            <h2>Choose your pottery pieces</h2>
            <p class="muted">Pick one or more pieces to paint. Tap to add, tap again to add more.</p>
            <div class="filters" id="fb-piece-filters"></div>
            <div class="piece-grid" id="fb-piece-grid"></div>
            <div class="booking-nav">
              <button type="button" class="btn btn--ghost" data-back>Back</button>
              <button type="button" class="btn btn--primary" id="fb-next-2">Continue</button>
            </div>
          </div>

          <!-- STEP 3 -->
          <div class="booking-pane" data-pane="3">
            <span class="eyebrow">Step 3</span>
            <h2>Paints and tools</h2>
            <p class="muted">All standard paints, brushes, sponges and stencils are included. Add specials if you want a particular look.</p>

            <h3 style="margin-top:28px; font-size:22px">Premium paints (optional)</h3>
            <div class="checklist">
              <label><input type="checkbox" name="addon" value="premium-paints"><span class="check-text"><strong>Premium paint set</strong><small>Pastels, soft neutrals · ₹80</small></span></label>
              <label><input type="checkbox" name="addon" value="metallic-paints"><span class="check-text"><strong>Metallic and pearl paints</strong><small>Gold, copper, pearlescent · ₹100</small></span></label>
            </div>

            <h3 style="margin-top:28px; font-size:22px">Detail tools (optional)</h3>
            <div class="checklist">
              <label><input type="checkbox" name="addon" value="detail-pens"><span class="check-text"><strong>Detail pens</strong><small>Fine line accents · ₹60</small></span></label>
              <label><input type="checkbox" name="addon" value="dotting-tools"><span class="check-text"><strong>Dotting tools</strong><small>Mandala dots, beads · ₹40</small></span></label>
            </div>

            <h3 style="margin-top:28px; font-size:22px">Technique kits (optional)</h3>
            <div class="checklist">
              <label><input type="checkbox" name="addon" value="mandala-kit"><span class="check-text"><strong>Mandala kit</strong><small>Stencils, pens, dotting tools · ₹150</small></span></label>
              <label><input type="checkbox" name="addon" value="marble-kit"><span class="check-text"><strong>Marble effect kit</strong><small>Swirl your own marbled glaze · ₹200</small></span></label>
              <label><input type="checkbox" name="addon" value="warli-kit"><span class="check-text"><strong>Warli stencil kit</strong><small>Tribal motif stencils · ₹150</small></span></label>
              <label><input type="checkbox" name="addon" value="block-kit"><span class="check-text"><strong>Block print kit</strong><small>Wooden block stamps for repeats · ₹180</small></span></label>
            </div>

            <div class="booking-nav">
              <button type="button" class="btn btn--ghost" data-back>Back</button>
              <button type="button" class="btn btn--primary" id="fb-next-3">Continue</button>
            </div>
          </div>

          <!-- STEP 4 -->
          <div class="booking-pane" data-pane="4">
            <span class="eyebrow">Step 4</span>
            <h2>Tell us who is coming</h2>
            <p class="muted">We will send your confirmation here.</p>
            <div class="field">
              <label>Number of guests</label>
              <input type="number" id="fb-guests" min="1" max="20" value="1">
            </div>
            <div class="field">
              <label>Full name</label>
              <input type="text" id="fb-name" placeholder="As you would like it on your booking">
            </div>
            <div class="field--row">
              <div class="field">
                <label>Email</label>
                <input type="email" id="fb-email" placeholder="you@example.com">
              </div>
              <div class="field">
                <label>Phone (Indian mobile)</label>
                <input type="tel" id="fb-phone" placeholder="+91 98xxxxxxxx">
              </div>
            </div>
            <div class="field">
              <label>Special occasion (optional)</label>
              <select id="fb-occasion">
                <option value="">Just visiting</option>
                <option>Birthday</option>
                <option>Date night</option>
                <option>Anniversary</option>
                <option>Friends day out</option>
                <option>Kitty party</option>
                <option>Corporate</option>
                <option>Other</option>
              </select>
            </div>
            <div class="field">
              <label>Anything we should know? (optional)</label>
              <textarea id="fb-notes" rows="3" placeholder="Allergies, mobility needs, surprise plans..."></textarea>
            </div>
            <div class="booking-nav">
              <button type="button" class="btn btn--ghost" data-back>Back</button>
              <button type="button" class="btn btn--primary" id="fb-next-4">Continue</button>
            </div>
          </div>

          <!-- STEP 5 -->
          <div class="booking-pane" data-pane="5">
            <span class="eyebrow">Step 5</span>
            <h2>Review your booking</h2>
            <div id="fb-summary"></div>
            <div class="field" style="margin-top:24px">
              <label>Coupon code (optional)</label>
              <div style="display:flex; gap:8px">
                <input type="text" id="fb-coupon" placeholder="Enter a code">
                <button type="button" class="btn btn--ghost btn--sm" id="fb-apply-coupon">Apply</button>
              </div>
              <p id="fb-coupon-msg" class="small muted" style="margin-top:6px"></p>
            </div>
            <div class="field" style="margin-top:18px">
              <label style="display:flex; align-items:flex-start; gap:10px; text-transform:none; letter-spacing:0; color:var(--c-ink); font-weight:400; font-size:14px"><input type="checkbox" id="fb-terms" style="margin-top:4px"><span>I agree to the studio rules. Painted pieces fire and are ready for takeaway at the end of the session. Bookings are non-refundable but can be rescheduled with 24 hours notice.</span></label>
            </div>
            <div class="booking-nav">
              <button type="button" class="btn btn--ghost" data-back>Back</button>
              <button type="button" class="btn btn--primary" id="fb-confirm">Confirm and pay</button>
            </div>
          </div>

          <!-- STEP 6 -->
          <div class="booking-pane" data-pane="6">
            <div class="confirm-card">
              <div class="seal">✓</div>
              <h2>Your seat is booked</h2>
              <p class="muted">A confirmation has been sent to your email.</p>
              <div class="confirm-num" id="fb-confirm-num"></div>
              <div id="fb-confirm-summary" style="text-align:left; max-width:420px; margin:24px auto"></div>
              <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap; margin-top:24px">
                <a href="<?php echo esc_url( home_url('/gallery/') ); ?>" class="btn btn--ghost">Browse more pieces</a>
                <a href="https://maps.app.goo.gl/BQ2voBeQHDuctzh69" target="_blank" rel="noopener" class="btn btn--primary">Open in Maps</a>
              </div>
              <p class="muted small" style="margin-top:32px">Your painted piece will be ready for takeaway at the end of your session. Walk in 10 minutes before your slot.</p>
            </div>
          </div>

        </div>
        <?php
        return ob_get_clean();
    }

    public static function pottery_grid( $atts = [] ) {
        $a = shortcode_atts( [ 'category' => '', 'columns' => 3, 'limit' => -1, 'inspiration' => 'no' ], $atts );
        $pieces = fb_get_pieces( [ 'posts_per_page' => $a['limit'] ] );
        $cats = array_unique( array_filter( array_map( fn($p)=>$p['category'], $pieces ) ) );
        sort( $cats );
        ob_start(); ?>
        <div data-filterable>
          <div class="filters">
            <button class="filter-chip active" data-filter="all">All</button>
            <?php foreach ( $cats as $c ) : ?>
              <button class="filter-chip" data-filter="<?php echo esc_attr( $c ); ?>"><?php echo esc_html( $c ); ?></button>
            <?php endforeach; ?>
          </div>
          <div class="grid grid-3" style="margin-top:32px">
            <?php foreach ( $pieces as $p ) : ?>
              <a class="piece-card fade-up" href="<?php echo esc_url( home_url('/booking/') ); ?>" data-cat="<?php echo esc_attr( $p['category'] ); ?>">
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
        <?php
        return ob_get_clean();
    }

    public static function packages( $atts = [] ) {
        $packages = fb_get_packages();
        if ( ! $packages ) return '';
        ob_start(); ?>
        <div class="grid grid-3" style="align-items:stretch">
        <?php foreach ( $packages as $p ) : ?>
          <div class="pkg-card fade-up<?php echo $p['featured'] ? ' featured' : ''; ?>">
            <?php if ( $p['featured'] ) echo '<div class="badge">Most loved</div>'; ?>
            <h3><?php echo esc_html( $p['name'] ); ?></h3>
            <p class="pkg-tag"><?php echo esc_html( $p['description'] ); ?></p>
            <p class="pkg-price"><?php echo fb_format_price( $p['price'] ); ?><small>all inclusive</small></p>
            <ul class="pkg-include">
              <?php foreach ( $p['includes'] as $inc ) : ?>
                <li><?php echo esc_html( $inc ); ?></li>
              <?php endforeach; ?>
            </ul>
            <a href="<?php echo esc_url( home_url('/booking/') ); ?>" class="btn <?php echo $p['featured'] ? 'btn--clay' : 'btn--ghost'; ?>">Book this</a>
          </div>
        <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function pricing_table( $atts = [] ) {
        ob_start(); ?>
        <table class="price-table">
          <thead>
            <tr><th>Item</th><th>Rate</th></tr>
          </thead>
          <tbody>
            <tr><td>Studio fee per person<br><small class="muted">Includes seat, apron, all standard paints, brushes, sponges, stencils, kiln firing, and packaging</small></td><td>₹250</td></tr>
            <tr><td>Pottery pieces</td><td>₹150 to ₹1500</td></tr>
            <tr><td>Premium paint set add-on</td><td>₹80</td></tr>
            <tr><td>Metallic and pearl paints add-on</td><td>₹100</td></tr>
            <tr><td>Detail pens / dotting tools</td><td>₹40 to ₹60</td></tr>
            <tr><td>Technique kits (Mandala, Marble, Warli, Block print)</td><td>₹150 to ₹200</td></tr>
            <tr><td>Group discount on 6 or more guests</td><td>10% off</td></tr>
            <tr><td>GST</td><td>18% applied to all</td></tr>
          </tbody>
        </table>
        <?php
        return ob_get_clean();
    }

    public static function cafe_menu( $atts = [] ) {
        $sections = fb_get_menu_items_grouped();
        if ( ! $sections ) return '';
        ob_start();
        $order = [ 'Hot Drinks', 'Cold Drinks', 'Snacks', 'Light Meals' ];
        $sorted = [];
        foreach ( $order as $k ) if ( isset( $sections[ $k ] ) ) $sorted[ $k ] = $sections[ $k ];
        foreach ( $sections as $k => $v ) if ( ! isset( $sorted[ $k ] ) ) $sorted[ $k ] = $v;
        foreach ( $sorted as $section => $items ) :
        ?>
          <div class="menu-section fade-up">
            <h3><?php echo esc_html( $section ); ?> <small><?php echo count( $items ); ?> options</small></h3>
            <?php foreach ( $items as $i ) : ?>
              <div class="menu-row">
                <div class="menu-row__name">
                  <?php echo esc_html( $i['name'] ); ?>
                  <?php if ( ! empty( $i['diet'] ) ) : foreach ( explode( ',', $i['diet'] ) as $d ) { $d = trim($d); if ($d) echo '<span class="menu-tag">' . esc_html($d) . '</span>'; } endif; ?>
                  <?php if ( ! empty( $i['subtitle'] ) ) echo '<small>' . esc_html( $i['subtitle'] ) . '</small>'; ?>
                </div>
                <div class="menu-row__price"><?php echo fb_format_price( $i['price'] ); ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php
        endforeach;
        return ob_get_clean();
    }

    public static function event_grid( $atts = [] ) {
        $events = fb_get_events();
        ob_start(); ?>
        <div class="grid grid-3">
        <?php foreach ( $events as $e ) : ?>
          <div class="feature-card fade-up">
            <div class="feature-card__num"><?php echo esc_html( $e['audience'] ?: '' ); ?></div>
            <h3><?php echo esc_html( $e['name'] ); ?></h3>
            <p><?php echo esc_html( $e['description'] ); ?></p>
            <div style="margin-top:18px; padding-top:18px; border-top:1px solid var(--c-line-soft); display:flex; gap:12px; flex-wrap:wrap; font-size:13px; color:var(--c-ink-mute)">
              <span><strong style="color:var(--c-ink); font-family:var(--ff-serif); font-size:18px"><?php echo intval( $e['min_group'] ); ?>+</strong> guests</span>
              <span><strong style="color:var(--c-ink); font-family:var(--ff-serif); font-size:18px"><?php echo esc_html( $e['duration'] ); ?></strong></span>
            </div>
            <div style="margin-top:16px; font-size:14px; color:var(--c-ink-soft)"><?php echo esc_html( $e['includes'] ); ?></div>
          </div>
        <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function event_form( $atts = [] ) {
        ob_start(); ?>
        <form id="fb-event-form" class="booking-shell" style="max-width:760px; margin:0 auto">
          <span class="eyebrow">Event Enquiry</span>
          <h2>Plan your event with us</h2>
          <p class="muted">Tell us a little about it. We will reply within a working day with availability and a quote.</p>
          <div class="field">
            <label>Event type</label>
            <select name="event_type" required>
              <option value="">Select an event type</option>
              <option>Kids Birthday Party</option>
              <option>Teen or Adult Birthday</option>
              <option>Corporate Team Building</option>
              <option>Kitty Party</option>
              <option>Bridal or Baby Shower</option>
              <option>Date Night</option>
              <option>Kids Camp</option>
              <option>School Field Trip</option>
              <option>Festival Special (Diwali, Holi, Rakhi)</option>
            </select>
          </div>
          <div class="field--row">
            <div class="field"><label>Preferred date</label><input type="date" name="preferred_date"></div>
            <div class="field"><label>Group size</label><input type="number" name="group_size" min="2" max="80" required></div>
          </div>
          <div class="field--row">
            <div class="field"><label>Age range</label><input type="text" name="age_range" placeholder="e.g. 7 to 11"></div>
            <div class="field"><label>Budget range</label><input type="text" name="budget" placeholder="e.g. ₹8000 to ₹15000"></div>
          </div>
          <div class="field"><label>Special themes or requirements</label><textarea name="theme" rows="3" placeholder="Any colors, characters, dietary needs, return gift ideas..."></textarea></div>
          <div class="field--row">
            <div class="field"><label>Your name</label><input type="text" name="name" required></div>
            <div class="field"><label>Phone</label><input type="tel" name="phone" placeholder="+91 98xxxxxxxx" required></div>
          </div>
          <div class="field"><label>Email</label><input type="email" name="email" required></div>
          <div class="field">
            <label>How did you hear about us?</label>
            <select name="source">
              <option>Instagram</option>
              <option>Google search</option>
              <option>Friend or family</option>
              <option>Walked past the studio</option>
              <option>Other</option>
            </select>
          </div>
          <div class="booking-nav">
            <span></span>
            <button type="submit" class="btn btn--primary">Send enquiry</button>
          </div>
          <p id="fb-event-msg" class="muted small" style="margin-top:14px"></p>
        </form>
        <script>
        (function(){
          const f = document.getElementById('fb-event-form');
          if (!f) return;
          f.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = Object.fromEntries(new FormData(f));
            const msg = document.getElementById('fb-event-msg');
            msg.textContent = 'Sending...';
            try {
              const r = await fetch('<?php echo esc_url_raw( rest_url( "fb/v1/event-enquiry" ) ); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
              });
              const j = await r.json();
              if (j.ok) {
                f.innerHTML = '<div class="confirm-card"><div class="seal">✓</div><h2>Got it</h2><p class="muted">Thanks for reaching out. We will get back to you within a working day.</p></div>';
                window.scrollTo({ top: f.offsetTop - 100, behavior: 'smooth' });
              } else {
                msg.textContent = 'Something went wrong. Please try again or message us on Instagram.';
              }
            } catch(e) { msg.textContent = 'Network error. Please try again.'; }
          });
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
