<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_simulador{

    public function __construct() {

        add_filter( 'wcsp_ticket_visibilty', array( $this, 'ticket_visiblity' ), 2, 2  );
        add_filter( 'appmaker_wc_product_tabs',array( $this, 'price_tab' ), 2, 1);
        add_filter('appmaker_wc_product_widgets', array($this, 'product_price_html'), 2, 2);
    }

    public function price_tab($tabs){
        if(!isset($tabs['price_tab'])) {
            $tabs['price_tab'] = array(
                'title' => __('Installment', 'appmaker-woocommerce-mobile-app-manager'),
                'priority' => 3,
                'callback' => 'woocommerce_product_description_tab'
            );
        }
        return $tabs;  /* Return all  tabs including the new New Custom Product Tab  to display */
    }

    public function ticket_visiblity($visibility, $product){
        $visibility = 'both';
        return $visibility;
    }

    public function product_price_html($return,$product ) {
        $tabs    = apply_filters( 'woocommerce_product_tabs', array() );
        $tabs    = apply_filters( 'appmaker_wc_product_tabs', $tabs);
        $content = str_replace('no boleto', '&nbsp;no boleto', wpautop( do_shortcode($product->get_price_html())));
       //$content  = wpautop( do_shortcode($product->get_price_html()));
       /*$content  ='<style>'.
       ' del {text-decoration: line-through;margin-right: 10px;font-style: italic; opacity: 0.5; }'.
      '  .best-value,.wc-simulador-parcelas-detalhes-valor {display: block;font-weight: bold;font-style: italic;}'.
      '</style>'.$content;*/

      $content = preg_replace ('/<del>(.*?)<\/del>/i', '', $content);

       foreach ( $tabs as $key => $tab ) {
            if ( $key == 'price_tab' ) {
                $return['price_tab'] = array(
                    'type' => 'text',
                    'title' =>__('Installment', 'appmaker-woocommerce-mobile-app-manager'),
                    'expandable' => true,
                    'expanded' => true,
                    'content' =>$content,
                );
            }
        }
        return $return;
    }
}
new APPMAKER_WC_simulador();
