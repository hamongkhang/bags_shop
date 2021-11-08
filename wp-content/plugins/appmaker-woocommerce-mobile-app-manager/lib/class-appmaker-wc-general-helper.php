<?php

class APPMAKER_WC_General_Helper {   


    public static function get_custom_html(){
        
        $output ='<style>.woocommerce-form-coupon-toggle,.woocommerce-form-login-toggle,footer,header{display:none!important}</style>';

        $options = get_option('appmaker_wc_custom_settings', array());
        if(!empty($options) && isset($options['custom_webview_head'])){

            $output = $options['custom_webview_head'];
        }
        return base64_encode($output);
    } 
    
    public static function get_custom_checkout_style() {
        $output = '<script>!function(e,t,s,n,c,o,r){e.AppmakerCheckoutObject=c,e[c]=e[c]||function(){(e[c].q=e[c].q||[]).push(arguments)},e[c].l=1*new Date,o=t.createElement("script"),r=t.getElementsByTagName("script")[0],o.async=1,o.src="https://scripts-cdn.appmaker.xyz/checkout/v1/js/main.bundle.js",r.parentNode.insertBefore(o,r)}(window,window.document,0,0,"AppmakerCheckout"),window.AppmakerCheckout("init",{allTabs:[{id:"address",title:"Address",toShow:[{selector:"#customer_details"}]},{id:"order",toShow:[{selector:"#order_review_heading"},{selector:"#order_review > table"}]},{id:"payment",toHide:[{selector:"#place_order"}],toShow:[{selector:"#payment"}]}],checkoutContainer:".entry-content"});const styleLink="https://scripts-cdn.appmaker.xyz/checkout/v1/styles/main.css";function applyStyle(){if(!document.getElementById("myCss")){var e=document.getElementsByTagName("head")[0],t=document.createElement("link");t.id="myCss",t.rel="stylesheet",t.type="text/css",t.href=styleLink,t.media="all",e.appendChild(t)}}applyStyle();</script>';
        $options = get_option('appmaker_wc_custom_settings', array());

        if(! empty( $options ) && isset( $options['custom_webview_header_checkout'] ) ) {
    
            $output = $options['custom_webview_header_checkout'];
        }
        return base64_encode($output);
    }

    public static function get_app_platform() {
        
        $platform = '';
        if( isset($_SERVER['HTTP_USER_AGENT']) ) {

            // $iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
            $iPhone = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
            // $iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
            $Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
            // $webOS   = stripos($_SERVER['HTTP_USER_AGENT'],"webOS");

            
            if ( $iPhone ) {
                $platform = 'ios';
                
            } elseif ($Android) {
                $platform = 'android';
            }
        }

        return $platform;
        
    }

}
new APPMAKER_WC_General_Helper();
