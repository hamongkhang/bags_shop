<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_YITH_GIFT_CARDS
{

    public function __construct()
    {
      add_filter('appmaker_wc_product_data',array($this,'yith_gift_product_price'), 2, 3 );
    }

    public function yith_gift_product_price( $data, $product, $expanded ){

        $product_id  = (int) $product->is_type( 'variation' ) ? $product->get_variation_id() : APPMAKER_WC_Helper::get_id( $product );        

        if( $product->get_type() === 'gift-card' ){

            $amounts = $product->get_amounts_to_be_shown ();
            if ( ! count ( $amounts ) ) {
				$price = $data['price'];
			} else {
				ksort ( $amounts, SORT_NUMERIC );

				$min_price = current ( $amounts );
				$min_price = $min_price['price'];
				$max_price = end ( $amounts );
				$max_price = $max_price['price'];
               
                $price     = $min_price !== $max_price ? sprintf( _x( '%1$s-%2$s', '', 'woocommerce' ), $min_price ,  $max_price ) : $min_price ;	
                $price_display    = $min_price !== $max_price ? sprintf( _x( '%1$s-%2$s', '', 'woocommerce' ), APPMAKER_WC_Helper::get_display_price( $min_price ), APPMAKER_WC_Helper::get_display_price( $max_price ) ) : APPMAKER_WC_Helper::get_display_price( $min_price );			
				//$price     = apply_filters ( 'yith_woocommerce_gift_cards_amount_range', $price, $product );

			}             
            
            $data['price'] =  $price;
            $data['price_display'] = $price_display;
            
        }

        return $data;
    }
}
new APPMAKER_WC_YITH_GIFT_CARDS();
