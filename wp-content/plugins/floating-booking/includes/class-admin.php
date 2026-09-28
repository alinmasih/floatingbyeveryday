<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class FB_Admin {

    public static function menu() {
        add_submenu_page(
            'edit.php?post_type=fb_booking',
            'Floating Settings',
            'Settings',
            'manage_options',
            'fb-settings',
            [ __CLASS__, 'settings_page' ]
        );
    }

    public static function register_settings() {
        register_setting( 'fb_settings_group', 'fb_settings' );
        register_setting( 'fb_brand_group', 'floating_brand' );
    }

    public static function assets( $hook ) {
        if ( strpos( $hook ?: '', 'fb-settings' ) !== false ) {
            wp_enqueue_style( 'fb-admin', FB_URI . 'assets/admin.css', [], FB_VERSION );
        }
    }

    public static function add_meta_boxes() {
        add_meta_box( 'fb_piece_meta', 'Pottery Piece Details', [ __CLASS__, 'piece_box' ], 'fb_piece', 'normal', 'high' );
        add_meta_box( 'fb_pkg_meta', 'Package Details', [ __CLASS__, 'pkg_box' ], 'fb_package', 'normal', 'high' );
        add_meta_box( 'fb_event_meta', 'Event Details', [ __CLASS__, 'event_box' ], 'fb_event', 'normal', 'high' );
        add_meta_box( 'fb_menu_meta', 'Menu Item Details', [ __CLASS__, 'menu_box' ], 'fb_menu_item', 'normal', 'high' );
        add_meta_box( 'fb_coupon_meta', 'Coupon Details', [ __CLASS__, 'coupon_box' ], 'fb_coupon', 'normal', 'high' );
        add_meta_box( 'fb_booking_meta', 'Booking Details', [ __CLASS__, 'booking_box' ], 'fb_booking', 'normal', 'high' );
    }

    public static function piece_box( $post ) {
        wp_nonce_field( 'fb_save', 'fb_nonce' );
        $price = get_post_meta( $post->ID, '_fb_price', true );
        $diff = get_post_meta( $post->ID, '_fb_difficulty', true );
        $cat  = get_post_meta( $post->ID, '_fb_category', true );
        echo '<p><label><strong>Price (INR):</strong> <input type="number" name="_fb_price" value="' . esc_attr( $price ) . '" step="1"></label></p>';
        echo '<p><label><strong>Category:</strong> <input type="text" name="_fb_category" value="' . esc_attr( $cat ) . '" placeholder="Cups & Mugs, Plates & Bowls, Diyas & Lamps, Figurines, Vases, Tiles & Coasters, Planters"></label></p>';
        echo '<p><label><strong>Difficulty:</strong> <select name="_fb_difficulty"><option value="beginner"' . selected( $diff, 'beginner', false ) . '>Beginner</option><option value="intermediate"' . selected( $diff, 'intermediate', false ) . '>Intermediate</option><option value="advanced"' . selected( $diff, 'advanced', false ) . '>Advanced</option></select></label></p>';
    }

    public static function pkg_box( $post ) {
        wp_nonce_field( 'fb_save', 'fb_nonce' );
        $price = get_post_meta( $post->ID, '_fb_price', true );
        $seats = get_post_meta( $post->ID, '_fb_seats', true );
        $inc = get_post_meta( $post->ID, '_fb_includes', true );
        $feat = get_post_meta( $post->ID, '_fb_featured', true );
        echo '<p><label><strong>Price (INR):</strong> <input type="number" name="_fb_price" value="' . esc_attr( $price ) . '"></label></p>';
        echo '<p><label><strong>Seats:</strong> <input type="number" name="_fb_seats" value="' . esc_attr( $seats ) . '"></label></p>';
        echo '<p><strong>Includes (one item per line):</strong></p><textarea name="_fb_includes" rows="6" style="width:100%">' . esc_textarea( $inc ) . '</textarea>';
        echo '<p><label><input type="checkbox" name="_fb_featured" ' . checked( $feat, 1, false ) . '> Mark as featured</label></p>';
    }

    public static function event_box( $post ) {
        wp_nonce_field( 'fb_save', 'fb_nonce' );
        $audience = get_post_meta( $post->ID, '_fb_audience', true );
        $min = get_post_meta( $post->ID, '_fb_min_group', true );
        $dur = get_post_meta( $post->ID, '_fb_duration', true );
        $inc = get_post_meta( $post->ID, '_fb_includes', true );
        echo '<p><label><strong>Audience:</strong> <input type="text" name="_fb_audience" value="' . esc_attr( $audience ) . '" placeholder="Ages 5-12, Adults, Couples..."></label></p>';
        echo '<p><label><strong>Minimum group:</strong> <input type="number" name="_fb_min_group" value="' . esc_attr( $min ) . '"></label></p>';
        echo '<p><label><strong>Duration:</strong> <input type="text" name="_fb_duration" value="' . esc_attr( $dur ) . '" placeholder="2 hours"></label></p>';
        echo '<p><label><strong>What\'s included:</strong> <textarea name="_fb_includes" rows="3" style="width:100%">' . esc_textarea( $inc ) . '</textarea></label></p>';
    }

    public static function menu_box( $post ) {
        wp_nonce_field( 'fb_save', 'fb_nonce' );
        $price = get_post_meta( $post->ID, '_fb_price', true );
        $section = get_post_meta( $post->ID, '_fb_section', true );
        $sub = get_post_meta( $post->ID, '_fb_subtitle', true );
        $diet = get_post_meta( $post->ID, '_fb_diet', true );
        echo '<p><label><strong>Price (INR):</strong> <input type="number" name="_fb_price" value="' . esc_attr( $price ) . '"></label></p>';
        echo '<p><label><strong>Section:</strong> <select name="_fb_section">';
        foreach ( [ 'Hot Drinks', 'Cold Drinks', 'Snacks', 'Light Meals' ] as $s ) {
            echo '<option value="' . esc_attr( $s ) . '"' . selected( $section, $s, false ) . '>' . esc_html( $s ) . '</option>';
        }
        echo '</select></label></p>';
        echo '<p><label><strong>Subtitle / description:</strong> <input type="text" name="_fb_subtitle" value="' . esc_attr( $sub ) . '" style="width:80%"></label></p>';
        echo '<p><label><strong>Diet labels (comma separated):</strong> <input type="text" name="_fb_diet" value="' . esc_attr( $diet ) . '" placeholder="Veg, Vegan, Jain"></label></p>';
    }

    public static function coupon_box( $post ) {
        wp_nonce_field( 'fb_save', 'fb_nonce' );
        $type = get_post_meta( $post->ID, '_fb_disc_type', true ) ?: 'percent';
        $val = get_post_meta( $post->ID, '_fb_disc_value', true );
        $exp = get_post_meta( $post->ID, '_fb_expiry', true );
        $min = get_post_meta( $post->ID, '_fb_min_subtotal', true );
        echo '<p>Use the post title as the coupon code.</p>';
        echo '<p><label><strong>Discount type:</strong> <select name="_fb_disc_type"><option value="percent"' . selected($type,'percent',false) . '>Percent</option><option value="flat"' . selected($type,'flat',false) . '>Flat amount</option></select></label></p>';
        echo '<p><label><strong>Value:</strong> <input type="number" name="_fb_disc_value" value="' . esc_attr( $val ) . '" step="1"></label></p>';
        echo '<p><label><strong>Expires (YYYY-MM-DD, optional):</strong> <input type="date" name="_fb_expiry" value="' . esc_attr( $exp ) . '"></label></p>';
        echo '<p><label><strong>Minimum subtotal (optional):</strong> <input type="number" name="_fb_min_subtotal" value="' . esc_attr( $min ) . '"></label></p>';
    }

    public static function booking_box( $post ) {
        $confirm = get_post_meta( $post->ID, '_fb_confirm', true );
        $name = get_post_meta( $post->ID, '_fb_contact_name', true );
        $email = get_post_meta( $post->ID, '_fb_contact_email', true );
        $phone = get_post_meta( $post->ID, '_fb_contact_phone', true );
        $date = get_post_meta( $post->ID, '_fb_date', true );
        $time = get_post_meta( $post->ID, '_fb_time', true );
        $guests = get_post_meta( $post->ID, '_fb_guests', true );
        $notes = get_post_meta( $post->ID, '_fb_notes', true );
        $occ = get_post_meta( $post->ID, '_fb_occasion', true );
        $quote = json_decode( get_post_meta( $post->ID, '_fb_quote', true ), true ) ?: [];
        $kind = get_post_meta( $post->ID, '_fb_kind', true );

        if ( $kind === 'event_enquiry' ) {
            $fields = [ 'name','email','phone','event_type','preferred_date','group_size','age_range','budget','theme','source' ];
            echo '<table class="form-table"><tbody>';
            foreach ( $fields as $f ) {
                $v = get_post_meta( $post->ID, "_fb_e_$f", true );
                echo '<tr><th>' . esc_html( ucfirst( str_replace( '_', ' ', $f ) ) ) . '</th><td>' . esc_html( $v ) . '</td></tr>';
            }
            echo '</tbody></table>';
            return;
        }

        echo '<table class="form-table"><tbody>';
        echo '<tr><th>Confirmation</th><td><strong>' . esc_html( $confirm ) . '</strong></td></tr>';
        echo '<tr><th>Guest</th><td>' . esc_html( $name ) . ' &middot; ' . esc_html( $email ) . ' &middot; ' . esc_html( $phone ) . '</td></tr>';
        echo '<tr><th>When</th><td>' . esc_html( $date ) . ' at ' . esc_html( $time ) . ' (' . intval( $guests ) . ' guests)</td></tr>';
        if ( $occ ) echo '<tr><th>Occasion</th><td>' . esc_html( $occ ) . '</td></tr>';
        if ( $notes ) echo '<tr><th>Notes</th><td>' . esc_html( $notes ) . '</td></tr>';
        echo '<tr><th>Pieces</th><td>';
        foreach ( $quote['pieces'] ?? [] as $p ) echo esc_html( $p['name'] ) . ' × ' . intval( $p['qty'] ) . ' &middot; ' . fb_format_price( $p['total'] ) . '<br>';
        echo '</td></tr>';
        echo '<tr><th>Studio fee</th><td>' . fb_format_price( $quote['studio_total'] ?? 0 ) . '</td></tr>';
        if ( ! empty( $quote['addons'] ) ) {
            echo '<tr><th>Add-ons</th><td>';
            foreach ( $quote['addons'] as $a ) echo esc_html( $a['name'] ) . ' &middot; ' . fb_format_price( $a['amount'] ) . '<br>';
            echo '</td></tr>';
        }
        if ( ! empty( $quote['group_discount'] ) ) echo '<tr><th>Group discount</th><td>-' . fb_format_price( $quote['group_discount'] ) . '</td></tr>';
        if ( ! empty( $quote['coupon_code'] ) ) echo '<tr><th>Coupon ' . esc_html( $quote['coupon_code'] ) . '</th><td>-' . fb_format_price( $quote['coupon_discount'] ) . '</td></tr>';
        echo '<tr><th>GST (' . esc_html( $quote['gst_rate'] ?? 18 ) . '%)</th><td>' . fb_format_price( $quote['gst'] ?? 0 ) . '</td></tr>';
        echo '<tr><th>Total</th><td><strong>' . fb_format_price( $quote['total'] ?? 0 ) . '</strong></td></tr>';
        echo '</tbody></table>';
    }

    public static function save_meta_boxes( $post_id, $post ) {
        if ( ! isset( $_POST['fb_nonce'] ) || ! wp_verify_nonce( $_POST['fb_nonce'], 'fb_save' ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;
        $fields = [
            '_fb_price','_fb_difficulty','_fb_category',
            '_fb_seats','_fb_includes','_fb_featured',
            '_fb_audience','_fb_min_group','_fb_duration',
            '_fb_section','_fb_subtitle','_fb_diet',
            '_fb_disc_type','_fb_disc_value','_fb_expiry','_fb_min_subtotal',
        ];
        foreach ( $fields as $f ) {
            if ( isset( $_POST[ $f ] ) ) {
                update_post_meta( $post_id, $f, sanitize_text_field( wp_unslash( $_POST[ $f ] ) ) );
            } elseif ( $f === '_fb_featured' ) {
                update_post_meta( $post_id, $f, 0 );
            }
        }
        if ( isset( $_POST['_fb_includes'] ) ) {
            update_post_meta( $post_id, '_fb_includes', sanitize_textarea_field( wp_unslash( $_POST['_fb_includes'] ) ) );
        }
    }

    public static function settings_page() {
        ?>
        <div class="wrap">
          <h1>Floating Settings</h1>
          <form method="post" action="options.php">
            <?php settings_fields( 'fb_settings_group' ); ?>
            <?php $s = get_option( 'fb_settings', [] ); ?>
            <h2>Pricing</h2>
            <table class="form-table">
              <tr><th>Studio fee per person (INR)</th><td><input type="number" name="fb_settings[studio_fee]" value="<?php echo esc_attr( $s['studio_fee'] ?? 250 ); ?>"></td></tr>
              <tr><th>GST rate (%)</th><td><input type="number" name="fb_settings[gst_rate]" value="<?php echo esc_attr( $s['gst_rate'] ?? 18 ); ?>"></td></tr>
              <tr><th>Group discount threshold (guests)</th><td><input type="number" name="fb_settings[group_threshold]" value="<?php echo esc_attr( $s['group_threshold'] ?? 6 ); ?>"></td></tr>
              <tr><th>Group discount (%)</th><td><input type="number" name="fb_settings[group_discount_pct]" value="<?php echo esc_attr( $s['group_discount_pct'] ?? 10 ); ?>"></td></tr>
            </table>

            <h2>Email</h2>
            <table class="form-table">
              <tr><th>Sender name</th><td><input type="text" name="fb_settings[sender_name]" value="<?php echo esc_attr( $s['sender_name'] ?? 'Floating By Everyday' ); ?>"></td></tr>
              <tr><th>Sender / admin email</th><td><input type="email" name="fb_settings[sender_email]" value="<?php echo esc_attr( $s['sender_email'] ?? get_option('admin_email') ); ?>"></td></tr>
            </table>

            <h2>Razorpay</h2>
            <p>Paste your Razorpay Key ID and Key Secret in the WooCommerce Razorpay plugin settings. Once you do, this site will accept payments. Until then, the booking flow will create a confirmed booking and send the email but the payment step will be marked as pending.</p>
            <p><a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=razorpay' ) ); ?>">Open Razorpay settings</a></p>

            <?php submit_button(); ?>
          </form>

          <hr>

          <form method="post" action="options.php">
            <?php settings_fields( 'fb_brand_group' ); ?>
            <?php $b = get_option( 'floating_brand', [] ); ?>
            <h2>Brand and contact details</h2>
            <table class="form-table">
              <tr><th>Tagline</th><td><input type="text" name="floating_brand[tagline]" value="<?php echo esc_attr( $b['tagline'] ?? 'Paint Your Own Pottery in Raipur' ); ?>" style="width:60%"></td></tr>
              <tr><th>Phone</th><td><input type="tel" name="floating_brand[phone]" value="<?php echo esc_attr( $b['phone'] ?? '+91 9999999999' ); ?>"></td></tr>
              <tr><th>Email</th><td><input type="email" name="floating_brand[email]" value="<?php echo esc_attr( $b['email'] ?? '' ); ?>"></td></tr>
              <tr><th>Painting studio address</th><td><input type="text" name="floating_brand[address_paint]" value="<?php echo esc_attr( $b['address_paint'] ?? '' ); ?>" style="width:80%"></td></tr>
              <tr><th>Making studio address</th><td><input type="text" name="floating_brand[address_make]" value="<?php echo esc_attr( $b['address_make'] ?? '' ); ?>" style="width:80%"></td></tr>
              <tr><th>Google Maps link</th><td><input type="url" name="floating_brand[maps_link]" value="<?php echo esc_attr( $b['maps_link'] ?? '' ); ?>" style="width:80%"></td></tr>
              <tr><th>Instagram URL</th><td><input type="url" name="floating_brand[instagram]" value="<?php echo esc_attr( $b['instagram'] ?? '' ); ?>" style="width:80%"></td></tr>
              <tr><th>Hours</th><td><input type="text" name="floating_brand[hours]" value="<?php echo esc_attr( $b['hours'] ?? '' ); ?>" style="width:80%"></td></tr>
            </table>
            <?php submit_button( 'Save brand details' ); ?>
          </form>
        </div>
        <?php
    }
}
