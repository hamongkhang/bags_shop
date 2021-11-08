<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_Order_Approval
{
    public $post_type='shop_order';
    public function __construct()
    {
       add_filter("woocommerce_rest_prepare_{$this->post_type}", array($this,'order_approval'),2,2);

    }
    public function order_approval($response,$post){
        
        $order     = wc_get_order(($response->data['id']));
        $message = '';
        $order_status = $order->get_status();
        if('approval-waiting' == $order_status ){
           $message = 'Checking cart contents. Order will soon be approved';
        }else if('approved' == $order_status){
            $message = 'Good news! Your order has been approved!';
        }else if('rejected' == $order_status ){
            $message = 'Your order has been rejected.';
        }
        if(!empty($message)){

            $response->data['top_notice'][] = array(
                'icon' => array(
                     'android' => 'info',
                     'ios'     => 'info',
                 ),
                 'message'=>strip_tags($message),    
                 'button'=> false,   
             ); 
        }           

        return $response;

    }

}
new APPMAKER_WC_Order_Approval();
