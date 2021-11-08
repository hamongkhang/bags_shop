<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_HXP extends APPMAKER_WC_REST_Posts_Abstract_Controller {

protected $namespace = 'appmaker-wc/v1';
protected $rest_base = 'products';
private $options;
public function __construct() {

    parent::__construct();
    register_rest_route($this->namespace, '/' . $this->rest_base . '/product_qa/(?P<id>[\d]+)', array(
        array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'get_product_qa' ),
            'permission_callback' => array( $this, 'api_permissions_check' ),
            'args'                => $this->get_collection_params(),
        ),
        'schema' => array( $this, 'get_public_item_schema' ),
    ) );
    register_rest_route($this->namespace, '/' . $this->rest_base . '/intercom', array(
        array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'show_intercom_chat' ),
            'permission_callback' => array( $this, 'api_permissions_check' ),
            'args'                => $this->get_collection_params(),
        ),
        'schema' => array( $this, 'get_public_item_schema' ),
    ) );

    register_rest_route($this->namespace, '/' . $this->rest_base . '/freshchat', array(
        array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( $this, 'show_freshchat_chat' ),
            'permission_callback' => array( $this, 'api_permissions_check' ),
            'args'                => $this->get_collection_params(),
        ),
        'schema' => array( $this, 'get_public_item_schema' ),
    ) );
    
    add_filter( 'appmaker_wc_product_widgets', array( $this, 'hxp_product_qa' ), 2, 2 );	
    add_filter( 'appmaker_wc_product_tabs', array($this,'new_product_tab' ),2,1);	
    $this->options = get_option('appmaker_wc_settings');
}

public function new_product_tab($tabs){
    global $product;

    if(!isset($tabs['hxp_product_qa'])) {           
           $tabs['hxp_product_qa'] = array(
               'title' => 'Product Q&A',
               'priority' => 2,
               'callback' => '',
           );
       
    }
    return $tabs;
}

public function hxp_product_qa( $return, $product_obj ) {

    global $product;
    $product = $product_obj;
    
    $tabs = apply_filters( 'woocommerce_product_tabs', array() );
    $tabs = apply_filters( 'appmaker_wc_product_tabs', $tabs );
    $url = site_url();		
    $api_key = $this->options['api_key'];
    foreach ( $tabs as $key => $tab ) {
                
        if ('hxp_product_qa' == $key ) {				
        // $shortcode  = strip_tags($tab['content']);
                       
            $product_id = $product->get_id();
            $return[ $key ] = array(
                'type'       => 'menu',
                'expandable' => isset( $tab['expandable'] ) ? $tab['expandable'] && true : true,
                'expanded'   => isset( $tab['expanded'] ) ? $tab['expanded'] && true : false,
                'title'      => APPMAKER_WC::$api->APPMAKER_WC_REST_Products_Controller->decode_html( apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ) ),
                'content'    => '',
                'action'     => array(
                    'type'   => 'OPEN_IN_WEB_VIEW',
                    'params' => array(
                        'url'  =>  $url.'/?rest_route=/appmaker-wc/v1/products/product_qa/'.$product_id. '&api_key=' . $api_key.'&from_app=1&key=1',
                        'title' => APPMAKER_WC::$api->APPMAKER_WC_REST_Products_Controller->decode_html( apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ) ),
                    ),
                ),
            );
        }
    }
        return $return;
}

public function get_product_qa($request){	
    
    $content = 'Product Q&A';  
    $product_id = $request['id'];

    $content = apply_filters('appmaker_hxp_product_qa',$content,$product_id); 
    
    ob_start();
    wp_head();
    echo $content; 
    wp_footer();
    $output = ob_get_contents();
    $output = <<<HTML
<html>
<head>
$output
</head>

</html>
HTML;

    ob_end_clean();
    header('Content-Type:text/html');
    echo $output;exit;
}

public function show_intercom_chat($request){	
    
    //$content = 'Product Q&A';  
    //$product_id = $request['id'];

    //$content = apply_filters('appmaker_hxp_product_qa',$content,$product_id); 
    
    ob_start();
    wp_head();
   // echo $content; 
    wp_footer();
   // $output = ob_get_contents();
    $output = <<<HTML
<html>
<head>
<script>
  window.intercomSettings = {
    app_id: "ch7vpubr",
    alignment: 'left',     
horizontal_padding: 20, 
vertical_padding: 20 
  };
</script>

<script>
// We pre-filled your app ID in the widget URL: 'https://widget.intercom.io/widget/ch7vpubr'
(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',w.intercomSettings);}else{var d=document;var i=function(){i.c(arguments);};i.q=[];i.c=function(args){i.q.push(args);};w.Intercom=i;var l=function(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/ch7vpubr';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);};if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();
</script>
<script>
		window.onload = function() {
			setTimeout(function(){Intercom('show');},500);
};
	</script>
</head>
<body style="background-color:#012169;">
</body>
</html>
HTML;

    ob_end_clean();
    header('Content-Type:text/html');
    echo $output;exit;
}

public function show_freshchat_chat($request){	
    
    //$content = 'Product Q&A';  
    //$product_id = $request['id'];

    //$content = apply_filters('appmaker_hxp_product_qa',$content,$product_id); 
    
    ob_start();
    wp_head();
   // echo $content; 
    wp_footer();
   // $output = ob_get_contents();
    $output = <<<HTML
<html>
<head>
    <script>
        function initFreshChat() {
          window.fcWidget.init({
            config: {
                "cssNames": {
                "expanded": "custom_fc_expanded",
                "widget": "custom_fc_frame"
                }
            },
            token: "cf03c02c-0e1c-49c0-a016-14dd3488a874",
            host: "https://wchat.in.freshchat.com",
            "open": true,
          });
        }
        function initialize(i,t){var e;i.getElementById(t)?initFreshChat():((e=i.createElement("script")).id=t,e.async=!0,e.src="https://wchat.in.freshchat.com/js/widget.js",e.onload=initFreshChat,i.head.appendChild(e))}function initiateCall(){initialize(document,"freshchat-js-sdk")}window.addEventListener?window.addEventListener("load",initiateCall,!1):window.attachEvent("load",initiateCall,!1);
      </script>

</head>
<body style="background-color:#012169;">
</body>
</html>
HTML;

    ob_end_clean();
    header('Content-Type:text/html');
    echo $output;exit;
}

}
new APPMAKER_WC_HXP();
