<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
class APPMAKER_WC_RANDOM_SORTING
{
    public function __construct()
    {
        add_filter('appmaker_wc_set_sort', array($this, 'appmaker_change_products_order'),10,1 );
    }

    public function appmaker_change_products_order($orderby){
		
        //if( empty($orderby) && $orderby == 'default') {
            $orderby = 'random_order';
            $_GET['orderby'] = 'random_order';
            return $orderby;
        //}		
	}
   
}
new APPMAKER_WC_RANDOM_SORTING();