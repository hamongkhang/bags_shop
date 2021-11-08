<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_woocommerce_subscription {

	public function __construct() {
        $this->options = get_option( 'appmaker_wc_settings' );

        add_filter( 'appmaker_wc_product_data', array( $this, 'product_wc_subscription_price' ), 2, 2 );
        add_filter('appmaker_wc_cart_items',array($this,'product_woocommerce_subscription'),2,1);
        //add_filter('appmaker_wc_validate_checkout',array($this,'wc_subscription_validate_checkout'));
        add_filter( 'appmaker_wc_account_page_response', array( $this, 'my_account_subscription' ), 10, 1 );
    }

    public function product_wc_subscription_price( $data, $product )
    {
        $wc_subcription_price = '';
        $add_to_cart_button_text = '';
        if($product->get_type() == 'subscription' || $product->get_type() == 'variable-subscription'){
            $wc_subcription_price = html_entity_decode(strip_tags($product->get_price_html()));
            $add_to_cart_button_text = get_option('woocommerce_subscriptions_add_to_cart_button_text');
            $data['price_display'] = $wc_subcription_price;
            $data['add_to_cart_button_text'] = $add_to_cart_button_text;
        }
        if($product->get_type() == 'variable-subscription'){
            $data['type'] = 'variable';
        }
        return $data;
    }
    
    public function product_woocommerce_subscription($return){
        $updated_price = '';
       foreach ( $return['products'] as $key => $product ) {
           $price = $product['product_price'];
           $qty = $product['quantity'];
        $variation_string = '';
        if ( $product['variation_id'] != 0 ) {
            $variation_id = $product['variation_id'];
            $variation    = wc_get_product( $variation_id );
            $product_id   = $variation->get_parent_id();
        } else {
            $product_id = $product['product_id'];
        }
            if($product['product_type'] == 'subscription' || $product['product_type'] == 'variable-subscription'){
                //$wc_subscription_data = $this->get_wc_subscription_product( $product_id );
                $return['products'][ $key ]['line_subtotal'] = $price * $qty;
                $updated_price = $return['products'][ $key ]['line_subtotal'];
                $return['products'][ $key ]['line_total_display'] = APPMAKER_WC_Helper::get_display_price($updated_price);
                $return['products'][ $key ]['product_price_display'] = APPMAKER_WC_Helper::get_display_price($updated_price) . $wc_subscription_data['period'];
                
            }
        }
        // if( $updated_price ) {
        //     $return['total'] += $updated_price;
        //     $return['total_display'] = APPMAKER_WC_Helper::get_display_price($return['total']);
        // }
        // applied coupon discount in total_display
		if ( ! empty( $return['coupon_discounted'] ) && is_array( $return['coupon_discounted'] ) ) {
			foreach ( $return['coupon_discounted'] as $coupons ) {
				$coupon_discounted_total = $return['total'] - $coupons['discount'];
				$return['total_display'] = APPMAKER_WC_Helper::get_display_price( $coupon_discounted_total );
			}
		}
    return $return;
        
   }

  /* public function get_wc_subscription_product( $product_id ) {
        global $product, $post;
        $product = wc_get_product( $product_id );
        $data = $product->get_data();
        $wc_subscription             = array();
        if(get_post_meta($product_id,'_subscription_period_interval',true) == '1'){
            $wc_subscription['period']        = ' / ' . get_post_meta($product_id,'_subscription_period',true);
        }
        if(get_post_meta($product_id,'_subscription_period_interval',true) != '1'){
            $wc_subscription['period']        = ' every ' . get_post_meta($product_id,'_subscription_period_interval',true) .' ' . get_post_meta($product_id,'_subscription_period',true);
        }
        if(get_post_meta($product_id,'_subscription_price',true) != ''){
            $wc_subscription['price']  = get_post_meta($product_id,'_subscription_price',true);
        }
        if(get_post_meta($product_id,'_subscription_payment_sync_date',true) != ''){
            $wc_subscription['period']        = ' on the ' . get_post_meta($product_id,'_subscription_payment_sync_date',true) . ' of each ' . get_post_meta($product_id,'_subscription_period',true);
        }
        return $wc_subscription;
    } */


    public function  wc_subscription_validate_checkout($return){  
        if(empty($return['gateways'])){
           return new wp_error('empty_payment_gateway', "Sorry, it seems there are no available payment methods which support subscriptions. Please contact us if you require assistance or wish to make alternate arrangements.");          
        }
        return $return ;
    }

    public function my_account_subscription( $return ) {

        $base_url     = site_url();
        $my_account_page_id = get_option( 'woocommerce_myaccount_page_id' );
		if ( $my_account_page_id ) {
			$url = get_permalink( $my_account_page_id );
        }
        if( empty($url) ){
            $url = $base_url . '/my-account';
        }
		$url          = $url.'/subscriptions';
		$api_key      = $this->options['api_key'];
		$user_id      = get_current_user_id();
		$access_token = apply_filters( 'appmaker_wc_set_user_access_token', $user_id );
		$url          = add_query_arg( array( 'from_app_support' => true ), $url );
		$url          = base64_encode( $url );
		$url          = $base_url . '/?rest_route=/appmaker-wc/v1/user/redirect/&url=' . $url . '&api_key=' . $api_key . '&access_token=' . $access_token . '&user_id=' . $user_id;
		$wallet       = array(
			'wallet' => array(
				'title'  => __( 'Subscriptions', 'woocommerce-subscriptions' ),
				'icon'   => array(
					'android' => 'refresh-cw',
					'ios'     => 'ios-refresh-cw-outline',
				),
				'action' => array(
					'type'   => 'OPEN_IN_WEB_VIEW',
					'params' => array( 'url' => $url ),
				),
			),
        );
        
		$return       = array_slice( $return, 0, 5, true ) +
		$wallet +
		array_slice( $return, 5, count( $return ) - 3, true );
		return $return;
    }

}
new APPMAKER_WC_woocommerce_subscription();


