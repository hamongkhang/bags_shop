<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
class APPMAKER_WC_qtranslatex
{
    public function __construct()
    {
        add_filter( 'appmaker_wc_product_attributes',array($this,'attribute_name_translation'),10,4 );
        add_filter('appmaker_product_widget_webview_content', array($this, 'product_widget_webview_translation'),10, 1 );
    }

    public function product_widget_webview_translation( $content ) {
        if( $content ) {
            $content    = qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage(  $content );
        }
        return $content;
    }

    public function attribute_name_translation($attributes, $product, $variations, $visible){

        foreach($attributes as $key => $attribute){
            if(is_array($attribute['options'])){
                foreach($attribute['options'] as $id => $option){
                    $attributes[$key]['options'][$id]['name'] = qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage($option['name']);
                }
            }
        }
        return $attributes;
    }
}
new APPMAKER_WC_qtranslatex();