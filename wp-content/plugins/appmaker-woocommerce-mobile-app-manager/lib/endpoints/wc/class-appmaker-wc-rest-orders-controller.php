<?php
/**
 * REST API Orders controller
 */

if ( ! defined( 'ABSPATH' ) ) {
	 exit;
}

	/**
 * REST API Orders controller class.
 *
 */
class APPMAKER_WC_REST_Orders_Controller extends APPMAKER_WC_REST_Posts_Abstract_Controller {

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
	protected $rest_base = 'orders';

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = 'shop_order';

	/**
	 * Initialize orders actions.
	 */
	public function __construct() {
		parent::__construct();
		add_filter( "woocommerce_rest_{$this->post_type}_query", array( $this, 'query_args' ), 10, 2 );
	}

	/**
	 * Register the routes for orders.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_items' ),
			'permission_callback' => array( $this, 'get_items_permissions_check' ),
			'args'                => $this->get_collection_params(),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
			array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( $this, 'get_item' ),
			'permission_callback' => array( $this, 'get_item_permissions_check' ),
			'args'                => array(
				'context' => $this->get_context_param( array( 'default' => 'view' ) ),
			),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)/notes', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'order_notes' ),
                'permission_callback' => array( $this, 'get_item_permissions_check' ),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
		) );
		
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/repeat_order/(?P<id>[\d]+)', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'repeat_order' ),
                'permission_callback' => array( $this, 'get_item_permissions_check' ),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
		) );
		
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/cancel_order/(?P<id>[\d]+)', array(
			array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'cancel_order' ),
			'permission_callback' => array( $this, 'get_item_permissions_check' ),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/items', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_recent_items' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'                => $this->get_collection_params(),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
		) );

	}

	/**
	 * Get the query params for collections.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		$params['status']  = array(
		'default'           => 'any',
		'description'       => __( 'Limit result set to orders assigned a specific status.', 'woocommerce' ),
		'type'              => 'string',
		'enum'              => array_merge( array( 'any' ), $this->get_order_statuses() ),
		'sanitize_callback' => 'sanitize_key',
		'validate_callback' => 'rest_validate_request_arg',
		);
		$params['product'] = array(
		'description'       => __( 'Limit result set to orders assigned a specific product.', 'woocommerce' ),
		'type'              => 'integer',
		'sanitize_callback' => 'absint',
		'validate_callback' => 'rest_validate_request_arg',
		);
		$params['dp']      = array(
		'default'           => 2,
		'description'       => __( 'Number of decimal points to use in each resource.', 'woocommerce' ),
		'type'              => 'integer',
		'sanitize_callback' => 'absint',
		'validate_callback' => 'rest_validate_request_arg',
		);

		return $params;
	}

	/**
	 * Get order statuses.
	 *
	 * @return array
	 */
	protected function get_order_statuses() {
		$order_statuses = array();

		foreach ( array_keys( wc_get_order_statuses() ) as $status ) {
			$order_statuses[] = str_replace( 'wc-', '', $status );
		}

		return $order_statuses;
	}

