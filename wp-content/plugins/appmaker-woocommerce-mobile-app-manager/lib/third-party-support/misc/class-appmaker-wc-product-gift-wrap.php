<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_Product_Gift_Wrap {

	public function __construct() {
		add_filter( 'appmaker_wc_product_fields', array( $this, 'product_fields' ), 2, 2 );
	}

	/**
	 * @param array $fields
	 * @param WC_Product $product
	 *
	 * @return array|mixed|void
	 */
	public function product_fields( $fields, $product ) {
		$is_wrappable = get_post_meta( $product->get_id(), '_is_gift_wrappable', true );

		if ( $is_wrappable === '' && get_option( 'product_gift_wrap_enabled' ) === 'yes' ) {
			$is_wrappable = 'yes';
		}

		if ( $is_wrappable === 'yes' ) {

			$cost = get_post_meta( $product->get_id(), '_gift_wrap_cost', true );

			if ( $cost === '' ) {
				$cost = get_option( 'product_gift_wrap_cost', 0 );
			}

			$price_text                = $cost > 0 ? woocommerce_price( $cost ) : __( 'free', 'woocommerce-product-gift-wrap' );
			$product_gift_wrap_message = get_option( 'product_gift_wrap_message' );
			$fields                    = array(
				'gift_wrap' => array(
					'type'     => 'checkbox',
					'label'    => strip_tags( html_entity_decode( str_replace( array( '{checkbox}', '{price}' ) , array(
						'',
						$price_text,
					), wp_kses_post( $product_gift_wrap_message ) ) ) ),
					'required' => false,
				),
			);
			$fields = APPMAKER_WC_Dynamic_form::get_fields( $fields, 'payment' );
		}
		return $fields;
	}

}

new APPMAKER_WC_Product_Gift_Wrap();
