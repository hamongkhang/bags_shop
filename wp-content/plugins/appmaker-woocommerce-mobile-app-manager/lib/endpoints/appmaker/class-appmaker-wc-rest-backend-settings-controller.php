<?php

/**
 * Access terms associated with a taxonomy
 */
class APPMAKER_WC_REST_Settings_Controller extends APPMAKER_WC_REST_BACKEND_Terms_Controller {


	public function __construct() {
		parent::__construct();
		$this->rest_base = 'backend/settings';
		add_filter( 'appmaker_wc_product_tabs', array( $this, 'new_product_tab' ), 2, 1 );

	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_settings' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),

				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'save_settings' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => array(
						'context' => $this->get_context_param( array( 'default' => 'view' ) ),
					),
				),
			)
		);
	}
	/**
	 * Adding new tab to Appmaker_WC_product_tabs
	 */

	public function new_product_tab( $tabs ) {
		/* Adds the new tab */
		if ( ! isset( $tabs['short_description'] ) ) {
			$tabs['short_description'] = array(
				'title'    => __( 'short Description', 'woocommerce' ),
				'priority' => 5,
				'callback' => 'woocommerce_product_description_tab',
			);
		}
		if ( ! isset( $tabs['related_products'] ) ) {
			$tabs['related_products'] = array(
				'title'    => __( 'Related products', 'woocommerce' ),
				'priority' => 35,
				'callback' => 'woocommerce_product_description_tab',
			);
		}
		if ( ! isset( $tabs['description'] ) ) {
			$tabs['description'] = array(
				'title'    => __( 'Description', 'woocommerce' ),
				'priority' => 2,
				'callback' => 'woocommerce_product_description_tab',
			);
		}


		return $tabs;  /* Return all  tabs including the new New Custom Product Tab  to display */
	}


	public function get_settings() {
		$options               = get_option( 'appmaker_wc_custom_settings', array() );
		$project_settings      = get_option( 'appmaker_wc_settings' );
		$project_id            = $project_settings['project_id'];
		$is_greater            = ( $project_id > 125427 ) ? 0 : 1 ;
		$is_greater_for_multi            = ( $project_id > 130400 ) ? 1 : 0;
		$return = array(
			'general'             => array(
				'id'     => 'general',
				'title'  => __( 'Social Login', 'appmaker-woocommerce-mobile-app-manager' ),
				'fields' => array(
					self::get_field(
						array(
							'id'    => 'firebase_project',
							'label' => 'Firebase Project ID',
						)
					),
					self::get_field(
						array(
							'id'    => 'facebook_id',
							'label' => 'Facebook App ID (Optional)',
						)
					),
					self::get_field(
						array(
							'id'    => 'facebook_secret',
							'label' => 'Facebook App Secret (Optional)',
						)
					),
				),
			),
			'language'            => array(
				'id'     => 'language',
				'title'  => __( 'Language', 'appmaker-woocommerce-mobile-app-manager' ),
				'fields' => array(
					self::get_field(
						array(
							'id'    => 'default_language',
							'label' => 'Default language code',
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'locale_code',
							'label'       => 'Use locale language code',
							'default'     => 0,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Yes',
									),
									array(
										'id'    => 0,
										'label' => 'No',
									),
								),
							),
						)
					),
				),
			),
			'cache'               => array(
				'id'     => 'cache',
				'title'  => __( 'Caching', 'appmaker-woocommerce-mobile-app-manager' ),
				'fields' => array(
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'cache_enabled',
							'label'       => 'Server Caching',
							'default'     => 0,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Enabled',
									),
									array(
										'id'    => 0,
										'label' => 'Disabled',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'id'      => 'cache_time',
							'label'   => 'Server Caching time (in Minutes)',
							'default' => 60,
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'client_cache',
							'label'       => 'Client caching',
							'default'     => 0,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Enabled',
									),
									array(
										'id'    => 0,
										'label' => 'Disabled',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'id'      => 'client_cache_time',
							'label'   => 'Client caching time (in Seconds)',
							'default' => 10800,
						)
					),
				),
			),
			'product_list'        => array(
				'id'     => 'product_list',
				'title'  => __( 'Product List', 'appmaker-woocommerce-mobile-app-manager' ),
				'fields' => array(
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'product_filter_attributes',
							'label'       => 'Filter Attributes',
							'default'     => wc_get_attribute_taxonomy_names(),
							'multi'       => true,
							'data_source' => array(
								'data' => $this->get_product_filter_settings(),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'hide_price_from_filter',
							'label'       => 'Hide Price Filter',
							'default'     => 0,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'hide',
									),
									array(
										'id'    => 0,
										'label' => 'show',
									),
								),
							),
						)
					),					
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'change_thumbnail_image_size',
							'label'       => 'Thumbnail image size',
							'default'     => 0,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'full',
									),
									array(
										'id'    => 0,
										'label' => 'medium',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'out_of_stock',
							'label'       => 'Show out of stock products at the bottom',
							'default'     => 0,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Yes',
									),
									array(
										'id'    => 0,
										'label' => 'No',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'disable_flash_sale_badge',
							'label'       => 'Disable flash sale badge',
							'default'     => 0,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Yes',
									),
									array(
										'id'    => 0,
										'label' => 'No',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'product_display_attributes',
							'label'       => 'Attributes to be displayed on the product page',
							'default'     => array(),
							'multi'       => true,
							'data_source' => array(
								'data' => $this->get_product_filter_settings(),
							),
						)
					),
				),
			),
			'checkout'            => array(
				'id'     => 'checkout',
				'title'  => __( 'Checkout Settings', 'appmaker-woocommerce-mobile-app-manager' ),
				'fields' => array(
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'show_shipping_address_fields',
							'label'       => 'Show shipping address fields',
							'default'     => 1,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Enabled',
									),
									array(
										'id'    => 0,
										'label' => 'Disabled',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'payment_gateways_enabled',
							'label'       => 'Payment Gateways',
							'default'     => array_values( WC()->payment_gateways()->get_payment_gateway_ids() ),
							'multi'       => true,
							'data_source' => array(
								'data' => $this->get_payment_gateways_settings(),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'payment_gateway_icon',
							'label'       => 'Show payment gateways icons',
							'default'     => 1,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Yes',
									),
									array(
										'id'    => 0,
										'label' => 'No',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'show_payment_gateways_in_webview',
							'label'       => 'Show payment gateways in webview',
							'default'     => 0,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Yes',
									),
									array(
										'id'    => 0,
										'label' => 'No',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'set_auth_cookie_redirect',
							'label'       => 'Set authentication cookie on redirect',
							'default'     => 1,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Enabled',
									),
									array(
										'id'    => 0,
										'label' => 'Disabled',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'force_disable_shipping',
							'label'       => 'Hide shipping option forcefully',
							'default'     => 0,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Yes',
									),
									array(
										'id'    => 0,
										'label' => 'No',
									),
								),
							),
						)
					),

				),
			),
			'product_detail_tabs' => array(
				'id'     => 'product_detail_tabs',
				'title'  => __( 'Product Detail Tabs', 'appmaker-woocommerce-mobile-app-manager' ),
				'fields' => $this->get_tab_settings(),
			),
			'orders'              => array(
				'id'     => 'orders',
				'title'  => __( 'Orders', 'appmaker-woocommerce-mobile-app-manager' ),
				'fields' => array(
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'enable_order_push',
							'label'       => 'Send push notification for order status change',
							'default'     => 1,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Enabled',
									),
									array(
										'id'    => 0,
										'label' => 'Disabled',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'id'    => 'fcm_server_key',
							'label' => 'FCM server key',
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'enable_order_notes',
							'label'       => 'Show order notes ',
							'default'     => 0,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Yes',
									),
									array(
										'id'    => 0,
										'label' => 'No',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'show_order_number',
							'label'       => 'Show order number instead of order id',
							'default'     => 0,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Yes',
									),
									array(
										'id'    => 0,
										'label' => 'No',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'enable_repeat_order',
							'label'       => 'Enable repeat order',
							'default'     => 1,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Yes',
									),
									array(
										'id'    => 0,
										'label' => 'No',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'order_in_web',
							'label'       => 'Orders in webview',
							'default'     => 0,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Enabled',
									),
									array(
										'id'    => 0,
										'label' => 'Disabled',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'show_payment_in_order',
							'label'       => 'Show payment in order',
							'default'     => 1,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Yes',
									),
									array(
										'id'    => 0,
										'label' => 'No',
									),
								),
							),
						)
					),
				),
			),
			'advanced'            => array(
				'id'     => 'advanced',
				'title'  => __( 'Advanced settings', 'appmaker-woocommerce-mobile-app-manager' ),
				'fields' => array(
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'sanitize_attribute_ids',
							'label'       => 'Sanitize attribute ids (Leave Enabled unless told/disabled by support)',
							'default'     => 1,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Enabled',
									),
									array(
										'id'    => 0,
										'label' => 'Disabled',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'enable_notifcation_history',
							'label'       => 'Enable push notification history on app',
							'default'     => 0,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Enabled',
									),
									array(
										'id'    => 0,
										'label' => 'Disabled',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'       => 'textarea',
							'id'         => 'custom_webview_header',
							'label'      => 'Customer HTML header for webview pages',
							'isEncoding' => true,
							'default'    => APPMAKER_WC_General_Helper::get_custom_html(),
						)
					),
					self::get_field(
						array(
							'type'       => 'text',
							'id'         => 'number_of_categories',
							'label'      => 'Type the number of categoies needed to be show in scroller',
							//'isEncoding' => false,
							'default'    => 5,
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'product_variations_data',
							'label'       => 'Include product variations in product list',
							'default'     => 0,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Yes',
									),
									array(
										'id'    => 0,
										'label' => 'No',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'add_product_fields_list',
							'label'       => 'Include product fields in product list',
							'default'     => 0,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Yes',
									),
									array(
										'id'    => 0,
										'label' => 'No',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'add_headers_in_webview',
							'label'       => 'Add headers in webview',
							'default'     => 0,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Yes',
									),
									array(
										'id'    => 0,
										'label' => 'No',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'appmaker_automate_woo',
							'label'       => 'Enable AutomateWoo features',
							'default'     => 0,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Yes',
									),
									array(
										'id'    => 0,
										'label' => 'No',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'force_hide_description',
							'label'       => 'Hide Product Description',
							'default'     => 0,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Yes',
									),
									array(
										'id'    => 0,
										'label' => 'No',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'enable_vendor_order_push',
							'label'       => 'Send push notification for new order',
							'default'     => 0,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Enabled',
									),
									array(
										'id'    => 0,
										'label' => 'Disabled',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'hide_short_description_product_list',
							'label'       => 'Hide short description from product list',
							'default'     => 1,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Yes',
									),
									array(
										'id'    => 0,
										'label' => 'No',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'enable_products_in_webview',
							'label'       => 'Show all products in webview',
							'default'     => 0,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Yes',
									),
									array(
										'id'    => 0,
										'label' => 'No',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'hide_wcfm_support_ticket_tab',
							'label'       => 'Hide WCFM support ticket tab from Myaccount page',
							'default'     => 0,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Yes',
									),
									array(
										'id'    => 0,
										'label' => 'No',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'hide_brand_images',
							'label'       => 'Show/Hide Brand Images',
							'default'     => 1,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Show',
									),
									array(
										'id'    => 0,
										'label' => 'Hide',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'cart_sync_qty',
							'label'       => 'Sync Product Quantities @ Cart',
							'default'     => 0,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Yes',
									),
									array(
										'id'    => 0,
										'label' => 'No',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'hide_shipping_in_cart',
							'label'       => 'Hide shipping details from cart',
							'default'     => 1,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Yes',
									),
									array(
										'id'    => 0,
										'label' => 'No',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'hide_cart_weight',
							'label'       => 'Hide Cart Weight',
							'default'     => 1,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Yes',
									),
									array(
										'id'    => 0,
										'label' => 'No',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'disable_appmaker_checkout_webview',
							'label'       => 'Disable Appmaker Webview ',
							'default'     => $is_greater,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Yes',
									),
									array(
										'id'    => 0,
										'label' => 'No',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'appmaker_checkout_type',
							'label'       => 'Appmaker checkout type',
							'default'     => $is_greater_for_multi,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Multi-step checkout',
									),
									array(
										'id'    => 0,
										'label' => 'Legacy multi-step checkout',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'       => 'textarea',
							'id'         => 'custom_webview_header_checkout',
							'label'      => 'Customer style for webview checkout',
							'isEncoding' => true,
							'default'    => APPMAKER_WC_General_Helper::get_custom_checkout_style(),
						)
					),


				),
			),
			'Account'             => array(
				'id'     => 'Account',
				'title'  => __( 'Account', 'appmaker-woocommerce-mobile-app-manager' ),
				'fields' => array(
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'hide_downloads',
							'label'       => 'Downloads',
							'default'     => 0,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Hidden',
									),
									array(
										'id'    => 0,
										'label' => 'Visible',
									),
								),
							),
						)
					),
					self::get_field(
						array(
							'type'        => 'select',
							'id'          => 'hide_language_chooser',
							'label'       => 'Language chooser',
							'default'     => 0,
							'data_source' => array(
								'data' => array(
									array(
										'id'    => 1,
										'label' => 'Hidden',
									),
									array(
										'id'    => 0,
										'label' => 'Visible',
									),
								),
							),
						)
					),
				),
			),
		);
		foreach ( $return as $section ) {
			foreach ( $section['fields'] as $field ) {
				if ( ! isset( $options[ $field['id'] ] ) && $field['type'] != 'title' ) {
					$options[ $field['id'] ] = $field['default'];
				}
			}
		}

		return array(
			'data' => array(
				'fields' => array_values( apply_filters( 'appmaker_wc_settings_fields', $return ) ),
				'values' => apply_filters( 'appmaker_wc_settings_values', $options ),
			),
		);
	}

	//custom html
	/*  public static function get_custom_html(){

		$output ='<style>.whb-sticky-header.whb-clone.whb-main-header.whb-sticked{box-shadow: none !important;}</style>';

		$options = get_option('appmaker_wc_custom_settings', array());
		if(!empty($options) && isset($options['custom_webview_head'])){

			$output = $options['custom_webview_head'];
		}
		return base64_encode($output);
	}    */

	public function save_settings( $request ) {
		$data = json_decode( $request['data'], true );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			$data = stripslashes( $request['data'] ); // To Fix issue for some users having slashes added.
			$data = json_decode( $data, true );
			if ( json_last_error() !== JSON_ERROR_NONE ) {
				return new WP_Error( 'invalid_json', __( 'Json is invalid.', 'appmaker-woocommerce-mobile-app-manager' ), array( 'status' => 400 ) );
			}
		}
		if ( is_array( $data ) ) {
			$options = get_option( 'appmaker_wc_custom_settings', array() );
			$options = array_merge( $options, $data );
			update_option( 'appmaker_wc_custom_settings', $options, false );
		}

		return $this->get_settings();
	}

	public static function get_field( $config ) {
		$defaults = array(
			'type'           => 'text',
			'id'             => 'action_value',
			'default'        => '',
			'display'        => true,
			'validation'     => array( 'required' ), //
			'label'          => '',
			'desc'           => '',
			'placeholder'    => isset( $config['label'] ) ? $config['label'] : '',
			'depended'       => false,
			'depended_value' => false,
			'data_source'    => array(),
		);

		return wp_parse_args( $config, $defaults );
	}

	public function get_product_filter_settings() {
		$attrs_raw = wc_get_attribute_taxonomy_names();
		$return    = array();
		foreach ( $attrs_raw as $filter ) {
			$return[] = array(
				'id'    => $filter,
				'label' => $this->decode_html( wc_attribute_label( $filter ) ),
			);
		}

		return $return;
	}

	public function get_payment_gateways_settings() {
		$gateways = WC()->payment_gateways()->get_available_payment_gateways();
		$return   = array();
		foreach ( $gateways as $id => $gateway ) {
			$return[] = array(
				'id'    => $id,
				'label' => $this->decode_html( wc_attribute_label( $gateway->title ) ),
			);
		}

		return $return;
	}


	public function get_tab_settings() {
		global $product;
		global $post;

		$product_id = APPMAKER_WC::$api->get_settings( 'product_tab_field_product_id', '' );
		if ( ! empty( $product_id ) ) {
			$product = wc_get_product( $product_id );
			if ( ! empty( $product ) ) {
				$post = get_post( APPMAKER_WC_Helper::get_id( $product ) );
			}
		}

		if ( empty( $product_id ) || empty( $product ) ) {
			$args                 = array(
				'posts_per_page' => 1,
				'orderby'        => 'rand',
				'post_type'      => 'product',
			);
			$args['meta_query'][] = array(
				array(
					'key'     => '_visibility',
					'value'   => array( 'catalog', 'visible' ),
					'compare' => 'IN',
				),
			);
			$product              = get_posts( $args );
		}
		$fields = array(
			self::get_field(
				array(
					'type'  => 'title',
					'id'    => 'tab_info',
					'label' => 'Enter a valid product ID if you cannot see all tabs',
				)
			),
			self::get_field(
				array(
					'type'    => 'text',
					'id'      => 'product_tab_field_product_id',
					'default' => '',
					'label'   => 'Product ID to load product tabs',
				)
			),
			self::get_field(
				array(
					'type'        => 'select',
					'id'          => 'hide_buy_now_block',
					'label'       => 'Hide Buy now and Add to cart button',
					'default'     => 0,
					'data_source' => array(
						'data' => array(
							array(
								'id'    => 1,
								'label' => 'hide',
							),
							array(
								'id'    => 0,
								'label' => 'show',
							),
						),
					),
				)
			),
			self::get_field(
				array(
					'type'        => 'select',
					'id'          => 'display_add_to_cart_block',
					'label'       => 'Show Add to cart button',
					'default'     => 1,
					'data_source' => array(
						'data' => array(
							array(
								'id'    => 0,
								'label' => 'hide',
							),
							array(
								'id'    => 1,
								'label' => 'show',
							),
						),
					),
				)
			),
			self::get_field(
				array(
					'type'        => 'select',
					'id'          => 'hide_quantity_block',
					'label'       => 'Hide Quantity Switcher',
					'default'     => 0,
					'data_source' => array(
						'data' => array(
							array(
								'id'    => 1,
								'label' => 'Yes',
							),
							array(
								'id'    => 0,
								'label' => 'No',
							),
						),
					),
				)
			),
			self::get_field(
				array(
					'type'        => 'select',
					'id'          => 'hide_product_rating',
					'label'       => 'Hide Product Rating',
					'default'     => 0,
					'data_source' => array(
						'data' => array(
							array(
								'id'    => 1,
								'label' => 'Yes',
							),
							array(
								'id'    => 0,
								'label' => 'No',
							),
						),
					),
				)
			),
		);

		if ( ! empty( $product ) ) {
			global $post,$wp_query;            
			if ( ! is_object( $product ) ) {
				$product = current( $product );
				$product = wc_get_product( $product );
				$post    = get_post( APPMAKER_WC_Helper::get_id( $product ) );
			}
			$wp_query->is_singular = true;
			$wp_query->post  = $post;
			$tabs = apply_filters( 'woocommerce_product_tabs', array() );
			$tabs = apply_filters( 'appmaker_wc_product_tabs', $tabs );

			$product_widgets    = array();
			$product_widget_ids = array();
			foreach ( $tabs as $key => $tab ) {
				if ( $key == 'additional_information' ) {
					$tab['title'] = __( 'Specification', 'appmaker-woocommerce-mobile-app-manager' );
				} elseif ( $key == 'reviews' ) {
					$tab['title'] = __( 'Reviews', 'woocommerce' );
				}

				$fields[] = self::get_field(
					array(
						'type'  => 'title',
						'id'    => 'product_tab_field_title_' . $key,
						'label' => $tab['title'],
					)
				);
				if ( $key != 'reviews' ) {
					$fields[] = self::get_field(
						array(
							'type'    => 'text',
							'id'      => 'product_tab_field_title_' . $key,
							'default' => $tab['title'],
							'label'   => 'Title',
						)
					);
				}
				$fields[]             = self::get_field(
					array(
						'type'        => 'select',
						'id'          => 'product_tab_display_type_' . $key,
						'label'       => 'Display Type',
						'data_source' => array(
							'data' => array(
								array(
									'id'    => 'DEFAULT',
									'label' => 'Default',
								),
								array(
									'id'    => 'OPEN_IN_WEB_VIEW',
									'label' => 'Open in WebView',
								),
								array(
									'id'    => 'HIDDEN',
									'label' => 'Hidden',
								),
							),
						),
						'default'     => ( $key == 'short_description' ) ? 'HIDDEN' : 'DEFAULT',
					)
				);
				$product_widget_ids[] = $key;
				$product_widgets[]    = array(
					'id'    => $key,
					'label' => $tab['title'],
				);
			}
			$fields[] = self::get_field(
				array(
					'type'        => 'select',
					'id'          => 'product_widgets_enabled',
					'label'       => 'Product Widgets',
					'default'     => $product_widget_ids,
					'multi'       => true,
					'data_source' => array(
						'data' => $product_widgets,
					),
				)
			);
			$fields   = apply_filters( 'appmaker_wc_product_tab_settings', $fields, $product_widget_ids, $product_widgets );
		}

		return $fields;
	}
}
