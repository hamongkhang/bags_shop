<?php
/**
 * REST API Shop controller
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API Shop controller class.
 */
class APPMAKER_WC_REST_WISHLIST_Controller extends APPMAKER_WC_REST_Posts_Abstract_Controller {
   
	protected $namespace = 'appmaker-wc/v1';
	protected $rest_base = 'wishlist';
    protected $post_type = 'product';

    public function __construct() {
		parent::__construct();	
	}

	public function register_routes() {
        /**
         * Register the routes for products.
         */
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base ,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),	
				'schema' => array( $this, 'get_public_item_schema' ),			
			)
		);
        register_rest_route(
			$this->namespace,
			'/' . $this->rest_base .'/add' ,
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'add_item' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),	
				'schema' => array( $this, 'get_public_item_schema' ),			
			)
		);
        register_rest_route(
			$this->namespace,
			'/' . $this->rest_base .'/remove',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'remove_item' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),	
				'schema' => array( $this, 'get_public_item_schema' ),			
			)
		);
        
	}

    public function get_items( $request ){
      
        $return = array();
        $return = apply_filters('appmaker_wc_wishlist_items', $return , $request );    
        $user_id = get_current_user_id();
		if( is_wp_error($return) ) {
			return $return;
		} elseif( isset( $return['status'] ) ) {
			return array();
		}
        //return contains product ids
        if( $return ){
            if ( class_exists( 'APPMAKER_WC' ) && ! isset( APPMAKER_WC::$api->APPMAKER_WC_REST_Products_Controller ) ) {
                $product_controller = new APPMAKER_WC_REST_Products_Controller();
            } elseif ( class_exists( 'APPMAKER_WC' ) ) {
                $product_controller = APPMAKER_WC::$api->APPMAKER_WC_REST_Products_Controller;
            }       
    
            foreach ( $return as $product_id ) {
                $product = APPMAKER_WC_Helper::get_product($product_id );
                if ( ! empty( $product ) ) {
                    $data[] = $product_controller->get_product_data( $product );
                }
            } 
            $return =  rest_ensure_response($data);  			
        } else {
			$return =  new WP_Error( 'wishlist_not_found', 'Wishlist not found ', array( 'status' => 404 ) );
		}
        
        return $return;
    }

    public function add_item( $request ) {
      
        $response = array();
        
        return apply_filters('appmaker_wc_wishlist_add_item_response' , $response , $request );
    }

    public function remove_item( $request ){

        $response = array();
        return apply_filters('appmaker_wc_wishlist_remove_item_response', $response, $request );
    }
}
new APPMAKER_WC_REST_WISHLIST_Controller();
	