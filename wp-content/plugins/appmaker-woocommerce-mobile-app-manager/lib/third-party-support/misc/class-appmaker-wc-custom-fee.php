<?php
/**
 * Created by IntelliJ IDEA.
 * User: shifa
 * Date: 1/24/19
 * Time: 7:40 PM
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_custom_fee
{

    public function __construct()
    {
        add_filter('appmaker_wc_cart_items',array($this,'custom_fee'),2,1);
    }

    public function custom_fee($return){

       $cart_total=WC()->cart->subtotal;
        $minimum = get_option('wacf_minimum' , 0 ) ;
        $minimum = floatval(str_replace(',', '',  $minimum));
        $maximum = get_option('wacf_maximum', 0);
        $maximum = floatval(str_replace(',', '',  $maximum));
        if(($maximum!=0 && $cart_total <= $maximum) || ($minimum!=0 && $cart_total >= $minimum)){
            $return['additional_fee_label']=get_option('wacf_fee_label', __('Custom Fee', 'wacf'));
            $return['additional_fee']= APPMAKER_WC_Helper::get_display_price(get_option('wacf_fee_charges', 0));
        }
        return $return;
    }

}
new APPMAKER_WC_custom_fee();