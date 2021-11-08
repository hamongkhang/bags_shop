<?php
/**
 * Created by IntelliJ IDEA.
 * User: shifa
 * Date: 4/5/19
 * Time: 12:12 PM
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_invisible_captcha{


    public function __construct()
    {
        remove_filter( 'woocommerce_registration_errors', array(anr_captcha_class::init(),'wc_registration_verify'),10);

    }

}
new APPMAKER_WC_invisible_captcha();