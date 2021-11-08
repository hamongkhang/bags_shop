<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_Unit_Of_Measure
{

    public function __construct()
    {
        add_filter('appmaker_wc_product_data', array($this, 'appmaker_unit_of_measure'), 2, 3);
        add_filter( 'appmaker_wc_rest_prepare_product', array($this, 'appmaker_variation_unit_of_measure'), 2, 3);
    }

    public function appmaker_unit_of_measure($data, $product, $expanded){
        
        $woo_uom_output = get_post_meta( $product->get_id(), '_woo_uom_input', true );
        $add_label      = apply_filters('appmaker_add_price_rtl_label', false );
        if( $woo_uom_output ){
            $data['price_display'] =  $add_label ? 'Price: '.$data['price_display'].' '.$woo_uom_output : $data['price_display'].' '.$woo_uom_output;
        }
        return $data;
    }

    public function appmaker_variation_unit_of_measure( $data, $post, $request ) {
       
        $product = wc_get_product( $post->ID );
        $woo_uom_output = get_post_meta( $post->ID, '_woo_uom_input', true );   
        $add_label      = apply_filters('appmaker_add_price_rtl_label', false );     
        if ( $product->is_type( 'variable' ) && !empty( $data['variations'] ) ) {

            foreach( $data['variations'] as $item => $item_data ) {
                $item_data['price_display'] =  $item_data['price_display'].' '. $woo_uom_output;
                $data['variations'][$item]['price_display'] = $add_label ? 'Price: '.$item_data['price_display'] : $item_data['price_display']  ;
            }
        }
        return $data;
    }
}
new APPMAKER_WC_Unit_Of_Measure();