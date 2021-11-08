<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_VarKTech
{

    public function __construct()
    {
      add_filter('appmaker_wc_product_data',array($this,'varktech_single_product_price'), 2, 3 );
    }

    public function varktech_single_product_price( $data, $product, $expanded ){

        $product_id  = (int) $product->is_type( 'variation' ) ? $product->get_variation_id() : APPMAKER_WC_Helper::get_id( $product );
        // if( $product->get_type() === 'variable' ){
        //     if(isset($_SESSION['vtprd_product_session_price_'.$product_id])) {
        //         $data['price'] = $_SESSION['vtprd_product_session_price_'.$product_id]['varParent_current_price_low'];
        //         $data['regular_price'] = $_SESSION['vtprd_product_session_price_'.$product_id]['varParent_current_price_high'];
        //         $data['price_display'] = APPMAKER_WC_Helper::get_display_price( $data['price']);
        //         $data['regular_price_display'] = APPMAKER_WC_Helper::get_display_price( $data['regular_price']);
        //     }

        // }
        if( $product->get_type() !== 'variable' ){
            if (isset($_SESSION['vtprd_product_old_price_'.$product_id])) {    
                $data['price'] =  $_SESSION['vtprd_product_old_price_'.$product_id]['single_product_discount_price'];
                $data['price_display'] = APPMAKER_WC_Helper::get_display_price( $data['price']);
            }
        }

        return $data;
    }
}
new APPMAKER_WC_VarKTech();
