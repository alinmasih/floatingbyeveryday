<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class FB_Booking {

    public static function compute_quote( $data ) {
        $studio_fee = floatval( fb_setting( 'studio_fee', 250 ) );
        $gst_rate = floatval( fb_setting( 'gst_rate', 18 ) );
        $group_threshold = intval( fb_setting( 'group_threshold', 6 ) );
        $group_pct = floatval( fb_setting( 'group_discount_pct', 10 ) );

        $guests = max( 1, intval( $data['guests'] ?? 1 ) );
        $pieces_in = $data['pieces'] ?? [];

        $studio_total = $studio_fee * $guests;

        $pieces_total = 0;
        $pieces_resolved = [];
        foreach ( $pieces_in as $row ) {
            $pid = intval( $row['id'] );
            $qty = max( 1, intval( $row['qty'] ?? 1 ) );
            $price = floatval( get_post_meta( $pid, '_fb_price', true ) );
            $name = get_the_title( $pid );
            $line = $price * $qty;
            $pieces_total += $line;
            $pieces_resolved[] = [
                'id' => $pid,
                'name' => $name,
                'qty' => $qty,
                'unit' => $price,
                'total' => $line,
            ];
        }

        $addons_total = 0;
        $addons_resolved = [];
        $ADDON_PRICES = [
            'premium-paints' => [ 'name' => 'Premium paints', 'price' => 80 ],
            'metallic-paints' => [ 'name' => 'Metallic paints', 'price' => 100 ],
            'detail-pens' => [ 'name' => 'Detail pens', 'price' => 60 ],
            'dotting-tools' => [ 'name' => 'Dotting tools', 'price' => 40 ],
            'mandala-kit' => [ 'name' => 'Mandala stencil kit', 'price' => 150 ],
            'marble-kit' => [ 'name' => 'Marble effect kit', 'price' => 200 ],
            'warli-kit' => [ 'name' => 'Warli stencil kit', 'price' => 150 ],
            'block-kit' => [ 'name' => 'Block print kit', 'price' => 180 ],
        ];
        foreach ( ( $data['addons'] ?? [] ) as $a ) {
            $key = sanitize_key( $a );
            if ( isset( $ADDON_PRICES[ $key ] ) ) {
                $addons_total += $ADDON_PRICES[ $key ]['price'];
                $addons_resolved[] = [ 'key' => $key, 'name' => $ADDON_PRICES[ $key ]['name'], 'amount' => $ADDON_PRICES[ $key ]['price'] ];
            }
        }

        $subtotal = $studio_total + $pieces_total + $addons_total;

        $group_discount = 0;
        if ( $guests >= $group_threshold ) {
            $group_discount = round( $subtotal * $group_pct / 100, 0 );
        }

        $coupon_discount = 0;
        $coupon_code = '';
        if ( ! empty( $data['coupon'] ) ) {
            $c = fb_validate_coupon( sanitize_text_field( $data['coupon'] ), $subtotal );
            if ( $c ) {
                $coupon_discount = $c['amount'];
                $coupon_code = $c['code'];
            }
        }

        $taxable = max( 0, $subtotal - $group_discount - $coupon_discount );
        $gst = round( $taxable * $gst_rate / 100, 0 );
        $total = $taxable + $gst;

        return [
            'currency' => 'INR',
            'guests' => $guests,
            'studio_fee_per_seat' => $studio_fee,
            'studio_total' => $studio_total,
            'pieces' => $pieces_resolved,
            'pieces_total' => $pieces_total,
            'addons' => $addons_resolved,
            'addons_total' => $addons_total,
            'subtotal' => $subtotal,
            'group_discount' => $group_discount,
            'group_discount_pct' => $guests >= $group_threshold ? $group_pct : 0,
            'coupon_code' => $coupon_code,
            'coupon_discount' => $coupon_discount,
            'gst_rate' => $gst_rate,
            'gst' => $gst,
            'total' => $total,
        ];
    }
}
