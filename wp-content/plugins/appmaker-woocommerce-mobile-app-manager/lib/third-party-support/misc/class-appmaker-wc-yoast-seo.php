<?php
/**
 * Created by IntelliJ IDEA.
 * User: shifa
 * Date: 8/7/18
 * Time: 2:17 PM
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_yoast_seo{

    public function __construct()
    {

        add_filter( 'yoast_local_seo_enhanced_search_enabled', '__return_false' );

    }

   
}
new APPMAKER_WC_yoast_seo();