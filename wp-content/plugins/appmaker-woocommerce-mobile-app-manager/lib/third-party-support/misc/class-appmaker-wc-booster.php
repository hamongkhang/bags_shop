<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class APPMAKER_WC_booster
{
    public $post_type='shop_order';
    private $options;
    public $plugin = 'appmaker_wc';
    public function __construct()
    {
        $this->options = get_option( $this->plugin . '_settings' );
        add_filter("woocommerce_rest_prepare_{$this->post_type}", array($this,'order_invoice'),2,2);
        add_filter( 'woocommerce_add_to_cart_redirect',array($this,'maybe_redirect_to_url_custom'), PHP_INT_MAX);
       // add_filter( 'appmaker_wc_payment_gateways_response',array($this,'payment_gateways_icon'),2,1);
       add_filter('appmaker_wc_cart_items',array($this,'product_booster_additional_fees'),2,1);
       if ( isset( $_REQUEST['currency'] ) &&  ( empty( $_REQUEST['currency'] ) || $_REQUEST['currency'] == 'null' ) ) {
        //    $obj = new WCJ_Multicurrency();
        //    $_REQUEST['currency'] = $obj->get_default_currency();
          $_REQUEST['currency'] = get_option( 'woocommerce_currency' );
       }
       if ( ! empty( $_REQUEST['currency'] ) &&  wcj_is_module_enabled( 'multicurrency' ) ) {
            $_POST['wcj-currency'] = $_REQUEST['wcj-currency'] = $_REQUEST['currency'];           
            wcj_session_set( 'wcj-currency',  $_REQUEST['currency']  );
            add_filter( 'appmaker_wc_account_page_response', array( $this, 'my_account_currency_switcher' ), 10, 1 );	                       
       }       
       
    }   

    public function my_account_currency_switcher( $return ) {

		
		$currency_switcher      = array(
			'currency_switcher' => array(
				'title'  => __( 'Change currency', 'appmaker-woocommerce-mobile-app-manager' ),
				'icon'   => array(
					'android' => 'credit-card',
					'ios'     => 'ios-card-outline',
				),
				'action' => array(
					'type' => 'OPEN_CURRENCY_PAGE',
				),
			)
		);
		$return       = array_slice( $return, 0, 5, true ) +
		$currency_switcher +
		array_slice( $return, 5, count( $return ) - 3, true );
		return $return;
	}

    public function product_booster_additional_fees($return){
          //print_r($return['fees']);exit;
          $fees_name = '';
          $fees_total = '';
          if(is_array($return['fees']) || is_object($return['fees'])){
            foreach($return['fees'] as $key => $activation_fees){
                $fees_name = $activation_fees->name;
                $fees_total = $activation_fees->total;
            }
            
            $return['additional_fee_label']= $fees_name ;
            $return['additional_fee']= $fees_total ;

          }          
          
    return $return;
     }

    public function maybe_redirect_to_url_custom() { 
        remove_filter('woocommerce_add_to_cart_redirect', array(WC_Jetpack::instance(),'maybe_redirect_to_url'));
    } 

  /*  public function payment_gateways_icon($return){
        foreach ($return['gateways'] as $gateways =>$gateway ){
            if ( 'yes' === get_option( 'wcj_gateways_icons_' . $gateway['id'] . '_icon_remove', 'no' ) ) {
                return $return;
            }
            $custom_icon_url = get_option( 'wcj_gateways_icons_' . $gateway['id'] . '_icon', '' );
            if($custom_icon_url!='') {
                $return[$gateways]['icon'] = $custom_icon_url;
            }
        }
        return $return;
    }*/

    public function order_invoice($response,$post){
        $order_id = $post->ID;
        $invoice_types_ids = wcj_get_enabled_invoice_types_ids();
        $invoice_type = '';
        if($invoice_types_ids){
            $invoice_type = $invoice_types_ids[0];
        }        
        $base_url = site_url();
        $api_key = $this->options['api_key'];
        $user_id= $user_id = get_current_user_id();
        $access_token = apply_filters( 'appmaker_wc_set_user_access_token', $user_id );
        $message = get_option('wcj_invoicing_invoice_link_text');
        $url='';
        if($invoice_type == 'invoice'){
        $url = base64_encode($base_url . '/my-account/orders/?order_id=' . $order_id . '&invoice_type_id='.$invoice_type.'&get_invoice=1');
        $url = $base_url.'/?rest_route=/appmaker-wc/v1/user/redirect/&url='.$url.'&api_key='.$api_key.'&access_token='.$access_token.'&user_id='.$user_id;
        $url = apply_filters('appmaker_invoice_download_url',$url,$order_id);
        $response->data['top_notice'][]=array(
            'icon' => array(
                'android' => 'file-download',
                'ios'     => 'ios-download-outline',
            ),
            'message'=> empty($message)?'Invoice'.' '.$order_id:$message.' '.$order_id,
            'button'=> array(
                'type'=> 'button',
                'text'=>empty($message)?'Invoice':$message,
                'action'=>array(
                    'type'   => 'OPEN_URL',
                    'params' => array( 'url' =>$url)
                )
            ),
        );
    }
        return $response;


    }

}
new APPMAKER_WC_booster();