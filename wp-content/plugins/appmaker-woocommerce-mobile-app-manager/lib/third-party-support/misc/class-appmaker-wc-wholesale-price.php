<?php
/**
 * Created by IntelliJ IDEA.
 * User: shifa
 * Date: 22/6/18
 * Time: 12:47 PM
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

define( 'DOING_AJAX', true );

class APPMAKER_WC_wholesale_price{

    public function __construct() {
        add_filter( 'appmaker_wc_product_data', array( $this, 'product_wholesale_price' ), 2, 2 );
        add_filter( 'appmaker_wc_rest_prepare_product', array($this, 'variable_product_wholesale_price'), 2, 3);
    }


    public function product_wholesale_price( $data, $product )
    {

        $user_wholesale_role = null;
        if (is_null($user_wholesale_role))
            $user_wholesale_role = WWP_Wholesale_Roles::getInstance()->getUserWholesaleRole();

        if (!empty($user_wholesale_role) && !empty($data['price'])) {

            $price_arr = WWP_Wholesale_Prices::get_product_wholesale_price_on_shop_v3(WWP_Helper_Functions::wwp_get_product_id($product), $user_wholesale_role);

            $raw_wholesale_price = $price_arr['wholesale_price'];
            if (!empty($raw_wholesale_price)){

                $data['price'] = $raw_wholesale_price;
                $data['price_display'] = APPMAKER_WC_Helper::get_display_price($raw_wholesale_price);
                if ( get_option('wwpp_settings_hide_original_price') !== "yes") {
                    $data['on_sale']       = true;
                }
           
        }

    }

        return $data;
    }

    public function variable_product_wholesale_price( $data, $post, $request ) {
      
        $product = wc_get_product( $post->ID );    
        $user_wholesale_role = null;
        if (is_null($user_wholesale_role))
            $user_wholesale_role = WWP_Wholesale_Roles::getInstance()->getUserWholesaleRole();  
        $min_price = 0;
        $max_price = 0;
        if ( $product->is_type( 'variable' ) && !empty( $data['variations'] ) && !empty( $user_wholesale_role ) ) {

            foreach( $data['variations'] as $item => $item_data ) {
                $price_arr = WWP_Wholesale_Prices::get_product_wholesale_price_on_shop_v3( $item_data['id'], $user_wholesale_role);

               $raw_wholesale_price = $price_arr['wholesale_price'];
               if (! empty( $raw_wholesale_price ) ){                    
                   
                    $min_price  = ($min_price == 0 )? $raw_wholesale_price : $min_price;
                    $min_price  =  ( $min_price < $raw_wholesale_price ) ? $min_price : $raw_wholesale_price;
                    $max_price  =  ( $max_price > $raw_wholesale_price ) ? $max_price  : $raw_wholesale_price;
                    $item_data['price']  = $raw_wholesale_price ;
                    $item_data['price_display'] =  APPMAKER_WC_Helper::get_display_price($raw_wholesale_price);                  
                    $data['variations'][$item]['price_display'] = $add_label ? 'Price: '.$item_data['price_display'] : $item_data['price_display'];
                    if ( get_option('wwpp_settings_hide_original_price') !== "yes") {
                        $data['variations'][$item]['on_sale'] = true;
                    }
               }
            }
            $data['price']            = $min_price !== $max_price ? sprintf( _x( '%1$s-%2$s', '', 'woocommerce' ), $min_price ,  $max_price ) : $min_price ;
            $data['price_display']    = $min_price !== $max_price ? sprintf( _x( '%1$s-%2$s', '', 'woocommerce' ), APPMAKER_WC_Helper::get_display_price( $min_price ), APPMAKER_WC_Helper::get_display_price( $max_price ) ) : APPMAKER_WC_Helper::get_display_price( $min_price );
            if ( get_option('wwpp_settings_hide_original_price') !== "yes") {
                $data['on_sale'] = true;
            }
        }
       
        return $data;
    }


}
new APPMAKER_WC_wholesale_price();
