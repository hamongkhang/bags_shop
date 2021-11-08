<?php


if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_city_select{


    public function __construct() {
      //  add_filter( 'appmaker_wc_checkout_fields', array( $this, 'checkout_fields' ),10, 2 );
        add_action( 'appmaker_wc_dynamic_form_response', array( $this, 'dynamic_form_response' ), 10, 3 );
        add_filter( 'appmaker_wc_dependency_billing_city', array(
			$this,
			'city_dependency',
        ), 10, 2 );
        add_filter( 'appmaker_wc_dependency_shipping_city', array(
			$this,
			'city_dependency',
		), 10, 2 );
    }

    public function dynamic_form_response( $response,$fields,$section) {
     
        if('billing' === $section || 'shipping' === $section) {                  
           
            $response['items'][ $section . '_city' ]['type'] = 'dependent-select';
			$response['items'][ $section . '_city' ]['options'] = self::get_city();
            $response['items'][ $section . '_city' ]['dependent'] = true;
            
        }

        return $response;
    }


    public function checkout_fields( $fields,$section) {
     
        if('billing' === $section || 'shipping' === $section) {           
           
            $fields[ $section . '_city' ]['type'] = 'dependent-select';
			$fields[ $section . '_city' ]['options'] = self::get_city();
            $fields[ $section . '_city' ]['dependent'] = true;
            
        }

        return $fields;
    }

    
	public function city_dependency( $dependency, $key ) {
		if ( 'billing_city' === $key ) {
			$dependency = array( 'on' => 'billing_state' );
		} elseif ( 'shipping_city' === $key ) {
			$dependency = array( 'on' => 'shipping_state' );
		}
		return $dependency;
	}

    public function get_city() {

        $obj  = new WC_City_Select();
        $data =  $obj->get_cities();
        $return    = array();
        //print_r($cities);exit;
        if(is_array($data)){
            foreach ($data as $country => $states) {            
                foreach ($states as $key => $cities ) {  
                    
                 $return[$key] = array(
                     'items' => array(),
                 );  
                 foreach ($cities as $id => $city) {
                     $return[ $key ]['items'][ $city ] = $city;
                 }   
                 
                }
             }
        }
        
        return $return;
    }

}

new APPMAKER_WC_city_select();
