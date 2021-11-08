<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_Estimated_Delivery {

	public function __construct() {

		add_filter( 'appmaker_wc_product_tabs', array( $this, 'estimated_delivery_tabs' ), 2, 1 );
        add_filter( 'appmaker_wc_product_widgets', array( $this, 'estimated_delivery_widget' ), 2, 2 );  
        add_filter('appmaker_wc_product_data', array($this, 'estimate_on_product_list_page'), 2, 3 );    
		
    }

    public function estimate_on_product_list_page( $data, $product, $expanded ) {
        
        
        $product_id = $product->get_id();
        if( class_exists('Pi_Edd_Product_Rule') ){

            $rule_obj = new Pi_Edd_Product_Rule($product_id);
            $msg  = $rule_obj->getCompiledMessageAsPerLocation('loop');
            $labels[]  = array('label' => $msg );
            
            if( isset( $data['labels'] ) ) {
                $data['labels'] = empty($data['labels']) ? $labels : array_merge( $data['labels'], $labels );
            }

        } 
        return $data;        
    }
    
    public function estimated_delivery_tabs( $tabs ) {

        global $product;
        
        if(!empty($product)){                      
 
              $tabs['pi-edd-product'] = array(
                  'title'    => __( 'Note', 'appmaker-woocommerce-mobile-app-manager' ),
                  'priority' => 2,
                  'callback' => 'woocommerce_product_description_tab',
              );                     
  
          }
      
      return $tabs; 
    }

    public function estimated_delivery_widget( $return, $product_local ) {
     
        global $product_obj,$product;
        $product_obj = $product_local;
        $product     = $product_local;
        $product_id = $product->get_id();

		$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );
        $product_tabs = apply_filters( 'appmaker_wc_product_tabs', $product_tabs );
        $widgets_enabled_in_app = APPMAKER_WC::$api->get_settings( 'product_widgets_enabled', array() );            
        if ( ! empty( $widgets_enabled_in_app ) && is_array( $widgets_enabled_in_app ) ) {
            foreach($widgets_enabled_in_app as $id){
                if(array_key_exists($id,$product_tabs)){
                    $tabs[$id] = $product_tabs[$id];
                }
            }
        }else{
            $tabs = $product_tabs;
        }      
        $content = '';

        $pi_Edd = new Pi_Edd();
        $plugin_name = $pi_Edd->get_plugin_name();
        $version =  $pi_Edd->get_version();
        
        if ( class_exists('Pi_Edd_Public') ){
            $plugin_public = new Pi_Edd_Public(  $plugin_name, $version );       
            $plugin_public->initialize();
            //$plugin_public->user_selection();
            // echo $plugin_public->delivery_estimate;
            // $plugin_public->estimated_date = date($plugin_public->calc_date_format, strtotime(' + '.$plugin_public->delivery_estimate.' days'));

            ob_start();
            $plugin_public->estimate_on_product_page();
            $content  = ob_get_clean();
        } elseif ( class_exists('Pi_Edd_Product_Rule') ) {
            $rule_obj = new Pi_Edd_Product_Rule($product_id);
            $content  = $rule_obj->getCompiledMessageAsPerLocation('single');
        } elseif( class_exists ('pisol_edd_single_product_page') ) {
            $obj = new pisol_edd_single_product_page();
            $obj->shipping_method_settings = $obj->getShippingSetting();
            ob_start();
            $obj->estimate();
            $content = ob_get_clean();                  
        }
        // elseif( class_exists('Pi_Edd_Product_Controller') ){
            
        //     $Pi_Edd_Product_Controller = new Pi_Edd_Product_Controller();
        //     ob_start();
        //     $Pi_Edd_Product_Controller->estimateOnProductPage();
        //     $estimate_on_product_page  =  ob_get_clean();print_r($estimate_on_product_page);
        //     preg_match_all( '/<div(.*?)<\/div>/i',  $estimate_on_product_page , $msg );
        //     if ( is_array( $msg[1] ) && ! empty( $msg[1] ) ) {
        //         foreach ( $msg[1] as $key => $message ) {
        //             $content = $message;
        //         }
        //     }            
        // }
        
        foreach($tabs as $key => $tab){

            if(!empty($content) && !$product->is_type( 'variable' )){
                //$title   = APPMAKER_WC::$api->get_settings( 'product_tab_field_title_'.$key );
                if ( 'pi-edd-product' === $key ) { 
                    $return['pi-edd-product'] = array(
						'type'       => 'menu',
						'expandable' => false,
						'expanded'   => false,
						'title'      => strip_tags(html_entity_decode($content)),
						'content'    => '',
                        'action'     => array('type' => 'NO_ACTION','params' => array()),
                    );
                }       
            }else {
                unset($return['pi-edd-product']);
            }
                
        }        
        
		return $return;
    }
}
new APPMAKER_WC_Estimated_Delivery();


