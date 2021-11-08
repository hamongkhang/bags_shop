<?php
/**
 * REST API Cart controller
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API Cart controller class.
 */
class APPMAKER_WC_REST_Cart_Controller extends APPMAKER_WC_REST_Controller {

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
	protected $rest_base = 'cart';


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
					'callback'            => array( $this, 'get_cart_items' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/meta',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_cart_meta' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/add',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'add_to_cart' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/set_quantity',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'set_quantity' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/coupon/add',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'add_coupon' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/coupon/remove',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'remove_coupon' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/count',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_cart_count' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/clear',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'force_clear_cart' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);


	}

	/**
	 * Add to cart route.
	 *
	 * @param array $request Request data array.
	 *
	 * @return array|bool|WP_Error
	 */
	public function add_to_cart( $request ) {
		add_filter( 'option_woocommerce_cart_redirect_after_add', '__return_false', 9999 );
		add_filter( 'woocommerce_add_to_cart_redirect', '__return_false', 9999 );
		do_action( 'appmaker_wc_before_add_to_cart', $request );
		$return               = apply_filters( 'appmaker_wc_add_to_cart_validate', array(), $request );
		$_POST['add-to-cart'] = $_REQUEST['add-to-cart'] = $request['product_id'];
		
		if ( isset( $request['quantity'] ) ) {
			$_POST['quantity'] = $_REQUEST['quantity'] = $request['quantity'];
		}

		if ( is_wp_error( $return ) ) {
			return $return;
		}

		if ( empty( $request['app-add-variations-to-cart'] ) ) {
			WC_Form_Handler::add_to_cart_action( false );
		}

		$return = $this->get_wc_notices_errors();

		if ( false !== $return ) {
			if ( ! is_wp_error( $return ) ) {
				$return = new WP_Error( 'error_add', __( 'Cannot add item to cart', 'appmaker-woocommerce-mobile-app-manager' ), array( 'status' => 405 ) );
			}
		} else {
			$return = $this->cart_items();
		}

		return apply_filters( 'appmaker_wc_add_to_cart_response', $return );
	}

	public function get_cart_items() {
		if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
			define( 'WOOCOMMERCE_CART', true );
		}

		if ( is_user_logged_in() ) {
			$cart = WC()->session->get( 'cart', null );
			if ( is_null( $cart ) || empty( $cart ) ) {
				WC()->cart->empty_cart( false );
				WC()->session->set( 'cart', null );
				WC()->cart->get_cart_from_session();
			}
		}
		$return = $this->cart_items();

