<?php
/**
 * Created by IntelliJ IDEA.
 * User: shifa
 * Date: 10/23/18
 * Time: 3:28 PM
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
class APPMAKER_WC_Gateway_amazonpay {
    public function __construct() {

        remove_action( 'woocommerce_checkout_init', array($GLOBALS['wc_amazon_payments_advanced'] ,'checkout_init') );

    }
}
new APPMAKER_WC_Gateway_amazonpay();