<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_TRANSLATEPRESS{


    public static function init()
    {
        
        global $TRP_NEEDED_LANGUAGE,$TRP_LANGUAGE;
        
        if ( ! empty( $_REQUEST['language'] ) && $_REQUEST['language'] != 'default' ) {
			$language = $_REQUEST['language'];
		} elseif ( isset( $_GET['from_app'] ) || ( !empty($_GET['rest_route']) && false != strpos($_SERVER['REQUEST_URI'], 'appmaker-wc') ) ){
			$language = APPMAKER_WC::$api->get_settings( 'default_language', 'default' );
		} else {
			$language = false;
		}       
        if ( ! empty( $language ) ) {
            //$_POST['action'] = 'trp_get_translations_regular';
           // $_REQUEST[ 'trp-form-language' ] = $language;
            $_POST['language'] = $language;
            $TRP_NEEDED_LANGUAGE = $TRP_LANGUAGE = $language ;
            $trp = TRP_Translate_Press::get_trp_instance();
            $translation_render = $trp->get_component( 'translation_render' );
            $translation_render->add_callbacks_for_translating_rest_api();
            
            add_filter('appmaker_wc_product_data', array('APPMAKER_WC_TRANSLATEPRESS', 'product_translation'), 2, 3);
            add_filter( 'appmaker_wc_product_widgets', array( 'APPMAKER_WC_TRANSLATEPRESS', 'product_widget_translations' ), 2, 2 );   
            add_filter( 'appmaker_wc_product_attributes',array('APPMAKER_WC_TRANSLATEPRESS','attribute_name_translation'),10,4 );
            add_filter( 'appmaker_wc_cart_items',array('APPMAKER_WC_TRANSLATEPRESS','product_name_cart_translation'),10,1 );
        }
        
    }

    public function product_translation( $data, $product, $expanded ) {

        $trp = TRP_Translate_Press::get_trp_instance();
        $translation_render = $trp->get_component( 'translation_render' );
        $data['name'] = $translation_render->translate_page($data['name']);
        return $data;
    }

    public function product_widget_translations ( $return,$product ) {

        $product_tabs = apply_filters( 'woocommerce_product_tabs', array() );
        $product_tabs = apply_filters( 'appmaker_wc_product_tabs', $product_tabs );
        $trp = TRP_Translate_Press::get_trp_instance();
        $translation_render = $trp->get_component( 'translation_render' );
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
        foreach ( $tabs as $key => $tab ) {
            $return[$key]['title'] = $translation_render->translate_page(  $tab['title'] );
        }
        return $return;
    }

    public function attribute_name_translation($attributes, $product, $variations, $visible){
        $trp = TRP_Translate_Press::get_trp_instance();
        $translation_render = $trp->get_component( 'translation_render' );
        foreach($attributes as $key => $attribute){
            $attributes[$key]['name'] = $translation_render->translate_page($attribute['name']);
            if(is_array($attribute['options'])){
                foreach($attribute['options'] as $id => $option){
                    $attributes[$key]['options'][$id]['name'] = $translation_render->translate_page($option['name']);
                }
            }
        }
        return $attributes;
    }

    public function product_name_cart_translation( $return ) {
        $trp = TRP_Translate_Press::get_trp_instance();
        $translation_render = $trp->get_component( 'translation_render' );
         foreach($return['products'] as $key =>$field){            
            $return['products'][$key]['product_title'] = $translation_render->translate_page($field['product_title']);          
        }
        return $return;
    }

}
APPMAKER_WC_TRANSLATEPRESS::init();