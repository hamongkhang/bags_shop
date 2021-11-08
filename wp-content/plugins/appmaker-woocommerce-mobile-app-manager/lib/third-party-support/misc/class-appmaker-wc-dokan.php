<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_dokan_lite extends APPMAKER_WC_REST_Posts_Abstract_Controller {

    protected $namespace = 'appmaker-wc/v1';

    /**
     * Route base.
     *
     * @var string
     */
    protected $rest_base = 'products';

    private $options;
    public function __construct()
    {
        parent::__construct();
        register_rest_route($this->namespace, '/' . $this->rest_base . '/chatnow', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_chat_script' ),
                'permission_callback' => array( $this, 'api_permissions_check' ),
                'args'                => $this->get_collection_params(),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

        register_rest_route(
			$this->namespace,
			'/inAppPages/dynamic/vendors',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'generate_vendor_listing' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

        register_rest_route(
			$this->namespace,
			'/'.'vendors'. '/(?P<id>[\d]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_categories_vendor' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),	
				'schema' => array( $this, 'get_public_item_schema' ),			
			)
		);
		register_rest_route(
			$this->namespace,
			'/' . 'vendors',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_vendor_list' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),	
				'schema' => array( $this, 'get_public_item_schema' ),			
			)
		);

        $this->options = get_option('appmaker_wc_settings');

       // remove_filter( 'woocommerce_process_registration_errors',array( WeDevs_Dokan::init()->container['registration'],  'validate_registration'));
      //  remove_filter( 'woocommerce_registration_errors', array( WeDevs_Dokan::init()->container['registration'], 'validate_registration'  ));

        add_filter( 'appmaker_wc_register_username_required', '__return_false' );
       // add_filter( 'appmaker_wc_register_email_required', '__return_false' );
        add_filter( 'appmaker_wc_register_phone_required', '__return_false' );
        add_filter( 'appmaker_wc_register_password_required', '__return_false' );  
              
        if( class_exists('Dokan_Pro') ){
            add_filter('appmaker_wc_registration_response',array($this,'dokan_registration_response'),2,1);
            add_filter( 'appmaker_wc_login_after_register_required', '__return_false' );
            add_filter('appmaker_wc_cart_items', array($this, 'add_shipping_rate'), 10, 1);
        }
        add_filter( 'appmaker_wc_product_widgets', array( $this, 'get_vendor_info' ), 1, 3 );
        add_filter( 'appmaker_wc_account_page_response', array($this,'vendor_dashboard'),10,1 );
        add_filter( 'appmaker_wc_product_tabs', array($this,'new_product_tab' ),2,1);

    }

    public function get_categories_vendor( $request ) {
        $return = array( 'vendor_id'=> '','store_name' => '' , 'profile_image' => '' ,'store_banner' => array(), 'categories' => array() , 'products' => array() );
		$taxonomy     = 'product';
		$orderby      = 'name';
		$show_count   = 0;      // 1 for yes, 0 for no
		$pad_counts   = 0;      // 1 for yes, 0 for no
		$hierarchical = 1;      // 1 for yes, 0 for no
		$title        = '';
        $parent		  =  0;
		$empty        = true;
		$request['author'] = $request['id'];
		$request['per_page'] = APPMAKER_WC::$api->get_settings( 'number_of_categories',  5 );		
		$products_data = APPMAKER_WC::$api->APPMAKER_WC_REST_Products_Controller->get_items($request);
		if( isset($products_data->data)) {
			$return['products'] = $products_data->data;
		} 
        $author_id = $request['author'];
        $return['vendor_id'] = (int) $author_id;
        $store_info = dokan_get_store_info( $author_id );
        $vendor = dokan()->vendor->get( $author_id );
        if( is_array($store_info) )
        {
            $return['store_name'] = isset( $store_info['store_name'] ) ? $store_info['store_name'] : '';
           // $return['store_logo'] =$vendor->get_avatar();
            $return['profile_image'] = $vendor->get_avatar();
            $banner_width    = dokan_get_option( 'store_banner_width', 'dokan_appearance', 625 );
            $banner_height   = dokan_get_option( 'store_banner_height', 'dokan_appearance', 300 );
            $banner           = $store_info['banner'];
            $banner_url      = $banner ? wp_get_attachment_url( $banner ) : '';
            $return['store_banner'] = array('image' => $banner_url, 'width' => $banner_width , 'height' => $banner_height );
        }  
        
        $categories = $vendor->get_store_categories();

        foreach($categories as $category ) {
			$return['categories'][] = array( 'id'    => $category->term_id,
						'label' => $category->name,
						'image' => $category->image,   
	            	);
		}

        return $return;
    }

    public function get_vendor_list( $request ) {
        $return = array();
        $return = $this->get_vendor_listing( $request );
        return $return;
    }

    public function new_product_tab($tabs){
        global $product;       
        
        if( ! isset( $tabs['seller'] ) ) {           
               $tabs['seller'] = array(
                   'title' => __("Store Name", "dokan-lite"),
                   'priority' => 2,
                   'callback' => '',
               );
           
        }

        if( ! isset( $tabs['chat_now'] ) && class_exists('Dokan_Pro') ) {
            $tabs['chat_now'] = array(                
                'title' => __('Chat Now', 'dokan'),
                'priority' => 3,
                'callback' => '',
            );
                
        }
        return $tabs;
    }

    public function vendor_dashboard($return){

        $user_id = get_current_user_id();
        $user = get_user_by( 'id',$user_id);
        if ( in_array( 'seller', (array) $user->roles ) ) {
            $base_url = site_url();
            $url = $base_url . '/dashboard/orders/';
            $api_key = $this->options['api_key'];
            $access_token = apply_filters('appmaker_wc_set_user_access_token', $user_id);
            $url = add_query_arg(array('from_app' => true), $url);

            $url = base64_encode($url);
            $url = $base_url . '/?rest_route=/appmaker-wc/v1/user/redirect/&url=' . $url . '&api_key=' . $api_key . '&access_token=' . $access_token . '&user_id=' . $user_id;
            $wallet = array('received_orders' => array(
                'title' => __('Vendor dashboard', 'appmaker-woocommerce-mobile-app-manager'),
                'icon' => array(
                    'android' => 'event-note',
                    'ios' => 'ios-copy-outline',
                ),
                'action' => array(
                    'type' => 'OPEN_IN_WEB_VIEW',
                    'params' => array('url' => $url),
                ),
            ),
            );
            $return = array_slice($return, 0, 3, true) +
                $wallet +
                array_slice($return, 3, count($return) - 3, true);
        }

        return $return;
    }

    public function dokan_registration_response($return){

             $notice = dokan_get_option( 'registration_notice', 'dokan_email_verification' );
             $return = array(
                 'status'       => 1,
                 'message'      =>$notice,
             );
             return $return;


    }
    public  function get_vendor_info($return,$product,$data) {

        $user_id = get_current_user_id();
        $author_id  = get_post_field( 'post_author', $product->get_id() );
        $author     = get_user_by( 'id', $author_id );
        $store_info = dokan_get_store_info( $author->ID );
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
            if( $key == 'seller' && is_array($store_info) && isset( $store_info['store_name'] ) ){
                //$return[$key]['content'] = $store_info['store_name'];
                $return[$key] = array(
                    'type'  => 'menu',
                    'title' => __( 'Store Name', 'dokan-lite' ).':  '.$store_info['store_name'],
    
                    'action' => array(
                        'type'   => 'LIST_PRODUCT',
                        'params' => array(
                            'author'  => $author_id,
                            'title' => $store_info['store_name'],
                        ),
                    )
                );
            }
            if( $user_id && class_exists('Dokan_Pro') && $key == 'chat_now' ) {
              $url = site_url();
              $api_key = $this->options['api_key'];
              $access_token = apply_filters('appmaker_wc_set_user_access_token', $user_id);
    
              $return[$key] = array(
                'type' => 'menu',
                'expandable' => true,
                'expanded' => false,
                'title' => __('Chat Now', 'dokan'),
                'content' => '',
                'action' => array(
                    'type' => 'OPEN_IN_WEB_VIEW',
                    'params' => array(
                        'url' => $url.'/?rest_route=/appmaker-wc/v1/products/chatnow'. '&api_key=' . $api_key . '&access_token=' . $access_token . '&user_id=' . $user_id,
                        'title' => __('Chat Now', 'dokan'),
                    ),
                ),
              );              
            }

        }
        // if( ! isset( $return['seller'] ) && is_array($store_info) && isset( $store_info['store_name'] ) ) {

        //     $return['seller'] = array(
		// 		'type'  => 'menu',
		// 		'title' => __("Store Name", "dokan-lite").' :  '.$store_info['store_name'],

		// 		'action' => array(
		// 			'type'   => 'LIST_PRODUCT',
		// 			'params' => array(
		// 				'author'  => $author_id,
		// 				'title' => $store_info['store_name'],
		// 			),
		// 		)
		// 	);
        // }

    //   if($user_id && class_exists('Dokan_Pro')) {
    //         $url = site_url();
    //       $api_key = $this->options['api_key'];
    //       $access_token = apply_filters('appmaker_wc_set_user_access_token', $user_id);

    //       array_splice($return, 1, 0, array('chat_now' => array(
    //           'type' => 'menu',
    //           'expandable' => true,
    //           'expanded' => false,
    //           'title' => __('Chat Now', 'dokan'),
    //           'content' => '',
    //           'action' => array(
    //               'type' => 'OPEN_IN_WEB_VIEW',
    //               'params' => array(
    //                   'url' => $url.'/?rest_route=/appmaker-wc/v1/products/chatnow'. '&api_key=' . $api_key . '&access_token=' . $access_token . '&user_id=' . $user_id,
    //                   'title' => __('Chat Now', 'dokan'),
    //               ),
    //           ),
    //       )));
    //   }
        return $return;
    }

    public function get_chat_script(){
        ob_start();
        do_shortcode( '[dokan-live-chat]' );
        $output = ob_get_contents();
        $output = <<<HTML
<html>
<head>
    $output
    <script>
	window.onload = function(){
setTimeout(function(){let chat_btn = document.querySelector( '.dokan-live-chat' );
	chat_btn.click();
console.log("add");},500);
};
</script>
</head>
<body>
<button class="dokan-btn dokan-btn-theme dokan-btn-sm dokan-live-chat" style="display:none;">	
		Chat Now
            </button>
</body>
</html>
HTML;

        ob_end_clean();
        header('Content-Type:text/html');
        echo $output;exit;
    }


    public function add_shipping_rate( $return ){
        
        // print_r(WC()->cart->get_cart());exit;        
        $shipping_packages                               = WC()->shipping()->calculate_shipping( WC()->cart->get_shipping_packages() );
        $return['price_details']                         = array();       
        $shipping_methods_title = __( 'Shipping', 'woocommerce' );       
        $hide_shipping = APPMAKER_WC::$api->get_settings('hide_shipping_in_cart', true );
        if ( is_array( $shipping_packages ) &&  WC()->cart->needs_shipping() && ! $hide_shipping ) {
             foreach ( $shipping_packages as $shipping_package ) {
                 $seller = '';
                 if(isset($shipping_package['seller_id'])){
                    $store_info = dokan_get_store_info( $shipping_package['seller_id'] );
                    $seller = $store_info['store_name'];
                 }
                 if ( isset( $shipping_package['rates'] ) ) {
                     foreach ($shipping_package['rates'] as $package ){                       
                         $return['price_details'][] = array('label' => $shipping_methods_title.':'.$seller , 'value' => $package->label.':'.APPMAKER_WC_Helper::get_display_price($package->cost) );
 
                     }
                 }
             }
            //$return['shipping_methods'] = $methods;
         }
 
         return $return;
     }

     public function generate_vendor_listing( $request ) {		
		$all_vendors = $this->get_vendor_listing( $request );
		$this->createVendorPage( $all_vendors );
    }
    
    public function get_vendor_listing( $request ) {	
		
        $response     = array();  
        $params = $request;

        $args = array(
            'number' => (int) $params['per_page'],
            'offset' => (int) ( $params['page'] - 1 ) * $params['per_page']
        );

        if ( ! empty( $params['search'] ) ) {
            $args['search']         = '*' . sanitize_text_field( ( $params['search'] ) ) . '*';
            $args['search_columns'] = array( 'user_login', 'user_email', 'display_name' );
        }

        if ( ! empty( $params['status'] ) ) {
            $args['status'] = sanitize_text_field( $params['status'] );
        }

        if ( ! empty( $params['orderby'] ) ) {
            $args['orderby'] = sanitize_sql_orderby( $params['orderby'] );
        }

        if ( ! empty( $params['order'] ) ) {
            $args['order'] = sanitize_text_field( $params['order'] );
        }

        if ( ! empty( $params['featured'] ) ) {
            $args['featured'] = sanitize_text_field( $params['featured'] );
        }

        $args = apply_filters( 'dokan_rest_get_stores_args', $args, $request );  

		$stores       = dokan()->vendor->get_vendors( $args );
        $data_objects = array();

        foreach ( $stores as $store ) {
            $stores_data    = $this->prepare_item_for_response( $store, $request );
            $data_objects[] = $this->prepare_response_for_collection( $stores_data );
        }        
        $response = rest_ensure_response( $data_objects );     

        return $response;
    }

    public function prepare_item_for_response( $store, $request, $additional_fields = array() ) {

        $data = $store->to_array();
        $data = array_merge( $data, apply_filters( 'dokan_rest_store_additional_fields', $additional_fields, $store, $request ) );
        $response = rest_ensure_response( $data );
       // $response->add_links( $this->prepare_links( $data, $request ) );

        return apply_filters( 'dokan_rest_prepare_store_item_for_response', $response );
    }
    
    public function createVendorPage( $vendors ) {
        $grid_items = array();
        foreach ( $vendors->data  as $vendor ) {
            $grid_items[] = $this->grid_item( $vendor );
        }
        $page_id     = 'vendor_home';
        $in_app_page = array(
            'id'      => $page_id,
            'title'   => __( 'Vendor List', 'wc-frontend-manager' ),
            'style'   => array( 'backgroundColor' => '#F2F5F8' ),
            'widgets' => $grid_items,
        );

        $in_app_page_json = json_encode( $in_app_page );

        echo $in_app_page_json;
        exit;
    }
    
    function grid_item( $vendor ) {
		//print_r($vendor);
		$data = array(
			'type'       => 'vendor',
			'attributes' => array(
				'title'        => $vendor['store_name'],
				'image'        => ! empty( $vendor['banner'] ) ? $vendor['banner'] : '',
				'rating'       => is_array( $vendor['rating'] ) ? $vendor['rating']['rating'] : '0',
				'orders'       => '',
				'store_action' => array(
					'type'   => 'LIST_PRODUCT',
					'params' => array(
						'author' => $vendor['id'],
						'title'  => $vendor['store_name'],
					),
				),
			),
		);
			 return $data;
	}

}
new APPMAKER_WC_dokan_lite();