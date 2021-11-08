<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_flexible_checkout_field
{


    public function __construct()
    {
        add_filter('appmaker_wc_checkout_fields', array($this, 'checkout_fields'), 10, 2);
    }
    public function checkout_fields($return,$section){
        foreach ($return as $fields => $value) {
            if( ( isset($value['required']) && $value['required'] != 1) && ( isset($value['type']) && $value['type'] != 'checkbox' && $value['type'] != 'hidden' ) ){
                $return[$fields]['label'] =  $return[$fields]['label'].' (optional)';
            }
        }
        return $return;
    }
}
new APPMAKER_WC_flexible_checkout_field();