<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_flexible_shipping
{

    const WEIGHT_ROUNDING_PRECISION = 6;

    public function __construct()
    {
        add_filter('appmaker_wc_cart_items', array($this, 'add_shipping_rate'), 10, 1);
    }

    public function add_shipping_rate( $return ){
        
       // print_r(WC()->cart->get_cart());exit;
       if( class_exists('WPDesk_Flexible_Shipping') ) {
            if( method_exists('WPDesk_Flexible_Shipping','cart_weight') ) {
                $WPDesk_Flexible_Shipping                        = new WPDesk_Flexible_Shipping();
                $total_weight                                    = $WPDesk_Flexible_Shipping->cart_weight();
            } else {
                $total_weight = $this->cart_weight();
            }                      
            $return['price_details']                         = array();
            $hide_weight = APPMAKER_WC::$api->get_settings('hide_cart_weight', true );
            if( $total_weight && !$hide_weight ) {
                $return['price_details'][]                   = array('label' => 'Total Weight' ,'value' => wc_format_weight( $total_weight ));
            }
       }      
       $shipping_packages                               = WC()->shipping()->calculate_shipping( WC()->cart->get_shipping_packages() ); 
       $shipping_methods_title = __( 'Shipping', 'woocommerce' );       
       $hide_shipping = APPMAKER_WC::$api->get_settings('hide_shipping_in_cart', true );
       if ( is_array( $shipping_packages ) &&  WC()->cart->needs_shipping() && ! $hide_shipping ) {
            foreach ( $shipping_packages as $shipping_package ) {
                if ( isset( $shipping_package['rates'] ) ) {
                    foreach ($shipping_package['rates'] as $package ){                       
                        $return['price_details'][] = array('label' => $shipping_methods_title , 'value' => $package->label.':'.APPMAKER_WC_Helper::get_display_price($package->cost) );

                    }
                }
            }
           //$return['shipping_methods'] = $methods;
        }

        return $return;
    }
    
    public function cart_weight() {
		if ( version_compare( WC_VERSION, '2.7', '<' ) ) {
			add_filter( 'woocommerce_product_weight', array( $this, 'woocommerce_product_weight' ) );
		}
		$cart_weight = WC()->cart->get_cart_contents_weight();
		if ( version_compare( WC_VERSION, '2.7', '<' ) ) {
			remove_filter( 'woocommerce_product_weight', array( $this, 'woocommerce_product_weight' ) );
		}
		return round( $cart_weight, apply_filters( 'flexible_shipping_weight_rounding_precision', self::WEIGHT_ROUNDING_PRECISION ) );
	}
    
    /* Fix for Woocommerce 2.6 weight calculation */
		/* PHP Warning:  A non-numeric value encountered in /wp-content/plugins/woocommerce/includes/class-wc-cart.php on line 359 */
    public function woocommerce_product_weight( $weight ) {
        if ( $weight === '' ) {
            return 0;
        }
        return $weight;
    }

}
new APPMAKER_WC_flexible_shipping();