<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class FB_REST {

    public static function register_routes() {
        $ns = 'fb/v1';

        register_rest_route( $ns, '/pieces', [
            'methods' => 'GET',
            'callback' => [ __CLASS__, 'get_pieces' ],
            'permission_callback' => '__return_true',
        ] );

        register_rest_route( $ns, '/slots', [
            'methods' => 'GET',
            'callback' => [ __CLASS__, 'get_slots' ],
            'permission_callback' => '__return_true',
            'args' => [ 'date' => [ 'required' => true ] ],
        ] );

        register_rest_route( $ns, '/availability', [
            'methods' => 'GET',
            'callback' => [ __CLASS__, 'get_availability' ],
            'permission_callback' => '__return_true',
            'args' => [
                'year'  => [ 'required' => true ],
                'month' => [ 'required' => true ],
            ],
        ] );

        register_rest_route( $ns, '/coupon', [
            'methods' => 'POST',
            'callback' => [ __CLASS__, 'apply_coupon' ],
            'permission_callback' => '__return_true',
        ] );

        register_rest_route( $ns, '/quote', [
            'methods' => 'POST',
            'callback' => [ __CLASS__, 'get_quote' ],
            'permission_callback' => '__return_true',
        ] );

        register_rest_route( $ns, '/book', [
            'methods' => 'POST',
            'callback' => [ __CLASS__, 'create_booking' ],
            'permission_callback' => '__return_true',
        ] );

        register_rest_route( $ns, '/event-enquiry', [
            'methods' => 'POST',
            'callback' => [ __CLASS__, 'event_enquiry' ],
            'permission_callback' => '__return_true',
        ] );
    }

    public static function get_pieces( $req ) {
        return rest_ensure_response( fb_get_pieces() );
    }

    public static function get_slots( $req ) {
        $date = sanitize_text_field( $req['date'] );
        if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) return new WP_Error( 'bad_date', 'Invalid date', [ 'status' => 400 ] );
        $slots = fb_default_slots( $date );
        foreach ( $slots as &$s ) {
            $s['remaining'] = fb_slot_remaining( $date, $s['time'] );
            $s['full'] = $s['remaining'] <= 0;
        }
        return rest_ensure_response( [ 'date' => $date, 'slots' => $slots ] );
    }

    public static function get_availability( $req ) {
        $y = intval( $req['year'] );
        $m = intval( $req['month'] );
        $days = (int) date( 't', strtotime( "$y-$m-01" ) );
        $today = current_time('Y-m-d');
        $out = [];
        for ( $d = 1; $d <= $days; $d++ ) {
            $date = sprintf( '%04d-%02d-%02d', $y, $m, $d );
            if ( $date < $today ) { $out[ $date ] = [ 'available' => false, 'reason' => 'past' ]; continue; }
            $slots = fb_default_slots( $date );
            if ( ! $slots ) { $out[ $date ] = [ 'available' => false, 'reason' => 'closed' ]; continue; }
            $any = false;
            foreach ( $slots as $s ) {
                if ( fb_slot_remaining( $date, $s['time'] ) > 0 ) { $any = true; break; }
            }
            $out[ $date ] = [ 'available' => $any ];
        }
        return rest_ensure_response( $out );
    }

    public static function apply_coupon( $req ) {
        $code = sanitize_text_field( $req['code'] ?? '' );
        $subtotal = floatval( $req['subtotal'] ?? 0 );
        $c = fb_validate_coupon( $code, $subtotal );
        if ( ! $c ) return new WP_Error( 'invalid_coupon', 'Coupon not valid', [ 'status' => 404 ] );
        return rest_ensure_response( $c );
    }

    public static function get_quote( $req ) {
        $body = $req->get_json_params();
        $quote = FB_Booking::compute_quote( $body );
        return rest_ensure_response( $quote );
    }

    public static function create_booking( $req ) {
        $body = $req->get_json_params();
        $required = [ 'date','time','duration','guests','contact','pieces','terms' ];
        foreach ( $required as $r ) {
            if ( ! isset( $body[ $r ] ) ) return new WP_Error( 'missing', "Missing $r", [ 'status' => 400 ] );
        }
        if ( empty( $body['terms'] ) ) return new WP_Error( 'terms', 'Please accept the terms', [ 'status' => 400 ] );
        $contact = $body['contact'];
        foreach ( [ 'name','email','phone' ] as $k ) {
            if ( empty( $contact[ $k ] ) ) return new WP_Error( 'contact', "Missing contact $k", [ 'status' => 400 ] );
        }

        $remaining = fb_slot_remaining( sanitize_text_field( $body['date'] ), sanitize_text_field( $body['time'] ) );
        $guests = intval( $body['guests'] );
        if ( $guests > $remaining ) return new WP_Error( 'full', "Only $remaining seats left in this slot", [ 'status' => 409 ] );

        $quote = FB_Booking::compute_quote( $body );

        $title = sprintf( 'Booking — %s — %s — %s', $contact['name'], $body['date'], $body['time'] );
        $post_id = wp_insert_post( [
            'post_type' => 'fb_booking',
            'post_status' => 'publish',
            'post_title' => $title,
        ], true );
        if ( is_wp_error( $post_id ) ) return $post_id;

        $confirm = 'FBE-' . date('ymd') . '-' . str_pad( $post_id, 4, '0', STR_PAD_LEFT );

        update_post_meta( $post_id, '_fb_confirm', $confirm );
        update_post_meta( $post_id, '_fb_date', sanitize_text_field( $body['date'] ) );
        update_post_meta( $post_id, '_fb_time', sanitize_text_field( $body['time'] ) );
        update_post_meta( $post_id, '_fb_duration', sanitize_text_field( $body['duration'] ) );
        update_post_meta( $post_id, '_fb_guests', intval( $body['guests'] ) );
        update_post_meta( $post_id, '_fb_contact_name', sanitize_text_field( $contact['name'] ) );
        update_post_meta( $post_id, '_fb_contact_email', sanitize_email( $contact['email'] ) );
        update_post_meta( $post_id, '_fb_contact_phone', sanitize_text_field( $contact['phone'] ) );
        update_post_meta( $post_id, '_fb_occasion', sanitize_text_field( $body['occasion'] ?? '' ) );
        update_post_meta( $post_id, '_fb_notes', sanitize_textarea_field( $body['notes'] ?? '' ) );
        update_post_meta( $post_id, '_fb_pieces', wp_json_encode( $body['pieces'] ) );
        update_post_meta( $post_id, '_fb_addons', wp_json_encode( $body['addons'] ?? [] ) );
        update_post_meta( $post_id, '_fb_paints', wp_json_encode( $body['paints'] ?? [] ) );
        update_post_meta( $post_id, '_fb_quote', wp_json_encode( $quote ) );
        update_post_meta( $post_id, '_fb_status', 'pending_payment' );

        FB_Email::send_booking_confirmation( $post_id );

        return rest_ensure_response( [
            'ok' => true,
            'id' => $post_id,
            'confirm' => $confirm,
            'quote' => $quote,
        ] );
    }

    public static function event_enquiry( $req ) {
        $body = $req->get_json_params();
        $required = [ 'event_type','name','email','phone','group_size' ];
        foreach ( $required as $r ) {
            if ( empty( $body[ $r ] ) ) return new WP_Error( 'missing', "Missing $r", [ 'status' => 400 ] );
        }
        $title = sprintf( 'Event enquiry — %s — %s', $body['event_type'], $body['name'] );
        $post_id = wp_insert_post( [
            'post_type' => 'fb_booking',
            'post_status' => 'publish',
            'post_title' => $title,
        ] );
        update_post_meta( $post_id, '_fb_kind', 'event_enquiry' );
        foreach ( $body as $k => $v ) {
            update_post_meta( $post_id, "_fb_e_$k", is_string( $v ) ? sanitize_text_field( $v ) : wp_json_encode( $v ) );
        }
        FB_Email::send_event_enquiry( $post_id );
        return rest_ensure_response( [ 'ok' => true ] );
    }
}
