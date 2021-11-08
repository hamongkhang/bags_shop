<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_Google_captcha{


    public function __construct()
    {
        add_filter('gglcptch_limit_attempts_check', array($this, 'Captcha_Validate'), 10, 2);
    }


    public function Captcha_Validate($return, $request )
    {
        return array(
            'response' => true,
            'reason'   => ''
        );
    }
}
new APPMAKER_WC_Google_captcha();
