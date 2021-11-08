<?php
/**
 * Created by IntelliJ IDEA.
 * User: shifa
 * Date: 4/25/19
 * Time: 1:08 PM
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_Waitlist
{

    public function __construct()
    {
        add_filter('appmaker_wc_product_widgets', array($this, 'Waitlist_field'), 2, 2);
        add_filter( 'allowed_http_origin', array($this,'appmaker_send_cors_headers' ),1,1);
        add_filter('allowed_http_origins', array($this,'add_allowed_origins'),1,1);
        add_action('init',array($this,'add_cors_http_header'));
    }

    public function add_cors_http_header(){
        header("Access-Control-Allow-Origin: *");
    }

    public function add_allowed_origins($origins) {
        $origins[] = 'http://127.0.0.1:5500/';
        return $origins;
    }

    public function appmaker_send_cors_headers( $origin ) {

        // Access-Control headers are received during OPTIONS requests
        if (  $origin && 'OPTIONS' === $_SERVER['REQUEST_METHOD'] ) {

            if ( isset( $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] ) ) {
                @header( 'Access-Control-Allow-Headers: '. $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] );
            }

        }

        return $origin;

    }

    public function Waitlist_field($return,$product){

        global $xoo_wl_gl_bntxt_value, $appmaker_product_id,$appmaker_user_id;
        $appmaker_product_id = $product->get_id();
        $appmaker_user_id=get_current_user_id();

        $tabs    = apply_filters( 'woocommerce_product_tabs', array() );
        $tabs    = apply_filters( 'appmaker_wc_product_tabs', $tabs);
        $in_stock = $product->is_in_stock();
        if(!isset( $tabs['notify']) && !$in_stock){
            $tabs['notify'] = '';
        }

        foreach ( $tabs as $key => $tab ) {
            $tab_type = APPMAKER_WC::$api->get_settings('product_tab_display_type_' . $key, 'DEFAULT');
            $tab_type='OPEN_IN_WEB_VIEW';
            if ( $key == 'notify' && $tab_type=='OPEN_IN_WEB_VIEW'){
                ob_start();
                include_once('class-appmaker-wc-waitlist-output.php');
                $content = ob_get_clean();
                $return['notify'] = array(
                    'type'       => 'menu',
                    'expandable' => isset( $tab['expandable'] ) ? $tab['expandable'] && true : true,
                    'expanded'   => isset( $tab['expanded'] ) ? $tab['expanded'] && true : false,
                    'title'      =>__($xoo_wl_gl_bntxt_value,'waitlist-woocommerce'),
                    'content'    => $content,
                    'action'     => array(
                        'type'   => 'OPEN_IN_WEB_VIEW',
                        'params' => array(
                            'html'  => $content,
                            'title' => __($xoo_wl_gl_bntxt_value,'waitlist-woocommerce'),
                        ),
                    ),
                );
            }
        }
        return $return;
    }
}
new APPMAKER_WC_Waitlist();