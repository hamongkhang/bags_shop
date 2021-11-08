<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_simulador_frete {

	public function __construct() {

		add_filter( 'appmaker_wc_product_tabs', array( $this, 'shipping_tab' ), 2, 1 );
		add_filter( 'appmaker_wc_product_widgets', array( $this, 'product_calculate_shipping_widget' ), 2, 2 );
		add_filter( 'appmaker_wc_submit_form', array( $this, 'calculate_shipping' ), 1, 1 );
	}

	public function shipping_tab( $tabs ) {

		if ( ! isset( $tabs['shipping_tab'] ) ) {
			$tabs['shipping_tab'] = array(
				'title'    => __( 'Calculate Shipping', 'appmaker-woocommerce-mobile-app-manager' ),
				'priority' => 4,
				'callback' => 'woocommerce_product_description_tab',
			);
		}
		return $tabs;  /* Return all  tabs including the new New Custom Product Tab  to display */
	}


	public function product_calculate_shipping_widget( $return, $product ) {

		$tabs    = apply_filters( 'woocommerce_product_tabs', array() );
		$tabs    = apply_filters( 'appmaker_wc_product_tabs', $tabs );
		$title   = APPMAKER_WC::$api->get_settings( 'product_tab_field_title_shipping_tab' );
		$user_id = get_current_user_id();

		if ( $user_id ) {
			$default_postcode = get_user_meta( $user_id, 'billing_postcode', true );
		} else {
			$default_postcode = '';
		}

		foreach ( $tabs as $key => $tab ) {
			if ( $key === 'shipping_tab' ) {
				$return['shipping_tab'] = array(
					'type'          => 'submit_form',
					'title'         => empty( $title ) ? __( 'Calculate Shipping', 'appmaker-woocommerce-mobile-app-manager' ) : $title,
					'expandable'    => false,
					'expanded'      => true,
					'content'       => '',
					'button_title'  => __( 'Calculate', 'appmaker-woocommerce-mobile-app-manager' ),
					'placeholder'   => __( '00000000', 'appmaker-woocommerce-mobile-app-manager' ),
					'default_value' => $default_postcode,

				);
			}
		}
		return $return;
	}

	public function wp_die_ajax_handler( $function ) {
		return '__return_true';
	}

	public function calculate_shipping( $request ) {

		$return = array(
			'error' => '',
			'rates' => '',
		);

		if ( ! empty( $request['postcode'] ) && ! empty( $request['id'] ) ) {

			define( 'DOING_AJAX', true );
			add_filter( 'wp_die_ajax_handler', array( $this, 'wp_die_ajax_handler' ) );

			$product_id         = $request['id'];
			$product            = wc_get_product( $product_id );
			$_GET['zipcode']    = $request['postcode'];
			$_GET['product_id'] = $product_id;
			$_GET['type']       = $product->get_type();
			$obj                = new WC_Shipping_Simulator_Show();
			// $user_state    = $obj->get_state_by_postcode( $postcode );

			ob_start();
			$obj->wc_shipping_simulator_ajax();
			$response_json = ob_get_clean();
			preg_match( '/({((?!}{).)*}){/', $response_json, $response );
			// $shipping_data = str_replace( '}{', '}', $response[0] );
			$shipping_data = $response[1];

			$data = (array) json_decode( $shipping_data );
			// print_r($data);exit;
			if ( ! empty( $data['rates'] ) ) {

				foreach ( $data['rates'] as $rate ) {
					$shipping_rates[] = array(
						'method_id' => $rate->label,
						'cost'      => html_entity_decode( strip_tags( $rate->cost ) ),
					);

					// $rate->cost =  html_entity_decode(strip_tags($rate->cost));
				}
				$return['rates'] = $shipping_rates;
			} elseif ( ! empty( $data ) ) {

				$return['error'] = $data['error'];
				$return['rates'] = $data['rates'];
			}
		}
		return $return;
	}
}
new APPMAKER_WC_simulador_frete();
