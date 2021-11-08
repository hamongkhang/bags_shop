<?php
/**
 * REST API Checkout_Form controller
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API Checkout_Form controller
 */
class APPMAKER_WC_REST_Checkout_Form_Controller extends APPMAKER_WC_REST_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'appmaker-wc/v1';

	/**
	 * Route base.

	 * @var string
	 */
	protected $rest_base = 'checkout-form';


	/**
	 * Register the routes for products.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'checkout_form' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/form/(?P<field>[\a-z]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_checkout_field' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/shipping_methods',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'shipping_methods' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/payment_gateways',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'payment_gateways' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/review',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'review' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Return checkout fields
	 *
	 * @return array|bool|mixed|object|WP_Error
	 */
	public function checkout_form() {

		$return =  array(
			'billing'  => $this->get_checkout_fields( 'billing' ),
			'shipping' => $this->get_checkout_fields( 'shipping' ),
			'order'    => $this->get_checkout_fields( 'order' ),
		);
		$order_in_web = APPMAKER_WC::$api->get_settings( 'order_in_web', false );
		
		if($order_in_web){
			$return['order_in_webview'] = true;
		}else{
			$return['order_in_webview'] = false;
		}
		
		return $return;
	}

	/**
	 * Return checkout fields for single field
	 *
	 * @param array $request Request Object.
	 *
	 * @return array|bool|mixed|object|void|WP_Error
	 */
	public function get_checkout_fields( $request ) {
		if ( is_string( $request ) ) {
			$section = $request;
		} else {
			$section = $request['field'];
		}

		$checkout_fields = WC()->checkout()->checkout_fields;
		if ( isset( $checkout_fields[ $section ] ) || $section == 'order' ) {
			if ( 'billing' == $section ) {

				$options = get_option( 'wccs_settings' );
				if ( isset( $options['buttons'] ) && count( $options['buttons'] ) > 0 && function_exists( 'wpml_string_wccm' ) ) :
					foreach ( $options['buttons'] as $btn ) :
						if ( ! empty( $btn['label'] ) && ( $btn['type'] == 'text' ) ) {
							$checkout_fields[ $section ][ $btn['cow'] ] = array(
								'id'          => $btn['cow'],
								'type'        => 'text',
								'class'       => array( 'wccs-field-class wccs-form-row-wide' ),
								'label'       => wpml_string_wccm( '' . $btn['label'] . '' ),
								'required'    => $btn['checkbox'],
								'placeholder' => wpml_string_wccm( '' . $btn['placeholder'] . '' ),
								'default'     => WC()->checkout()->get_value( '' . $btn['cow'] . '' ),
							);
						}
						if ( ! empty( $btn['label'] ) && ( $btn['type'] == 'select' ) ) {
							$checkout_fields[ $section ][ $btn['cow'] ] = array(
								'id'          => $btn['cow'],
								'type'        => 'select',
								'class'       => array( 'wccs-field-class wccs-form-row-wide' ),
								'label'       => wpml_string_wccm( '' . $btn['label'] . '' ),
								'options'     => array(),
								'required'    => $btn['checkbox'],
								'placeholder' => wpml_string_wccm( '' . $btn['placeholder'] . '' ),
								'default'     => WC()->checkout()->get_value( '' . $btn['cow'] . '' ),
							);

							if ( isset( $btn['option_a'] ) && ! empty( $btn['option_a'] ) ) {
								$checkout_fields[ $section ][ $btn['cow'] ]['options'][ '' . wpml_string_wccm( '' . $btn['option_a'] . '' ) . '' ] = '' . wpml_string_wccm( '' . $btn['option_a'] . '' ) . '';
							}
							if ( isset( $btn['option_b'] ) && ! empty( $btn['option_b'] ) ) {
								$checkout_fields[ $section ][ $btn['cow'] ]['options'][ '' . wpml_string_wccm( '' . $btn['option_b'] . '' ) . '' ] = '' . wpml_string_wccm( '' . $btn['option_b'] . '' ) . '';
							}
							if ( isset( $btn['option_c'] ) && ! empty( $btn['option_c'] ) ) {
								$checkout_fields[ $section ][ $btn['cow'] ]['options'][ '' . wpml_string_wccm( '' . $btn['option_c'] . '' ) . '' ] = '' . wpml_string_wccm( '' . $btn['option_c'] . '' ) . '';
							}
							if ( isset( $btn['option_d'] ) && ! empty( $btn['option_d'] ) ) {
								$checkout_fields[ $section ][ $btn['cow'] ]['options'][ '' . wpml_string_wccm( '' . $btn['option_d'] . '' ) . '' ] = '' . wpml_string_wccm( '' . $btn['option_d'] . '' ) . '';
							}
						}

						if ( ! empty( $btn['label'] ) && ( $btn['type'] == 'date' ) ) {
							$checkout_fields[ $section ][ $btn['cow'] ] = array(
								'id'          => $btn['cow'],
								'type'        => 'text',
								'class'       => array( 'wccs-field-class MyDate-' . $btn['cow'] . ' wccs-form-row-wide' ),
								'label'       => wpml_string_wccm( '' . $btn['label'] . '' ),
								'required'    => $btn['checkbox'],
								'placeholder' => wpml_string_wccm( '' . $btn['placeholder'] . '' ),
								'default'     => WC()->checkout()->get_value( '' . $btn['cow'] . '' ),
							);
						}

						if ( ! empty( $btn['label'] ) && ( $btn['type'] == 'checkbox' ) ) {
							$checkout_fields[ $section ][ $btn['cow'] ] = array(
								'id'          => $btn['cow'],
								'type'        => 'checkbox',
								'class'       => array( 'wccs-field-class wccs-form-row-wide' ),
								'label'       => wpml_string_wccm( '' . $btn['label'] . '' ),
								'required'    => $btn['checkbox'],
								'placeholder' => wpml_string_wccm( '' . $btn['placeholder'] . '' ),
								'default'     => WC()->checkout()->get_value( '' . $btn['cow'] . '' ),
							);
						}
					endforeach;
				endif;

			}

			if ( 'order' == $section ) {
				$checkout_fields[ $section ]['wpnonce'] = array(
					'id'      => 'wpnonce',
					'type'    => 'hidden',
					'default' => wp_create_nonce( 'woocommerce-process_checkout' ),
				);
			}

			if ( 'billing' == $section ) {
				if ( ! ( ( get_option( 'woocommerce_enable_guest_checkout' ) == 'yes' ) || is_user_logged_in() ) && isset( $checkout_fields['account'] ) ) {
					$checkout_fields[ $section ] = array_merge( $checkout_fields[ $section ], $checkout_fields['account'] );
				}

				$show_shipping_fields                                     = APPMAKER_WC::$api->get_settings( 'show_shipping_address_fields', true );
				$show_shipping_fields                                     = $show_shipping_fields && WC()->cart->needs_shipping_address();
				$checkout_fields[ $section ]['ship_to_different_address'] = array(
					'id'      => 'ship_to_different_address',
					'type'    => $show_shipping_fields ? 'checkbox' : 'hidden',
					'label'   => __( 'Ship to a different address?', 'woocommerce' ),
					'default' => 0,
				);
			}
			if ( isset( $checkout_fields['billing']['billing_phone'] ) ) {
				$checkout_fields['billing']['billing_phone']['priority'] = 40;
			}
			if ( isset( $checkout_fields['billing']['billing_postcode'] ) ) {
				$checkout_fields['billing']['billing_postcode']['priority'] = 45;
			}
			if ( isset( $checkout_fields['shipping']['shipping_phone'] ) ) {
				$checkout_fields['shipping']['shipping_phone']['priority'] = 40;
			}
			if ( isset( $checkout_fields['shipping']['shipping_postcode'] ) ) {
				$checkout_fields['shipping']['shipping_postcode']['priority'] = 45;
			}

			$return = APPMAKER_WC_Dynamic_form::get_fields( apply_filters( 'appmaker_wc_checkout_fields', $checkout_fields[ $section ], $section, $request ), $section );

			if ( ! is_wp_error( $return ) ) {
				$return['status']  = true;
				$return['section'] = $section;
			}

			$return = apply_filters( 'appmaker_wc_checkout_fields_response', $return, $section, $request );
		} else {
			$return['status'] = 0;
		}

		return $return;
	}

	/**
	 * Return checkout fields for single field
	 *
	 * @param array $request Request Object.
	 *
	 * @return array|bool|mixed|object|void|WP_Error
	 */
	public function get_checkout_field( $request ) {
		return $this->get_checkout_fields( $request['field'] );
	}

	/**
	 * Shipping methods
	 *
	 * @param $request
	 *
	 * @return array|bool|mixed|object|void|WP_Error
	 */
	public function shipping_methods( $request ) {
		$return = array();
		$key    = 'shipping';
		$data   = array();

		$request = apply_filters( 'appmaker_wc_before_shipping_methods', $request );

		if ( ! isset( $request['ship_to_different_address'] ) || ( isset( $request['ship_to_different_address'] ) && ( $request['ship_to_different_address'] === false || $request['ship_to_different_address'] === 'false' || ! $request['ship_to_different_address'] ) ) ) {
			$key = 'billing';
		}
		if ( isset( $request[ $key . '_country' ] ) ) {
			$data['calc_shipping_country'] = $request[ $key . '_country' ];
		} else {
			$data['calc_shipping_country'] = '';
		}

		if ( isset( $request[ $key . '_state' ] ) ) {
			$data['calc_shipping_state'] = $request[ $key . '_state' ];
		} else {
			$data['calc_shipping_state'] = '';
		}

		if ( apply_filters( 'woocommerce_shipping_calculator_enable_postcode', true ) && isset( $request[ $key . '_postcode' ] ) && ! empty( $request[ $key . '_postcode' ] ) && $request[ $key . '_postcode' ] != 'null' ) {
			$data['calc_shipping_postcode'] = wc_clean( $request[ $key . '_postcode' ] );
		} else {
			$data['calc_shipping_postcode'] = '';
		}

		if ( apply_filters( 'woocommerce_shipping_calculator_enable_city', true ) && isset( $request[ $key . '_city' ] ) && ! empty( $request[ $key . '_city' ] ) && $request[ $key . '_city' ] != 'null' ) {
			$data['calc_shipping_city'] = wc_clean( $request[ $key . '_city' ] );
		} else {
			$data['calc_shipping_city'] = '';
		}

		if ( empty( $data['calc_shipping_country'] ) || $data['calc_shipping_country'] == 'null' ) {
			$data['calc_shipping_country'] = false;
		}

		if ( empty( $data['calc_shipping_state'] ) || $data['calc_shipping_state'] == 'null' ) {
			$data['calc_shipping_state'] = '';
		}

		if ( isset( $request[ $key . '_address_1' ] ) ) {
			WC()->customer->set_address( $request[ $key . '_address_1' ] );
			WC()->customer->set_shipping_address( $request[ $key . '_address_1' ] );
		}

		if ( isset( $request[ $key . '_address_2' ] ) ) {
			WC()->customer->set_address_2( $request[ $key . '_address_2' ] );
			WC()->customer->set_shipping_address_2( $request[ $key . '_address_2' ] );
		}

		$_POST['post_data'] = http_build_query( $_POST ); // To make sure advanced shipping methods will show
		$calculate_shipping = $this->calculate_shipping( $data );
		if ( is_wp_error( $calculate_shipping ) ) {
			$return = $calculate_shipping;
		}
		if ( ! is_wp_error( $return ) ) {
			WC()->session->set( 'wc_shipping_calculate_details', $data );
			$return = $this->get_shipping_methods();
		}

		return $return;
	}

	/**
	 * Calculate shipping
	 *
	 * @param array $data shipping data.
	 *
	 * @return array|WP_Error
	 */
	public function calculate_shipping( $data ) {
		$return = false;
		try {
			WC()->shipping()->reset_shipping();
			$country  = $data['calc_shipping_country'];
			$state    = $data['calc_shipping_state'];
			$postcode = $data['calc_shipping_postcode'];
			$city     = $data['calc_shipping_city'];
			if ( ! empty( $postcode ) && ! WC_Validation::is_postcode( $postcode, $country ) ) {
				$return = new WP_Error( 'invalid_postcode', __( 'Please enter a valid postcode/ZIP.', 'woocommerce' ) );
			} elseif ( ! empty( $postcode ) ) {
				$postcode = wc_format_postcode( $postcode, $country );
			}
			if ( $country ) {
				WC()->customer->set_location( $country, $state, $postcode, $city );
				WC()->customer->set_shipping_location( $country, $state, $postcode, $city );
			} else {
				WC()->customer->set_to_base();
				WC()->customer->set_shipping_to_base();
			}

			WC()->customer->calculated_shipping( true );
			do_action( 'woocommerce_calculated_shipping' );
		} catch ( Exception $e ) {
			$return = new WP_Error( 'unable_to_process', 'Unable to process' );
		}

		$errors = $this->get_wc_notices_errors();
		if ( false !== $errors ) {
			$return = $errors;
		}
		if ( ! is_wp_error( $return ) ) {
			WC()->session->set( 'wc_shipping_calculate_details', $data );
			$return = true;
		} else {
			$return = $this->get_wc_notices_errors();
		}

		return $return;
	}

	/**
	 * Shipping methods
	 *
	 * @return array|bool|mixed|object|void|WP_Error
	 */
	protected function get_shipping_methods() {
		$return = array();
		$force_disable_shipping =  APPMAKER_WC::$api->get_settings( 'force_disable_shipping', false );
		if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() && !$force_disable_shipping ) {
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

	/**
	 * Parse each method in shipping methods
	 *
	 * @param object[] $methods Shipping method object array.
	 *
	 * @return array
	 */
	private function getMethodsInArray( $methods ) {
		$return = array();
		$tax_display = get_option( 'woocommerce_tax_display_cart' );
		foreach ( $methods as $method ) {

			if ( ! empty( $method->taxes ) && 'incl' == $tax_display ) {
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

		return apply_filters( 'appmaker-wc-shipping-methods', $return );
	}

	/**
	 * @param $request
	 *
	 * @return mixed
	 */
	public function payment_gateways( $request ) {
		$this->set_shipping_method( $request['shipping_methods'] );
		add_filter( 'woocommerce_is_checkout', '__return_true' );
		WC()->cart->calculate_totals();
		$available_payment_gateways = WC()->payment_gateways()->get_available_payment_gateways();
		if ( isset( $available_payment_gateways['amazon_payments_advanced'] ) ) {
			unset( $available_payment_gateways['amazon_payments_advanced'] );
		}

		$return                  = array();
		$gateways_enabled_in_app = APPMAKER_WC::$api->get_settings( 'payment_gateways_enabled', array() );
		foreach ( $available_payment_gateways as $key => $gateway ) {
			if ( ! empty( $gateways_enabled_in_app ) && is_array( $gateways_enabled_in_app ) && ! in_array( $gateway->id, $gateways_enabled_in_app ) ) {
				continue;
			}

			/** @var $gateway WC_Payment_Gateway */
			if ( apply_filters( 'appmaker_wc_payment_gateway_' . $gateway->id . '_enabled', true ) ) {
				$return['gateways'][] = apply_filters(
					'appmaker_wc_payment_gateway_' . $gateway->id,
					array(
						'id'                => $gateway->id,
						'title'             => html_entity_decode( wp_strip_all_tags( $gateway->get_title() ), ENT_QUOTES, 'UTF-8' ),
						'description'       => wp_strip_all_tags( $gateway->get_description() ),
						'icon'              => APPMAKER_WC::$api->get_settings( 'payment_gateway_icon', true ) ? $gateway->get_icon() : '',
						'chosen'            => $gateway->chosen,
						'order_button_text' => $gateway->order_button_text,
						'enabled'           => $gateway->enabled,
						'fields'            => apply_filters( 'appmaker_wc_payment_gateway_' . $gateway->id . '_fields', null ),
					),
					$gateway
				);
			}
		}
		if ( isset( $return['gateways'] ) && ( is_array($return['gateways']) || is_object( $return['gateways'] ) ) ){
		foreach ( $return['gateways'] as $gateways => $gateway ) {
			$html = $gateway['icon'];
			preg_match( '@src="([^"]+)"@', $html, $match );
			$src = array_pop( $match );
			// will return /images/image.jpg
			$return['gateways'][ $gateways ]['icon'] = $src;
		}
	}
		$page_id = wc_get_page_id( 'terms' );
		if ( ( $page_id > 0 ) && ( apply_filters( 'woocommerce_checkout_show_terms', true ) || get_option( 'yith_wctc_enable_popup' ) === 'yes' ) ) {
			$terms_is_checked = apply_filters( 'woocommerce_terms_is_checked_default', isset( $request['terms'] ) );

			if ( empty( $page_id ) ) {
				$terms_page_id = wc_get_page_id( 'terms' );
			} else {
				$terms_page_id = $page_id;
			}
			$return['terms'] = array(
				'show'    => 1,
				'link'    => esc_url( get_permalink( $terms_page_id ) ),
				'checked' => ( $terms_is_checked ) ? 1 : 0,
			);
		} else {
			$return['terms'] = array(
				'show'    => 0,
				'link'    => '',
				'checked' => 1,
			);
		}
		$return['skip_payment_gateways'] = APPMAKER_WC::$api->get_settings( 'show_payment_gateways_in_webview', 0 );
		if ( $return['skip_payment_gateways'] !== 0 ) {
			$order_id = WC()->checkout()->create_order( $request );
			$order    = wc_get_order( $order_id );

			$billing_array = array(
				'first_name' => $request['billing_first_name'],
				'last_name'  => $request['billing_last_name'],
				'company'    => $request['billing_company'],
				'address_1'  => $request['billing_address_1'],
				'address_2'  => $request['billing_address_2'],
				'city'       => $request['billing_city'],
				'state'      => $request['billing_state'],
				'postcode'   => $request['billing_postcode'],
				'country'    => $request['billing_country'],
				'email'      => $request['billing_email'],
				'phone'      => $request['billing_phone'],
			);
			if ( isset( $request['shipping_first_name'] ) ) {
				$shipping_array = array(
					'first_name' => $request['shipping_first_name'],
					'last_name'  => $request['shipping_last_name'],
					'company'    => $request['shipping_company'],
					'address_1'  => $request['shipping_address_1'],
					'address_2'  => $request['shipping_address_2'],
					'city'       => $request['shipping_city'],
					'state'      => $request['shipping_state'],
					'postcode'   => $request['shipping_postcode'],
					'country'    => $request['shipping_country'],
					'email'      => $request['shipping_email'],
					'phone'      => $request['shipping_phone'],
				);
			} else {
				$shipping_array = array(
					'first_name' => $request['billing_first_name'],
					'last_name'  => $request['billing_last_name'],
					'company'    => $request['billing_company'],
					'address_1'  => $request['billing_address_1'],
					'address_2'  => $request['billing_address_2'],
					'city'       => $request['billing_city'],
					'state'      => $request['billing_state'],
					'postcode'   => $request['billing_postcode'],
					'country'    => $request['billing_country'],
					'email'      => $request['billing_email'],
					'phone'      => $request['billing_phone'],
				);
			}
			$order->set_address( $billing_array, 'billing' );
			$order->set_address( $shipping_array, 'shipping' );
			$pay_now_url            = esc_url( $order->get_checkout_payment_url() );
			$pay_now_url            = preg_replace( '/#.*;/', '', $pay_now_url );
			$pay_now_url            = add_query_arg( array( 'from_app' => true ), $pay_now_url );
			$return['redirect_url'] = $pay_now_url;
			if ( is_a( $order, 'WC_Order' ) ) {
				if ( ! get_post_meta( APPMAKER_WC_Helper::get_id( $order ), 'from_app' ) ) {
					$order->add_order_note( __( 'Order from App', 'appmaker-woocommerce-mobile-app-manager' ) );
					add_post_meta( APPMAKER_WC_Helper::get_id( $order ), 'from_app', true );
				}
				if ( ! get_post_meta( APPMAKER_WC_Helper::get_id( $order ), 'appmaker_mobile_platform' ) && isset($request['platform'] ) ) {                            
					$note = sprintf( __( 'Order from #%s app', 'appmaker-woocommerce-mobile-app-manager' ), $request['platform'] );
					$order->add_order_note( $note );
					add_post_meta( APPMAKER_WC_Helper::get_id( $order ), 'appmaker_mobile_platform', $request['platform'] );
				}
				$key = method_exists( $order, 'get_order_key' ) ? $order->get_order_key() : $order->order_key;
				WC()->session->set( 'last_order_key', $key );
				WC()->session->set( 'last_order_id', $order_id );
			}
		}
		$validation_response = apply_filters('appmaker_wc_validate_checkout_review', $request);
		if(is_wp_error($validation_response)){
			return $validation_response;
		}
		return apply_filters( 'appmaker_wc_payment_gateways_response', $return );
	}

	/**
	 * @param bool|array $shipping_methods
	 */
	public function set_shipping_method( $shipping_methods = false ) {
		if ( empty( $shipping_methods ) ) {
			$shipping_methods = WC()->session->get( 'wc_chosen_shipping_methods', $shipping_methods );
		}

		$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
		if ( isset( $shipping_methods ) && is_array( $shipping_methods ) && ! empty( $shipping_methods ) ) {
			foreach ( $shipping_methods as $i => $value ) {
				$chosen_shipping_methods[ $i ] = wc_clean( $value );
			}

			WC()->session->set( 'wc_chosen_shipping_methods', $shipping_methods );
			WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );
		}
	}

	public function review( $request ) {
		do_action( 'appmaker_wc_before_review', $request );
		define( 'WOOCOMMERCE_CHECKOUT', true );
		$this->set_shipping_method( $request['shipping_methods'] );
		$_POST['post_data'] = http_build_query( $_POST ); // To make sure advanced shipping methods will show

		return apply_filters( 'appmaker_wc_order_review', APPMAKER_WC::$api->APPMAKER_WC_REST_Cart_Controller->cart_items(), 'order' );
	}

	public function prepare_state_response( $countries, $states_c ) {
		$return = array();
		foreach ( $countries as $key => $country ) {
			$return[ $key ] = array(
				'name'   => html_entity_decode( $country ),
				'states' => array(),
			);
			if ( isset( $states_c[ $key ] ) && is_array( $states_c[ $key ] ) ) {
				foreach ( $states_c[ $key ] as $state_key => $state ) {
					$return[ $key ]['states'][] = array(
						'id'   => $key,
						'name' => html_entity_decode( $state ),
					);
				}
			}
		}

		return $return;
	}

}
