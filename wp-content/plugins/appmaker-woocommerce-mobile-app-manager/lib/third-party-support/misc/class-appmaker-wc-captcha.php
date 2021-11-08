<?php
/**
 * Created by IntelliJ IDEA.
 * User: shifa
 * Date: 8/13/18
 * Time: 6:30 PM
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_captcha{


    public function __construct()
    {

        remove_filter('woocommerce_registration_errors', array('WC_Ncr_Registration_Captcha', 'validate_captcha_wc_registration'), 10);
        remove_action( 'woocommerce_process_login_errors', array( __CLASS__, 'validate_login_captcha'));
    }



}
new APPMAKER_WC_captcha();