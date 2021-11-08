<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_BULK_VARIATIONS {


	public function __construct() {
		if ( ! empty( $_POST['app-add-variations-to-cart'] ) ) {
			add_action( 'appmaker_wc_before_add_to_cart', array( $this, 'addVariationIdBeforeCart' ), 1, 1 );
		}
	}

	public function addVariationIdBeforeCart( $request ) {
		$order_info = $_POST['order_info'];

		$productId = $_POST['product_id'];

		if ( WC_Bulk_Variations_Compatibility::is_wc_version_gte_2_4() ) {
			$matrix_data = woocommerce_bulk_variations_create_matrix_v24( $productId );
		} else {
			$matrix_data = woocommerce_bulk_variations_create_matrix( $productId );
		}

		foreach ( $order_info as $key => $order ) {
			$row_attribute    = $order['variation_data'][ "attribute_$matrix_data[row_attribute]" ];
			$column_attribute = $order['variation_data'][ "attribute_$matrix_data[column_attribute]" ];

			$variation_id                                   = ( $matrix_data['matrix'][ $row_attribute ][ $column_attribute ]['variation_id'] );
			$_REQUEST['order_info'][ $key ]['variation_id'] = $_POST['order_info'][ $key ]['variation_id'] = $variation_id;
		}

		$GLOBALS['wc_bulk_variations']->process_matrix_submission();
	}


}

new APPMAKER_WC_BULK_VARIATIONS();
