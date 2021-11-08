<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_Vendors {

	public function __construct() {
		add_filter( 'appmaker_wc_product_widgets', array( $this, 'add_vendor_name' ), 10, 3 );
		add_filter( 'appmaker_wc_product_tabs', array($this,'new_product_tab' ),2,1);
    }

    public function new_product_tab($tabs){
        global $product;       
        
        if( ! isset( $tabs['seller_info'] ) ) {           
               $tabs['seller_info'] = array(
                   'title' => __("Store Name", "dokan-lite"),
                   'priority' => 2,
                   'callback' => '',
               );
           
        }
       
        return $tabs;
    }

	/**
	 * @param $return
	 * @param WC_Product $product
	 * @param $data
	 *
	 * @return mixed
	 */
	public function add_vendor_name( $return, $product, $data ){
		// Add sold by to product loop before add to cart

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
		
        foreach ( $tabs as $key => $tab ) {
			if( WC_Vendors::$pv_options->get_option( 'sold_by' ) && $key == 'seller_info'  ) {
            
				$vendor_id     = WCV_Vendors::get_vendor_from_product( $product->get_id() );
				$sold_by_label = WC_Vendors::$pv_options->get_option( 'sold_by_label' );
				$sold_by = WCV_Vendors::is_vendor( $vendor_id ) ?  WCV_Vendors::get_vendor_sold_by( $vendor_id ) : get_bloginfo( 'name' );
	
				$return['seller_info'] = array(
					'type'  => 'menu',
					'title' => apply_filters('wcvendors_sold_by_in_loop', $sold_by_label ).' '.$sold_by,
	
					'action' => array(
						'type'   => 'LIST_PRODUCT',
						'params' => array(
							'author'  => $vendor_id,
							'title' => $sold_by
						),
					)
				);
			}
		}
		
		return $return;
	}
}

new APPMAKER_WC_Vendors();