	/**
	 * Prepare a single order output for response.
	 *
	 * @param WP_Post $post Post object.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response $data
	 */
	public function prepare_item_for_response( $post, $request ) {
		global $wpdb;

		$order = wc_get_order( $post );
		$dp    = $request['dp'];

		if( isset($request['from_thankyou_page'] ) && $request['from_thankyou_page'] ) {
			WC()->cart->empty_cart();
		}

		//$disable_cancel_order = APPMAKER_WC::$api->get_settings( 'disable_order_cancel', false );
		$user_can_cancel  = current_user_can( 'cancel_order', $this->get_order_id( $order ));
		$statuses_for_cancel=apply_filters( 'woocommerce_valid_order_statuses_for_cancel', array(
            'pending',
            'failed',

        ) , $order) ;
		$statuses_for_cancel = apply_filters('appmaker_wc_valid_order_statuses_for_cancel', $statuses_for_cancel);
		$order_can_cancel = $order->has_status( $statuses_for_cancel);
		$order_can_repeat  = $order->has_status( apply_filters( 'woocommerce_valid_order_statuses_for_order_again', array( 'completed' ) ) );
		$enable_order_repeat = APPMAKER_WC::$api->get_settings( 'enable_repeat_order',true );

		$show_payment_in_order = APPMAKER_WC::$api->get_settings( 'show_payment_in_order',true );
		$order_needs_payment   = $order->needs_payment();
		$data = array(
		'id'            => $this->get_order_id( $order ),
		'status_label'  => $this->get_order_status_label( $order->get_status() ),
		'status'        => $order->get_status(),
		'order_key'     => $this->get_order_key( $order ),
		'number'        => $order->get_order_number(),
		'currency'      => method_exists( $order, 'get_currency' ) ? $order->get_currency() : $order->order_currency,
		'version'       => method_exists( $order, 'get_version' ) ? $order->get_version() : $order->order_version,
		'date_created'  => APPMAKER_WC_Helper::wc_rest_prepare_date_response( $post->post_date_gmt ),
		'date_modified' => APPMAKER_WC_Helper::wc_rest_prepare_date_response( $post->post_modified_gmt ),

		'discount_total' =>  $order->get_total_discount(),
		//'discount_'         => wc_format_decimal( $order->cart_discount_tax, $dp ),
		'shipping_total' => $order->get_total_shipping(),
		'shipping_tax'   =>  $order->get_shipping_tax(),
		'cart_tax'       =>  $order->get_cart_tax(),
		'subtotal'       =>  $order->get_subtotal(),
		'total'          =>  $order->get_total(),
		'total_tax'      =>  $order->get_total_tax(),

		'billing'              => array(),
		'shipping'             => array(),
		'payment_method_title' => method_exists( $order, 'get_payment_method_title' ) ? $order->get_payment_method_title() : $order->payment_method_title,
		'date_completed'       => APPMAKER_WC_Helper::wc_rest_prepare_date_response( method_exists( $order, 'get_date_completed' ) ? $order->get_date_completed() : $order->completed_date ),
		'line_items'           => array(),
		'tax_lines'            => array(),
		'shipping_lines'       => array(),
		'fee_lines'            => array(),
		'coupon_lines'         => array(),
		'refunds'              => array(),
		'can_cancel_order'     => $user_can_cancel && $order_can_cancel,
		'can_repeat_order'     => $order_can_repeat && $enable_order_repeat,
		'repeat_order_title'   => __( 'Order again', 'woocommerce' ),
		'should_make_payment'  => $show_payment_in_order && $order_needs_payment,
		'payment_url'          => $order->get_checkout_payment_url(),
		'show_tax'             => WC()->cart->display_cart_ex_tax,

		);
		$data['discount_total_display'] = APPMAKER_WC_Helper::get_display_price( $data['discount_total'] );
		// $data['discount_tax_display'] = APPMAKER_WC_Helper::get_display_price( $data['discount_tax'] );
		$data['shipping_total_display'] = APPMAKER_WC_Helper::get_display_price( $data['shipping_total'] + $data['shipping_tax'] );
		$data['shipping_tax_display']   = APPMAKER_WC_Helper::get_display_price( $data['shipping_tax'] );
		$data['cart_tax_display']       = APPMAKER_WC_Helper::get_display_price( $data['cart_tax'] );
		$data['total_display']          = APPMAKER_WC_Helper::get_display_price( $data['total'] );
		$data['total_tax_display']      = APPMAKER_WC_Helper::get_display_price( $data['total_tax'] );		
		$data['subtotal_display']       = WC()->cart->display_cart_ex_tax ? APPMAKER_WC_Helper::get_display_price( $data['subtotal'] ) :  APPMAKER_WC_Helper::get_display_price($data['subtotal']+$data['cart_tax']). ' ( '.__( 'including tax', 'appmaker-woocommerce-mobile-app-manager' )
		                                                                                                                                                        .' '. $data['cart_tax_display']  . ' )';
		
		//show order number instead of order id
		$show_order_number = APPMAKER_WC::$api->get_settings( 'show_order_number', false );
        $data['order_id_display'] =($show_order_number)?$data['number']:$data['id'];

        // Add addresses.
		$data['billing']  = $order->get_address( 'billing' );
		$show_shipping_fields                                     = APPMAKER_WC::$api->get_settings( 'show_shipping_address_fields', true );		

		if($show_shipping_fields){
			$data['shipping'] = $order->get_address( 'shipping' );
		}		

		$gateway = wc_get_payment_gateway_by_order( $order );
		if ( ! empty( $gateway ) ) {
			$data['instructions'] = property_exists( $gateway, 'instructions' ) ? strip_tags( html_entity_decode( $gateway->instructions ) ) : false;
			$data['show_instructions'] = false;
			$gateway_id = property_exists( $gateway, 'id' ) ? $gateway->id : 'cod';
			if( $data['instructions'] && 'cod' != $gateway_id ){
				$data['show_instructions'] = true;
			}
			$data['account_details'] = property_exists( $gateway, 'account_details' ) ? $gateway->account_details : false;
			if(!empty($data['account_details'])) {
                $data['account_details_title'] = array(
                    'account_details'=>__( 'Account details:', 'woocommerce' ),
                    'account_name'=> __('Account name', 'woocommerce'),
                    'account_number'=> __('Account number', 'woocommerce'),
                    'sort_code' => __('Sort code', 'woocommerce'),
                    'bank_name' => __('Bank', 'woocommerce'),
                    'iban' =>__('IBAN', 'woocommerce'),
                    'bic'=>__('BIC / Swift', 'woocommerce'),
                );
                }
		}


		// Add line items.
		foreach ( $order->get_items() as $item_id => $item ) {
			$product      = $order->get_product_from_item( $item );
			$product_id   = 0;
			$variation_id = 0;
				$product_sku  = null;

				// Check if the product exists.
			if ( is_object( $product ) ) {
				$product_id   = APPMAKER_WC_Helper::get_id( $product );
				$variation_id = APPMAKER_WC_Helper::get_id( $product );
				$product_sku  = $product->get_sku();
			}
			// add product image
			$featured_src  = wp_get_attachment_image_src( get_post_thumbnail_id( APPMAKER_WC_Helper::get_id( $product ) ) );
			$parent = method_exists( $product, 'get_parent_id' ) ? $product->get_parent_id() : ( ! empty( $product->parent ) ? $product->parent->id : false );
			if ( ! ( isset( $featured_src[0] ) && ! empty( $featured_src[0] ) ) && ! empty( $parent ) ) {
				$featured_src = wp_get_attachment_image_src( get_post_thumbnail_id( $parent ) );
			}
	
			if ( isset( $featured_src[0] ) && ! empty( $featured_src[0] ) ) {
				$featured_src = $this->ensure_absolute_link( $featured_src[0] );
			} else {
				$featured_src = $this->ensure_absolute_link( wc_placeholder_img_src() );
			}

				$line_item = array(
				'id'           => $item_id,
				'name'         => strip_tags( html_entity_decode($item['name'])),
				'featured_src' => $featured_src,
				'sku'          => $product_sku,
				'product_id'   => (int) $product_id,
				'variation_id' => (int) $variation_id,
				'quantity'     => wc_stock_amount( $item['qty'] ),
				'tax_class'    => ! empty( $item['tax_class'] ) ? $item['tax_class'] : '',
				'price'        => $order->get_item_total( $item, false, false ),
				'subtotal'     => $order->get_line_subtotal( $item, false, false ),
				'subtotal_tax' =>  $item['line_subtotal_tax'],
				'total'        => $order->get_line_total( $item, false, false ),
				'total_tax'    =>  $item['line_tax'],
				'taxes'        => array(),
				);

			if ( isset( $item_line_taxes ) ) {
					$item_line_taxes = maybe_unserialize( $item['line_tax_data'] );
				if ( isset( $item_line_taxes['total'] ) ) {
					$line_tax = array();

					foreach ( $item_line_taxes['total'] as $tax_rate_id => $tax ) {
						$line_tax[ $tax_rate_id ] = array(
							'id'       => $tax_rate_id,
							'total'    => $tax,
							'subtotal' => '',
						);
					}

					foreach ( $item_line_taxes['subtotal'] as $tax_rate_id => $tax ) {
						$line_tax[ $tax_rate_id ]['subtotal'] = $tax;
					}

					$line_item['taxes'] = array_values( $line_tax );
				}
			}

				$line_item['price_display']        = APPMAKER_WC_Helper::get_display_price( $line_item['price'] );
				$line_item['subtotal_display']     = APPMAKER_WC_Helper::get_display_price( $line_item['subtotal'] );
				$line_item['subtotal_tax_display'] = APPMAKER_WC_Helper::get_display_price( $line_item['subtotal_tax'] );
				$line_item['total_display']        = APPMAKER_WC_Helper::get_display_price( $line_item['total'] );
				$line_item['total_tax_display']    = APPMAKER_WC_Helper::get_display_price( $line_item['total_tax'] );

				$line_total_with_tax = $line_item['total'] + $line_item['total_tax'];
				$total_cart_price = WC()->cart->display_cart_ex_tax ? $line_item['total'] : $line_total_with_tax;
				$line_item['product_price_display']  = APPMAKER_WC_Helper::get_display_price( $total_cart_price );
				$line_item['total_display']        = WC()->cart->display_cart_ex_tax ? $line_item['total_display'] : $line_item['product_price_display'];
				$data['line_items'][] = $line_item;
		}

			// Add taxes.
		foreach ( $order->get_items( 'tax' ) as $key => $tax ) {
			$tax_line = array(
			'id'                 => $key,
			'rate_code'          => $tax['name'],
			'rate_id'            => $tax['rate_id'],
			'label'              => isset( $tax['label'] ) ? $tax['label'] : $tax['name'],
			'compound'           => (bool) $tax['compound'],
			'tax_total'          =>APPMAKER_WC_Helper::get_display_price( $tax['tax_amount'] + $tax['shipping_tax_amount']),
			'shipping_tax_total' =>APPMAKER_WC_Helper::get_display_price(wc_format_decimal( $tax['shipping_tax_amount'], $dp )),
			);

			$data['tax_lines'][] = $tax_line;
		}

			// Add shipping.
		foreach ( $order->get_shipping_methods() as $shipping_item_id => $shipping_item ) {
			$shipping_line = array(
			'id'           => $shipping_item_id,
			'method_title' => $shipping_item['name'],
			'method_id'    => $shipping_item['method_id'],
			'total'        => wc_format_decimal( $shipping_item['cost'], $dp ),
			'total_tax'    => wc_format_decimal( '', $dp ),
			'taxes'        => array(),
			);
			if ( isset( $shipping_item['taxes'] ) ) {
					$shipping_taxes = maybe_unserialize( $shipping_item['taxes'] );

				if ( ! empty( $shipping_taxes ) ) {
					$shipping_line['total_tax'] = wc_format_decimal( array_sum( $shipping_taxes ), $dp );

					foreach ( $shipping_taxes as $tax_rate_id => $tax ) {
						$shipping_line['taxes'][] = array(
							'id'    => $tax_rate_id,
							'total' => $tax,
						);
					}
				}
			}
			$data['shipping_lines'][] = $shipping_line;
		}

			// Add fees.
		foreach ( $order->get_fees() as $fee_item_id => $fee_item ) {
			$fee_line = array(
			'id'         => $fee_item_id,
			'name'       => $fee_item['name'],
			'tax_class'  => ! empty( $fee_item['tax_class'] ) ? $fee_item['tax_class'] : '',
			'tax_status' => 'taxable',
			'total'      => APPMAKER_WC_Helper::get_display_price(wc_format_decimal( $order->get_line_total( $fee_item ), $dp )),
			'total_tax'  => wc_format_decimal( $order->get_line_tax( $fee_item ), $dp ),
			'taxes'      => array(),
			);

			$fee_line_taxes = maybe_unserialize( $fee_item['line_tax_data'] );
			if ( isset( $fee_line_taxes['total'] ) ) {
					$fee_tax = array();

				foreach ( $fee_line_taxes['total'] as $tax_rate_id => $tax ) {
					$fee_tax[ $tax_rate_id ] = array(
						'id'       => $tax_rate_id,
						'total'    => $tax,
						'subtotal' => '',
					);
				}

				if ( isset( $fee_line_taxes['subtotal'] ) ) {
					foreach ( $fee_line_taxes['subtotal'] as $tax_rate_id => $tax ) {
						$fee_tax[ $tax_rate_id ]['subtotal'] = $tax;
					}
				}

					$fee_line['taxes'] = array_values( $fee_tax );
			}

			$data['fee_lines'][] = $fee_line;
		}

			// Add coupons.
		foreach ( $order->get_items( 'coupon' ) as $coupon_item_id => $coupon_item ) {
			$coupon_line = array(
			'id'           => $coupon_item_id,
			'code'         => $coupon_item['name'],
			'discount'     =>  $coupon_item['discount_amount'],
			'discount_tax' => $coupon_item['discount_amount_tax'],
			);

			$data['coupon_lines'][] = $coupon_line;
		}

			// Add refunds.
		foreach ( $order->get_refunds() as $refund ) {
			$data['refunds'][] = array(
			'id'     => $refund->id,
			'refund' => $refund->get_refund_reason() ? $refund->get_refund_reason() : '',
			'total'  => '-' . wc_format_decimal( $refund->get_refund_amount(), $dp ),
			);
		}

			$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
			$data    = $this->add_additional_fields_to_object( $data, $request );
			$data    = $this->filter_response_by_context( $data, $context );

			// Wrap the data in a response object.
			$response = rest_ensure_response( $data );

			$response->add_links( $this->prepare_links( $order, $request ) );

            if($response->data['status'] == 'failed'){
                $response->data['top_notice'][]=array(
                    'icon' => array(
                        'android' => 'error',
                        'ios'     => 'warning',
                    ),
					'message' =>'Payment details you entered is incorrect. Please check your card and try again.',
					'button'=> false, 
                );
            }
            if(APPMAKER_WC::$api->get_settings( 'enable_order_notes', false )){
                $response->data['top_notice'][] = array(
                    'icon' => array(
                        'android' => 'local-shipping',
                        'ios'     => 'ios-paper-plane-outline',
                    ),
                    'message' =>__('Order tracking information', 'appmaker-woocommerce-mobile-app-manager'),
                    'button'=> array(
                        'type'=> 'button',
                        'text'=>__('Track', 'appmaker-woocommerce-mobile-app-manager'),
                        'action'=>array(
                            'type'   => 'OPEN_ORDER_TRACKING',
                            'params' => array('title'=>__('Order Details', 'appmaker-woocommerce-mobile-app-manager'),
                                               'id' =>$response->data['id']
                            )
                        )
                    ),
                );
            }

			/**
		 * Filter the data for a response.
		 *
		 * The dynamic portion of the hook name, $this->post_type, refers to post_type of the post being
		 * prepared for the response.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param WP_Post $post Post object.
		 * @param WP_REST_Request $request Request object.
		 */
			return apply_filters( "woocommerce_rest_prepare_{$this->post_type}", $response, $post, $request );
	}

