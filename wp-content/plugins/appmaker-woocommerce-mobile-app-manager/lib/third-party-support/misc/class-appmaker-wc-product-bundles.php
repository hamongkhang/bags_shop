<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class APPMAKER_WC_SMART_COUPONS
 */
class APPMAKER_WC_PRODUCT_BUNDLES {

	/**
	 * Function __construct.
	 */
	public function __construct() {
	    add_filter( 'appmaker_wc_cart_items', array( $this, 'set_quantity_bundled_products' ), 10, 1 );
	}

	/**
	 * Function to fix order minimum amount.
	 *
	 * @param array $return Return.
	 *
	 * @return mixed
	 */
	public function set_quantity_bundled_products( $return ) {
		if ( is_array ( $return['products'] ) ) {
			foreach( $return['products'] as $id => $product ) {

                if( isset ( $product['stamp'] ) && ! empty( $product['stamp'] ) && isset ( $product['bundled_by'] ) ) {
                    //foreach ( $product['stamp'] as $id => $bundled_item_data ) {
                        // $bundled_item_id  = $bundled_item_data[ 'product_id' ];
                        // $bundle           = WC()->cart->cart_contents[ $product['key'] ][ 'data' ];
                        // $bundled_item     = $bundle->get_bundled_item( $bundled_item_id );print_r($bundled_item);exit;
                        //$bundled_product = wc_get_product( $bundled_item_data[ 'product_id' ] );
                        $cart_item = WC()->cart->cart_contents[ $product['key']  ];

                        if ( $container_item = wc_pb_get_bundled_cart_item_container( $cart_item ) ) {

                            $bundled_item_id = $cart_item[ 'bundled_item_id' ];
                            $bundled_item    = $container_item[ 'data' ]->get_bundled_item( $bundled_item_id );

                            $min_quantity = $bundled_item->get_quantity( 'min' );
                            $max_quantity = $bundled_item->get_quantity( 'max' );
                        }                        
                        $return['products'][$id]['qty_config']['max_value'] = $return['products'][$id]['quantity'];
						$return['products'][$id]['qty_config']['min_value'] = $return['products'][$id]['quantity'];
						$return['products'][$id]['qty_config']['input_value'] = $return['products'][$id]['quantity'];
						$return['products'][$id]['qty_config']['display']   = false;
						$return['products'][$id]['hide_delete_button'] = true;
						if($return['products'][$id]['product_price'] == 0 ) {
							$return['products'][$id]['product_price_display'] = '';
						}
                    //}
                }               
               
            }
		}

		return $return;
	}

}

new APPMAKER_WC_PRODUCT_BUNDLES();
