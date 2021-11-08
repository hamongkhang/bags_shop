<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_PPOM
{

    public function __construct()
    {
        add_filter('appmaker_wc_product_data', array($this, 'update_product_type'), 2, 3);
    }

    public function update_product_type($data, $product, $expanded){
        
        if( $product ) {
            if( function_exists('ppom_get_product_id') ) {
                $product_id = ppom_get_product_id($product);
                $ppom		= new PPOM_Meta( $product_id );
                if ( ! $expanded && $ppom->is_exists && ! in_array($product->get_type(), array('variable', 'grouped', 'external')) ) {                              
                   $data['type'] = 'variable';                
                }
            } elseif ( function_exists('nm_get_product_id') ) {
                $selected_meta_id = get_post_meta ( nm_get_product_id($product), '_product_meta_id', true );
                if ($selected_meta_id == 0 || $selected_meta_id == 'None'){                    
                    $nmpersonalizedproduct = NM_PersonalizedProduct::get_instance();
                    if( ! $nmpersonalizedproduct -> check_meta_category(nm_get_product_id($product)) ){
                        return $data;
                    }
                }
                
                if ( ! in_array($product->get_type(), array('variable', 'grouped', 'external')) && $selected_meta_id && ! $expanded ) {
                    $data['type'] = 'variable';
                }
            }            
          
        }
        return $data;
    }
}
new APPMAKER_WC_PPOM();