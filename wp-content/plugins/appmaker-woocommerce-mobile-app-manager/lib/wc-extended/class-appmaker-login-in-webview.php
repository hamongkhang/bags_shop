<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class APPMAKER_Login_In_Webview {
	public function __construct() {
		add_filter( 'woocommerce_login_redirect', array( $this, 'login_redirect' ), 10, 2 );
		add_filter( 'woocommerce_registration_redirect', array( $this, 'login_redirect' ), 10, 1 );
		add_filter( 'woocommerce_add_to_cart_redirect', array( $this,'appmaker_add_to_cart_redirect'), 10 , 1 );			
	}
	
	public function login_redirect( $url , $user = false ) {
		$base_url = site_url();
		$options  = get_option( 'appmaker_wc_settings' );
		$api_key  = $options['api_key'];
		if ( empty( $user ) ) {
			$user = wp_get_current_user();
		}
		$user_id      = $user->ID;
		$access_token = $this->set_user_access_token( $user_id );
		$url          = "{$base_url}/?rest_route=/appmaker-wc/v1/user/login/webview-redirect&api_key={$api_key}&user_id={$user_id}&access_token={$access_token}";
		return $url;
	}

	public function set_user_access_token( $user, $force_new = false ) {
		$access_token = get_user_meta( $user, 'appmaker_wc_access_token', true );
		if ( empty( $access_token ) || $force_new ) {
			$access_token = 'token_' . wc_rand_hash();
			update_user_meta( $user, 'appmaker_wc_access_token', $access_token );
		}
		return $access_token;
	}

	public function appmaker_add_to_cart_redirect($url) {
		
		$url = apply_filters( 'woocommerce_get_cart_url', wc_get_page_permalink( 'cart' ) );
				
	    return $url;
	}

}

if ( isset( $_GET['from_app'] ) || isset( $_COOKIE['from_app_cookie'] ) ) {
	new APPMAKER_Login_In_Webview();
	require_once( APPMAKER_WC::$root . '/lib/wc-extended/class-appmaker-login-in-webview-compatibilities.php');
}