		/**
	 * Get order status.
	 *
	 * @return string
	 */
	protected function get_order_status_label( $order_status ) {
		$order_statuses = array();

		foreach ( wc_get_order_statuses() as $key => $status ) {
			$key                    = str_replace( 'wc-', '', $key );
			$order_statuses[ $key ] = $status;
		}
		if ( isset( $order_statuses[ $order_status ] ) ) {
			return $order_statuses[ $order_status ];
		} else {
			return '';
		}
	}

		/**
	 * Prepare links for the request.
	 *
	 * @param WC_Order $order Order object.
	 *
	 * @return array Links for the given order.
	 */
	protected function prepare_links( $order, $request ) {
		$links = array(
		'self'       => array(
			'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $this->get_order_id( $order ) ) ),
		),
		'collection' => array(
			'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ),
		),
		);

		if ( 0 !== (int) $order->get_user_id() ) {
			$links['customer'] = array(
			'href' => rest_url( sprintf( '/%s/customers/%d', $this->namespace, $order->get_user_id() ) ),
			);
		}

		return $links;
	}

		/**
	 * Query args.
	 *
	 * @param array $args
	 * @param WP_REST_Request $request
	 *
	 * @return array
	 */
	public function query_args( $args, $request ) {
		global $wpdb;

		if ( ! empty( $args['meta_query'] ) ) {
			$args['meta_query'] = array();
		}

		$args['meta_query'][] = array(
		'key'   => '_customer_user',
		'value' => get_current_user_id(),
		'type'  => 'NUMERIC',
		);

		// Search by product.
		if ( ! empty( $request['product'] ) ) {
			$order_ids = $wpdb->get_col( $wpdb->prepare( "
				SELECT order_id
				FROM {$wpdb->prefix}woocommerce_order_items
				WHERE order_item_id IN ( SELECT order_item_id FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key = '_product_id' AND meta_value = %d )
				AND order_item_type = 'line_item'
			 ", $request['product'] ) );

			// Force WP_Query return empty if don't found any order.
			$order_ids = ! empty( $order_ids ) ? $order_ids : array( 0 );

			$args['post__in'] = $order_ids;
		}

		// Search.
		if ( ! empty( $args['s'] ) ) {
			$order_ids = wc_order_search( $args['s'] );

			if ( ! empty( $order_ids ) ) {
				unset( $args['s'] );
				$args['post__in'] = array_merge( $order_ids, array( 0 ) );
			}
		}

		return $args;
	}

		/**
	 * Get the Order's schema, conforming to JSON Schema.
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
		'$schema'    => 'http://json-schema.org/draft-04/schema#',
		'title'      => $this->post_type,
		'type'       => 'object',
		'properties' => array(
			'id'                   => array(
				'description' => __( 'Unique identifier for the resource.', 'woocommerce' ),
				'type'        => 'integer',
				'context'     => array( 'view' ),
				'readonly'    => true,
			),
			'status_label'         => array(
				'description' => __( 'Order status label.', 'woocommerce' ),
				'type'        => 'string',
				'default'     => 'Pending',
				'enum'        => $this->get_order_statuses(),
				'context'     => array( 'view' ),
			),
			'status'               => array(
				'description' => __( 'Order status.', 'woocommerce' ),
				'type'        => 'string',
				'default'     => 'pending',
				'enum'        => $this->get_order_statuses(),
				'context'     => array( 'view' ),
			),
			'order_key'            => array(
				'description' => __( 'Order key.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => array( 'view' ),
				'readonly'    => true,
			),
			'number'               => array(
				'description' => __( 'Order number.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => array( 'view' ),
				'readonly'    => true,
			),
			'currency'             => array(
				'description' => __( 'Currency the order was created with, in ISO format.', 'woocommerce' ),
				'type'        => 'string',
				'default'     => get_woocommerce_currency(),
				'enum'        => array_keys( get_woocommerce_currencies() ),
				'context'     => array( 'view' ),
			),
			'version'              => array(
				'description' => __( 'Version of WooCommerce when the order was made.', 'woocommerce' ),
				'type'        => 'integer',
				'context'     => array( 'view' ),
				'readonly'    => true,
			),
			'date_created'         => array(
				'description' => __( "The date the order was created, in the site's timezone.", 'woocommerce' ),
				'type'        => 'date-time',
				'context'     => array( 'view' ),
				'readonly'    => true,
			),
			'date_modified'        => array(
				'description' => __( "The date the order was last modified, in the site's timezone.", 'woocommerce' ),
				'type'        => 'date-time',
				'context'     => array( 'view' ),
				'readonly'    => true,
			),
			'discount_total'       => array(
				'description' => __( 'Total discount amount for the order.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => array( 'view' ),
				'readonly'    => true,
			),
			//	'discount_tax'         => array(
			//		'description' => __( 'Total discount tax amount for the order.', 'woocommerce' ),
			//		'type'        => 'string',
			//		'context'     => array( 'view' ),
			//		'readonly'    => true,
			//	),
			'shipping_total'       => array(
				'description' => __( 'Total shipping amount for the order.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => array( 'view' ),
				'readonly'    => true,
			),
			'shipping_tax'         => array(
				'description' => __( 'Total shipping tax amount for the order.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => array( 'view' ),
				'readonly'    => true,
			),
			'cart_tax'             => array(
				'description' => __( 'Sum of line item taxes only.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => array( 'view' ),
				'readonly'    => true,
			),
			'subtotal'             => array(
				'description' => __( 'Grand total.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => array( 'view' ),
				'readonly'    => true,
			),
			'total'                => array(
				'description' => __( 'Subtotal.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => array( 'view' ),
				'readonly'    => true,
			),
			'total_tax'            => array(
				'description' => __( 'Sum of all taxes.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => array( 'view' ),
				'readonly'    => true,
			),
			'billing'              => array(
				'description' => __( 'Billing address.', 'woocommerce' ),
				'type'        => 'object',
				'context'     => array( 'view' ),
				'properties'  => array(
					'first_name' => array(
						'description' => __( 'First name.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'last_name'  => array(
						'description' => __( 'Last name.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'company'    => array(
						'description' => __( 'Company name.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'address_1'  => array(
						'description' => __( 'Address line 1.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'address_2'  => array(
						'description' => __( 'Address line 2.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'city'       => array(
						'description' => __( 'City name.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'state'      => array(
						'description' => __( 'ISO code or name of the state, province or district.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'postcode'   => array(
						'description' => __( 'Postal code.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'country'    => array(
						'description' => __( 'Country code in ISO 3166-1 alpha-2 format.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'email'      => array(
						'description' => __( 'Email address.', 'woocommerce' ),
						'type'        => 'string',
						'format'      => 'email',
						'context'     => array( 'view' ),
					),
					'phone'      => array(
						'description' => __( 'Phone number.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
				),
			),
			'shipping'             => array(
				'description' => __( 'Shipping address.', 'woocommerce' ),
				'type'        => 'object',
				'context'     => array( 'view' ),
				'properties'  => array(
					'first_name' => array(
						'description' => __( 'First name.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'last_name'  => array(
						'description' => __( 'Last name.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'company'    => array(
						'description' => __( 'Company name.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'address_1'  => array(
						'description' => __( 'Address line 1.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'address_2'  => array(
						'description' => __( 'Address line 2.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'city'       => array(
						'description' => __( 'City name.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'state'      => array(
						'description' => __( 'ISO code or name of the state, province or district.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'postcode'   => array(
						'description' => __( 'Postal code.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'country'    => array(
						'description' => __( 'Country code in ISO 3166-1 alpha-2 format.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
				),
			),
			'payment_method_title' => array(
				'description' => __( 'Payment method title.', 'woocommerce' ),
				'type'        => 'string',
				'context'     => array( 'view' ),
			),
			'date_completed'       => array(
				'description' => __( "The date the order was completed, in the site's timezone.", 'woocommerce' ),
				'type'        => 'date-time',
				'context'     => array( 'view' ),
				'readonly'    => true,
			),
			'line_items'           => array(
				'description' => __( 'Line items data.', 'woocommerce' ),
				'type'        => 'array',
				'context'     => array( 'view' ),
				'properties'  => array(
					'id'           => array(
						'description' => __( 'Item ID.', 'woocommerce' ),
						'type'        => 'integer',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
					'name'         => array(
						'description' => __( 'Product name.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
					'sku'          => array(
						'description' => __( 'Product SKU.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
					'product_id'   => array(
						'description' => __( 'Product ID.', 'woocommerce' ),
						'type'        => 'integer',
						'context'     => array( 'view' ),
					),
					'variation_id' => array(
						'description' => __( 'Variation ID, if applicable.', 'woocommerce' ),
						'type'        => 'integer',
						'context'     => array( 'view' ),
					),
					'quantity'     => array(
						'description' => __( 'Quantity ordered.', 'woocommerce' ),
						'type'        => 'integer',
						'context'     => array( 'view' ),
					),
					'tax_class'    => array(
						'description' => __( 'Tax class of product.', 'woocommerce' ),
						'type'        => 'integer',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
					'price'        => array(
						'description' => __( 'Product price.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
					'subtotal'     => array(
						'description' => __( 'Line subtotal (before discounts).', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'subtotal_tax' => array(
						'description' => __( 'Line subtotal tax (before discounts).', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'total'        => array(
						'description' => __( 'Line total (after discounts).', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'total_tax'    => array(
						'description' => __( 'Line total tax (after discounts).', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'taxes'        => array(
						'description' => __( 'Line taxes.', 'woocommerce' ),
						'type'        => 'array',
						'context'     => array( 'view' ),
						'readonly'    => true,
						'properties'  => array(
							'id'       => array(
								'description' => __( 'Tax rate ID.', 'woocommerce' ),
								'type'        => 'integer',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'total'    => array(
								'description' => __( 'Tax total.', 'woocommerce' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'subtotal' => array(
								'description' => __( 'Tax subtotal.', 'woocommerce' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
						),
					),
					'meta'         => array(
						'description' => __( 'Line item meta data.', 'woocommerce' ),
						'type'        => 'array',
						'context'     => array( 'view' ),
						'readonly'    => true,
						'properties'  => array(
							'key'   => array(
								'description' => __( 'Meta key.', 'woocommerce' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'label' => array(
								'description' => __( 'Meta label.', 'woocommerce' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'value' => array(
								'description' => __( 'Meta value.', 'woocommerce' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
						),
					),
				),
			),
			'tax_lines'            => array(
				'description' => __( 'Tax lines data.', 'woocommerce' ),
				'type'        => 'array',
				'context'     => array( 'view' ),
				'readonly'    => true,
				'properties'  => array(
					'id'                 => array(
						'description' => __( 'Item ID.', 'woocommerce' ),
						'type'        => 'integer',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
					'rate_code'          => array(
						'description' => __( 'Tax rate code.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
					'rate_id'            => array(
						'description' => __( 'Tax rate ID.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
					'label'              => array(
						'description' => __( 'Tax rate label.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
					'compound'           => array(
						'description' => __( 'Show if is a compound tax rate.', 'woocommerce' ),
						'type'        => 'boolean',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
					'tax_total'          => array(
						'description' => __( 'Tax total (not including shipping taxes).', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
					'shipping_tax_total' => array(
						'description' => __( 'Shipping tax total.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
				),
			),
			'shipping_lines'       => array(
				'description' => __( 'Shipping lines data.', 'woocommerce' ),
				'type'        => 'array',
				'context'     => array( 'view' ),
				'properties'  => array(
					'id'           => array(
						'description' => __( 'Item ID.', 'woocommerce' ),
						'type'        => 'integer',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
					'method_title' => array(
						'description' => __( 'Shipping method name.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'method_id'    => array(
						'description' => __( 'Shipping method ID.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'total'        => array(
						'description' => __( 'Line total (after discounts).', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'total_tax'    => array(
						'description' => __( 'Line total tax (after discounts).', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
					'taxes'        => array(
						'description' => __( 'Line taxes.', 'woocommerce' ),
						'type'        => 'array',
						'context'     => array( 'view' ),
						'readonly'    => true,
						'properties'  => array(
							'id'    => array(
								'description' => __( 'Tax rate ID.', 'woocommerce' ),
								'type'        => 'integer',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'total' => array(
								'description' => __( 'Tax total.', 'woocommerce' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
						),
					),
				),
			),
			'fee_lines'            => array(
				'description' => __( 'Fee lines data.', 'woocommerce' ),
				'type'        => 'array',
				'context'     => array( 'view' ),
				'properties'  => array(
					'id'         => array(
						'description' => __( 'Item ID.', 'woocommerce' ),
						'type'        => 'integer',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
					'name'       => array(
						'description' => __( 'Fee name.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'tax_class'  => array(
						'description' => __( 'Tax class of fee.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'tax_status' => array(
						'description' => __( 'Tax status of fee.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'total'      => array(
						'description' => __( 'Line total (after discounts).', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'total_tax'  => array(
						'description' => __( 'Line total tax (after discounts).', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'taxes'      => array(
						'description' => __( 'Line taxes.', 'woocommerce' ),
						'type'        => 'array',
						'context'     => array( 'view' ),
						'readonly'    => true,
						'properties'  => array(
							'id'       => array(
								'description' => __( 'Tax rate ID.', 'woocommerce' ),
								'type'        => 'integer',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'total'    => array(
								'description' => __( 'Tax total.', 'woocommerce' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'subtotal' => array(
								'description' => __( 'Tax subtotal.', 'woocommerce' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
						),
					),
				),
			),
			'coupon_lines'         => array(
				'description' => __( 'Coupons line data.', 'woocommerce' ),
				'type'        => 'array',
				'context'     => array( 'view' ),
				'properties'  => array(
					'id'           => array(
						'description' => __( 'Item ID.', 'woocommerce' ),
						'type'        => 'integer',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
					'code'         => array(
						'description' => __( 'Coupon code.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'discount'     => array(
						'description' => __( 'Discount total.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
					),
					'discount_tax' => array(
						'description' => __( 'Discount total tax.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
				),
			),
			'refunds'              => array(
				'description' => __( 'List of refunds.', 'woocommerce' ),
				'type'        => 'array',
				'context'     => array( 'view' ),
				'readonly'    => true,
				'properties'  => array(
					'id'     => array(
						'description' => __( 'Refund ID.', 'woocommerce' ),
						'type'        => 'integer',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
					'reason' => array(
						'description' => __( 'Refund reason.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
					'total'  => array(
						'description' => __( 'Refund total.', 'woocommerce' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
				),
			),
		),
		);

		return $this->add_additional_fields_schema( $schema );
	}

		/**
	 * Prepare a single order for create.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_Error|stdClass $data Object.
	 */
	protected function prepare_item_for_database( $request ) {
		$data = new stdClass;

		// Set default order args.
		$data->status        = $request['status'];
		$data->customer_id   = $request['customer_id'];
		$data->customer_note = $request['customer_note'];

		/**
		 * Filter the query_vars used in `get_items` for the constructed query.
		 *
		 * The dynamic portion of the hook name, $this->post_type, refers to post_type of the post being
		 * prepared for insertion.
		 *
		 * @param stdClass $data An object representing a single item prepared
		 *                                 for inserting the database.
		 * @param WP_REST_Request $request Request object.
		 */
		return apply_filters( "woocommerce_rest_pre_insert_{$this->post_type}", $data, $request );
	}

		/**
	 * Get a collection of posts.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		$args                        = array();
		$args['offset']              = $request['offset'];
		$args['order']               = $request['order'];
		$args['orderby']             = $request['orderby'];
		$args['paged']               = $request['page'];
		$args['post__in']            = $request['include'];
		$args['post__not_in']        = $request['exclude'];
		$args['posts_per_page']      = $request['per_page'];
		$args['name']                = $request['slug'];
		$args['post_parent__in']     = $request['parent'];
		$args['post_parent__not_in'] = $request['parent_exclude'];
		$args['s']                   = $request['search'];
		$args['post_parent']           = 0 ;

		$args['date_query'] = array();
		// Set before into date query. Date query must be specified as an array of an array.
		if ( isset( $request['before'] ) ) {
			$args['date_query'][0]['before'] = $request['before'];
		}

		// Set after into date query. Date query must be specified as an array of an array.
		if ( isset( $request['after'] ) ) {
			$args['date_query'][0]['after'] = $request['after'];
		}

		if ( is_array( $request['filter'] ) ) {
			$args = array_merge( $args, $request['filter'] );
			unset( $args['filter'] );
		}

		// Force the post_type argument, since it's not a user input variable.
		$args['post_type'] = $this->post_type;

		/**
		 * Filter the query arguments for a request.
		 *
		 * Enables adding extra arguments or setting defaults for a post
		 * collection request.
		 *
		 * @param array $args Key value array of query var to query value.
		 * @param WP_REST_Request $request The request used.
		 */
		$args                      = apply_filters( "woocommerce_rest_{$this->post_type}_query", $args, $request );
		$query_args                = $this->prepare_items_query( $args, $request );
		$query_args['post_status'] = 'any';
		$posts_query               = new WP_Query();

		$query_result = $posts_query->query( $query_args );
		$posts        = array();
		foreach ( $query_result as $post ) {
			if ( get_post_meta( $post->ID, '_customer_user', true ) != get_current_user_id() ) {
				continue;
			}

			$data    = $this->prepare_item_for_response( $post, $request );
			$posts[] = $this->prepare_response_for_collection( $data );
		}

		$page        = (int) $query_args['paged'];
		$total_posts = $posts_query->found_posts;

		if ( $total_posts < 1 ) {
			// Out-of-bounds, run the query again without LIMIT for total count.
			unset( $query_args['paged'] );
			$count_query = new WP_Query();
			$count_query->query( $query_args );
			$total_posts = $count_query->found_posts;
		}

		$max_pages = ceil( $total_posts / (int) $query_args['posts_per_page'] );

		$response = rest_ensure_response( $posts );
		$response->header( 'X-WP-Total', (int) $total_posts );
		$response->header( 'X-WP-TotalPages', (int) $max_pages );

		$request_params = $request->get_query_params();
		if ( ! empty( $request_params['filter'] ) ) {
			// Normalize the pagination params.
			unset( $request_params['filter']['posts_per_page'] );
			unset( $request_params['filter']['paged'] );
		}
		$base = add_query_arg( $request_params, rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ) );

		if ( $page > 1 ) {
			$prev_page = $page - 1;
			if ( $prev_page > $max_pages ) {
				$prev_page = $max_pages;
			}
			$prev_link = add_query_arg( 'page', $prev_page, $base );
			$response->link_header( 'prev', $prev_link );
		}
		if ( $max_pages > $page ) {
			$next_page = $page + 1;
			$next_link = add_query_arg( 'page', $next_page, $base );
			$response->link_header( 'next', $next_link );
		}

		return $response;
	}


