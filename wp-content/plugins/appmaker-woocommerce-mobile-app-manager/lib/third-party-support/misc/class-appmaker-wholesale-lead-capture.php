<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_WHOLESALE_LEAD{


    public function __construct()
    {
        add_filter( 'wwlc_login_redirect_url', '__return_false', 9999 );
        
    }
}
new APPMAKER_WC_WHOLESALE_LEAD();