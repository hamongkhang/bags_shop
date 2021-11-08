<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
class APPMAKER_WC_Gateway_Appmaker extends WC_Payment_Gateway {

	public static $original_gateway = '';
	private $options;

	public static function className() {
		return 'APPMAKER_WC_Gateway_Appmaker';
	}

	public static function init() {
		if ( isset( $_POST['payment_method'] ) && in_array( $_POST['payment_method'], apply_filters( 'appmaker_wc_checkout_redirect_gateways', array( 'payfort', 'square', 'Pwacheckout', 'midtrans','cashfree','eway_payments','paypal_plus','razorpay','juno-credit-card','nmwoo_2co'  ) ) ) ) {
			self::$original_gateway = apply_filters( 'appmaker_wc_checkout_redirect_gateway_id', $_POST['payment_method'] );
			add_filter( 'woocommerce_payment_gateways', array( self::className(), 'add_appmaker_gateway' ) );
			add_filter( 'woocommerce_available_payment_gateways', array( self::className(), 'make_appmaker_gateway_available' ) );
			$_POST['payment_method'] = 'appmaker_webview';
		}
	}

	public static function add_appmaker_gateway( $methods ) {
		$methods[] = 'APPMAKER_WC_Gateway_Appmaker';

		return $methods;
	}

	public static function make_appmaker_gateway_available( $methods ) {
		if ( ! isset( $methods['appmaker_webview'] ) ) {
			$methods['appmaker_webview'] = new APPMAKER_WC_Gateway_Appmaker();
		}
		return $methods;
	}

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {
		$this->id                 = 'appmaker_webview';
		$this->method_title       = __( 'Appmaker Webview', 'woocommerce' );
		$this->method_description = __( 'Redirect to checkout page in webview page', 'woocommerce' );
		$this->has_fields         = false;

		// Load the settings
		$this->init_settings();

		// Get settings
		$this->title              = $this->get_option( 'title' );
		$this->description        = $this->get_option( 'description' );
		$this->instructions       = $this->get_option( 'instructions', $this->description );
		$this->enable_for_methods = $this->get_option( 'enable_for_methods', array() );
		$this->enable_for_virtual = $this->get_option( 'enable_for_virtual', 'yes' ) === 'yes' ? true : false;
	}

	/**
	 * Check If The Gateway Is Available For Use.
	 *
	 * @return bool
	 */
	public function is_available() {
		return true;
	}


	/**
	 * Process the payment and return the result.
	 *
	 * @param int $order_id
	 *
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$return['result']   = 'success';
		$order              = new WC_Order( $order_id );
		$url = $order->get_checkout_payment_url();

		$order = wc_get_order( $order_id );
		if ( is_a( $order, 'WC_Order' ) ) {
			if ( ! get_post_meta( $order_id, 'from_app' ) ) {
				$order->add_order_note( __( 'Order from App', 'appmaker-woocommerce-mobile-app-manager' ) );
				add_post_meta( $order_id, 'from_app', true );
			}
			$platform = APPMAKER_WC_General_Helper::get_app_platform();
			if ( ! get_post_meta( $order_id, 'appmaker_mobile_platform' ) && $platform  ) {                            
				$note = sprintf( __( 'Order from #%s app', 'appmaker-woocommerce-mobile-app-manager' ), $platform );
				$order->add_order_note( $note );
				add_post_meta( $order_id, 'appmaker_mobile_platform', $platform );
			}
			$key = method_exists( $order, 'get_order_key' ) ?  $order->get_order_key() : $order->order_key;
			WC()->session->set( 'last_order_key', $key );
			WC()->session->set( 'last_order_id', $order_id );
		}

		if ( self::$original_gateway === 'Pwacheckout' ) {
			$url = wc_get_checkout_url();
		}
		if ( in_array( self::$original_gateway, apply_filters( 'appmaker_wc_sdk_gateways', array() ) ) ) {
			$return['type'] = 'sdk';
			$return['sdk'] = self::$original_gateway;
			$return['order_id'] = $order_id;
		} else {						
			$return['redirect'] = add_query_arg( array(
				'payment_from_app' => '1',
				'payment_gateway'  => self::$original_gateway,
				'order_id' => $order_id,
			), $url );

			$should_redirect = false;
			$should_redirect = apply_filters('appmaker_wc_redirect_payment_gateway_url',$should_redirect);
			if($should_redirect){
				$this->options = get_option('appmaker_wc_settings');
				$base_url = site_url();				
				$user_id = get_current_user_id();
				$api_key = $this->options['api_key'];
				$access_token = apply_filters('appmaker_wc_set_user_access_token', $user_id);
				$redirect_url = base64_encode($return['redirect']);
				$return['redirect']  = $base_url . '/?rest_route=/appmaker-wc/v1/user/redirect/&url=' . $redirect_url . '&api_key=' . $api_key . '&access_token=' . $access_token . '&user_id=' . $user_id.'&from_app=1';
				
			}
		}
		return $return;
	}

}