		/**
	 * Cancel order request.
	 *
	 * @param WP_REST_Request $request Reguest Object.
	 *
	 * @return bool|WP_Error|WP_REST_Response
	 */
	public function cancel_order( $request ) {
		$order                = wc_get_order( $request['id'] );
		try {
            $cancel_order=apply_filters('appmaker_wc_should_cancel_order',true,$order);
            if($cancel_order === true ) {
                $_GET['order']        = $this->get_order_key( $order );
                $_GET['order_id']     = $this->get_order_id( $order );
                $_GET['redirect']     = false;
                $_GET['_wpnonce']     = wp_create_nonce( 'woocommerce-cancel_order' );
                $_GET['cancel_order'] = true;
                do_action('appmaker_wc_before_cancel_order',$order);
                 WC_Form_Handler::cancel_order();
              }

              $errors = $this->get_wc_notices_errors();
              if (is_wp_error($errors)) {
                  return $errors;
              }

			return $this->get_item( $request );
		} catch ( Exception $e ) {
			return new WP_Error( 'error', 'Unable to cancel order' );
		}
	}

		/**
	 * Repeat order request.
	 *
	 * @param WP_REST_Request $request Reguest Object.
	 *
	 * @return bool|WP_Error|WP_REST_Response
	 */
	public function repeat_order( $request ) {
		$order                = wc_get_order( $request['id'] );
		$enabled_reorder      = APPMAKER_WC::$api->get_settings( 'enable_repeat_order',true );		
		$user                 = get_current_user_id();
		$enabled_reorder      = apply_filters('appmaker_update_repeat_order_settings', $enabled_reorder );
		if ( $order &&  $order->has_status( apply_filters( 'woocommerce_valid_order_statuses_for_order_again', array( 'completed' ) ) ) && $user && $enabled_reorder  ) {				
			
			$_GET['order']        = $this->get_order_key( $order );
			$order_id             = $this->get_order_id( $order );
			$_GET['redirect']     = false;
			$_GET['_wpnonce']     = wp_create_nonce( 'woocommerce-order_again' );
			$_GET['order_again']  = $order_id;
			APPMAKER_WC::$api->APPMAKER_WC_REST_Cart_Controller->get_cart_items(); 
			$errors = $this->get_wc_notices_errors();
			if (is_wp_error($errors)) {
				return $errors;
			}						
			$return['action'] = array(
				'type'   => 'OPEN_CART',
				'params' => array(),
			);
			return $return;
				  
		}else if(! $enabled_reorder ){
			$return = array();
			return apply_filters('appmaker_repeat_order_woocommerce', $return , $request );
		}else {
			return new WP_Error( 'reorder_error', 'Unable to repeat the order' );
		}		
	}

