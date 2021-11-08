<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_knawat_image{

	public function __construct() {
        
        add_filter('appmaker_wc_product_image_url',array($this,'product_image_url'),2,2);
	}
	
	public function product_image_url( $image , $size ) {

        global $knawatfibu;
        //Remove query string from image url
       // $pos = strpos($image['url'], "?");
        //$image['url'] = substr($image['url'], 0, $pos);
        $image['url'] = $knawatfibu->common->knawatfibu_resize_image_on_the_fly( $image['url'], $size ); 
        return $image;      

    }
}
new APPMAKER_WC_knawat_image();