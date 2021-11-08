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
class APPMAKER_WC_REST_Category_Controller extends APPMAKER_WC_REST_Posts_Abstract_Controller {
   
	protected $namespace = 'appmaker-wc/v1';
    protected $rest_base = 'frontend';
    //protected $taxonomy = 'category';
    
    public function __construct() {
		parent::__construct();	
	}

	public function register_routes() {
        /**
         * Register the routes for products.
         */
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base .'/category',
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
			'/' . $this->rest_base .'/category/browse',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_category_details' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),	
				'schema' => array( $this, 'get_public_item_schema' ),			
			)
		);
	}

	public function get_category_details( $request ) {
		$taxonomy     = 'product_cat';
		$orderby      = 'name';
		$show_count   = 0;      // 1 for yes, 0 for no
		$pad_counts   = 0;      // 1 for yes, 0 for no
		$hierarchical = 1;      // 1 for yes, 0 for no
		$title        = '';
        $parent		  =  isset($_GET['category_id']) ? $_GET['category_id'] : 0;
		$empty        = true;
		$number       = APPMAKER_WC::$api->get_settings( 'number_of_categories',  5 );

		$args           = array(
			'taxonomy'     => $taxonomy,
			'orderby'      => $orderby,
			'show_count'   => $show_count,
			'pad_counts'   => $pad_counts,
			'hierarchical' => $hierarchical,
			'hide_empty'   => $empty,	
			'number'       => $number,	
           
		);  
		if( !isset( $request['expanded'] ) ) {
            $args['parent']       = $parent;
        }      
		if( $parent !== 0 ){
			$args['child_of'] = $parent;
		}
        $all_categories = get_categories( $args );
        $response = array(); 
        
        if( empty($all_categories) && isset($_GET['category_id']) && ! empty ( $_GET['category_id'] ) ) {
            $all_categories[] = get_term($_GET['category_id']);
		}		
		if( isset( $request['expanded'] ) && true == $request['expanded'] ){
			$all_categories = $this->buildTree( $all_categories, array(
				'parent_id_column_name' => 'parent',
				'children_key_name'     => 'children',
				'id_column_name'        => 'term_id',
			), $parent );
		}		           
		 
		foreach ( $all_categories as $term ) {
			$data       = $this->prepare_item_for_response_browse( $term, $request );            
			$response[] = $this->prepare_response_for_collection( $data );
		}
		$response = rest_ensure_response( $response );
		return $response;

	}

	public function prepare_item_for_response_browse( $item, $request ) {

        $image = $this->generate_category_image($item->term_id);
		$request['category'] = $item->term_id;
		$products = array();
		$products_data = APPMAKER_WC::$api->APPMAKER_WC_REST_Products_Controller->get_items($request);
		if( isset($products_data->data)) {
			$products = $products_data->data;
		}
		
		$data = array(
			'id'    => (int) $item->term_id,
            'label' => $item->name,
            'image' => $image,
			'products' => $products,			
		); 
		if( isset( $request['expanded'] ) && true == $request['expanded'] && ! empty($item->children) ){
            foreach( $item->children as $children ){
                $data['children'][] = $this->get_subcategories_browse( $children, $request ); 
            }                

        }  
		return $data;
	}

	public function get_subcategories_browse( $child , $request ) {

        $children = array();
        //foreach ( $item as $child ){
            $image = $this->generate_category_image($child->term_id);
			$request['category'] = $child->term_id;
			// $child_products = array();
			// $child_products_data = APPMAKER_WC::$api->APPMAKER_WC_REST_Products_Controller->get_items($request);
			// if( isset($child_products_data->data) ) {
			// 	$child_products = $child_products_data->data;
			// }//print_r($child_products);exit;
            $children = array(
                'id'    => (int) $child->term_id,
                'label' => $child->name,
                'image' => $image,   
				//'products' =>  $child_products
            ); 
            // if( ! empty($child->children) ) {
            //     foreach ( $child->children as $sub_child ){
            //         $children['children'][] = $this->get_subcategories_browse($sub_child , $request);
            //     }                
            // }
        //} 
        return $children;   
    }
	
    /**
	 * Get Product Categories
	 *
	 * @return object
	 */
	public function get_items($request) {

		$taxonomy     = 'product_cat';
		$orderby      = 'name';
		$show_count   = 0;      // 1 for yes, 0 for no
		$pad_counts   = 0;      // 1 for yes, 0 for no
		$hierarchical = 1;      // 1 for yes, 0 for no
		$title        = '';
        $parent		  =  isset($_GET['category_id']) ? $_GET['category_id'] : 0;
		$empty        = true;

		$args           = array(
			'taxonomy'     => $taxonomy,
			'orderby'      => $orderby,
			'show_count'   => $show_count,
			'pad_counts'   => $pad_counts,
			'hierarchical' => $hierarchical,
			'hide_empty'   => $empty,
           
		);
        if( !isset( $request['expanded'] ) ) {
            $args['parent']       = $parent;
        }
		if( $parent !== 0 ){
			$args['child_of'] = $parent;
		}
        $all_categories = get_categories( $args );
        $response = array(); 

        if( isset( $request['expanded'] ) && true == $request['expanded'] ){

           $all_categories = $this->buildTree( $all_categories, array(
			'parent_id_column_name' => 'parent',
			'children_key_name'     => 'children',
			'id_column_name'        => 'term_id',
		), $parent );
            //$data['children'] = $this->get_child_terms( $term );                
        }
        if( empty($all_categories) && isset($_GET['category_id']) && ! empty ( $_GET['category_id'] ) ) {
            $all_categories[] = get_term($_GET['category_id']);
		}
		foreach ( $all_categories as $term ) {
			$data       = $this->prepare_item_for_response( $term, $request );            
			$response[] = $this->prepare_response_for_collection( $data );
		}
		$response = rest_ensure_response( $response );
		return $response;
    }
    
    public function prepare_item_for_response( $item, $request ) {

        $image = $this->generate_category_image($item->term_id);
		$data = array(
			'id'    => (int) $item->term_id,
            'label' => $item->name,
            'image' => $image,

		); 
        if( isset( $request['expanded'] ) && true == $request['expanded'] && ! empty($item->children) ){
            foreach( $item->children as $children ){
                $data['children'][] = $this->get_subcategories($children); 
            }                

        }       

		return $data;
	}

    public function generate_category_image($term_id) {
        $thumbnail_id = get_woocommerce_term_meta( $term_id, 'thumbnail_id', true );

		if ( ! empty( $thumbnail_id ) ) {
			$image_url        = wp_get_attachment_url( $thumbnail_id );
			$image = $image_url;
		}else{
			$image = $this->ensure_absolute_link( wc_placeholder_img_src() );

		}
        return $image;
    }

    public function get_subcategories( $child ) {

        $children = array();
        //foreach ( $item as $child ){
            $image = $this->generate_category_image($child->term_id);
            $children = array(
                'id'    => (int) $child->term_id,
                'label' => $child->name,
                'image' => $image,    
            ); 
            if( ! empty($child->children) ) {
                foreach ( $child->children as $sub_child ){
                    $children['children'][] = $this->get_subcategories($sub_child);
                }                
            }
        //} 
        return $children;   
    }

    public function buildTree( array $elements, $options = array(
		'parent_id_column_name' => 'parent',
		'children_key_name'     => 'children',
		'id_column_name'        => 'term_id',
	), $parentId = 0 ) {
		$branch = array();
		foreach ( $elements as $element ) {
			if ( $element->{$options['parent_id_column_name']} == $parentId ) {
				$children = $this->buildTree( $elements, $options, $element->{$options['id_column_name']} );
				if ( $children ) {
					$element->{$options['children_key_name']} = $children;
				} else {
					$element->{$options['children_key_name']} = array();
				}
				$branch[] = $element;
			}
		}
		return $branch;
	}
   

}
new APPMAKER_WC_REST_Category_Controller();
