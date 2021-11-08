<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WP_PASSWORD_PROTECT {

	public function __construct() {
		add_filter( 'appmaker_wc_product_data', array( $this, 'add_product_to_webiew' ), 10, 3 );
	}

	/**
	 * @param $return
	 *
	 * @return mixed
	 */
	public function add_product_to_webiew( $data, $product, $expanded ) {
        if(! empty( $product ) ) {
            $product_id = $product->get_id();
            $services = new PPW_Pro_Password_Services();
            $protected = $services->is_protected_content( $product_id );
            $whitelisted_role = $services->is_whitelist_roles( $product_id );
            if( $protected && ! $whitelisted_role ) {
                $data['product_in_webview'] = true;
                $product_url =  add_query_arg( array( 'from_app' => true , 'key' => true ), $product->get_permalink() );
                if ( strpos( $product_url, 'from_app' ) !== false && ! isset( $_COOKIE['from_app_cookie'] ) ) {
                    $expire = time() + 60 * 60;
                    wc_setcookie( 'from_app_cookie', 1, $expire, false );
                }            
                $data['product_in_webview_action'] = array(                
                        'type' => 'OPEN_IN_WEB_VIEW',
                        'params' => array( 'url' => $product_url, 'title'  => ''),         
                    );
            }

        }
		
		return $data;
	}
}

new APPMAKER_WP_PASSWORD_PROTECT();
