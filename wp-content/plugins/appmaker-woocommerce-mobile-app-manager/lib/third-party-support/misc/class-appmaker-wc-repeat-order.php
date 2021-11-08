<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class APPMAKER_WC_Repeat_Order extends APPMAKER_WC_REST_Posts_Abstract_Controller
{

    protected $namespace = 'appmaker-wc/v1';
    protected $rest_base = 'orders';
    
    public function __construct()
    {
        parent::__construct();		

        register_rest_route($this->namespace, '/' . $this->rest_base . '/repeat_order/redirect', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'repeat_order_redirect' ),
                'permission_callback' => array( $this, 'api_permissions_check' ),
                'args'                => $this->get_collection_params(),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
		) );

		$this->options = get_option( 'appmaker_wc_settings' );
        $enabled_reorder      = APPMAKER_WC::$api->get_settings( 'enable_repeat_order',true );	
		$enabled_reorder      = apply_filters('appmaker_update_repeat_order_settings', $enabled_reorder );
        if( $enabled_reorder ) {
            add_filter('woocommerce_get_cart_url', array($this, 'appmaker_repeat_order_cart_redirect_url'), 1, 1);
        }       
    }

    public function appmaker_repeat_order_cart_redirect_url($url)
	{
		if ( isset( $_GET['order_again'], $_GET['_wpnonce'] ) && is_user_logged_in() && wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), 'woocommerce-order_again' ) ) { // WPCS: input var ok, sanitization ok.
			//$GLOBALS['repeat_order_from_app'] = 'appmaker';
            $api_key   = $this->options['api_key'];
            $base_url  = site_url();            
            $user_id = get_current_user_id();           
            $access_token = apply_filters( 'appmaker_wc_set_user_access_token', $user_id ); 
            $url          = $base_url.'/?rest_route=/appmaker-wc/v1/orders/repeat_order/redirect'. '&api_key=' . $api_key. '&access_token='.$access_token.'&user_id='.$user_id;             
            $url          = add_query_arg( array( 'from_app' => true ), $url);
            
		}
		return $url;
	}

    public function repeat_order_redirect ( $request )
	{
        $errors = $this->get_wc_notices_errors();
		if (is_wp_error($errors)) {
			return $errors;
		}						
		$return['action'] = array(
			'type'   => 'OPEN_CART',
			'params' => array(),
		);
		return $return;

    }
}
new APPMAKER_WC_Repeat_Order();