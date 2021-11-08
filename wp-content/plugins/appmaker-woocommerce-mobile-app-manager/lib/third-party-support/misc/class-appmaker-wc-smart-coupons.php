<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class APPMAKER_WC_SMART_COUPONS
 */
class APPMAKER_WC_SMART_COUPONS {

	/**
	 * Function __construct.
	 */
	public function __construct() {
	    add_filter( 'appmaker_wc_cart_items', array( $this, 'set_quantity_smart_coupons' ), 10, 1 );
	}

	/**
	 * Function to fix order minimum amount.
	 *
	 * @param array $return Return.
	 *
	 * @return mixed
	 */
	public function set_quantity_smart_coupons( $return ) {
		if ( is_array ( $return['products'] ) && ! empty( $return['coupons_applied'] ) ) {
			foreach( $return['products'] as $id => $product ) {
                if( isset ( $product['wc_sc_product_source'] ) ) {
                    $coupon      = new WC_Coupon( $product['wc_sc_product_source'] );
                    $coupon_id   = $coupon->id;
                    $add_product_details = get_post_meta( $coupon_id, 'wc_sc_add_product_details', true );
			        $add_product_qty     = ( isset( $add_product_details[0]['quantity'] ) ) ? $add_product_details[0]['quantity'] : 1;
                    $return['products'][$id]['qty_config']['max_value'] = (int)$add_product_qty;
                }
            }
		}

		return $return;
	}

}

new APPMAKER_WC_SMART_COUPONS();
