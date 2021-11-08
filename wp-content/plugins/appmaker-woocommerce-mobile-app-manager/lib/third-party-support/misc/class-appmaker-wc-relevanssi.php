<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

// class APPMAKER_WC_RELEVANSSI_SEARCH
// {

//     public function __construct()
//     {       
//          add_filter('appmaker_wc_product_query_result',array($this,'product_query_result'),2,2);

//     }    

//     public function product_query_result( $query_result, $query_args )
//     {       
       
//         $search_query = new WP_Query( $query_args );            
//         $query_result = relevanssi_do_query($search_query);         
//         return $query_result;

//     }
// }
// new  APPMAKER_WC_RELEVANSSI_SEARCH();