		/**
	 * Check if a given request has access to read an item.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|boolean
	 */
	public function get_item_permissions_check( $request ) {
		$api_check = $this->api_permissions_check( $request );
		if ( true === $api_check ) {
			$order = wc_get_order( (int) $request['id'] );
			if ( $order && ( ( get_current_user_id() != 0 && $order->get_user_id() == get_current_user_id() ) || WC()->session->get( 'last_order_key' ) == $this->get_order_key( $order ) ) ) {
				return true;
			}

			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot view this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		} else {
			return $api_check;
		}
	}

		/**
	 * Check if a given request has access to read items.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		$api_check = $this->api_permissions_check( $request );
		if ( true === $api_check ) {
			if ( ! is_user_logged_in() ) {
				return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
			}

			return true;
		} else {
			return $api_check;
		}
	}

	public function get_order_id( $order ) {
		if ( class_exists( 'WC_Seq_Order_Number' ) ) {
			return $order->order_number;
		} elseif ( method_exists( $order, 'get_id' ) ) {
			return $order->get_id();
		} else {
			return $order->id;
		}
	}

		/**
	 * @param WC_Order $order
	 *
	 * @return mixed
	 */
	public function get_order_key( $order ) {
		if ( method_exists( $order, 'get_order_key' ) ) {
			return $order->get_order_key();
		} else {
			return $order->order_key;
		}
	}

