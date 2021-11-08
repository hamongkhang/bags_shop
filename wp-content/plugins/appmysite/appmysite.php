<?php
/**
 * Plugin Name: AppMySite
 * Plugin URI: https://www.appmysite.com
 * Description: This plugin enables WordPress & WooCommerce users to sync their websites with native iOS and Android apps, created on <a href="https://www.appmysite.com/"><strong>www.appmysite.com</strong></a>
 * Version: 3.2.0
 * Author: AppMySite
 * Text Domain: appmysite
 * Author URI: https://appmysite.com
 * Tested up to: 5.8.1
 * WC tested up to: 5.8.0
 * WC requires at least: 3.8.0
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 **/

	// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die( 'No script kiddies please!' );
}

	/******************************************************************************
	 * Show warning to all where WordPress version is below minimum requirement.
	 */

	global $wp_version;
if ( $wp_version <= 4.9 ) {
	function wo_incompatibility_with_wp_version() {
		?>
			<div class="notice notice-error">
				<p><?php esc_html_e( 'AppMySite requires that WordPress 4.9 or greater be used. Update to the latest WordPress version.', 'appmysite' ); ?>
					<a href="<?php echo esc_url( admin_url( 'update-core.php' ) ); ?>"><?php esc_html_e( 'Update Now', 'appmysite' ); ?></a></p>
			</div>
			<?php
	}

	add_action( 'admin_notices', 'wo_incompatibility_with_wp_version' );
}

	add_action(
		'rest_api_init',
		function () {
			register_rest_route(
				'wc/v3',
				'/ams-get-plugin-info',
				array(
					'methods'  => 'GET',
					'callback' => 'ams_get_version_info',
					'permission_callback' => '__return_true',
				)
			);
		}
	);

	
	add_action(
		'rest_api_init',
		function () {
			register_rest_route(
				'wc/v3',
				'/ams-menu',
				array(
					'methods'  => 'GET',
					'callback' => 'ams_get_menu_items',
					'permission_callback' => '__return_true',
				)
			);
		}
	);

	add_action(
		'rest_api_init',
		function () {
			register_rest_route(
				'wc/v3',
				'/ams-menu-names',
				array(
					'methods'  => 'GET',
					'callback' => 'ams_get_menu_names',
					'permission_callback' => '__return_true',
				)
			);
		}
	);

	add_action(
		'rest_api_init',
		function () {
			register_rest_route(
				'wc/v3',
				'/ams-login',
				array(
					'methods'  => 'POST',
					'callback' => 'ams_login',
					'permission_callback' => '__return_true',
				)
			);
		}
	);

	add_action(
		'rest_api_init',
		function () {
			register_rest_route(
				'wc/v3',
				'/ams-verify-user',
				array(
					'methods'  => 'POST',
					'callback' => 'ams_verify_user',
					'permission_callback' => '__return_true',
				)
			);
		}
	);

	add_action(
		'rest_api_init',
		function () {
			register_rest_route(
				'wc/v3',
				'/ams-profile-meta',
				array(
					'methods'  => 'GET',
					'callback' => 'ams_get_profile_meta',
					'permission_callback' => '__return_true',
				)
			);
		}
	);

	add_action(
		'rest_api_init',
		function () {
			register_rest_route(
				'wc/v3',
				'/ams-order-payment-url',
				array(
					'methods'  => 'POST',
					'callback' => 'ams_get_order_payment_url',
					'permission_callback' => '__return_true',
				)
			);
		}
	);
	
	add_action('rest_api_init', function ()
	{
		register_rest_route('wc/v3', '/ams-verify-application-password', array(
			'methods' => 'GET',
			'callback' => 'ams_verify_application_password',
			'permission_callback' => function() {
				return current_user_can('manage_options');
			},
		));
	});
	
	add_action('rest_api_init', function ()
	{
		register_rest_route('wc/v3', '/ams-wp-get-user-auth-cookies', array(
			'methods' => 'POST',
			'callback' => 'ams_wp_get_user_auth_cookies',
			'permission_callback' => function() {
				return current_user_can('manage_options');
			},
			'args' => array(
				'user_id' => array(
					'required' => true,
					'type' => 'integer',
					'description' => 'User ID',
				)
			) ,
		));
	});
	
	add_action(
		'rest_api_init',
		function () {
			register_rest_route(
				'wc/v3',
				'/ams-order-total',
				array(
					'methods'  => 'POST',
					'callback' => 'ams_get_order_total',
					'permission_callback' => '__return_true',
				)
			);
		}
	);

	add_action(
		'rest_api_init',
		function () {
			register_rest_route(
				'wc/v3',
				'/ams-send-password-reset-link',
				array(
					'methods'  => 'POST',
					'callback' => 'ams_send_password_reset_link',
					'permission_callback' => '__return_true',
				)
			);
		}
	);

	add_action(
		'rest_api_init',
		function () {
			register_rest_route(
				'wc/v3',
				'/ams-applicable-shipping-method',
				array(
					'methods'  => 'POST',
					'callback' => 'ams_applicable_shipping_method',
					'permission_callback' => '__return_true',
				)
			);
		}
	);

	add_action(
		'rest_api_init',
		function () {
			register_rest_route(
				'wc/v3',
				'/ams-product-search',
				array(
					'methods'  => 'GET',
					'callback' => 'ams_product_search',
					'permission_callback' => '__return_true',
				)
			);
		}
	);

	add_action(
		'rest_api_init',
		function () {
			register_rest_route(
				'wc/v3',
				'/ams-verify-cart-items',
				array(
					'methods'  => 'POST',
					'callback' => 'ams_verify_cart_items',
					'permission_callback' => '__return_true',
				)
			);
		}
	);

	add_action(
		'rest_api_init',
		function () {
			register_rest_route(
				'wc/v3',
				'/ams-categories',
				array(
					'methods'  => 'GET',
					'callback' => 'ams_categories',
					'permission_callback' => '__return_true',
				)
			);
		}
	);

	add_action(
		'rest_api_init',
		function () {
			register_rest_route(
				'wc/v3',
				'/ams-post-categories',
				array(
					'methods'  => 'GET',
					'callback' => 'ams_post_categories',
					'permission_callback' => '__return_true',
				)
			);
		}
	);

	add_action(
		'rest_api_init',
		function () {
			register_rest_route(
				'wc/v3',
				'/ams-checkout-fields',
				array(
					'methods'  => 'GET',
					'callback' => 'ams_checkout_fields',
					'permission_callback' => '__return_true',
				)
			);
		}
	);

	add_action(
		'rest_api_init',
		function () {
			register_rest_route(
				'wc/v3',
				'/ams-wc-points-rewards-effective-discount',
				array(
					'methods'  => 'POST',
					'callback' => 'ams_wc_points_rewards_effective_discount',
					'permission_callback' => '__return_true',
					'args'     => array(
						'line_items'  => array(
							'required'    => true,
							'type'        => 'array',
							'description' => 'Cart items',
						),
						'customer_id' => array(
							'required'    => true,
							'type'        => 'integer',
							'description' => 'Customer ID',
						),
						'wc_points_rewards_discount_amount' => array(
							'required'    => true,
							'type'        => 'number',
							'description' => 'Requested user discount',
						),
					),
				)
			);
		}
	);

	add_action(
		'rest_api_init',
		function () {
			register_rest_route(
				'wc/v3',
				'/ams-wc-points-rewards-settings',
				array(
					'methods'  => 'GET',
					'callback' => 'ams_wc_points_rewards_settings',
					'permission_callback' => '__return_true',
				)
			);
		}
	);
	
	add_action(
		'rest_api_init',
		function () {
			register_rest_route(
				'wc/v3',
				'/ams-change-password',
				array(
					'methods'  => 'POST',
					'callback' => 'ams_change_password',
					'permission_callback' => '__return_true',
					'args' => array(
							
							'customer_id' => array(
								'required' => true,
								'type' => 'integer',
								'description' => 'Customer ID',
							),
							'old_password' => array(
								'required' => true,
								'type' => 'text',
								'description' => 'Old Password',
							),
							'new_password' => array(
								'required' => true,
								'type' => 'text',
								'description' => 'New Password',
							),
							'confirm_password' => array(
								'required' => true,
								'type' => 'text',
								'description' => 'New Password',
							),
						)
				)
			);
		}
	);
	
		/******
	 * Load customer payment page without the need of customer login.
	 */

	add_filter( 'user_has_cap', 'ams_allow_payment_without_login', 10, 3 );
	function ams_allow_payment_without_login( $allcaps, $caps, $args ) {

		if ( ! isset( $caps[0] ) || $caps[0] != 'pay_for_order' ) {
			return $allcaps;
		}
		if ( ! isset( $_GET['key'] ) ) {
			return $allcaps;
		}
		$order = wc_get_order( $args[2] );
		if ( ! $order ) {
			return $allcaps;
		}
		$order_key                = $order->get_order_key();
		$order_key_check          = sanitize_text_field( wp_unslash( $_GET['key'] ) );
		$allcaps['pay_for_order'] = ( $order_key == $order_key_check );
		return $allcaps;
	}
		/******
	 * Add Default Catalog Orderby Settings in the setting API .
	 */
	add_filter( 'woocommerce_get_settings_products', 'add_subtab_settings', 10, 2 );
	function add_subtab_settings( $settings ) {
		$current_section = get_option( 'woocommerce_default_catalog_orderby' );

		if ( isset( $current_section ) ) {
			$settings[] = array(
				'name'     => __( 'AMS WC Default Catalog Orderby Settings', 'woocommerce' ),
				'id'       => 'woocommerce_default_catalog_orderby',
				'label'    => 'Woocommerce Default Catalog Orderby',
				'type'     => 'select',
				'desc'     => __( 'This setting determines the sorting order of products in the catalog.', 'woocommerce' ),
				'desc_tip' => true,
				'options'  => array(
					'price'      => __( 'Sort by price (asc)', 'woocommerce' ),
					'date'       => __( 'Sort by most recent', 'woocommerce' ),
					'rating'     => __( 'Average rating', 'woocommerce' ),
					'popularity' => __( 'Popularity (sales)', 'woocommerce' ),
					'menu_order' => __( 'Default sorting (custom ordering + name)', 'woocommerce' ),
					'price-desc' => __( 'Sort by price (desc)', 'woocommerce' ),
				),
				'default'  => '',
				'value'    => get_option( 'woocommerce_default_catalog_orderby' ),

			);
			return $settings;
		} else {
			return $settings; // If not, return the standard settings
		}
	}

	/******

	 * Adds post's featured media to REST API.
	 */

	register_rest_field(
		'post',
		'featured_image_src',
		array(
			'get_callback'    => 'ams_get_image_src',
			'update_callback' => null,
			'schema'          => null,
		)
	);
	
	add_action( 'rest_api_init', 'ams_register_rest_field_for_custom_post_type' );
	
	function ams_register_rest_field_for_custom_post_type(){
			$ams_custom_post_types = array_values(get_post_types(array('public' => true,'exclude_from_search' => false,'_builtin' => false),'names','and'));
			if (($key = array_search('product', $ams_custom_post_types)) !== false) { // We don't need this field for products
				unset($ams_custom_post_types[$key]);
			}
			register_rest_field(
			$ams_custom_post_types, //['product','course','projects']
			'featured_image_src',
			array(
				'get_callback'    => 'ams_get_image_src',
				'update_callback' => null,
				'schema'          => null,
			)
		);
	}
	
	function ams_get_image_src( $object, $field_name, $request ) {
		$feat_img_array = wp_get_attachment_image_src(
			$object['featured_media'], // Image attachment ID
			'large',  // Size.  Ex. "thumbnail", "large", "full", etc..
			false // Whether the image should be treated as an icon.
		);
		return $feat_img_array[0];
	}
	function ams_get_version_info( WP_REST_Request $request ){
		
			if ( ! function_exists( 'plugins_api' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
			}
			
			if( ! function_exists( 'get_plugin_data' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			$version_current = get_plugin_data( __FILE__ )['Version'];
			
			$args = array(
				'slug' => 'appmysite',
				'fields' => array(
					'version' => true,
				)
			);

			$call_api = plugins_api( 'plugin_information', $args );

			if ( is_wp_error( $call_api ) ) {

				$api_error = $call_api->get_error_message();

				return( rest_ensure_response([
											"AMS_PLUGIN_LATEST_VERSION"=>"0.0.0",
											"AMS_PLUGIN_CURRENT_VERSION"=>"0.0.0"
											]));							
			} else {

				if ( ! empty( $call_api->version ) ) {

					$version_latest = $call_api->version;
					return( rest_ensure_response( ["AMS_PLUGIN_LATEST_VERSION"=>$version_latest,
												  "AMS_PLUGIN_CURRENT_VERSION"=>$version_current] ) );
				}

			}						
						
	}
	
	function ams_verify_application_password( WP_REST_Request $request ) { 

		$user = get_user_by( 'ID', apply_filters('determine_current_user', false) ); // | User by ID 
		if ( isset( $user->errors ) ) {
			$error_message = strip_tags( ams_convert_error_to_string( $user->errors ) );
			$error         = new WP_Error();
			$error->add( 'message', __( $error_message . '' ) );
			return $error;
		} elseif ( isset( $user->data ) ) {
			$user->data->user_pass = '';
			$user->data->user_activation_key = '';
			$user->data->id = $user->ID; //integer
			$user->data->first_name = get_user_meta( $user->ID, 'first_name', true );
			$user->data->last_name = get_user_meta( $user->ID, 'last_name', true );			
			$user->data->roles = $user->roles;
			####get user wp_generate_auth_cookie####
			
			########################################
			return rest_ensure_response( $user->data );
		} else {
			return new WP_Error('ams_error', 'Something went wrong. Please contact support.', array('status' => 500));
		}
	}
	


	/******

	 * Adds post's medium & large media to REST API.
	 */

	register_rest_field(
		'post',
		'blog_images',
		array(
			'get_callback'    => 'ams_get_images_urls',
			'update_callback' => null,
			'schema'          => null,
		)
	);

	function ams_get_images_urls( $object, $field_name, $request ) {
		$medium     = wp_get_attachment_image_src( get_post_thumbnail_id( $object['id'] ), 'medium' );
		$medium_url = $medium['0'];

		$large     = wp_get_attachment_image_src( get_post_thumbnail_id( $object['id'] ), 'large' );
		$large_url = $large['0'];

		return array(
			'medium' => $medium_url,
			'large'  => $large_url,
		);
	}

	/******

	 * Get default variant in rest api.
	 */
	register_rest_field(
		'product',
		'ams_default_variation_id',
		array(
			'get_callback'    => 'ams_get_default_variant',
			'update_callback' => null,
			'schema'          => null,
		)
	);
	function ams_get_default_variant( $object, $field_name, $request ) {

		$product = wc_get_product( $object['id'] );
		if ( $product->is_type( 'variable' ) ) {
			$default_attributes = $product->get_default_attributes();
			if ( ! empty( $default_attributes ) ) {
				foreach ( $product->get_available_variations() as $variation_values ) {
					foreach ( $variation_values['attributes'] as $key => $attribute_value ) {
						$attribute_name = str_replace( 'attribute_', '', $key );
						$default_value  = $product->get_variation_default_attribute( $attribute_name );
						if ( $default_value == $attribute_value ) {
							$is_default_variation = true;
						} else {
							$is_default_variation = false;
							break;
						}
					}
					if ( $is_default_variation ) {
						$variation_id = $variation_values['variation_id'];
						break;
					}
				}
				return $variation_id;
			} else {
				return 0;
			}
		} else {
			return 0;
		}

	}

	register_rest_field(
		['product','product_variation'],
		'ams_product_points_reward',
		array(
			'get_callback'    => 'ams_get_product_points_reward',
			'update_callback' => null,
			'schema'          => null,
		)
	);
	function ams_get_product_points_reward( $object, $field_name, $request ) {

		$product = wc_get_product( $object['id'] );
		$product_id = $object['id'];
		
		if ( ! is_admin() && ! $product->is_type( 'variable' ) ) {			
			$_wc_max_points_earned = get_post_meta( $product_id, '_wc_max_points_earned' );
			$_wc_min_points_earned = get_post_meta( $product_id, '_wc_min_points_earned' );
			return get_post_meta( $product_id, '_wc_min_points_earned' );
			
		} else {
			return get_post_meta( $product_id, '_wc_points_earned' );
		}

	}

	/******

	 * Get discout percentage in rest api
	 */
	register_rest_field(
		'product',
		'ams_product_discount_percentage',
		array(
			'get_callback'    => 'ams_get_product_discount_percentage',
			'update_callback' => null,
			'schema'          => null,
		)
	);
	function ams_get_product_discount_percentage( $object, $field_name, $request ) {

		$product = wc_get_product( $object['id'] );
		if ( $product->is_on_sale() && ! is_admin() && ! $product->is_type( 'variable' ) ) {
			$regular_price       = (float) $product->get_regular_price(); // Regular price
			$sale_price          = (float) $product->get_price();
			$saving_price        = wc_price( $regular_price - $sale_price );
			$precision           = 2;
			$discount_percentage = round( 100 - ( $sale_price / $regular_price * 100 ), 2 );
			return $discount_percentage;
		} else {
			return 0.00;
		}

	}
	
	/******
	 * Added product display price in rest API to support show price inclusive/exclusive of tax.
	 ******/
	 
	register_rest_field(
		['product','product_variation'],
		'ams_price_to_display',
		array(
			'get_callback'    => 'ams_get_product_price_to_display',
			'update_callback' => null,
			'schema'          => null,
		)
	);
	function ams_get_product_price_to_display( $object, $field_name, $request ) {

		$product = wc_get_product( $object['id'] );
		$ams_price_to_display = wc_get_price_to_display($product,[]);
		return $ams_price_to_display;
		
	}
	
	/******
	 * users_can_register settings for wordpress APIs
	 ******/
	 
	add_action('rest_api_init', function ()
	{		
		register_setting('general', 'ams_users_can_register', array(
		 'show_in_rest' => true,
		 'type' => 'boolean',
		 'default' => get_option( 'users_can_register' )
		));
	});

	/******

	 * This adds product's thumbnail and medium images in the rest api for catalog listing.
	 ******/

	function ams_prepare_product_images( $response, $post, $request ) {
		global $_wp_additional_image_sizes;

		if ( empty( $response->data ) ) {
			return $response;
		}
		if (is_array($response->data['images']) || is_object($response->data['images'])) {
			foreach ( $response->data['images'] as $key => $image ) {
					$image_info                                    = wp_get_attachment_image_src( $image['id'], 'thumbnail' );
					$response->data['images'][ $key ]['thumbnail'] = $image_info[0];
					$image_info                                    = wp_get_attachment_image_src( $image['id'], 'medium' );
					$response->data['images'][ $key ]['medium']    = $image_info[0];
			}
		}
		return $response;

	}
	add_filter( 'woocommerce_rest_prepare_product_object', 'ams_prepare_product_images', 10, 3 );


	/******

	 * Provision for coupon in rest api. This modifies order by applying coupon immediately after creating the order.
	 * Adds checkout payment url in rest order api.
	 * Adds thumbnail image of product in get order api
	 */

	add_filter( 'woocommerce_rest_prepare_shop_order_object', 'ams_rest_apply_coupon', 10, 3 );

	function ams_rest_apply_coupon( $response, $object, $request ) {
				// this section is to apply coupon
		// If Reward point plugin is enabled #################
		if ( in_array(
			'woocommerce-points-and-rewards/woocommerce-points-and-rewards.php',
			apply_filters( 'active_plugins', get_option( 'active_plugins' ) )
		)
		) {
			$already_redeemed                  = get_post_meta( $response->data['id'], '_wc_points_redeemed', true );
			$order                             = wc_get_order( $response->data['id'] );
			$wc_points_rewards_discount_amount = $order->get_meta( '_ams_wc_points_redeemed' );
			$customer_id                       = $request->get_param( 'customer_id' );
			$line_items                        = $request->get_param( 'line_items' );
			$reward_amount                     = ams_get_points_rewards_discount_amount( $customer_id, $wc_points_rewards_discount_amount, $line_items ); // to be calculated
			$reward_coupon_code                = sprintf( 'wc_points_redemption_%s_%s_@%u', $customer_id, date( 'Y_m_d_h_i', current_time( 'timestamp' ) ), $reward_amount );
			// get actual point to be logged on the basis of allowed reward amount.
			$actual_points_to_redeemed = WC_Points_Rewards_Manager::calculate_points_for_discount( $reward_amount );

			// update_post_meta for reference
			if ( empty( $already_redeemed ) && $reward_amount > 0 ) {
				$results = $order->apply_coupon( $reward_coupon_code );
				update_post_meta( $response->data['id'], '_ams_wc_points_redeemed', $actual_points_to_redeemed );
				update_post_meta( $response->data['id'], '_ams_wc_points_rewards_discount_code', $reward_coupon_code );
				update_post_meta( $response->data['id'], '_ams_wc_points_rewards_discount_amount', $reward_amount );

			}
		}
		// If Reward point plugin is enabled #################

		if ( ! empty( $request->get_param( 'coupon_lines' ) ) ) {
			foreach ( $request->get_param( 'coupon_lines' ) as $item ) {
				if ( is_array( $item ) ) {
					if ( isset( $item['id'] ) ) {
						if ( ! isset( $item['code'] ) ) {
							throw new WC_REST_Exception( 'woocommerce_rest_invalid_coupon', __( 'Coupon code is required.', 'woocommerce' ), 400 );
						}
						$order   = wc_get_order( $response->data['id'] );
						$results = $order->apply_coupon( wc_clean( $item['code'] ) );

						if ( is_wp_error( $results ) ) {
							throw new WC_REST_Exception( 'woocommerce_rest_' . $results->get_error_code(), $results->get_error_message(), 400 );
						}
						return $response;
					}
				}
			}
		}
				// this section is to add extra field into order api
				$ams_order_checkout_url                       = ( $value = esc_url( $object->get_checkout_payment_url() ) ) ? $value : '';
				$response->data['order_checkout_payment_url'] = html_entity_decode( $ams_order_checkout_url );

		foreach ( $response->data['line_items'] as $key => $lineItem ) {
			$product_id = $lineItem['product_id'];
			$medium_url = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'thumbnail' );
			$response->data['line_items'][ $key ]['ams_order_thumbnail'] = $medium_url[0];
		}

			return $response;

	}


	/******

	 * Get all menu names.
	 ******/

	function ams_get_menu_names() {

		$nav_menu_locations = wp_get_nav_menus();
		$result = [];
		foreach((array)$nav_menu_locations as $item){
			$result[$item->slug]=$item->term_id;
		}
		return( rest_ensure_response( $result ) );

	}

	/******

	 * Get all items of given menu.
	 ******/

	function ams_get_menu_items( WP_REST_Request $request ) {

		$menu_name = 'primary-menu'; // primary-menu, top

		if ( isset( $request['menu_name'] ) ) {
			$menu_name = $request['menu_name'];
		}
		$nav_menu_items     = wp_get_nav_menu_items( $menu_name );  //slug,id
		return( rest_ensure_response( $nav_menu_items ) );

	}

	/******

	 * Get all product and post categories in a binary tree.
	 ******/
	function ams_categories() {

		$orderby    = 'name';
		$order      = 'asc';
		$hide_empty = true;
		$cat_args   = array(
			'orderby'    => $orderby,
			'order'      => $order,
			'hide_empty' => $hide_empty,
		);

		$product_categories             = array_values( get_terms( 'product_cat', $cat_args ) );
		$array_product_categories_items = json_decode( json_encode( $product_categories ), true );
		if ( empty( $array_product_categories_items ) ) {
			return rest_ensure_response( $array_product_categories_items );
		}
		$category_tree = ams_build_category_tree( $array_product_categories_items, 'parent', 'term_id' );
		return rest_ensure_response( $category_tree );

	}

	function ams_post_categories() {

		$orderby    = 'name';
		$order      = 'asc';
		$hide_empty = true;
		$cat_args   = array(
			'orderby'    => $orderby,
			'order'      => $order,
			'hide_empty' => $hide_empty,
		);

		$product_categories             = array_values( get_terms( 'category', $cat_args ) );
		$array_product_categories_items = json_decode( json_encode( $product_categories ), true );
		if ( empty( $array_product_categories_items ) ) {
			return rest_ensure_response( $array_product_categories_items );
		}
		$category_tree = ams_build_category_tree( $array_product_categories_items, 'parent', 'term_id' );
		return rest_ensure_response( $category_tree );

	}


	/******

	 * Cart items verification
	 ******/

	function ams_verify_cart_items( WP_REST_Request $request ) {

		$params   = $request->get_params();
		$validate = ams_basic_validate( $params, array( 'line_items' ) );
		if ( $validate != true ) {
			return $validate;
		}

		$line_items = $params['line_items'];
		$result     = array();
		foreach ( $line_items as $key => $value ) {
			if ( array_key_exists( 'variation_id', $value ) ) {

				$variation = wc_get_product( $value['variation_id'] );

				if ( $variation ) {
					$result[ $key ]['product_id']     = $variation->get_id();
					$result[ $key ]['variation_id']   = $value['variation_id'];
					$result[ $key ]['name']           = $variation->get_name();
					$result[ $key ]['type']           = $variation->get_type();
					$result[ $key ]['status']         = $variation->get_status();
					$result[ $key ]['price']          = $variation->get_price();
					$result[ $key ]['regular_price']  = $variation->get_regular_price();
					$result[ $key ]['sale_price']     = $variation->get_sale_price();
					$result[ $key ]['manage_stock']   = $variation->get_manage_stock();
					$result[ $key ]['stock_quantity'] = $variation->get_stock_quantity();
					if ( $result[ $key ]['stock_quantity'] == null ) {
						$result[ $key ]['stock_quantity'] = '';
					}
					$result[ $key ]['stock_status'] = $variation->get_stock_status();
					$result[ $key ]['on_sale']      = $variation->is_on_sale();
					if ( 1 == $result[ $key ]['on_sale'] ) {

						$result[ $key ]['on_sale'] = true;
					} else {
						$result[ $key ]['on_sale'] = false;
					}
				}
			} else {
				// get product details
				$product = wc_get_product( $value['product_id'] );
				if ( $product ) {
					$result[ $key ]['product_id']        = $product->get_id();
					$result[ $key ]['name']        		 = $product->get_name();
					$result[ $key ]['type']              = $product->get_type();
					$result[ $key ]['status']            = $product->get_status();
					$result[ $key ]['get_price']         = $product->get_price();
					$result[ $key ]['get_regular_price'] = $product->get_regular_price();
					$result[ $key ]['get_sale_price']    = $product->get_sale_price();
					$result[ $key ]['manage_stock']      = $product->get_manage_stock();
					$result[ $key ]['stock_quantity']    = $product->get_stock_quantity();
					if ( $result[ $key ]['stock_quantity'] == null ) {
						$result[ $key ]['stock_quantity'] = '';
					}
					$result[ $key ]['stock_status'] = $product->get_stock_status();
					$result[ $key ]['on_sale']      = $product->is_on_sale();
					if ( 1 == $result[ $key ]['on_sale'] ) {
						$result[ $key ]['on_sale'] = true;
					} else {
						$result[ $key ]['on_sale'] = false;
					}
				}
			}
		}

		return rest_ensure_response( array( 'line_items' => $result ) );
	}


	add_action( 'pre_get_posts', 'ams_catalog_hidden_products_search_query_fix' );
	
	function ams_catalog_hidden_products_search_query_fix( $query = false ) {
		if ( ! is_admin() && isset( $query->query['post_type'] ) && $query->query['post_type'] === 'product' ) {
			$tax_query = $query->get( 'tax_query' );		
			if(!is_array($tax_query)){$tax_query=[];}
			array_push($tax_query,[
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'exclude-from-catalog',
					'operator' => 'NOT IN',
				]);
			array_push($tax_query,[
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'exclude-from-catalog',
					'operator' => '!=',
				]);
			$tax_query['relation']='AND';					
			$query->set( 'tax_query', $tax_query );
		}
	}
	
	/******

	 * Search API with support of multiple sort and order_by parameters .
	 ******/
	function ams_product_search( WP_REST_Request $request ) {

		$param        = $request->get_params();
		$category     = $param['category'];
		$filters      = $param['filter'];
		$per_page     = $param['per_page'];
		$page         = $param['page'];
		$order        = $param['order'];
		$orderby      = $param['orderby'];
		$featured     = $param['featured'];
		$on_sale      = $param['on_sale'];
		$min_price    = $param['min_price'];
		$max_price    = $param['max_price'];
		$stock_status = $param['stock_status'];
		$output       = array();
		// Use default arguments.
		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => get_option( 'posts_per_page' ),
			'post_status'    => 'publish',
			'paged'          => 1,

		);
		// Posts per page.
		if ( ! empty( $per_page ) ) {
			$args['posts_per_page'] = $per_page;
		}
		// Pagination, starts from 1.
		if ( ! empty( $page ) ) {
			$args['paged'] = $page;
		}

		// Order condition. ASC/DESC.
		if ( ! empty( $order ) ) {
			$args['order'] = $order;
		}
		// Order condition. ASC/DESC.
		if ( ! empty( $orderby ) ) {
			if ( $orderby == 'price' ) {
				$args['orderby']  = 'meta_value_num';
				$args['meta_key'] = '_price';

			} elseif ( $orderby == 'popularity' ) {   // For Popularity case, the sort order will always be desc.
				$args['orderby']  = 'meta_value_num';
				$args['meta_key'] = 'total_sales';

			} else {
				$args['orderby'] = $orderby;
			}
		}

		if ( ! empty( $featured ) ) {
			if ( $featured == true ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'featured',
				);
			}
		}

		if ( ! empty( $on_sale ) ) {
			if ( $on_sale == true ) {

				$args['meta_query'][] = array(
					'key'     => '_sale_price',
					'value'   => 0,
					'compare' => '>',
					'type'    => 'numeric',
				);
			}
		}
		if ( ! empty( $stock_status ) ) {
				$args['meta_query'][] = array(
					'key'   => '_stock_status',
					'value' => $stock_status,
				);
		}
		if ( isset( $min_price ) || isset( $max_price ) ) {

			$price_request = array();
			if ( isset( $min_price ) ) {
				$price_request['min_price'] = $min_price;
			}

			if ( isset( $max_price ) ) {
				$price_request['max_price'] = $max_price;
			}
			$args['meta_query'][] = wc_get_min_max_price_meta_query( $price_request );
		}

		if ( ! empty( $category ) || ! empty( $filters ) ) {

			$args['tax_query']['relation'] = 'AND';
			if ( ! empty( $category ) ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'product_cat',
					'field'    => 'id',
					'terms'    => explode( ',', $category ),        // [ $category ],
				);
			}

			if ( ! empty( $filters ) ) {
				foreach ( $filters as $filter_key => $filter_value ) {
					if ( $filter_key === 'min_price' || $filter_key === 'max_price' ) {
						continue;
					}
					$args['tax_query'][] = array(
						'taxonomy' => $filter_key,
						'field'    => 'term_id',
						'terms'    => explode( ',', $filter_value ),
					);
				}
			}
		}

		$the_query = new \WP_Query( $args );

		if ( ! $the_query->have_posts() ) {
			return $output;
		}
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$product_ids[] = $the_query->post->ID;
			$output[]      = get_the_title();
		}
		wp_reset_postdata();

		$request    = new WP_REST_Request( 'GET', '/wc/v3/products' );
		$parameters = array( 'include' => $product_ids );
		if ( ! empty( $order ) ) {
			$parameters += array( 'order' => $order );
		}
		if ( ! empty( $orderby ) ) {
			$parameters += array( 'orderby' => $orderby );
		}
		if ( ! empty( $per_page ) ) {
			$parameters += array( 'per_page' => $per_page );
		}
		$request->set_query_params( $parameters );
		$response = rest_do_request( $request );
		$server   = rest_get_server();
		$data     = $server->response_to_data( $response, false );
		return rest_ensure_response( $data );
	}


	/******

	 * Authenticate the user.
	 ******/

	function ams_login( WP_REST_Request $request ) {

		$req = $request->get_json_params();

		$validate = ams_basic_validate( $req, array( 'username', 'password' ) );
		if ( $validate != true ) {
			return $validate;
		}
			$wp_version = get_bloginfo( 'version' );
			$user       = wp_authenticate( sanitize_email( $req['username'] ), sanitize_text_field( $req['password'] ) );  // htmlspecialchars

		if ( isset( $user->errors ) ) {
			$error_message = strip_tags( ams_convert_error_to_string( $user->errors ) );
			$error = new WP_Error();
			$error->add( 'message', __( $error_message . '' ) );
			return $error;
		} elseif ( isset( $user->data ) ) {
			$user->data->user_pass  = '';
			$user->data->user_activation_key = '';
			$user->data->id = $user->ID; //integer
			$user->data->first_name = get_user_meta( $user->ID, 'first_name', true );
			$user->data->last_name = get_user_meta( $user->ID, 'last_name', true );						
			$user->data->roles = $user->roles;			
			$user->data->wp_version = $wp_version;
			return rest_ensure_response( $user->data );
		} else {
			return new WP_Error('ams_error', 'Something went wrong. Please contact support.', array('status' => 500));
		}
	}


	/******

	 * Verify the user.
	 ******/
	function ams_verify_user( WP_REST_Request $request ) {

		$req = $request->get_json_params();

		$validate = ams_basic_validate( $req, array( 'username' ) );
		if ( $validate != true ) {
			return $validate;
		}

		$is_email = is_email($req['username']);
		if(!$is_email){
			$user = get_user_by( 'login', $req['username'] ); // | ID | slug | email | login.
		}else{			
			$user = get_user_by( 'email', $req['username'] ); // | ID | slug | email | login.
		}
		
		if ( isset( $user->errors ) ) {
			$error_message = strip_tags( ams_convert_error_to_string( $user->errors ) );
			$error = new WP_Error();
			$error->add( 'message', __( $error_message . '' ) );
			return $error;
		} elseif ( isset( $user->data ) ) {
			$user->data->user_pass = '';
			$user->data->user_activation_key = '';
			$user->data->id = $user->ID; //integer
			$user->data->first_name = get_user_meta( $user->ID, 'first_name', true );
			$user->data->last_name = get_user_meta( $user->ID, 'last_name', true );			
			$user->data->roles = $user->roles;						
			return rest_ensure_response( $user->data );
		} else {
			return rest_ensure_response( array() ); // User not found.
		}

	}

	function ams_get_profile_meta( WP_REST_Request $request ) {

		if ( isset( $request['id'] ) ) {
			$user_id = sanitize_text_field( $request['id'] );
		}
		$validate = ams_basic_validate( $req, array( 'id' ) );
		if ( $validate != true ) {
			return $validate;
		}
		$user_meta_data          = get_user_meta( $user_id, 'wp_user_avatar', true );
		$profile_image_full_path = wp_get_attachment_image_src( $user_meta_data );
		return rest_ensure_response( array( 'wp_user_avatar' => $profile_image_full_path ) );
	}

		/******

		 * get check out payment url.
		 * Note: This will be removed in next vesrion.
		 ******/
	function ams_get_order_payment_url( WP_REST_Request $request ) {

		$req      = $request->get_json_params();
		$validate = ams_basic_validate( $req, array( 'order_id' ) );
		if ( $validate != true ) {
			return $validate;
		}
		$order_id = sanitize_text_field( $req['order_id'] );
		$order    = wc_get_order( $order_id );  // Returns WC_Product|null|false
		if ( ! isset( $order ) || $order == false ) {
			$error = new WP_Error();
			$error->add( 'message', __( 'The order ID appears to be invalid. Please try again.' ) );
			return $error;
		}  // Verify Valid Order ID
		$pay_now_url = esc_url( $order->get_checkout_payment_url() );
		return( rest_ensure_response( html_entity_decode( $pay_now_url ) ) );
	}

	function ams_wp_get_user_auth_cookies( WP_REST_Request $request ) {  

		$user_id = sanitize_text_field($request->get_param('user_id'));
		$user = get_user_by( 'ID', $user_id ); // | ID | slug | email | login.
		if ( isset( $user->errors ) ) {
			$error_message = strip_tags( ams_convert_error_to_string( $user->errors ) );
			$error         = new WP_Error();
			$error->add( 'message', __( $error_message . '' ) );
			return $error;
		} elseif ( isset( $user->data ) ) {
			$user->data->user_pass = '';
			$user->data->user_activation_key = '';
			$user->data->id = $user->ID; //integer
			$user->data->first_name = get_user_meta( $user->ID, 'first_name', true );
			$user->data->last_name = get_user_meta( $user->ID, 'last_name', true );			
			$user->data->roles = $user->roles;
			####get user wp_generate_auth_cookie####
				$expiration = time() + apply_filters('auth_cookie_expiration', 14 * DAY_IN_SECONDS , $user->ID, true);
				$site_url = get_site_url();//get_site_option('site_url');
				if($site_url){$cookie_hash=md5($site_url);}else{$cookie_hash='';}
				$user->data->expiration = $expiration;
				//$user->data->expire = $expiration + ( 12 * HOUR_IN_SECONDS );
				$user->data->cookie_hash = $cookie_hash;
				$user->data->wordpress_logged_in_ = wp_generate_auth_cookie($user->ID, $expiration, 'logged_in');
				$user->data->wordpress_ = wp_generate_auth_cookie($user->ID, $expiration, 'secure_auth');						
			########################################
			return rest_ensure_response( $user->data );
		} else {
			return new WP_Error('ams_error', 'Something went wrong. Please contact support.', array('status' => 500));
		}
	}
	
	/******

	 * Sends rest password link on user's email.
	 ******/

	function ams_send_password_reset_link( WP_REST_Request $request ) {
			$req      = $request->get_json_params();
			$validate = ams_basic_validate( $req, array( 'email' ) );
		if ( $validate != true ) {
			return $validate;
		}
			$email = sanitize_email( $req['email'] );
			$user  = get_user_by( 'email', $email );
		if ( ! $user ) {
			$error = new WP_Error();
			$error->add( 'message', __( 'The email address appears to be incorrect. Please try again.' ) );
			return $error;
		}
			$firstname  = $user->first_name;
			$email      = $user->user_email;
			$adt_rp_key = get_password_reset_key( $user );
			$user_login = $user->user_login;
			$rp_link    = '<a href="' . wp_lostpassword_url() . "?key=$adt_rp_key&login=" . rawurlencode( $user_login ) . '">' . wp_lostpassword_url() . "?key=$adt_rp_key&login=" . rawurlencode( $user_login ) . '</a>';

		if ( $firstname == '' ) {
			$firstname = 'Customer';
		}
			$message  = 'Hi ' . $firstname . ',<br>';
			$message .= 'Looks like you�ve requested a new password. Click below to reset it. <br>';
			$message .= $rp_link . '<br>';
			$message .= 'You can safely ignore this email if you do not wish to reset your password.<br><br><br>';
			$message .= 'Thanks.<br>';

			   $subject = __( 'Reset Password on ' . get_bloginfo( 'name' ) );
			   $headers = array();

			add_filter(
				'wp_mail_content_type',
				function( $content_type ) {
					return 'text/html';
				}
			);
			   $headers[] = 'From: ' . get_bloginfo( 'name' ) . '<' . get_bloginfo( 'admin_email' ) . '>' . "\r\n";
			   wp_mail( $email, $subject, $message, $headers );
			   remove_filter( 'wp_mail_content_type', 'set_html_content_type' );

			return( rest_ensure_response( array( 'message' => 'Reset Password link sent successfully!' ) ) );

	}


	/******

	 * Calculates shipping methods based on cart-items (line_items) , shipping address and coupon.
	 ******/

	function ams_applicable_shipping_method( WP_REST_Request $request ) {

		$req      = $request->get_json_params();
		$validate = ams_basic_validate( $req, array( 'shipping', 'line_items' ) );
		if ( $validate != true ) {
			return $validate;
		}

		$shipping                = $req['shipping'];
		$line_items              = $req['line_items'];
		$free_shipping_by_coupon = rest_sanitize_boolean( $req['free_shipping_by_coupon'] );
		$cartTotal               = 0;
		$content =  [];
		$do_we_need_to_calculate_table_rates = false;
		$table_rate_method_instance_ids = [];		
		$shipping_class_ids = [];
		
		foreach ( $line_items as $key => $value ) {  // prepare cart content which will be used for table rate plugin
			if ( array_key_exists( 'variation_id', $value ) ) {

				$variation = wc_get_product( $value['variation_id'] );
				array_push($shipping_class_ids,$variation->get_shipping_class_id());
				$cartTotal += ( wc_get_price_excluding_tax( $variation ) * $line_items[ $key ]['quantity'] ); // price without VAT
				//code related to content
				$content[$key] = ["key"=>$key,"product_id"=>$value['product_id'],
										"variation_id"=>$value['variation_id'],
										"quantity"=>$value['quantity'],
										"line_total"=>($variation->get_price()*$value['quantity']),
										"line_tax"=>(wc_get_price_including_tax($variation)- wc_get_price_excluding_tax( $variation ))*$value['quantity'],
									 ];
				
			}else{
				$product = wc_get_product( $value['product_id'] );
				array_push($shipping_class_ids,$product->get_shipping_class_id());
				$cartTotal += ( wc_get_price_excluding_tax( $product ) * $line_items[ $key ]['quantity'] ); // price without VAT
				//code related to content
				$content[$key] = ["key"=>$key,"product_id"=>$value['product_id'],
										"quantity"=>$value['quantity'],
										"line_total"=>($product->get_price()*$value['quantity']),
										"line_tax"=>(wc_get_price_including_tax($product)- wc_get_price_excluding_tax( $product ))*$value['quantity'],
									 ];
			}
		}
		
		$stateCode                     = sanitize_text_field( $shipping['state'] );
		$postcode                      = sanitize_text_field( $shipping['postcode'] );
		$countryCode                   = sanitize_text_field( $shipping['country'] );
		$wc_applicable_shipping_methods= array();
		$product_qty                   = array_column( $line_items, 'quantity' );
		$only_shipping_zone_zero_exist = false;

		// GET REQUEST##########################################
		$request_a     = new WP_REST_Request( 'GET', '/wc/v3/shipping/zones' );
		$response_a    = rest_do_request( $request_a );
		$server_a      = rest_get_server();
		$data_a        = $server_a->response_to_data( $response_a, false );
		$shippingZones = $data_a;
		// GET REQUEST##########################################

			$shippingZoneId = 0;  // Default shipping zone id (Locations not covered by other zones)
			unset( $shippingZones[0] );  // Remove default zone from the zone list which is to be matched.
			$shippingZones = array_values( $shippingZones );

		if ( empty( $shippingZones ) ) {
			$only_shipping_zone_zero_exist = true;
		}

		foreach ( $shippingZones as $key => $shippingZone ) {
			// Get location of the each zone
			// GET REQUEST##########################################
			$request_b                          = new WP_REST_Request( 'GET', "/wc/v3/shipping/zones/{$shippingZone['id']}/locations" );
			$response_b                         = rest_do_request( $request_b );
			$server_b                           = rest_get_server();
			$data_b                             = $server_b->response_to_data( $response_b, false );
			$shippingZones[ $key ]['locations'] = $data_b;
			// GET REQUEST##########################################

			if ( ! empty( $shippingZones[ $key ]['locations'] ) ) {
				foreach ( $shippingZones[ $key ]['locations'] as $locationkey => $locations ) {
					if ( $shippingZones[ $key ]['locations'][ $locationkey ]['type'] == 'postcode' ) {
						$does_zone_contain_location_of_type_postcode = true;
					} else {
						$does_zone_contain_location_of_type_postcode = false;
					}
				}
				foreach ( $shippingZones[ $key ]['locations'] as $locationkey => $locations ) {
					unset( $shippingZones[ $key ]['locations'][ $locationkey ]['_links'] );

					if ( $shippingZones[ $key ]['locations'][ $locationkey ]['type'] == 'postcode' ) {
						// if country code and state code does not match but the zone defines post code, we need to match post code

						$haystack = $shippingZones[ $key ]['locations'][ $locationkey ]['code'];

						if ( strpos( $haystack, '...' ) !== false ) {
							$zips = explode( '...', $haystack );
							if ( $postcode >= $zips[0] && $postcode <= $zips[1] ) {
								$shippingZoneId = $shippingZones[ $key ]['id'];
							}
						} elseif ( strpos( $haystack, '*' ) !== false ) { // if zone defines preg match of zip Ex. 1100*
							$haystack = str_replace( '*', '', $haystack );

							if ( preg_match( "/^{$haystack}/", $postcode ) ) {
								$shippingZoneId = $shippingZones[ $key ]['id'];
							}
						} elseif ( strtolower( $haystack ) == strtolower( $postcode ) ) {
							$shippingZoneId = $shippingZones[ $key ]['id'];
						} else {
							// if post code not matched, let's continue the same process for other zones.
						}
					} elseif ( ( ! $does_zone_contain_location_of_type_postcode ) && ( $shippingZones[ $key ]['locations'][ $locationkey ]['type'] == 'state' ) && ( $shippingZones[ $key ]['locations'][ $locationkey ]['code'] == $countryCode . ':' . $stateCode ) ) {
						$shippingZoneId = $shippingZones[ $key ]['id'];
					} elseif ( ( ! $does_zone_contain_location_of_type_postcode ) && ( $shippingZones[ $key ]['locations'][ $locationkey ]['type'] == 'country' ) && ( $shippingZones[ $key ]['locations'][ $locationkey ]['code'] == $countryCode ) ) {
						$shippingZoneId = $shippingZones[ $key ]['id'];

					} elseif ( ( ! $does_zone_contain_location_of_type_postcode ) && ( $shippingZones[ $key ]['locations'][ $locationkey ]['type'] == 'continent' ) && ( $shippingZones[ $key ]['locations'][ $locationkey ]['code'] == ams_get_country_continent( $countryCode ) ) ) {
						$shippingZoneId = $shippingZones[ $key ]['id'];

					} else {
						// continue
					}
				}
				if ( $shippingZoneId != 0 ) {
					break;
				}
			} else // If no location are associated with particular zone, possible with worldwide location
			{
				$shippingZoneId = $shippingZones[ $key ]['id'];
				break;
			}
		}

		// REQUEST##########################################
		$request_c  = new WP_REST_Request( 'GET', "/wc/v3/shipping/zones/{$shippingZoneId}/methods" );
		$response_c = rest_do_request( $request_c );
		$server_c   = rest_get_server();
		$methods    = $server_c->response_to_data( $response_c, false );
		// REQUEST##########################################

		if ( $shippingZoneId == 0 && empty( $methods ) && $only_shipping_zone_zero_exist ) { // If only zone = 0 present and there are no methods in this zone. Additionally, no other zones found, we need to skip the shipping screen, with 0 price.
				$wc_applicable_shipping_methods[] = array(
					'id'        => (string) ( 0 ),
					'title'     => 'zone_zero_with_no_shipping_methods',
					'method_id' => 'zone_zero_with_no_shipping_methods',
					'cost'      => floatval( '0.00' ),
				);
				return( rest_ensure_response( $wc_applicable_shipping_methods ) );
		}

		foreach ( $methods as $methodKey => $method ) {
			if ( isset( $method['settings']['min_amount'] ) ) {
				if ( $method['settings']['requires']['value'] == 'min_amount' && $cartTotal > $method['settings']['min_amount']['value'] ) {
						$methods[ $methodKey ]['methodcost'] = 0;

				} elseif ( ( $method['settings']['requires']['value'] == 'coupon' ) && ( $free_shipping_by_coupon == true ) ) {

						$methods[ $methodKey ]['methodcost'] = 0;

				} elseif ( ( $method['settings']['requires']['value'] == 'both' ) && ( $free_shipping_by_coupon == true ) && ( $cartTotal > $method['settings']['min_amount']['value'] ) ) {

						$methods[ $methodKey ]['methodcost'] = 0;

				} elseif ( ( $method['settings']['requires']['value'] == 'either' ) && ( $free_shipping_by_coupon == true || $cartTotal > $method['settings']['min_amount']['value'] ) ) {

						$methods[ $methodKey ]['methodcost'] = 0;

				} elseif ( $method['settings']['requires']['value'] == '' ) {

						$methods[ $methodKey ]['methodcost'] = 0;

				} else {
					unset( $methods[ $methodKey ] ); // remove methods those does not require amount to be applicable (in case of coupon or both coupon and amount)
				}
			} elseif ( isset( $method['settings']['type'] ) ) {
					$methodCost = $method['settings']['cost']['value'];
					$methodCost = trim( trim( $methodCost, ']' ), '[' );
				if ( strpos( $methodCost, 'fee percent' ) !== false ) {
					$methodCost    = str_replace( 'fee percent', 'fee_percent', $methodCost );
					$methodCostArr = explode( ' ', $methodCost );

					foreach ( $methodCostArr as $methodCostkey => $methodCostArrVal ) {
						$methodCostArrVal = explode( '="', trim( $methodCostArrVal, '"' ) );
						switch ( $methodCostArrVal[0] ) {
							case 'fee_percent':
								$fee_percent = $methodCostArrVal[1];
								break;
							case 'min_fee':
								$min_fee = $methodCostArrVal[1];
								break;
							case 'max_fee':
								$max_fee = $methodCostArrVal[1];
								break;
							default:
								break;
						}
					}
					$methodNewCost = ( $cartTotal * $fee_percent ) / 100;

					if ( isset( $min_fee ) && $min_fee > $methodNewCost ) {
						$methodNewCost = $min_fee;
					}
					if ( isset( $max_fee ) && $methodNewCost > $max_fee ) {
						$methodNewCost = $max_fee;
					}
				} elseif ( strpos( $methodCost, 'qty' ) !== false ) {
					$multiplier    = (float) $methodCost;
					$qty           = array_sum( $product_qty ); // $product_qty[$shippinkey];
					$methodNewCost = ( $multiplier * $qty );
				} else {
					$methodNewCost = (float) $methodCost;
				}

					$methods[ $methodKey ]['settings']['cost']['value'] = $methodNewCost;

				foreach ( $shipping_class_ids as $shippinkey => $shipping_class ) {

					$shippingClassId = $shipping_class != 0 ? 'class_cost_' . $shipping_class : 'no_class_cost';
					$currCost        = $method['settings'][ $shippingClassId ]['value'];
					$currCost        = trim( trim( $currCost, ']' ), '[' );
					$methodCost      = $method['settings']['cost']['value'];

					if ( strpos( $currCost, 'fee percent' ) !== false ) {
						$currCost    = str_replace( 'fee percent', 'fee_percent', $currCost );
						$currCostArr = explode( ' ', $currCost );

						foreach ( $currCostArr as $currCostkey => $currCostArrVal ) {
							$currCostArrVal = explode( '="', trim( $currCostArrVal, '"' ) );
							switch ( $currCostArrVal[0] ) {
								case 'fee_percent':
									$fee_percent = $currCostArrVal[1];
									break;
								case 'min_fee':
									$min_fee = $currCostArrVal[1];
									break;
								case 'max_fee':
									$max_fee = $currCostArrVal[1];
									break;
								default:
									break;
							}
						}

						$methodNewCost = ( $cartTotal * $fee_percent ) / 100;

						if ( isset( $min_fee ) && $min_fee > $methodNewCost ) {
							$methodNewCost = $min_fee;
						}

						if ( isset( $max_fee ) && $methodNewCost > $max_fee ) {
							$methodNewCost = $max_fee;
						}
						$methods[ $methodKey ]['cost'][] = $methodNewCost;

					} elseif ( strpos( $currCost, 'qty' ) !== false ) {
						$multiplier                      = (float) $currCost;
						$qty                             = $product_qty[ $shippinkey ];
						$methodNewCost                   = ( $multiplier * $qty );
						$methods[ $methodKey ]['cost'][] = $methodNewCost;
					} else {
						$methodNewCost = (float) $currCost;
						$methods[ $methodKey ]['cost'][] = $methodNewCost;
					}
				}

				if ( $method['settings']['type']['value'] == 'class' ) {
					$methods[ $methodKey ]['methodcost'] = array_sum( $methods[ $methodKey ]['cost'] ) + $methods[ $methodKey ]['settings']['cost']['value'];  // Bugfix  +$methodCost
				} elseif ( $method['settings']['type']['value'] == 'order' ) {
					$methods[ $methodKey ]['methodcost'] = max( $methods[ $methodKey ]['cost'] ) + $methods[ $methodKey ]['settings']['cost']['value'];  // Bugfix   +$methodCost

				} else {
					// Continue
				}
			} else {
				$methods[ $methodKey ]['methodcost'] = $methods[ $methodKey ]['settings']['cost']['value'] ? $methods[ $methodKey ]['settings']['cost']['value'] : 0;
			}
			if ( $methods[ $methodKey ]['enabled'] != 1 || $methods[ $methodKey ]['method_id']=='table_rate') {	// remove disabled and table-rate shipping methods
				//if this method is table_rate then save its instance_id.
				if($methods[ $methodKey ]['method_id']=='table_rate'){
					array_push($table_rate_method_instance_ids,$methods[ $methodKey ]['instance_id']) ;
					$do_we_need_to_calculate_table_rates = true;
				}
				unset( $methods[ $methodKey ] );
			} else {
				$wc_applicable_shipping_methods[] = array(
					'id'        => (string) $methods[ $methodKey ]['id'],
					'title'     => $methods[ $methodKey ]['title'],
					'method_id' => $methods[ $methodKey ]['method_id'],
					'cost'      => floatval( $methods[ $methodKey ]['methodcost'] ),
				);
			}
		}
		
		######Table rate calculation#######
		$formatted_table_rate_shipping_methods =  [] ;
		if ( in_array(
			'woocommerce-table-rate-shipping/woocommerce-table-rate-shipping.php',
			apply_filters( 'active_plugins', get_option( 'active_plugins' ) )
		)&& $do_we_need_to_calculate_table_rates
		) {
			require_once untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/includes/class-ams-wc-shipping-table-rate.php';
			
			//prepare pacakage
			$pacakage = [
				'contents' => $content,
				'contents_cost' => $cartTotal,
				'applied_coupons' => [],
				'destination' => $shipping,
				'cart_subtotal' => $cartTotal,
			];
					
			foreach($table_rate_method_instance_ids as $key =>$table_rate_method_instance_id){
				//get table rate shipping method
				$object = new AMS_WC_Shipping_Table_Rate($table_rate_method_instance_id);
				$table_rate_shipping_methods = $object->get_rates($pacakage);
				
				foreach($table_rate_shipping_methods as $key =>$method){
					//unset pacakage which of no use.				
					unset($table_rate_shipping_methods[$key]['package']);
					if(is_array($method['cost'])){$method_cost=(float)$method['cost'][0] + (float)$method['cost']['order'];}else{$method_cost=(float)$method['cost'];}
					$formatted_table_rate_shipping_methods = array_merge($formatted_table_rate_shipping_methods,[['id'=>$method['id'],'title'=>$method['label'],'method_id'=>'table_rate','cost'=>round($method_cost,2)]]);
				
				}
			}
			
		}
						
		$result = array_merge(array_values($wc_applicable_shipping_methods),array_values($formatted_table_rate_shipping_methods));
		return( rest_ensure_response( $result ) );

	}

	function ams_checkout_fields() {
		if ( in_array(
			'woocommerce-checkout-field-editor/woocommerce-checkout-field-editor.php',
			apply_filters( 'active_plugins', get_option( 'active_plugins' ) )
		)
		) {
				$result['checkout_shipping_fields']   = ams_object_to_array( get_option( 'wc_fields_shipping', array() ) );
				$result['checkout_billing_fields']    = ams_object_to_array( get_option( 'wc_fields_billing', array() ) );
				$result['checkout_additional_fields'] = ams_object_to_array( get_option( 'wc_fields_additional', array() ) );
				return rest_ensure_response( $result );
		} else {
			return rest_ensure_response( array() );
		}
	}

	function ams_wc_points_rewards_effective_discount( WP_REST_Request $request ) {
		if ( in_array(
			'woocommerce-points-and-rewards/woocommerce-points-and-rewards.php',
			apply_filters( 'active_plugins', get_option( 'active_plugins' ) )
		)
		) {
			$params                            = $request->get_params();
			$line_items                        = $params['line_items'];
			$customer_id                       = $params['customer_id'];
			$wc_points_rewards_discount_amount = $params['wc_points_rewards_discount_amount'];
			$granted_user_discount             = 0;

			// Construct local cart
			if ( is_null( WC()->cart ) ) {
				wc_load_cart();
			}
			WC()->cart->empty_cart();
			foreach ( $line_items as $key => $value ) {
				if ( array_key_exists( 'variation_id', $value ) ) {
					WC()->cart->add_to_cart( $value['product_id'], $value['quantity'], $value['variation_id'] );
				} else {
					WC()->cart->add_to_cart( $value['product_id'], $value['quantity'] );
				}
			}

			// Verify Global settings of points and reward plugin
			$available_user_discount = WC_Points_Rewards_Manager::get_users_points_value( $customer_id );

			// no discount
			if ( $available_user_discount <= 0 ) {
				// return 0;
				$error = new WP_Error();
				$error->add( 'message', __( 'No reward point available.' ) );
				return $error;
			}

			if ( 'yes' === get_option( 'wc_points_rewards_partial_redemption_enabled' ) && $wc_points_rewards_discount_amount ) {
				$requested_user_discount = WC_Points_Rewards_Manager::calculate_points_value( $wc_points_rewards_discount_amount );
				if ( $requested_user_discount > 0 && $requested_user_discount < $available_user_discount ) {
					$available_user_discount = $requested_user_discount;
				}
			}

			// Limit the discount available by the global minimum discount if set.
			$minimum_discount = get_option( 'wc_points_rewards_cart_min_discount', '' );
			if ( $minimum_discount > $available_user_discount ) {
				// return 0;
				$error = new WP_Error();
				$error->add( 'message', __( 'Please enter atleast '.$minimum_discount.' points global minimum discount error.' ) );
				return $error;
			}

			// apply product level setting of point and reward plugin.
			$discount_applied = 0;

			if ( ! did_action( 'woocommerce_before_calculate_totals' ) ) {
				WC()->cart->calculate_totals();
			}
			
			$max_points_discount_of_all_products = 0;			
			foreach ( WC()->cart->get_cart() as $item_key => $item ) {

				$discount     = 0;
				$max_discount = WC_Points_Rewards_Product::get_maximum_points_discount_for_product( $item['data'] );

				if ( is_numeric( $max_discount ) ) {

					// adjust the max discount by the quantity being ordered
					$max_discount *= $item['quantity'];

					$max_points_discount_of_all_products += $max_discount ; 

					// if the discount available is greater than the max discount, apply the max discount
					$discount = ( $available_user_discount <= $max_discount ) ? $available_user_discount : $max_discount;

					// Max should be product price. As this will be applied before tax, it will respect other coupons.
				} else {
					/*
					 * Only exclude taxes when configured to in settings and when generating a discount amount for displaying in
					 * the checkout message. This makes the actual discount money amount always tax inclusive.
					 */
					if ( 'exclusive' === get_option( 'wc_points_rewards_points_tax_application', wc_prices_include_tax() ? 'inclusive' : 'exclusive' ) && $for_display ) {
						if ( function_exists( 'wc_get_price_excluding_tax' ) ) {
							$max_discount = wc_get_price_excluding_tax( $item['data'], array( 'qty' => $item['quantity'] ) );
						} elseif ( method_exists( $item['data'], 'get_price_excluding_tax' ) ) {
							$max_discount = $item['data']->get_price_excluding_tax( $item['quantity'] );
						} else {
							$max_discount = $item['data']->get_price( 'edit' ) * $item['quantity'];
						}
					} else {
						if ( function_exists( 'wc_get_price_including_tax' ) ) {
							$max_discount = wc_get_price_including_tax( $item['data'], array( 'qty' => $item['quantity'] ) );
						} elseif ( method_exists( $item['data'], 'get_price_including_tax' ) ) {
							$max_discount = $item['data']->get_price_including_tax( $item['quantity'] );
						} else {
							$max_discount = $item['data']->get_price( 'edit' ) * $item['quantity'];
						}
					}

					// if the discount available is greater than the max discount, apply the max discount
					$discount = ( $available_user_discount <= $max_discount ) ? $available_user_discount : $max_discount;
				}

				// add the discount to the amount to be applied
				$discount_applied += $discount;

				// reduce the remaining discount available to be applied
				$available_user_discount -= $discount;
			}
			// limit customer if requested amount to avail discount exceeds the product's maximum discount. 
			if($wc_points_rewards_discount_amount > $max_points_discount_of_all_products){
						$error = new WP_Error();
						$error->add( 'message', __( 'You cannot enter more than '.$max_points_discount_of_all_products.' points on these products.' ) );
						return $error;	
			}
			// limit the discount available by the global maximum discount if set
			$max_discount = get_option( 'wc_points_rewards_cart_max_discount' );

			if ( false !== strpos( $max_discount, '%' ) ) {
				$max_discount = ams_ams_calculate_discount_modifier( $max_discount );
			}
			$max_discount = filter_var( $max_discount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );

			if ( $max_discount && $max_discount < $discount_applied ) {
				$error = new WP_Error();
				$error->add( 'message', __( 'You cannot enter more than '.$max_discount.' points.' ) );
				return $error;
				//$discount_applied = $max_discount;
			}
			$discount_applied = filter_var( $discount_applied, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
			return rest_ensure_response( array( array( 'effective_discount_value' => (float) $discount_applied ) ) );

		} else {
			return rest_ensure_response( array() );
		}
	}

	function ams_wc_points_rewards_settings() {
		if ( in_array(
			'woocommerce-points-and-rewards/woocommerce-points-and-rewards.php',
			apply_filters( 'active_plugins', get_option( 'active_plugins' ) )
		)
		) {

			$result['wc_points_rewards_cart_max_discount']          = get_option( 'wc_points_rewards_cart_max_discount' );
			$result['wc_points_rewards_write_review_points']        = get_option( 'wc_points_rewards_write_review_points' );
			$result['wc_points_rewards_account_signup_points']      = get_option( 'wc_points_rewards_account_signup_points' );
			$result['wc_points_rewards_points_expiry']              = get_option( 'wc_points_rewards_points_expiry' );
			$result['wc_points_rewards_points_expire_points_since'] = get_option( 'wc_points_rewards_points_expire_points_since' );
			$result['wc_points_rewards_version']                    = get_option( 'wc_points_rewards_version' );
			$result['wc_points_rewards_earn_points_ratio']          = get_option( 'wc_points_rewards_earn_points_ratio', '' );
			$result['wc_points_rewards_earn_points_rounding']       = get_option( 'wc_points_rewards_earn_points_rounding' );
			$result['wc_points_rewards_redeem_points_ratio']        = get_option( 'wc_points_rewards_redeem_points_ratio', '' );
			$result['wc_points_rewards_partial_redemption_enabled'] = get_option( 'wc_points_rewards_partial_redemption_enabled' );
			$result['wc_points_rewards_cart_min_discount']          = get_option( 'wc_points_rewards_cart_min_discount' );
			$result['wc_points_rewards_max_discount']               = get_option( 'wc_points_rewards_max_discount' );
			$result['wc_points_rewards_points_tax_application']     = get_option( 'wc_points_rewards_points_tax_application' );
			$result['wc_points_rewards_points_expiry_number']       = get_option( 'wc_points_rewards_points_expiry_number' );
			$result['wc_points_rewards_points_expiry_period']       = get_option( 'wc_points_rewards_points_expiry_period' );
			$result['wc_points_rewards_single_product_message']     = get_option( 'wc_points_rewards_single_product_message' );
			$result['wc_points_rewards_variable_product_message']   = get_option( 'wc_points_rewards_variable_product_message' );
			$result['wc_points_rewards_earn_points_message']        = get_option( 'wc_points_rewards_earn_points_message' );
			$result['wc_points_rewards_redeem_points_message']      = get_option( 'wc_points_rewards_redeem_points_message' );
			$result['wc_points_rewards_thank_you_message']          = get_option( 'wc_points_rewards_thank_you_message' );

			return rest_ensure_response( $result );
		} else {
			return rest_ensure_response( array() );
		}
	}

	add_filter( 'woocommerce_get_shop_coupon_data', 'ams_create_virtual_coupon', 10, 2 );

	function ams_create_virtual_coupon( $false, $data ) {

		// Do not interfere with coupon creation and editing.
		if ( is_admin() ) {
			return $false;
		}
			$coupon_code_valid = false;
			$coupon_settings   = null;
			$coupon_amount     = 0;
		if ( ams_is_coupon_code_valid( $data ) ) {
			$coupon_code_valid = true;
		}
		if ( ( $pos = strpos( $data, '@' ) ) !== false ) {
			$coupon_amount = (float) substr( $data, $pos + 1 );
		}
		// Create a coupon with the properties you need
		if ( $coupon_code_valid ) {
			$coupon_settings = array(
				'id'                         => true,
				'discount_type'              => 'fixed_cart', // 'fixed_cart', 'percent' or 'fixed_product'
				'amount'                     => $coupon_amount, // value or percentage.
				'expiry_date'                => date( 'Y-m-d', strtotime( 'tomorrow' ) ), // YYYY-MM-DD
				'individual_use'             => false,
				'product_ids'                => array(),
				'exclude_product_ids'        => array(),
				'usage_limit'                => '1',
				'usage_limit_per_user'       => '1',
				'limit_usage_to_x_items'     => '',
				'usage_count'                => '',
				'free_shipping'              => false,
				'product_categories'         => array(),
				'exclude_product_categories' => array(),
				'exclude_sale_items'         => false,
				'minimum_amount'             => '',
				'maximum_amount'             => '',
				'customer_email'             => array(),
			);

			return $coupon_settings;
		} else {
			return false;
		}

	}

	add_action( 'rest_api_init', 'ams_create_api_customer_field' );

	function ams_create_api_customer_field() {

		// register_rest_field ( 'name-of-post-type', 'name-of-field-to-return', array-of-callbacks-and-schema() )
		register_rest_field(
			'customer',
			'ams-rewards-points-balance',
			array(
				'get_callback' => 'ams_get_rewards_points_balance',
				'schema'       => null,
			)
		);
	}

	function ams_get_rewards_points_balance( $object ) {
		if ( in_array(
			'woocommerce-points-and-rewards/woocommerce-points-and-rewards.php',
			apply_filters( 'active_plugins', get_option( 'active_plugins' ) )
		)
		) {
			// get the value of the user's point balance
			$available_user_discount = WC_Points_Rewards_Manager::get_users_points( $object['id'] );
			// return the post meta
			return (float) $available_user_discount;
		} else {
			return 0;
		}
	}

	add_action( 'woocommerce_order_status_processing', 'ams_order_processing' );
	function ams_order_processing( $order_id ) {

		if ( in_array(
			'woocommerce-points-and-rewards/woocommerce-points-and-rewards.php',
			apply_filters( 'active_plugins', get_option( 'active_plugins' ) )
		)
		) { // order-redeem
			$already_redeemed  = get_post_meta( $order_id, '_wc_points_redeemed', true );
			$logged_redemption = get_post_meta( $order_id, '_wc_points_logged_redemption', true );

			// Points has already been redeemed
			if ( ! empty( $already_redeemed ) ) {
				return;
			}
			$order       = wc_get_order( $order_id );
			$customer_id = version_compare( WC_VERSION, '3.0', '<' ) ? $order->user_id : $order->get_user_id();

			// bail for guest user
			if ( ! $customer_id ) {
				return;
			}

			$discount_code = $order->get_meta( '_ams_wc_points_rewards_discount_code' );

			if ( ! empty( $logged_redemption ) ) {
				$points_redeemed = $logged_redemption['points'];
				$discount_amount = $logged_redemption['amount'];
				$discount_code   = $logged_redemption['discount_code'];
			} else {
				$points_redeemed = $order->get_meta( '_ams_wc_points_redeemed' );
				// bail if ams is not involved
				if ( ! $points_redeemed ) {
					return;
				}
				// Get amount of discount
				$discount_amount = $order->get_meta( '_ams_wc_points_rewards_discount_amount' );

			}
			WC_Points_Rewards_Manager::decrease_points(
				$customer_id,
				$points_redeemed,
				'order-redeem',
				array(
					'discount_code'   => $discount_code,
					'discount_amount' => $discount_amount,
				),
				$order_id
			);
			update_post_meta( $order_id, '_wc_points_redeemed', $points_redeemed );
			update_post_meta(
				$order_id,
				'_wc_points_logged_redemption',
				array(
					'points'        => $points_redeemed,
					'amount'        => $discount_amount,
					'discount_code' => $discount_code,
				)
			);

			// add order note
			/* translators: 1: points earned 2: points label 3: discount amount */
			$order->add_order_note( sprintf( __( '%1$d %2$s redeemed for a %3$s discount.', 'woocommerce-points-and-rewards' ), $points_redeemed, ams_get_points_label( $points_redeemed ), wc_price( $discount_amount ) ) );

		}

	}
	function ams_change_password(WP_REST_Request $request){

		$req = $request->get_json_params();

		$user       = wp_authenticate( sanitize_email( $req['username'] ), sanitize_text_field( $req['password'] ) );  // htmlspecialchars
		$customer_id = sanitize_text_field( $req['customer_id'] );
		$old_password = sanitize_text_field( $req['old_password'] );
		$new_password = sanitize_text_field( $req['new_password'] );
		$confirm_password = sanitize_text_field( $req['confirm_password'] );
		
		$user = get_user_by( 'id', $customer_id );
		if ( isset( $user->errors ) ) {
			$error_message = strip_tags( ams_convert_error_to_string( $user->errors ) );
			$error         = new WP_Error();
			$error->add( 'message', __( $error_message . '' ) );
			return $error;
		}
		
		$x = wp_check_password( $old_password, $user->data->user_pass, $user->data->ID );
		
		if ( isset( $x->errors ) ) {
			$error_message = strip_tags( ams_convert_error_to_string( $x->errors ) );
			$error         = new WP_Error();
			$error->add( 'message', __( $error_message . '' ) );
			return $error;
		}		
		if($x)
		{	 		
			if($new_password == $old_password)
			{
				return new WP_Error( 'ams_error', 'Sorry, your new password and old password can not be the same.', array( 'status' => 422 ) );
			}
			if($new_password == $confirm_password)
			{
				$user_data['ID'] = $user->data->ID;
				$user_data['user_pass'] = $new_password;
				$uid = wp_update_user( $user_data );
				//wp_set_password($new_password , $user_id);
				if($uid) 
				{
					unset($passdata);
					$user->data->user_pass = '';
					$user->data->user_activation_key = '';
					$user->data->id = $user->ID; //integer
					$user->data->first_name = get_user_meta( $user->ID, 'first_name', true );
					$user->data->last_name = get_user_meta( $user->ID, 'last_name', true );														
					$user->data->roles = $user->roles;				
					return rest_ensure_response( $user->data );
				} else {						
					return new WP_Error( 'ams_error', 'Sorry, your account was not updated. Please try again later.', array( 'status' => 422 ) );
				}
			}
			else
			{					
				return new WP_Error( 'ams_error', 'Sorry, both your passwords do not match. Please try again. ', array( 'status' => 422 ) );				
			}			
		} 
		else 
		{			
			return new WP_Error( 'ams_error', 'Sorry, your old password is not correct. Please try again.', array( 'status' => 422 ) );
		}		
		
	}

	// //
	// Other Helping Function ###########################################//
	// //
	function ams_get_points_rewards_discount_amount( $customer_id, $wc_points_rewards_discount_amount, $line_items ) {

			$granted_user_discount = 0;

			// Construct local cart
		if ( is_null( WC()->cart ) ) {
			wc_load_cart();
		}
		WC()->cart->empty_cart();
		foreach ( $line_items as $key => $value ) {
			if ( array_key_exists( 'variation_id', $value ) ) {
				WC()->cart->add_to_cart( $value['product_id'], $value['quantity'], $value['variation_id'] );
			} else {
				WC()->cart->add_to_cart( $value['product_id'], $value['quantity'] );
			}
		}

			$available_user_discount = WC_Points_Rewards_Manager::get_users_points_value( $customer_id );

			// no discount
		if ( $available_user_discount <= 0 ) {
			return 0;
		}
		if ( 'yes' === get_option( 'wc_points_rewards_partial_redemption_enabled' ) && $wc_points_rewards_discount_amount ) {
			$requested_user_discount = WC_Points_Rewards_Manager::calculate_points_value( $wc_points_rewards_discount_amount );
			if ( $requested_user_discount > 0 && $requested_user_discount < $available_user_discount ) {
				$available_user_discount = $requested_user_discount;
			}
		}

			// Limit the discount available by the global minimum discount if set.
			$minimum_discount = get_option( 'wc_points_rewards_cart_min_discount', '' );
		if ( $minimum_discount > $available_user_discount ) {
			return 0;
		}
			$discount_applied = 0;

		if ( ! did_action( 'woocommerce_before_calculate_totals' ) ) {
			WC()->cart->calculate_totals();
		}

		foreach ( WC()->cart->get_cart() as $item_key => $item ) {

			$discount     = 0;
			$max_discount = WC_Points_Rewards_Product::get_maximum_points_discount_for_product( $item['data'] );

			if ( is_numeric( $max_discount ) ) {

				// adjust the max discount by the quantity being ordered
				$max_discount *= $item['quantity'];

				// if the discount available is greater than the max discount, apply the max discount
				$discount = ( $available_user_discount <= $max_discount ) ? $available_user_discount : $max_discount;

				// Max should be product price. As this will be applied before tax, it will respect other coupons.
			} else {
				/*
				 * Only exclude taxes when configured to in settings and when generating a discount amount for displaying in
				 * the checkout message. This makes the actual discount money amount always tax inclusive.
				 */
				if ( 'exclusive' === get_option( 'wc_points_rewards_points_tax_application', wc_prices_include_tax() ? 'inclusive' : 'exclusive' ) && $for_display ) {
					if ( function_exists( 'wc_get_price_excluding_tax' ) ) {
						$max_discount = wc_get_price_excluding_tax( $item['data'], array( 'qty' => $item['quantity'] ) );
					} elseif ( method_exists( $item['data'], 'get_price_excluding_tax' ) ) {
						$max_discount = $item['data']->get_price_excluding_tax( $item['quantity'] );
					} else {
						$max_discount = $item['data']->get_price( 'edit' ) * $item['quantity'];
					}
				} else {
					if ( function_exists( 'wc_get_price_including_tax' ) ) {
						$max_discount = wc_get_price_including_tax( $item['data'], array( 'qty' => $item['quantity'] ) );
					} elseif ( method_exists( $item['data'], 'get_price_including_tax' ) ) {
						$max_discount = $item['data']->get_price_including_tax( $item['quantity'] );
					} else {
						$max_discount = $item['data']->get_price( 'edit' ) * $item['quantity'];
					}
				}

				// if the discount available is greater than the max discount, apply the max discount
				$discount = ( $available_user_discount <= $max_discount ) ? $available_user_discount : $max_discount;
			}

			// add the discount to the amount to be applied
			$discount_applied += $discount;

			// reduce the remaining discount available to be applied
			$available_user_discount -= $discount;
		}
			// limit the discount available by the global maximum discount if set
			$max_discount = get_option( 'wc_points_rewards_cart_max_discount' );

		if ( false !== strpos( $max_discount, '%' ) ) {
			$max_discount = ams_calculate_discount_modifier( $max_discount );
			WC()->cart->empty_cart();
		}
			$max_discount = filter_var( $max_discount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );

		if ( $max_discount && $max_discount < $discount_applied ) {
			$discount_applied = $max_discount;
		}
			// clear cart before returning
			WC()->cart->empty_cart();
			$discount_applied = filter_var( $discount_applied, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
			return $discount_applied;

	}

	function ams_get_points_label( $count ) {

		list( $singular, $plural ) = explode( ':', get_option( 'wc_points_rewards_points_label' ) );

		return 1 == $count ? $singular : $plural;
	}
	function ams_is_coupon_code_valid( $coupon_code ) {
		if ( 0 === strpos( $coupon_code, 'wc_points_redemption_' ) ) {
			return true;
		}
		return false;
	}

	function ams_calculate_discount_modifier( $percentage ) {

		$percentage = str_replace( '%', '', $percentage ) / 100;

		if ( 'no' === get_option( 'woocommerce_prices_include_tax' ) ) {
			$discount = WC()->cart->subtotal_ex_tax;

		} else {
			$discount = WC()->cart->subtotal;

		}

		return $percentage * $discount;
	}

	function ams_object_to_array( $data ) {
		if ( is_array( $data ) || is_object( $data ) ) {
			$result = array();
			foreach ( $data as $key => $value ) {
				$value['field_name'] = $key;
				$value['options']    = array_values( $value['options'] );
				$result[]            = $value;

			}
			return $result;
		}
		return $data;
	}

	function ams_build_category_tree( $flat, $pidKey, $idKey = null ) {
		$grouped = array();
		foreach ( $flat as $sub ) {
			$grouped[ $sub[ $pidKey ] ][] = $sub;
		}
		$level     = 0;
		$fnBuilder = function( $siblings, $level ) use ( &$fnBuilder, $grouped, $idKey ) {
			foreach ( $siblings as $k => $sibling ) {
				if ( $sibling['slug'] == 'uncategorized' ) {
					unset( $siblings[ $k ] );
					continue;
				}
				$id                     = $sibling[ $idKey ];
				$sibling['description'] = '';
				$level++;
				if ( isset( $grouped[ $id ] ) ) {
					$sibling['depth']    = $level;
					$sibling['children'] = array_values( $fnBuilder( $grouped[ $id ], $level ) );
				} else {
					$sibling['depth']    = $level;
					$sibling['children'] = array();
				}
				$siblings[ $k ] = $sibling;
				$level--;
			}
			return $siblings;
		};

		if ( isset( $grouped[0] ) ) {
			$tree = $fnBuilder( $grouped[0], $level );
		}
		if ( ! empty( $tree ) ) {
			return array_values( $tree );
		} else {
			foreach ( $flat as $key => $value ) {
				if ( $value['slug'] == 'uncategorized' ) {
					unset( $flat[ $key ] );
					continue;
				}
				$flat[ $key ]['children'] = array();
			}
			return array_values( $flat );
		}
	}

	function ams_convert_error_to_string( $er ) {
		 $string = ' ';
		foreach ( $er as $key => $value ) {

			$string = $string . '' . $key . ':';
			foreach ( $value as $newkey => $newvalue ) {

				$string = $string . '' . $newvalue . ' ';
			}
		}
		 $string = str_replace( 'Lost your password?', '', $string );
		 $string = str_replace( 'Error:', '', $string );
		 $string = str_replace( '[message]', '', $string );
		 return( $string );
	}

	function ams_basic_validate( $request, $keys ) {
		foreach ( $keys as $key => $value ) {

			if ( ! isset( $request[ $value ] ) ) {
				status_header( 400 );
				echo ( json_encode(
					array(
						'message' => 'There is a problem with your input!',
						'error'   => $value . ': Field is required!',
					),
					JSON_UNESCAPED_UNICODE
				) );
				die();
			}
			if ( empty( $request[ $value ] ) ) {
				status_header( 400 );
				echo ( json_encode(
					array(
						'message' => 'There is a problem with your input!',
						'error'   => $value . ': Can not be empty!',
					),
					JSON_UNESCAPED_UNICODE
				) );
				die();
			}
		}
		 return true;
	}
	function ams_get_country_continent( $country ) {
		$data = array(
			'EU' => array( 'AD', 'AL', 'AT', 'AX', 'BA', 'BE', 'BG', 'BY', 'CH', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FO', 'FR', 'FX', 'GB', 'GG', 'GI', 'GR', 'HR', 'HU', 'IE', 'IM', 'IS', 'IT', 'JE', 'LI', 'LT', 'LU', 'LV', 'MC', 'MD', 'ME', 'MK', 'MT', 'NL', 'NO', 'PL', 'PT', 'RO', 'RS', 'RU', 'SE', 'SI', 'SJ', 'SK', 'SM', 'TR', 'UA', 'VA' ),
			'AS' => array( 'AE', 'AF', 'AM', 'AP', 'AZ', 'BD', 'BH', 'BN', 'BT', 'CC', 'CY', 'CN', 'CX', 'GE', 'HK', 'ID', 'IL', 'IN', 'IO', 'IQ', 'IR', 'YE', 'JO', 'JP', 'KG', 'KH', 'KP', 'KR', 'KW', 'KZ', 'LA', 'LB', 'LK', 'MY', 'MM', 'MN', 'MO', 'MV', 'NP', 'OM', 'PH', 'PK', 'PS', 'QA', 'SA', 'SG', 'SY', 'TH', 'TJ', 'TL', 'TM', 'TW', 'UZ', 'VN' ),
			'OC' => array( 'AS', 'AU', 'CK', 'FJ', 'FM', 'GU', 'KI', 'MH', 'MP', 'NC', 'NF', 'NR', 'NU', 'NZ', 'PF', 'PG', 'PN', 'PW', 'SB', 'TK', 'TO', 'TV', 'UM', 'VU', 'WF', 'WS' ),
			'NA' => array( 'CA', 'MX', 'US', 'AG', 'AI', 'AN', 'AW', 'BB', 'BL', 'BM', 'BS', 'BZ', 'CR', 'CU', 'DM', 'DO', 'GD', 'GL', 'GP', 'GT', 'HN', 'HT', 'JM', 'KY', 'KN', 'LC', 'MF', 'MQ', 'MS', 'NI', 'PA', 'PM', 'PR', 'SV', 'TC', 'TT', 'VC', 'VG', 'VI' ),
			'SA' => array( 'AR', 'BO', 'BR', 'CL', 'CO', 'EC', 'FK', 'GF', 'GY', 'GY', 'PE', 'PY', 'SR', 'UY', 'VE' ),
			'AF' => array( 'AO', 'BF', 'BI', 'BJ', 'BW', 'CD', 'CF', 'CG', 'CI', 'CM', 'CV', 'DJ', 'DZ', 'EG', 'EH', 'ER', 'ET', 'GA', 'GH', 'GM', 'GN', 'GQ', 'GW', 'YT', 'KE', 'KM', 'LY', 'LR', 'LS', 'MA', 'MG', 'ML', 'MR', 'MU', 'MW', 'MZ', 'NA', 'NE', 'NG', 'RE', 'RW', 'SC', 'SD', 'SH', 'SL', 'SN', 'SO', 'ST', 'SZ', 'TD', 'TG', 'TN', 'TZ', 'UG', 'ZA', 'ZM', 'ZW' ),
		);
		foreach ( $data as $continent => $countries ) {
			if ( in_array( $country, $countries ) ) {
				return $continent;
			}
		}
		return '';
	}

