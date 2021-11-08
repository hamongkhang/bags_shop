<?php

/**
 * Class APPMAKER_WC_API
 *
 * @property APPMAKER_WC_REST_Products_Controller $APPMAKER_WC_REST_Products_Controller
 * @property APPMAKER_WC_REST_User_Controller $APPMAKER_WC_REST_User_Controller
 * @property APPMAKER_WC_REST_Cart_Controller $APPMAKER_WC_REST_Cart_Controller
 * @property APPMAKER_WC_REST_Orders_Controller $APPMAKER_WC_REST_Orders_Controller
 * @property APPMAKER_WC_REST_Checkout_Form_Controller $APPMAKER_WC_REST_Checkout_Form_Controller
 * @property APPMAKER_WC_REST_Checkout_Controller $APPMAKER_WC_REST_Checkout_Controller
 * @property APPMAKER_WC_REST_BACKEND_NAV_Controller $APPMAKER_WC_REST_BACKEND_NAV_Controller
 * @property APPMAKER_WC_REST_Posts_Controller $APPMAKER_WC_REST_Posts_Controller
 */
class APPMAKER_WC_API {
	static $wp_api;
	static $_instance;
	public $settings = false;
	public $order;
	/**
	 * APPMAKER_WC_API constructor.
	 */
	public function __construct() {

		// Add query vars.
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );

		// Register API endpoints.
		add_action( 'init', array( $this, 'add_endpoint' ), 0 );

		// Handle appmaker-wc-api endpoint requests.
		add_action( 'parse_request', array( $this, 'handle_api_requests' ), 0 );

		// Ensure payment gateways are initialized in time for API requests.
		add_action( 'woocommerce_api_request', array( 'WC_Payment_Gateways', 'instance' ), 0 );

