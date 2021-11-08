<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_Product_Brands {

	public function __construct() {
		add_filter( 'appmaker_wc_product_filters', array( $this, 'filters' ), 10, 3 );
	}

	/**
	 * @param $return
	 *
	 * @return mixed
	 */
	public function filters( $return ) {
		$brands_list = get_terms( 'product_brand', array(
			'orderby'    => 'name',
			'order'      => 'ASC',
			'hide_empty' => true,
		) );

		$brands_list = get_terms( 'product_brand' );

		if ( ! empty( $brands_list ) && is_array( $brands_list ) ) {
			$return['items']['product_brand'] = array(
				'id'     => 'product_brand',
				'type'   => 'checkbox',
				'label'  => __( 'Brands' ),
				'values' => array(),
			);

			foreach ( $brands_list as $term ) {
				$return['items']['product_brand']['values'][] = array(
					'label' => strip_tags( html_entity_decode( $term->name ) ),
					'value' => $term->slug,
				);
			}
		}

		return $return;
	}
}

new APPMAKER_WC_Product_Brands();
