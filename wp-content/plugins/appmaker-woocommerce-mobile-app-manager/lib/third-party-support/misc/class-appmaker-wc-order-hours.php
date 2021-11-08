<?php
namespace Zhours;

use \Zhours\Aspect\Page, \Zhours\Aspect\TabPage, \Zhours\Aspect\Box;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class APPMAKER_WC_Minimum_Order_Cart
 */
class APPMAKER_WC_Order_hours {

	/**
	 * Function __construct.
	 */
	public function __construct() {
		add_filter( 'appmaker_wc_cart_meta_response', array( $this, 'order_hours' ), 2, 1 );

	}

		/**
		 * Function to fix order minimum amount.
		 *
		 * @param array $return Return.
		 *
		 * @return mixed
		 */
	public	function order_hours( $return ) {
    
       if(!get_current_status()){
        $return['can_proceed']   = false;
        $return['error_message'] =  __( 'Sorry, shop is closed now', 'appmaker-woocommerce-mobile-app-manager' );
       }
			
		

		return $return;
	}
}

new APPMAKER_WC_Order_hours();
