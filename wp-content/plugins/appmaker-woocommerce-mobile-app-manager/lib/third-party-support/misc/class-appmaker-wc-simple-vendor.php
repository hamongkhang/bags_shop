<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_Simple_Vendors {

	public function __construct() {

		add_filter( 'appmaker_wc_product_widgets', array( $this, 'get_vendor_name' ), 10, 3 );
	}

	public function get_vendor_name( $return, $product, $data ){

		$productId = $product->id;
		$simple_vendor = get_post_meta( $productId, 'simple-vendor', true );
		global $wpdb;

		if ($simple_vendor) {
			$display_name = $wpdb->get_var("SELECT `display_name` FROM $wpdb->users WHERE `ID` = " . $simple_vendor);
			if (!empty($display_name)) {
				$ret = ''.__('Sold By').' : '.$display_name.'';
				array_splice($return,0,0,array('vendor' => array(
					'type'  => 'text',
					'title' =>$ret,
					'expandable' => false,
					'expanded'   => false,
					'content'=>''
				)));

			}
		}

		return $return;
	}



}
new APPMAKER_WC_Simple_Vendors();