    /**
     * get order notes
     */
    public function order_notes( $request ){

       if(!empty($request['id'])){

            $order_id = $request['id'];
            $notes = array();
            remove_filter('comments_clauses', array('WC_Comments', 'exclude_order_comments'));

            $comments = get_comments(array(
                'post_id' => $order_id,
                'approve' => 'approve',
                'type' => ''
            ));

            add_filter('comments_clauses', array('WC_Comments', 'exclude_order_comments'));

            if (!empty($comments)) {

                foreach ($comments as $key => $comment) {
                    $old_date = strtotime($comment->comment_date);
                    $new_date = date('l, F d Y h:i:s A', $old_date);
                    $notes[] = array(
                        'date' => $new_date,
                        'content' => strip_tags( html_entity_decode( $comment->comment_content ) ),
                    );

                }
            }
            return $notes;

        }else {
            return new WP_Error( 'error', 'Unable to view order notes' );
        }

    }

	/**
	 * Get recently ordered items	 
	*/

	public function get_recent_items( $request ) {
		$return = array('label' => __( 'Recent orders', 'woocommerce' ) , 'products' => array());
		$current_user = get_current_user_id();
		if( $current_user ) {
			$customer_orders = get_posts( array(
				'numberposts' 		=> -1,
				'meta_key'    		=> '_customer_user',
				'meta_value'  		=> $current_user,
				'post_type'   		=> wc_get_order_types(),
				'post_status' 		=> array_keys( wc_get_is_paid_statuses() ),
				'orderby'     		=> 'date',
				'posts_per_page'    => isset($request['per_page']) ? $request['per_page'] : 5,
			) );
			if ( $customer_orders ) {
				$product_ids = array();
				$data        = array();
				foreach ( $customer_orders as $customer_order ) {
					$order = wc_get_order( $customer_order->ID );
					$items = $order->get_items();
					foreach ( $items as $item ) {
						$product_id = $item->get_product_id();
						$product_ids[] = $product_id;
					}
				}
				$product_ids = array_unique( $product_ids );
				if(!empty($product_ids)){
					foreach ( $product_ids as $id ) {           
						$product = APPMAKER_WC_Helper::get_product( $id );
						if ( ! empty( $product ) ) {
							$data[] = APPMAKER_WC::$api->APPMAKER_WC_REST_Products_Controller->get_product_data( $product );
						}					
					}
				}
				$return['products'] = $data;
			}   
            
		}
		return $return;
	 }
}