		add_filter( 'appmaker_wc_get_settings', array( $this, 'get_settings' ), 10, 2 );
		if ( isset( $_GET['payment_from_app'] ) ) {
			add_filter( 'woocommerce_get_checkout_order_received_url', array(
				$this,
				'override_order_receive_url',
			), 10, 2 );
			add_filter( 'woocommerce_get_cancel_order_url_raw', array(
				$this,
				'override_order_cancel_url',
			), 10, 1 );
		}
		// WP REST API.
		$this->rest_api_init();
	}

	public function load_settings() {
		$this->settings = get_option( 'appmaker_wc_custom_settings', array() );
	}

	public function get_settings( $key, $default_value = '' ) {
		if ( $this->settings === false ) {
			$this->load_settings();
		}
		if ( isset( $this->settings[ $key ] ) ) {
		    if(!is_numeric($this->settings[ $key ]) && is_string($this->settings[ $key ])) {
                return trim($this->settings[$key]);
            }else {
                return $this->settings[$key];
            }
        } else {
			return $default_value;
		}
	}

	/**
	 * Function to Override order receive url.
	 *
	 * @param string $url Url.
	 * @param WC_Order $order Order object.
	 *
	 * @return string
	 */
	public function override_order_receive_url( $url, $order ) {
		$this->order = $order;
		$order->add_order_note( __( 'Order from App', 'appmaker-woocommerce-mobile-app-manager' ) );
		if ( method_exists( $order, 'get_id' ) ) {
			$order_id = $order->get_id();
		} else {
			$order_id = $order->id;
		}
		update_post_meta( $order_id, 'from_app', true );

		return add_query_arg( array( 'app_order_id_value' => $order_id ), $url );
	}

	/**
	 * Function to Override order receive url.
	 *
	 * @param string $url Url.
	 *
	 * @return string
	 */
	public function override_order_cancel_url( $url ) {

		return add_query_arg( array( 'app_order_id_value' => $this->order->id ), $url );
	}

	/**
	 * Init WP REST API.
	 * @since 2.6.0
	 */
	private function rest_api_init() {
		global $wp_version;

		// REST API was included starting WordPress 4.4.
		if ( version_compare( $wp_version, 4.4, '<' ) ) {
			return;
		}

		//		$this->rest_api_includes();

		// Init REST API routes.
		add_action( 'rest_api_init', array( $this, 'rest_api_includes' ) );
		do_action( 'appmaker_wc_rest_api_init' );
	}

	/**
	 * Include REST API classes.
	 * @since 2.6.0
	 */
	public function rest_api_includes() {
		if ( empty( WC()->cart ) && false != strpos($_SERVER['REQUEST_URI'], 'appmaker-wc') ) {
			WC()->frontend_includes();
			wc_load_cart();
		}
		defined( 'APPMAKER_WC_REQUEST' ) || define( 'APPMAKER_WC_REQUEST', true );
		$this->load_settings();
		
		include_once( 'class-appmaker-wc-helper.php' );
		include_once( 'third-party-support/class-appmaker-wc-third-party-support.php' );

		//	// Authentication.
		include_once( 'class-appmaker-wc-rest-authentication.php' );

		// WP-API classes and functions.
		include_once( 'vendor/wp-rest-functions.php' );
		if ( ! class_exists( 'WP_REST_Controller' ) ) {
			include_once( 'vendor/class-wp-rest-controller.php' );
		}

		// WordPress Classes
		include_once( 'appmaker-wp/abstracts/abstract-appmaker-wp-wc-rest-controller.php' );
		// WordPress Classes
		include_once( 'appmaker-wp/class-appmaker-wp-wc-converter.php' );
		include_once( 'appmaker-wp/endpoints/appmaker/class-appmaker-wp-wc-rest-backend-inapppage-controller.php' );
		include_once( 'appmaker-wp/endpoints/appmaker/class-appmaker-wp-wc-rest-frontend-controller.php' );
		include_once( 'appmaker-wp/endpoints/appmaker/class-appmaker-wp-wc-rest-backend-media-controller.php' );
		include_once( 'appmaker-wp/endpoints/appmaker/class-appmaker-wp-wc-rest-backend-nav-controller.php' );
		include_once( 'appmaker-wp/endpoints/appmaker/class-appmaker-wp-wc-rest-backend-posts-controller.php' );
		include_once( 'appmaker-wp/endpoints/appmaker/class-appmaker-wp-wc-rest-backend-terms-controller.php' );
		include_once( 'appmaker-wp/endpoints/class-appmaker-wp-wc-rest-posts-controller.php' );

		include_once( 'endpoints/appmaker/class-appmaker-wc-rest-backend-inapppage-controller.php' );
		include_once( 'endpoints/appmaker/class-appmaker-wc-rest-frontend-controller.php' );
		include_once( 'endpoints/appmaker/class-appmaker-wc-rest-backend-media-controller.php' );
		include_once( 'endpoints/appmaker/class-appmaker-wc-rest-backend-nav-controller.php' );
		include_once( 'endpoints/appmaker/class-appmaker-wc-rest-backend-posts-controller.php' );
		include_once( 'endpoints/appmaker/class-appmaker-wc-rest-backend-terms-controller.php' );
		include_once( 'endpoints/appmaker/class-appamker-wc-rest-backend-report-controller.php' );
		include_once( 'endpoints/appmaker/class-appmaker-wc-rest-backend-settings-controller.php' );
		include_once( 'endpoints/appmaker/class-appmaker-wc-rest-backend-plugin-controller.php' );
		include_once( 'endpoints/wp/class-appmaker-wc-rest-posts-controller.php' );

		// Abstract controllers.
		include_once( 'abstracts/abstract-appmaker-wc-rest-controller.php' );
		include_once( 'abstracts/abstract-appmaker-wc-rest-posts-controller.php' );

		include_once( 'class-appmaker-wc-dynamic-form.php' );

		include_once( 'endpoints/wc/class-appmaker-wc-rest-products-controller.php' );
		include_once( 'endpoints/wc/class-appmaker-wc-rest-user-controller.php' );
		include_once( 'endpoints/wc/class-appmaker-wc-rest-cart-controller.php' );
		include_once( 'endpoints/wc/class-appmaker-wc-rest-checkout-form-controller.php' );
		include_once( 'endpoints/wc/class-appmaker-wc-rest-checkout-controller.php' );
		include_once( 'endpoints/wc/class-appmaker-wc-rest-orders-controller.php' );
		include_once( 'endpoints/wc/class-appmaker-wc-rest-dynamic-form-controller.php' );
		include_once( 'endpoints/wc/class-appmaker-wc-rest-shop-controller.php' );
		include_once( 'endpoints/appmaker/class-appmaker-wc-rest-notifications-controller.php' );
		include_once( 'endpoints/wc/class-appmaker-wc-rest-wishlist-controller.php' );
		include_once( 'endpoints/wc/class-appmaker-wc-rest-category-controller.php' );

		//performance controller
		include_once( 'endpoints/appmaker/class-appmaker-wc-rest-backend-performance-controller.php' );
		include_once( 'endpoints/appmaker/class-appmaker-wc-rest-backend-notifications-controller.php' );

		$this->register_rest_routes();
	}

	public static function get_instance() {
		if ( ! is_a( self::$_instance, 'APPMAKER_WC_API' ) ) {
			self::$_instance = new APPMAKER_WC_API();
		}

		return self::$_instance;
	}

	/**
	 * WC API for payment gateway IPNs, etc.
	 * @since 2.0
	 */
	public static function add_endpoint() {
		add_rewrite_endpoint( 'appmaker-wc-api', EP_ALL );
	}

	/**
	 * Add new query vars.
	 *
	 * @since 2.0
	 *
	 * @param array $vars
	 *
	 * @return string[]
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'appmaker-wc-api';

		return $vars;
	}

	/**
	 * API request - Trigger any API requests.
	 *
	 * @since   2.0
	 * @version 2.4
	 */
	public function handle_api_requests() {
		global $wp;

		if ( ! empty( $_GET['appmaker-wc-api'] ) ) {
			$wp->query_vars['appmaker-wc-api'] = $_GET['appmaker-wc-api'];
		}

		// Appmaker-wc-api endpoint requests.
		if ( ! empty( $wp->query_vars['appmaker-wc-api'] ) ) {

			// Buffer, we won't want any output here.
			ob_start();

			// No cache headers.
			nocache_headers();

			// Clean the API request.
			$api_request = strtolower( wc_clean( $wp->query_vars['appmaker-wc-api'] ) );

			// Trigger generic action before request hook.
			do_action( 'woocommerce_api_request', $api_request );

			// Is there actually something hooked into this API request? If not trigger 400 - Bad request.
			status_header( has_action( 'woocommerce_api_' . $api_request ) ? 200 : 400 );

			// Trigger an action which plugins can hook into to fulfill the request.
			do_action( 'woocommerce_api_' . $api_request );

			// Done, clear buffer and exit.
			ob_end_clean();
			die( '-1' );
		}
	}

	/**
	 * Register REST API routes.
	 * @since 2.6.0
	 */
	public function register_rest_routes() {
		global $wp_post_types;
		global $wp_taxonomies;

        if (!function_exists('wp_generate_attachment_metadata')) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
        }

		if ( isset( $wp_post_types['post'] ) ) {
			$wp_post_types['post']->show_in_rest = true;
			$wp_post_types['post']->rest_base    = 'posts';
		}

		if ( isset( $wp_post_types['page'] ) ) {
			$wp_post_types['page']->show_in_rest = true;
			$wp_post_types['page']->rest_base    = 'pages';
		}

		if ( isset( $wp_taxonomies['category'] ) ) {
			$wp_taxonomies['category']->show_in_rest = true;
			$wp_taxonomies['category']->rest_base    = 'categories';
		}

		if ( isset( $wp_taxonomies['post_tag'] ) ) {
			$wp_taxonomies['post_tag']->show_in_rest = true;
			$wp_taxonomies['post_tag']->rest_base    = 'tags';
		}
		APPMAKER_WC_Third_party_support::init();
		$controllers = array(
			'APPMAKER_WC_REST_Products_Controller',
			'APPMAKER_WC_REST_User_Controller',
			'APPMAKER_WC_REST_Cart_Controller',
			'APPMAKER_WC_REST_Orders_Controller',

			'APPMAKER_WC_REST_Checkout_Form_Controller',
			'APPMAKER_WC_REST_Dynamic_Form_Controller',
			'APPMAKER_WC_REST_Checkout_Controller',

			'APPMAKER_WC_REST_BACKEND_INAPPPAGE_Controller',
			'APPMAKER_WC_REST_BACKEND_NAV_Controller',
			'APPMAKER_WC_REST_BACKEND_MEDIA_Controller',
			'APPMAKER_WC_REST_BACKEND_Posts_Controller',
			'APPMAKER_WC_REST_BACKEND_Terms_Controller',
			'APPMAKER_WC_REST_BACKEND_Report_Controller',
			'APPMAKER_WC_REST_Settings_Controller',
			'APPMAKER_WC_REST_BACKEND_Plugin_Controller',
			'APPMAKER_WC_REST_FRONTEND_INAPPPAGE_Controller' => array(
				'APPMAKER_WC_REST_FRONTEND_Controller',
				'inAppPages',
			),
			'APPMAKER_WC_REST_FRONTEND_NAV_Controller'       => array(
				'APPMAKER_WC_REST_FRONTEND_Controller',
				'navigationMenu',
			),

			'APPMAKER_WC_REST_Posts_Controller' => array( 'APPMAKER_WC_REST_Posts_Controller', 'post' ),
			'APPMAKER_WC_REST_Pages_Controller' => array( 'APPMAKER_WC_REST_Posts_Controller', 'page' ),
			'APPMAKER_WC_Backend_Performance_Controller',
			'APPMAKER_WC_REST_Notifications_Controller',
			'APPMAKER_WC_REST_Backend_Notifications_Controller',
			'APPMAKER_WC_REST_SHOP_Controller',
			'APPMAKER_WC_REST_WISHLIST_Controller',
			'APPMAKER_WC_REST_Category_Controller'
		);

		foreach ( $controllers as $key => $controller ) {
			if ( is_array( $controller ) ) {
				$this->{$key} = new $controller[0]( $controller[1] );
				$this->$key->register_routes();
			} else {
				$this->$controller = new $controller();
				$this->$controller->register_routes();
			}
		}

		do_action( 'appmaker_wc_register_rest_routes' );
	}
}