		return apply_filters( 'appmaker_wc_cart_items_response', $return );
	}

	/**
	 * Return cart count
	 */
	public function get_cart_count(){
		
		if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
			define( 'WOOCOMMERCE_CART', true );
		}        
		// if ( is_user_logged_in() ) {
		// 	$cart = WC()->cart->get_cart_from_session();			
		// 	if ( is_null( $cart ) || empty( $cart ) ) {
		// 		WC()->cart->empty_cart( false );
		// 		WC()->session->set( 'cart', null );
		// 		WC()->cart->get_cart_from_session();
		// 		WC()->cart->calculate_totals();
		// 		WC()->cart->get_cart();			
		// 	}		
		// }

		$count = count( WC()->cart->get_cart() );
		return array('count'=>$count);		
		
	}

	/**
	 * cart clear
	 */
	public function force_clear_cart() {
		
		if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
			define( 'WOOCOMMERCE_CART', true );
		}        
		
		WC()->cart->empty_cart();	
		WC()->session->set( 'cart', null );
		return array('clear_cart'=> true );		
		
	}


	/**
	 * Return cart items.
	 *
	 * @return array
	 */
	public function cart_items() {
		WC()->cart->calculate_totals();
		$cart               = WC()->cart->get_cart();
		$return             = $this->get_cart_meta();
		$return['products'] = array();
		foreach ( $cart as $key => $item ) {
			$item['key']      = $key;
			$variation        = array();
			$variation_string = '';
			$product          = APPMAKER_WC_Helper::get_product( $item['product_id'] );
			
			if ( isset( $item['variation'] ) && is_array( $item['variation'] ) ) {
				foreach ( $item['variation'] as $id => $variation_value ) {
					// If this is a term slug, get the term's nice name.
					if ( taxonomy_exists( esc_attr( str_replace( 'attribute_', '', $id ) ) ) ) {
						$term = get_term_by( 'slug', $variation_value, esc_attr( str_replace( 'attribute_', '', $id ) ) );
						if ( ! is_wp_error( $term ) && ! empty( $term->name ) ) {
							$value = $term->name;
						} else {
							$value = ucwords( str_replace( '-', ' ', $variation_value ) );
						}
					} else {
						$value = ucwords( str_replace( '-', ' ', $variation_value ) );
					}
					$value             = $this->decode_html( trim( esc_html( apply_filters( 'woocommerce_variation_option_name', $value ) ) ), ENT_QUOTES, 'UTF-8' );
					$value             = html_entity_decode( $value, ENT_QUOTES, 'UTF-8' );
					$value			   = wc_sanitize_taxonomy_name($value);
					$name              = $this->decode_html( wc_attribute_label( str_replace( 'attribute_', '', $id ), $product ) );
					$name              = str_replace( 'pa_', '', $name );
					$variation[]       = array(
						'id'       => str_replace( 'attribute_', '', $id ),
						'name'     => $name,
						'value_id' => $variation_value,
						'value'    => $value,
					);
					$variation_string .= $name . ' : ' . $value . "\n";
				}
			}

			if ( ! $item['data']->has_enough_stock( $item['quantity'] ) ) {
				$item['quantity']         = $item['data']->get_stock_quantity();
				$cart[ $key ]['quantity'] = $item['data']->get_stock_quantity();
				WC()->cart->set_quantity( $item['key'], floatval( $cart[ $key ]['quantity'] ), true );
				WC()->cart->check_cart_items();
				WC()->cart->persistent_cart_update();
			}
			$item['variation']        = $variation;
			$item['variation_string'] = $variation_string;
			$item                     = array_merge( $item, $this->get_product_short_details( $item['data'] ) );
			unset( $item['data'] );

			//$item['on_sale'] = ( $product->get_price() < $product->get_regular_price() || $product->is_on_sale() );

			$line_total_with_tax = $item['line_subtotal'] + $item['line_subtotal_tax'];
			$total_cart_price    = WC()->cart->display_cart_ex_tax ? $item['line_subtotal'] : $line_total_with_tax;
			$total_regular_price = 0;
			if($item['product_regular_price']){
				$total_regular_price = $item['product_regular_price'] * $item['quantity'];				
			}		
			$item['regular_price_display'] = APPMAKER_WC_Helper::get_display_price( $total_regular_price );
			$item['line_total']            =  $line_total_with_tax ;
			$item['line_total_display']    = APPMAKER_WC_Helper::get_display_price( $line_total_with_tax );
			$item['product_price_display'] = APPMAKER_WC_Helper::get_display_price( $total_cart_price );			
			if ( $item['quantity'] > 0 ) {
				$return['products'][] = $item;
			}
		}
		$return['additional_fee_label'] = 'Additional fee';
		$return['additional_fee']       = 0;
		$return['need_shipping']        = APPMAKER_WC::$api->get_settings( 'show_shipping_address_fields', true );
		                               
		
		wc_clear_notices();
		if ( ! headers_sent() ) {
			WC()->session->set_customer_session_cookie(true); // NOTE: Added to prevent products not adding to cart for guest users in iOS app (Products in WebView)
		}
		WC()->cart->maybe_set_cart_cookies();

		return apply_filters( 'appmaker_wc_cart_items', $return );
	}

	/**
	 * Rteurn cart meta.
	 *
	 * @return mixed|void
	 */
	public function get_cart_meta() {
		WC()->cart->calculate_totals();
		$cart                    = WC()->cart->get_cart();
		$coupon_discount_amounts = array();
		$tax_display = get_option( 'woocommerce_tax_display_cart' );
		foreach ( WC()->cart->coupon_discount_amounts as $coupon => $price ) {
			$the_coupon = new WC_Coupon( $coupon );
			if ( method_exists( $the_coupon, 'get_free_shipping' ) ) {
				$free_shipping = $the_coupon->get_free_shipping();
			} else {
				$free_shipping = false;
			}
			$coupon_tax                = ( isset( WC()->cart->coupon_discount_tax_amounts[ $coupon ] ) && 'incl' == $tax_display )  ? WC()->cart->coupon_discount_tax_amounts[ $coupon ] : 0;
			$coupon_price_with_tax     = $price + $coupon_tax;
			$coupon_discount_amounts[] = array(
				'coupon'           => (string) $coupon,
				'discount'         => $coupon_price_with_tax,
				'discount_display' => APPMAKER_WC_Helper::get_display_price( $coupon_price_with_tax ),
				'free_shipping'    => $free_shipping,
			);

		}		
		if ( 'incl' == $tax_display ){
			$shipping_fee = ! empty( WC()->cart->shipping_total ) ? WC()->cart->shipping_total + WC()->cart->shipping_tax_total : 0;
		} else {
			$shipping_fee = ! empty( WC()->cart->shipping_total ) ? WC()->cart->shipping_total : 0;
		}
		$return = array(
			'coupons_applied'          => WC()->cart->get_applied_coupons(),
			'coupon_discounted'        => $coupon_discount_amounts,
			'show_apply_coupon'        => apply_filters( 'woocommerce_coupons_enabled', 'yes' === get_option( 'woocommerce_enable_coupons' ) ),
			// 'enable_guest_checkout' => ( ( get_option( 'woocommerce_enable_guest_checkout' ) === 'yes' ) || ( get_option( 'woocommerce_enable_signup_and_login_from_checkout' ) === 'no' ) ),
			'enable_guest_checkout'    => ( get_option( 'woocommerce_enable_guest_checkout' ) === 'yes' ),
			'count'                    => WC()->cart->get_cart_contents_count(),
			'shipping_fee'             => $shipping_fee ,
			'shipping_fee_display'     => APPMAKER_WC_Helper::get_display_price( $shipping_fee ),
			'tax'                      => WC()->cart->tax_total + WC()->cart->shipping_tax_total,
			'tax_display'              => APPMAKER_WC_Helper::get_display_price( WC()->cart->tax_total + WC()->cart->shipping_tax_total ),
			'fees'                     => array(),
			'currency'                 => get_woocommerce_currency(),
			'currency_symbol'          => html_entity_decode(get_woocommerce_currency_symbol(),ENT_QUOTES, 'UTF-8'),

			'total'                    => WC()->cart->subtotal,
			'cart_total'               => WC()->cart->cart_contents_total,
			'order_total'              => WC()->cart->total,

			'total_display'            =>  ( wc_tax_enabled() && 'incl' == $tax_display ) ? APPMAKER_WC_Helper::get_display_price( WC()->cart->subtotal ) : APPMAKER_WC_Helper::get_display_price( WC()->cart->cart_contents_total ),
			'cart_total_display'       => APPMAKER_WC_Helper::get_display_price( WC()->cart->cart_contents_total ),
			'order_total_display'      => APPMAKER_WC_Helper::get_display_price( WC()->cart->total ),
			// 'product_subtotal_display' => APPMAKER_WC_Helper::get_display_price_from_html( WC()->cart->get_cart_subtotal(false) ),
			'product_subtotal_display' => WC()->cart->display_cart_ex_tax ? APPMAKER_WC_Helper::get_display_price_from_html( WC()->cart->get_cart_subtotal( false ) ) : APPMAKER_WC_Helper::get_display_price_from_html( WC()->cart->get_cart_subtotal( false ) ) . ' ( ' . __( 'including tax', 'appmaker-woocommerce-mobile-app-manager' )
																																										   . ' ' . APPMAKER_WC_Helper::get_display_price( WC()->cart->tax_total + WC()->cart->shipping_tax_total ) . ' )',

			'header_message'           => false,
			'price_format'             => get_woocommerce_price_format(),
			'timezone'                 => wc_timezone_string(),
			'tax_included'             => ( 'yes' === get_option( 'woocommerce_prices_include_tax' ) ),
			'weight_unit'              => get_option( 'woocommerce_weight_unit' ),
			'dimension_unit'           => get_option( 'woocommerce_dimension_unit' ),
			'can_proceed'              => ! WC()->cart->is_empty(),
			'error_message'            => __( 'Cart is empty', 'appmaker-woocommerce-mobile-app-manager' ),
			'show_tax'                 => WC()->cart->display_cart_ex_tax,
		);

		// applied coupon discount in total_display
		if ( ! empty( $return['coupon_discounted'] ) && is_array( $return['coupon_discounted'] ) && ( 'incl' == $tax_display ) ) {
			foreach ( $return['coupon_discounted'] as $coupons ) {
				$coupon_discounted_total = $return['total'] - $coupons['discount'];
				$return['total_display'] = APPMAKER_WC_Helper::get_display_price( $coupon_discounted_total );
			}
		}
		$additional_fees = WC()->cart->get_fees();
		if ( ! empty( $additional_fees ) && is_array( $additional_fees ) ) {
			foreach ( $additional_fees as $fees => $fee ) {	
				$additional_fees[$fees]->total = APPMAKER_WC_Helper::get_display_price( $fee->total);
				$return['fees'][] = $additional_fees[$fees] ;
			}			
		}

		$return['product_count']          = 0;
		$return['total_quantity_in_cart'] = 0;
		foreach ( $cart as $key => $item ) {
			if ( ! $item['data']->has_enough_stock( $item['quantity'] ) ) {
				$cart[ $key ]['quantity'] = $item['data']->get_stock_quantity();
			}
			$return['product_count']          += 1;
			$return['total_quantity_in_cart'] += $cart[ $key ]['quantity'];
		}
		$cart_check = do_action( 'woocommerce_check_cart_items' );
		if ( ! $cart_check ) {
			$errors = $this->get_wc_notices_errors();
			if ( is_wp_error( $errors ) ) {
				$return['can_proceed'] = false;
				if(! empty ( $errors ) ) {
					foreach( $errors->errors as $err => $error_message) {
						$return['error_message'] = $error_message[0];
					}
				}
			}
		} 
		return apply_filters( 'appmaker_wc_cart_meta_response', $return );
	}

	/**
	 * Return product short detail.
	 *
	 * @param WC_Product $product Product Object.
	 *
	 * @return mixed
	 */
	public function get_product_short_details( $product ) {
		if ( ! is_a( $product, 'WC_Product' ) ) {
			$product = new WC_Product( $product );
		}		
		
		$parent = method_exists( $product, 'get_parent_id' ) ? $product->get_parent_id() : ( ! empty( $product->parent ) ? $product->parent->id : false );
		
		$product_id = (0 != $parent) ? $parent : APPMAKER_WC_Helper::get_id( $product );
		
		$details['product_id'] = $product_id;
		// $details['product_total_stock'] = (int) $product->total_stock;
		$details['product_title'] = strip_tags( html_entity_decode( $product->get_title() ) );
		$details['featured_src']  = wp_get_attachment_image_src( get_post_thumbnail_id( APPMAKER_WC_Helper::get_id( $product ) ) );
		
		if ( ! ( isset( $details['featured_src'][0] ) && ! empty( $details['featured_src'][0] ) ) && ! empty( $parent ) ) {
			$details['featured_src'] = wp_get_attachment_image_src( get_post_thumbnail_id( $parent ) );
		}

		if ( isset( $details['featured_src'][0] ) && ! empty( $details['featured_src'][0] ) ) {
			$details['featured_src'] = $this->ensure_absolute_link( $details['featured_src'][0] );
		} else {
			$details['featured_src'] = $this->ensure_absolute_link( wc_placeholder_img_src() );
		}
		$details['product_type'] = $product->get_type();

		$details['product_price']         = ( ( get_option( 'woocommerce_prices_include_tax', 'no' ) == 'no' ) && ( get_option( 'woocommerce_tax_display_shop', 'inc' ) == 'incl' ) ) ? $product->get_price_including_tax() : $product->get_price();
		$details['product_regular_price'] = ( ( get_option( 'woocommerce_prices_include_tax', 'no' ) == 'no' ) && ( get_option( 'woocommerce_tax_display_shop', 'inc' ) == 'incl' ) ) ? wc_get_price_including_tax( $product, array( 'price' => $product->get_regular_price() ) ) : $product->get_regular_price();
		$details['product_sale_price']    = $product->get_sale_price();

		// $details['product_price_display']         =    APPMAKER_WC_Helper::get_display_price( $product->get_price() );
		$details['product_regular_price_display'] = APPMAKER_WC_Helper::get_display_price( ( ( get_option( 'woocommerce_prices_include_tax', 'no' ) == 'no' ) && ( get_option( 'woocommerce_tax_display_shop', 'inc' ) == 'incl' ) ) ? wc_get_price_including_tax( $product, array( 'price' => $product->get_regular_price() ) ) : $product->get_regular_price() );
		$details['product_sale_price_display']    = APPMAKER_WC_Helper::get_display_price( $product->get_sale_price() );

		$sale_percentage                     = APPMAKER_WC::$api->APPMAKER_WC_REST_Products_Controller->get_sale_percentage( $product );
		$details['on_sale']                  = ( $details['product_price']  < $details['product_regular_price'] || $product->is_on_sale() );
		$details['sale_percentage']          = ($sale_percentage != 0)? $sale_percentage.'%': false;
		
		//product in webview
		$product_in_webview_array           =  APPMAKER_WC::$api->APPMAKER_WC_REST_Products_Controller->get_product_in_webview($product); 
		$details['product_in_webview']      = false;
        if( is_array($product_in_webview_array) && $product_in_webview_array['product_in_webview'] ) {
            $details['product_in_webview'] = $product_in_webview_array['product_in_webview'];
            $details['product_in_webview_action'] = $product_in_webview_array['product_in_webview_action'];
        }    
		$details['qty_config'] = $this->get_qty_args( $product, array( 'min_value' => 1 ) );

		return $details;
	}

	/**
	 * Return qty config.
	 *
	 * @param WC_Product $product Product object.
	 * @param array      $args Arguments.
	 *
	 * @return array|mixed|void
	 */
	public function get_qty_args( $product, $args = array() ) {
		$defaults = array(
			'type'        => 'normal',
			'label'       => __( 'Quantity', 'woocommerce' ),
			'display'     => ! $product->is_sold_individually() && ( APPMAKER_WC::$api->get_settings( 'hide_quantity_block', 0 ) == 0 ),
			'input_value' => '1',
			'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product ),
			'min_value'   => apply_filters( 'woocommerce_quantity_input_min', '1', $product ),
			'step'        => apply_filters( 'woocommerce_quantity_input_step', '1', $product ),
		);
		if ( function_exists('get_max_purchase_quantity') ) {
			$defaults['max_value']   = apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product );
		}

		$args = apply_filters( 'woocommerce_quantity_input_args', wp_parse_args( $args, $defaults ), $product );

		// Set min and max value to empty string if not set.
		$args['min_value'] = isset( $args['min_value'] ) ? $args['min_value'] : '';
		$args['max_value'] = isset( $args['max_value'] ) ? $args['max_value'] : '';

		// Apply sanity to min/max args - min cannot be lower than 0.
		if ( '' !== $args['min_value'] && is_numeric( $args['min_value'] ) && $args['min_value'] < 0 ) {
			$args['min_value'] = 0; // Cannot be lower than 0.
		}

		// Max cannot be lower than 0 or min.
		if ( is_numeric( $args['max_value'] ) ) {
			$args['max_value'] = 0 < $args['max_value'] ? $args['max_value'] : '';
			if('' !== $args['max_value'] && $args['max_value'] < $args['min_value'] ) {
			   $args['max_value'] = $args['min_value'];
			}
		}
		if( empty( $args['max_value'] ) ){
			$args['max_value'] = '1000';
		}

		if ( floatval( $args['input_value'] ) < floatval( $args['min_value'] ) ) {
			$args['input_value'] = $args['min_value'];
		}

		return $args;
	}


	/**
	 * Set quantity route.
	 *
	 * @param array $request Request data array.
	 *
	 * @return array|bool|WP_Error
	 */
	public function set_quantity( $request ) {
		if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
			define( 'WOOCOMMERCE_CART', true );
		}


		if( $request['product_id'] ) {

			$cart = WC()->cart->get_cart();
            if ( ! empty($cart) ) {
               foreach ( $cart as $cart_item_key => $cart_item ){
                  $product_id = $cart_item['product_id'];
                  if( $request['product_id'] == $product_id ){
                       $request['key'] = $cart_item['key'];
                    }
                               
                }
            }
            if( empty($request['key']) ){
                $request['key'] = WC()->cart->generate_cart_id( $request['product_id'] );
            }
		}	
		WC()->cart->calculate_totals();

		$product = WC()->cart->get_cart_item( $request['key'] );
		if( ! isset( $product['data'] ) ) {
			return new WP_Error( 'product_not_found',  __( 'Product not found','appmaker-woocommerce-mobile-app-manager') ) ; 
		}
		$args = $this->get_qty_args( $product['data'], array( 'min_value' => 1 ) );		
		if ( $request['quantity'] > 0 && ( isset( $product['data'] ) && ! $product['data']->has_enough_stock( $request['quantity'] ) ) ) {
			return new WP_Error( 'cannot_add', sprintf( __( 'You cannot add that amount to the cart &mdash; we have %1$s in stock and you already have %1$s in your cart.', 'woocommerce' ), $product['data']->get_stock_quantity(), $product['quantity'] ), array( 'status' => 405 ) );
		} elseif ( !empty( $args ) && $request['quantity'] > $args['max_value'] ) {
			return new WP_Error( 'max_purchase_reached',  __( 'Maximum quantity for this product is reached. please check your cart','appmaker-woocommerce-mobile-app-manager') , array( 'status' => 405 ) ) ;
		} elseif ( isset( $product['data'] ) ) {
			if ( ! isset( $request['refresh_totals'] ) ) {
				$request['refresh_totals'] = true;
			}
			if ( 0 == $request['quantity'] && method_exists( WC()->cart, 'remove_cart_item' ) ) {
				WC()->cart->remove_cart_item( $request['key'] );
			} else {
				WC()->cart->set_quantity( $request['key'], floatval( $request['quantity'] ), $request['refresh_totals'] );
			}
		}
		WC()->cart->check_cart_items();
		WC()->cart->persistent_cart_update();

		return apply_filters('appmaker_wc_set_quanitity', $this->cart_items() );
	}

	/**
	 * Add coupon route.
	 *
	 * @param array $request Request data array.
	 *
	 * @return array|bool|WP_Error
	 */
	public function add_coupon( $request ) {
		if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
			define( 'WOOCOMMERCE_CART', true );
		}
		$added = WC()->cart->add_discount( $request['coupon'] );
		if ( ! $added ) {
			$return = $this->get_wc_notices_errors();

			if ( ! is_wp_error( $return ) ) {
				$return = new WP_Error( 'invalid_coupon', 'Invalid coupon', array( 'status' => 405 ) );
			}
		} else {
			WC()->cart->persistent_cart_update();
			$return = $this->cart_items();

		}

		return $return;
	}

	/**
	 * Remove coupon route.
	 *
	 * @param array $request Request data array.
	 *
	 * @return array|bool|WP_Error
	 */
	public function remove_coupon( $request ) {
		if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
			define( 'WOOCOMMERCE_CART', true );
		}
		$added = WC()->cart->remove_coupon( $request['coupon'] );
		if ( ! $added ) {
			$return = $this->get_wc_notices_errors();
			if ( ! is_wp_error( $return ) ) {
				$return = new WP_Error( 'invalid_coupon', 'Invalid coupon', array( 'status' => 405 ) );
			}
		} else {
			WC()->cart->persistent_cart_update();
			$return = $this->cart_items();
		}

		return $return;
	}



}
