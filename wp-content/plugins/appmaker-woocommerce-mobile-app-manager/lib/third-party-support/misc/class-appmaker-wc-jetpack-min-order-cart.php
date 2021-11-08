<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class APPMAKER_WC_Minimum_Order_Cart
 */
class APPMAKER_WC_Minimum_Order_Cart {

	/**
	 * Function __construct.
	 */
	public function __construct() {
	    add_filter( 'appmaker_wc_cart_meta_response', array( $this, 'order_minimum_amount' ), 10, 1 );
	}

	/**
	 * Function to fix order minimum amount.
	 *
	 * @param array $return Return.
	 *
	 * @return mixed
	 */
	public function order_minimum_amount( $return ) {
		if ( ! WC()->cart->is_empty() ) {
			$minimum_amount = get_option( 'wcj_order_minimum_amount', 0 );
			if ( $minimum_amount > 0 && $minimum_amount > $return['total'] ) {
				$return['can_proceed']   = false;
				$return['error_message'] = sprintf( get_option( 'wcj_order_minimum_amount_error_message' ), APPMAKER_WC_Helper::get_display_price( $minimum_amount ), APPMAKER_WC_Helper::get_display_price( $return['total'] ) );
			}
		}

		return $return;
	}

}

new APPMAKER_WC_minimum_order_cart();
