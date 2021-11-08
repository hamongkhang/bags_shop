<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_wcam {

	public function __construct() {

		add_filter( 'appmaker_wc_product_tabs', array( $this, 'wcam_tabs' ), 2, 1 );
        add_filter( 'appmaker_wc_product_widgets', array( $this, 'wcam_widget' ), 2, 2 );      
		
    }
    
    public function wcam_tabs( $tabs ) {

        global $product;
        
        if(!empty($product)){                      
 
              $tabs['wcam'] = array(
                  'title'    => __( 'Note', 'appmaker-woocommerce-mobile-app-manager' ),
                  'priority' => 2,
                  'callback' => 'woocommerce_product_description_tab',
              );                     
  
          }
      
      return $tabs; 
    }

    public function wcam_widget( $return, $product_local ) {
     
        global $product_obj,$product;
        $product_obj = $product_local;
        $product     = $product_local;
        
		$tabs    = apply_filters( 'woocommerce_product_tabs', array() );
        $tabs    = apply_filters( 'appmaker_wc_product_tabs', $tabs );       
        $content = '';

        $wcam    = new WCAM_Messages();
        $messages = $wcam->get_messages();
        if(!empty( $messages)){

            foreach ($messages as $message ){ 

                $location = get_post_meta( $message->ID, '_location', true );
                if(strpos($location, 'woocommerce_single_product') !== false){
    
                    $wcam = new WCAM_Messages();                   
                    $conditions    = get_post_meta( $message->ID, '_wcam_advanced_message_conditions', true );
                    if ( false !== wpc_match_conditions( (array) $conditions, array( 'context' => 'wcam' ) ) ){
                        $content = $wcam->get_message($message->ID);
                    }
    
                }
    
            }
        }
       
        foreach($tabs as $key => $tab){

            $title   = APPMAKER_WC::$api->get_settings( 'product_tab_field_title_'.$key );
            if ( 'wcam' === $key ) { 
                $return['wcam'] = array(
                    'type'          => 'text',
                    'title'         => empty( $title ) ? 'Note' : $title,
                    'expandable'    => false,
                    'expanded'      => true,
                    'content'       => $content,				
                    'default_value' => '',
                );
            }            
        }
        
        
		return $return;
    }
}
new APPMAKER_WC_wcam();


