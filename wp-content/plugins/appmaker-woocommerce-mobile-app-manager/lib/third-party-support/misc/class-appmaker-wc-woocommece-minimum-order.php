<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class APPMAKER_WC_Minimum_Order_Cart
 */
class APPMAKER_WC_Minimum_Order {

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
	public	function order_minimum_amount( $return ) {
		if ( ! WC()->cart->is_empty() ) {
			$minimum_amount = get_option( 'min_amount', 0 );
			if ( $minimum_amount > 0 && $minimum_amount > $return['total'] ) {
				$return['can_proceed']   = false;
				$return['error_message'] = get_option( 'error_message' );
			}
		}

		return $return;
	}
}

new APPMAKER_WC_Minimum_Order();
