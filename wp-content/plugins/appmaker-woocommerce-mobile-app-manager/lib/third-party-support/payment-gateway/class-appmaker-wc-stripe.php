<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_Stripe extends APPMAKER_WC_REST_Controller {
	 /**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'appmaker-wc/v1';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'apple_pay';
	public function __construct() {
		parent::__construct();
		if ( ! isset( $_POST['stripe_token'] ) ) {
			add_filter( 'appmaker_wc_checkout_redirect_gateways', array( $this, 'add_stripe_gateway' ), 1, 1 );
			add_filter( 'appmaker_wc_redirect_payment_gateway_url', '__return_true' );
		}

		if( isset( $_POST['stripe_token'] ) ){

			add_action( 'appmaker_wc_before_checkout', array( $this, 'process_stripe_data' ), 1, 1 );
		}		

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/shipping_methods',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'cart_shipping_methods' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/payment_intents',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'apple_pay_intents' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

	}

	public function apple_pay_intents( $request ) {

		$payment_intent = WC_Stripe_API::request(
			[
				'amount'               => $request['amount'],
				'currency'             => strtolower(  $request['currency'] ),
				//'payment_method_types' => $enabled_payment_methods,
				//'capture_method'       => $capture ? 'automatic' : 'manual',
			],
			'payment_intents'
		);
		
		$client_secret = $payment_intent->client_secret;
		return $payment_intent;
	}

	public function add_stripe_gateway( $gateways ) {

		if ( is_array( $gateways ) ) {
			array_push( $gateways, 'stripe' );
		}
		return $gateways;
	}



	public function stripe_create_source_id( $token ) {
		$source = WC_Stripe_API::request(
			[
				'type'  => 'card',
				'token' => $token,
			],
			'sources'
		);
		return $source->id;
	}

	public function process_stripe_data( $return ) {
		$billing_country  = ! empty( $_POST['billing_country'] ) ? wc_clean( $_POST['billing_country'] ) : '';
		$shipping_country = ! empty( $_POST['shipping_country'] ) ? wc_clean( $_POST['shipping_country'] ) : '';
		$billing_state    = ! empty( $_POST['billing_state'] ) ? wc_clean( $_POST['billing_state'] ) : '';
		$shipping_state   = ! empty( $_POST['shipping_state'] ) ? wc_clean( $_POST['shipping_state'] ) : '';

		if ( $billing_state && $billing_country ) {
			$valid_states = WC()->countries->get_states( $billing_country );

			// Valid states found for country.
			if ( ! empty( $valid_states ) && is_array( $valid_states ) && sizeof( $valid_states ) > 0 ) {
				foreach ( $valid_states as $state_abbr => $state ) {
					if ( preg_match( '/' . preg_quote( $state ) . '/i', $billing_state ) ) {
						$_POST['billing_state'] = $state_abbr;
					}
				}
			}
		}

		if ( $shipping_state && $shipping_country ) {
			$valid_states = WC()->countries->get_states( $shipping_country );

			// Valid states found for country.
			if ( ! empty( $valid_states ) && is_array( $valid_states ) && sizeof( $valid_states ) > 0 ) {
				foreach ( $valid_states as $state_abbr => $state ) {
					if ( preg_match( '/' . preg_quote( $state ) . '/i', $shipping_state ) ) {
						$_POST['shipping_state'] = $state_abbr;
					}
				}
			}
		}

		$_POST['stripe_source'] = $this->stripe_create_source_id( $_POST['stripe_token'] );
		unset( $_POST['stripe_token'] );

		return $return;
	}

	public function cart_shipping_methods() {
		$return = array();
		if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) {
			$return['show_shipping'] = 1;
			WC()->cart->calculate_shipping();
			$packages = WC()->shipping()->get_packages();
			foreach ( $packages as $i => $package ) {
				$chosen_method = isset( WC()->session->chosen_shipping_methods[ $i ] ) ? WC()->session->chosen_shipping_methods[ $i ] : '';
				$methods       = $this->getMethodsInArray( $package['rates'] );
				if ( empty( $chosen_method ) & ! empty( $methods ) ) {
					$chosen_method = $methods[0]['id'];
				}
				$return['shipping'][] = array(
					'methods' => $this->getMethodsInArray( $package['rates'] ),
					'chosen'  => $chosen_method,
					'index'   => $i,
				);
			}
		} else {
			$return['show_shipping'] = 0;
			$return['shipping']      = array();
		}
		if ( empty( $return['shipping'] ) || is_null( $return['shipping'] ) || ! is_array( $return['shipping'] ) ) {
			$return['show_shipping'] = 0;
			$return['shipping']      = array();
		}

		return $return;

	}

	private function getMethodsInArray( $methods ) {
		$return = array();
		foreach ( $methods as $method ) {

			if ( ! empty( $method->taxes ) ) {
				foreach ( $method->taxes as $tax ) {
					$shipping_cost = $method->cost + $tax;
				}
			} else {
				$shipping_cost = $method->cost;
			}

			$return[] = array(
				'id'           => $method->id,
				'label'        => html_entity_decode( $method->label, ENT_QUOTES, 'UTF-8' ),
				'cost'         => $shipping_cost,
				'cost_display' => APPMAKER_WC_Helper::get_display_price( $shipping_cost ),
				'taxes'        => $method->taxes,
				'method_id'    => $method->method_id,
			);

		}

		return $return;
	}


}
new APPMAKER_WC_Stripe();
