<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_EXTENDED_SEARCH
{

    public function __construct()
    {
         add_filter('appmaker_wc_rest_product_query',array($this,'add_search'),2,2);        

    }

    public function add_search( $args, $request ) {
        if(isset($request['search'])) {
			$_REQUEST['wpessid'] = true;
        }
        return $args;
    }
    
}
new  APPMAKER_WC_EXTENDED_SEARCH();