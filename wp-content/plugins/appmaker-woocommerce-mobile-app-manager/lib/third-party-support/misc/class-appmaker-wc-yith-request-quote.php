<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_YITH_REQUEST_QUOTE extends APPMAKER_WC_REST_Posts_Abstract_Controller {

    protected $namespace = 'appmaker-wc/v1';
	protected $rest_base = 'products';
	private $options;
	public function __construct() {
        parent::__construct();
        $this->options = get_option('appmaker_wc_settings');
        register_rest_route($this->namespace, '/' . $this->rest_base . '/request-quote/(?P<id>[\d]+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'request_quote_page' ),
                'permission_callback' => array( $this, 'api_permissions_check' ),
                'args'                => $this->get_collection_params(),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
		) );
        add_filter('appmaker_wc_product_data', array( $this, 'appmaker_request_quote'), 2, 2 );
		
    }
    
    public function appmaker_request_quote( $data, $product ) {

        $product_id = $product->get_id();
        $api_key   = $this->options['api_key'];
        $base_url  = site_url();
        //$product_id   = yit_get_prop( $product, 'id', true );
        $user_id = get_current_user_id();
        if ( $user_id ) {
            $access_token = apply_filters( 'appmaker_wc_set_user_access_token', $user_id ); 
            $url          = $base_url.'/?rest_route=/appmaker-wc/v1/products/request-quote/'.$product_id. '&api_key=' . $api_key. '&access_token='.$access_token.'&user_id='.$user_id;
        } else {
            $url          = $base_url.'/?rest_route=/appmaker-wc/v1/products/request-quote/'.$product_id. '&api_key=' . $api_key;
        }       
        $url              = add_query_arg( array( 'from_app' => true, 'key' => true ), $url);
        $exists           =  YITH_Request_Quote()->exists( $product_id ); 
        $preorder         =  get_post_meta( $product_id, 'is_preorder', true );
        $hide_price       =  apply_filters('appmaker_hide_request_quote_price',false);
        if( ( function_exists('ywraq_allow_raq_out_of_stock') && ! ywraq_allow_raq_out_of_stock() && $product && ! $product->is_in_stock() ) || ( function_exists('ywraq_show_btn_only_out_of_stock') && ywraq_show_btn_only_out_of_stock() && $product && $product->is_type( 'simple' ) && $product->is_in_stock() ) ) {
           $show_raq_button = false;
        }else {
            $show_raq_button = true;
        }
        if( ! $exists && !empty($data) && ! $product->is_type('variable') && 
        ( function_exists('ywraq_is_in_exclusion') && ! ywraq_is_in_exclusion( $product_id ) ) &&
        ( class_exists('YITH_Request_Quote_Premium') && YITH_Request_Quote_Premium()->check_user_type() ) && $show_raq_button ) {
         //if($preorder =='yes') {              
            if( $hide_price ) {
                $data['price_display'] = '';
                $data['regular_price_display'] = '';
            }            
            $data['display_add_to_cart'] = false;
            $data['buy_now_button_text'] =  __( 'Add to Quote', 'yith-woocommerce-request-a-quote' );

            $data['buy_now_action']       = array(
                                        'type'   => 'OPEN_IN_WEB_VIEW',
                                        'params' => array('url' => $url),
                                        );
            

        } 
        elseif ( $exists ) {
            if( $hide_price ) {
                $data['price_display'] = '';
                $data['regular_price_display'] = '';
            }  
            $data['display_add_to_cart'] = false;
            $request_quote_url = YITH_Request_Quote()->get_raq_page_url();
            if(!$request_quote_url) {
                $request_quote_url = $base_url.'/request-quote';
            }        
            $request_quote_url = add_query_arg( array( 'from_app' => true, 'key' => true ), $request_quote_url);
            $data['buy_now_button_text'] =   __( 'Browse the list', 'yith-woocommerce-request-a-quote' ) ;

            $data['buy_now_action']       = array(
                                        'type'   => 'OPEN_IN_WEB_VIEW',
                                        'params' => array('url' => $request_quote_url),
                                        );
        }
        if ( strpos($url,'from_app') != false && ! isset( $_COOKIE['from_app_cookie'] ) ) {
            $expire = time() + 60 * 60*24;
            wc_setcookie( 'from_app_cookie', 1, $expire, false );
        }
        return $data;
    }

    public function request_quote_page ( $request ) {
        $product_id = $request['id'];
        $product = wc_get_product( $product_id ); 
        $_POST['product_id'] = $product_id;
        $_POST['quantity'] = 1;  
        YITH_Request_Quote()->add_item($_POST);
        // $raq_content = YITH_Request_Quote()->get_raq_return();
        // // if(empty($raq_content)){
        // //     $raq_content = array ( '' => array ( 'product_id' => 28 ,'quantity' => 1 )  );
        // // }        
        // $atts = array();
		// $args = shortcode_atts(
		// 	array(
		// 		'raq_content'   => $raq_content,
		// 		'template_part' => 'view',
		// 		'show_form'     => 'yes',
        //     ),	
        //     $atts		
		// );

        // $args['args'] = apply_filters( 'ywraq_request_quote_page_args', $args, $raq_content );
        $style_button = ( get_option( 'ywraq_show_btn_link' ) == 'button' ) ? 'button' : 'ywraq-link';
        $label = function_exists('ywraq_get_label')? ywraq_get_label( 'btn_link_text' ) :apply_filters( 'ywraq_product_add_to_quote', get_option( 'ywraq_show_btn_link_text' ) );
        $browse_list =  function_exists('ywraq_get_label')? ywraq_get_label( 'browse_list' ) : apply_filters( 'ywraq_product_added_view_browse_list', __( 'Browse the list', 'yith-woocommerce-request-a-quote' ) );
        $args = array(
			'class'         => 'add-request-quote-button ' . $style_button,
			'wpnonce'       => wp_create_nonce( 'add-request-quote-' . $product_id ),
			'product_id'    => $product_id,
			'label'         => $label,
			'label_browse'  => $browse_list,
			'template_part' => 'button',
			'rqa_url'       => YITH_Request_Quote()->get_raq_page_url(),
			'exists'        => ( $product->is_type( 'variable' ) ) ? false : YITH_Request_Quote()->exists( $product_id ),
		);
        ob_start();
		wp_head();
        //echo yith_ywraq_render_button( $product_id );
        wc_get_template( 'add-to-quote-button.php', apply_filters( 'ywraq_add_to_quote_args', $args ), '', YITH_YWRAQ_TEMPLATE_PATH . '/' );
        //YITH_YWRAQ_Default_Form()->get_form_template( $args );
        //echo do_shortcode('yith_ywraq_request_quote'); 
       // wc_get_template( 'request-quote.php', $args, '', YITH_YWRAQ_TEMPLATE_PATH . '/' );        
        //wc_get_template( 'request-quote-form.php', $args, '', YITH_YWRAQ_TEMPLATE_PATH . '/' );
		wp_footer();
		$output = ob_get_contents();
        $output = <<<HTML
<html>
<head>
    $output
    <script>
	window.onload = function(){
setTimeout(function(){let add_btn = document.querySelector( '.add-request-quote-button' );
	add_btn.click();
console.log("add");},0);
};
</script>
<style>.add-request-quote-button{display: none !important;}</style>
</head>

</html>
HTML;

        ob_end_clean();
        header('Content-Type:text/html');
        echo $output;exit;
    }
}
new APPMAKER_WC_YITH_REQUEST_QUOTE();