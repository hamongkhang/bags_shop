<?php


if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_shipcloud {


    public function __construct() {
        remove_action( 'woocommerce_shipping_calculator_enable_city', array( 'WC_Shipcloud_Shipping', 'add_calculate_shipping_form_fields' ) );
        remove_action( 'woocommerce_calculated_shipping', array( 'WC_Shipcloud_Shipping', 'add_calculate_shipping_fields' ) );
    }
}
new APPMAKER_WC_shipcloud();