<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class FB_Email {

    public static function from() {
        $brand = function_exists('floating_brand') ? floating_brand() : [];
        $name = fb_setting( 'sender_name', 'Floating By Everyday' );
        $email = fb_setting( 'sender_email', $brand['email'] ?? get_option('admin_email') );
        return [ "From: $name <$email>", "Content-Type: text/html; charset=UTF-8" ];
    }

    public static function send_booking_confirmation( $post_id ) {
        $confirm = get_post_meta( $post_id, '_fb_confirm', true );
        $name = get_post_meta( $post_id, '_fb_contact_name', true );
        $email = get_post_meta( $post_id, '_fb_contact_email', true );
        $phone = get_post_meta( $post_id, '_fb_contact_phone', true );
        $date = get_post_meta( $post_id, '_fb_date', true );
        $time = get_post_meta( $post_id, '_fb_time', true );
        $duration = get_post_meta( $post_id, '_fb_duration', true );
        $guests = get_post_meta( $post_id, '_fb_guests', true );
        $occ = get_post_meta( $post_id, '_fb_occasion', true );
        $notes = get_post_meta( $post_id, '_fb_notes', true );
        $quote = json_decode( get_post_meta( $post_id, '_fb_quote', true ), true ) ?: [];

        $brand = floating_brand();

        $rows = '';
        foreach ( $quote['pieces'] ?? [] as $row ) {
            $rows .= sprintf( '<tr><td>%s × %d</td><td style="text-align:right">%s</td></tr>', esc_html( $row['name'] ), $row['qty'], fb_format_price( $row['total'] ) );
        }

        $body = '
        <div style="font-family: -apple-system, Segoe UI, Helvetica, sans-serif; background:#f7f1e6; padding:32px; color:#1a1612">
          <div style="max-width:540px; margin:0 auto; background:#fbf6ec; border-radius:18px; padding:36px 32px; border:1px solid #e7dcc7">
            <h1 style="font-family: Georgia, serif; font-size:28px; margin:0 0 6px; letter-spacing:-0.01em">Your seat is booked</h1>
            <p style="color:#7a6e5e; margin:0 0 24px">Thank you for booking with Floating By Everyday.</p>
            <div style="background:#ede4d2; padding:14px 16px; border-radius:10px; margin-bottom:18px">
              <p style="margin:0; font-size:12px; letter-spacing:0.1em; text-transform:uppercase; color:#7a6e5e">Confirmation Number</p>
              <p style="margin:4px 0 0; font-family: monospace; font-size:18px; letter-spacing:0.08em">' . esc_html( $confirm ) . '</p>
            </div>
            <p><strong>' . esc_html( $name ) . '</strong>, here are your details.</p>
            <table style="width:100%; border-collapse:collapse; font-size:15px; margin-top:8px">
              <tr><td style="padding:8px 0; color:#7a6e5e">Date</td><td style="padding:8px 0; text-align:right">' . esc_html( date( 'l, F j, Y', strtotime( $date ) ) ) . '</td></tr>
              <tr><td style="padding:8px 0; color:#7a6e5e">Time</td><td style="padding:8px 0; text-align:right">' . esc_html( date( 'g:i A', strtotime( $time ) ) ) . '</td></tr>
              <tr><td style="padding:8px 0; color:#7a6e5e">Duration</td><td style="padding:8px 0; text-align:right">' . esc_html( $duration ) . '</td></tr>
              <tr><td style="padding:8px 0; color:#7a6e5e">Guests</td><td style="padding:8px 0; text-align:right">' . intval( $guests ) . '</td></tr>
            </table>
            <h3 style="font-family: Georgia, serif; font-size:18px; margin:24px 0 8px; border-top:1px solid #e7dcc7; padding-top:18px">What you picked</h3>
            <table style="width:100%; border-collapse:collapse; font-size:14px">
              ' . $rows . '
              <tr><td style="padding:8px 0; color:#7a6e5e">Studio fee × ' . intval( $guests ) . '</td><td style="padding:8px 0; text-align:right">' . fb_format_price( $quote['studio_total'] ?? 0 ) . '</td></tr>
              <tr><td style="padding:8px 0; color:#7a6e5e">GST</td><td style="padding:8px 0; text-align:right">' . fb_format_price( $quote['gst'] ?? 0 ) . '</td></tr>
              <tr><td style="padding:14px 0 0; font-weight:600; border-top:1px solid #e7dcc7">Total</td><td style="padding:14px 0 0; text-align:right; font-weight:600; font-size:18px; border-top:1px solid #e7dcc7">' . fb_format_price( $quote['total'] ?? 0 ) . '</td></tr>
            </table>
            <div style="background:#f0e4cf; padding:16px 18px; border-radius:10px; margin-top:24px; font-size:14px; color:#5d4a32">
              Your painted piece will be ready for takeaway at the end of your session. Walk in 10 minutes before your slot to settle in.
            </div>
            <h3 style="font-family: Georgia, serif; font-size:18px; margin:28px 0 8px">How to find us</h3>
            <p style="font-size:14px; color:#4a3f33; margin:0 0 8px"><strong>Pottery Painting:</strong> ' . esc_html( $brand['address_paint'] ) . '</p>
            <p style="font-size:14px; color:#4a3f33; margin:0 0 16px"><strong>Pottery Making:</strong> ' . esc_html( $brand['address_make'] ) . '</p>
            <a href="' . esc_url( $brand['maps_link'] ) . '" style="display:inline-block; padding:12px 24px; background:#1a1612; color:#fbf6ec; border-radius:999px; text-decoration:none; font-size:14px">Open in Google Maps</a>
            <p style="margin-top:32px; color:#7a6e5e; font-size:13px">If you need to change your booking, reply to this email or call ' . esc_html( $brand['phone'] ) . '.</p>
          </div>
          <p style="text-align:center; color:#7a6e5e; font-size:12px; margin-top:24px">Floating By Everyday · Raipur, Chhattisgarh</p>
        </div>';

        wp_mail( $email, 'Your booking is confirmed — Floating By Everyday (' . $confirm . ')', $body, self::from() );

        $admin = fb_setting( 'sender_email', get_option('admin_email') );
        $admin_body = '<h2>New booking</h2><p>' . esc_html( $name ) . ' (' . esc_html( $email ) . ', ' . esc_html( $phone ) . ')</p><p>' . esc_html( $date ) . ' at ' . esc_html( $time ) . ' · ' . intval( $guests ) . ' guests</p><p>Total: ' . fb_format_price( $quote['total'] ?? 0 ) . '</p>';
        if ( $occ ) $admin_body .= '<p>Occasion: ' . esc_html( $occ ) . '</p>';
        if ( $notes ) $admin_body .= '<p>Notes: ' . esc_html( $notes ) . '</p>';
        $admin_body .= '<p><a href="' . esc_url( admin_url( "post.php?post=$post_id&action=edit" ) ) . '">Open booking</a></p>';
        wp_mail( $admin, 'New booking — ' . $name . ' — ' . $date, $admin_body, self::from() );
    }

    public static function send_event_enquiry( $post_id ) {
        $email = get_post_meta( $post_id, '_fb_e_email', true );
        $name = get_post_meta( $post_id, '_fb_e_name', true );
        $type = get_post_meta( $post_id, '_fb_e_event_type', true );
        $body = '<div style="font-family: -apple-system, Segoe UI, Helvetica, sans-serif; max-width:520px; margin:24px auto; background:#fbf6ec; border-radius:14px; padding:32px"><h2 style="font-family: Georgia, serif">We got your enquiry</h2><p>Hi ' . esc_html( $name ) . ',</p><p>Thanks for asking about a ' . esc_html( $type ) . ' at Floating By Everyday. We will reply within a working day with availability and a quote.</p></div>';
        wp_mail( $email, 'Thanks for your enquiry — Floating By Everyday', $body, self::from() );

        $admin = fb_setting( 'sender_email', get_option('admin_email') );
        wp_mail( $admin, 'Event enquiry — ' . $type . ' — ' . $name, '<p><a href="' . esc_url( admin_url( "post.php?post=$post_id&action=edit" ) ) . '">Open enquiry</a></p>', self::from() );
    }
}
