<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
class APPMAKER_WC_Woo_Wallet
{
    private $options;
    public function __construct()
    {
        $this->options = get_option('appmaker_wc_settings');
        add_filter( 'appmaker_wc_account_page_response', array($this,'my_account_woo_wallet'),10,1 );
        add_filter( 'appmaker_wc_cart_meta_response', array( $this, 'updateCartMeta' ) );
    }
    public function my_account_woo_wallet($return){

        $base_url = site_url();
//        $url = $base_url.'/my-account/woo-wallet';
        $url = esc_url( wc_get_account_endpoint_url( get_option( 'woocommerce_woo_wallet_endpoint', 'woo-wallet' ) ) );
        $url = apply_filters('appmaker_wc_woo-wallet-redirect-url', $url );
        $api_key = $this->options['api_key'];
        $user_id =  get_current_user_id();
        $access_token = apply_filters( 'appmaker_wc_set_user_access_token', $user_id );
        $url = add_query_arg( array( 'from_app_wallet' => true ), $url);
        if (strpos($url,'from_app_wallet') != false) {
            $expire = time() + 60 * 60 * 24 * 30;
            wc_setcookie( 'from_app_wallet_set', 1, $expire, false );
        }
        $url = base64_encode($url);
        $url = $base_url.'/?rest_route=/appmaker-wc/v1/user/redirect/&url='.$url.'&api_key='.$api_key.'&access_token='.$access_token.'&user_id='.$user_id;
        $wallet = array('wallet'=>array(
            'title'  =>apply_filters('woo_wallet_account_menu_title', __('My Wallet', 'woo-wallet')),
            'icon'   => array(
                'android' => 'account-balance-wallet',
                'ios'     => 'ios-wallet',
            ),
            'action' => array(
                'type' => 'OPEN_IN_WEB_VIEW',
                'params' => array( 'url' =>$url),
            ),
        ),
        );
        $return = array_slice($return, 0, 3, true) +
            $wallet +
            array_slice($return, 3, count($return)-3, true);
        return $return;
    }

    public function updateCartMeta( $meta )
    {
        if (woo_wallet()->cashback->calculate_cashback() && !is_wallet_rechargeable_cart() && apply_filters('display_cashback_notice_at_woocommerce_page', true)) {
            $cashback_amount = woo_wallet()->cashback->calculate_cashback();
            if (is_user_logged_in()) {
                $meta['header_message'] = apply_filters('woo_wallet_cashback_notice_text', sprintf(__('Upon placing this order a cashback of %s will be credited to your wallet.', 'woo-wallet'), APPMAKER_WC_Helper::get_display_price($cashback_amount)), $cashback_amount);
            } else {
                $meta['header_message'] =  apply_filters('woo_wallet_cashback_notice_text', sprintf(__('Please log in to avail %s cashback from this order.', 'woo-wallet'),  APPMAKER_WC_Helper::get_display_price($cashback_amount)), $cashback_amount);
            }

        }
        if(!empty($meta['coupon_discounted']) && !empty($meta['coupons_applied'])){

            foreach( $meta['coupon_discounted'] as $key => $coupon){
                $coupon = $coupon['coupon'];
                $the_coupon = new WC_Coupon( $coupon );
                $_is_coupon_cashback = get_post_meta( $the_coupon->get_id(), '_is_coupon_cashback', true);
                $_is_coupon_cashback = ( 'yes' === $_is_coupon_cashback ) ? true : false;
                $meta['coupon_discounted'][$key]['is_coupon_cashback'] =  $_is_coupon_cashback;
                //$meta['total_display'] = APPMAKER_WC_Helper::get_display_price( $meta['total'] );
            }

        }
        return $meta;
    }

}
new APPMAKER_WC_Woo_Wallet();