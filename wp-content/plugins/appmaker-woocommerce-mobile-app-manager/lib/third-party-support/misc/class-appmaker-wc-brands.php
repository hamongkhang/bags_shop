<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_Brands extends APPMAKER_WC_REST_Posts_Abstract_Controller {
	protected $type;
	protected $namespace = 'appmaker-wc/v1';
	
	public function __construct() {
		parent::__construct();
		$this->type      = 'inAppPages';
		$this->rest_base = "$this->type";
		register_rest_route(
			$this->namespace,
			'/inAppPages/dynamic/brands',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'generate_brands_listing' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		$this->options = get_option( 'appmaker_wc_settings' );
		add_filter( 'appmaker_wc_product_widgets', array( $this, 'brand_product_list' ), 10, 3 );	
		add_filter( 'appmaker_wc_product_filters', array( $this, 'brand_filter' ), 10, 3 );	
	}	

	public function brand_filter( $return ) {
		$brands_list = get_terms( 'product_brand', array(
			'orderby'    => 'name',
			'order'      => 'ASC',
			'hide_empty' => true,
		) );

		$brands_list = get_terms( 'product_brand' );

		if ( ! empty( $brands_list ) && is_array( $brands_list ) ) {
			$return['items']['product_brand'] = array(
				'id'     => 'product_brand',
				'type'   => 'checkbox',
				'label'  => 'Brands',
				'values' => array(),
			);

			foreach ( $brands_list as $term ) {
				$return['items']['product_brand']['values'][] = array(
					'label' => strip_tags( html_entity_decode( $term->name ) ),
					'value' => $term->slug,
				);
			}
		}

		return $return;
	}

	/**
	 * @param $return
	 * @param WC_Product $product
	 * @param $data
	 *
	 * @return mixed
	 */
	public function brand_product_list( $return, $product, $data ){
        
        //if ( is_singular( 'product' ) ) {			
          $terms = get_the_terms( $product->get_id(), 'product_brand' );          
         // $brand_count = is_array( $terms ) ? sizeof( $terms ) : 0;
         // $taxonomy = get_taxonomy( 'product_brand' );
         // $labels   = $taxonomy->labels;       
        if(! empty($terms)){
			foreach ( $terms as $term ) {

				/*$posts = get_posts(
					array(
						'post_type'     => 'product',
						'numberposts'   => -1,
						'post_status'   => 'publish',
						'fields'        => 'ids',
						'no_found_rows' => true,
						'tax_query'     => array(
	
							'relation' => 'AND',
							array(
								'taxonomy' => 'product_brand',
								'terms'    => $term,
								'field'    => 'id'
							)
						)
							));		*/
	
				$return['brand'] = array(
					'type'  => 'menu',
					'title' => 'Brand: '.strip_tags( html_entity_decode($term->name)),
			  
					'action' => array(
						'type'   => 'LIST_PRODUCT',
						'title' => strip_tags( html_entity_decode($term->name)),
						'params' => array(
							'product_brand' => $term
						),
					)
				);
			}
		}
        
			
		return $return;
	}

	public function generate_brands_listing( $request ) {      
		
		$all_brands = $this->get_brands_listing( $request );
		$this->createBrandsPage( $all_brands , $request);
	}

    public function get_brands_listing( $request ) {
		
        $response   = array();     
        $brands     = array(); 
        
        //if( isset( $request['category'] ) ){
            $result_brands = array();
            $brands_list   = array();
            $cat_name      = '';
            if( isset( $request['category']))
                 $cat_name = get_term($request['category'], 'product_cat', ARRAY_A );
            $attrs_raw              = wc_get_attribute_taxonomy_names();
            if(!empty($cat_name ) && !is_wp_error($cat_name )){
    
                $args = array(
                    'category'  => array($cat_name['slug'] ),
                    'limit' => -1,
                    'hierarchical' => 1,
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'exclude' => '',
                    'include' => '',
                    'child_of' => 0,
                    'number' => '',
                    'pad_counts' => false,
                    'hide_empty'   => true,
                );
                
                foreach( wc_get_products($args) as $product ){
                    $product_brands = wp_get_post_terms($product->get_id(), 'product_brand');
                    foreach($product_brands as $brand) {
                        if( 0 != $brand->parent ) {                           
                        $result_brands[] = $brand->term_id;
                        }
                    }
                }
                $result_brands= array_unique($result_brands);
                foreach ($result_brands as $brand) {
                        $brands_list[]= get_term($brand);
                }
            } else{
                $brands_list = get_terms('product_brand', array('hide_empty' => true));
            }
            foreach ($brands_list as $brand) {
                $brands[$brand->term_id] = $brand->name;
              }
                         
        //} 

        if(! empty($brands) ){
            asort($brands);
            foreach( $brands as $term_id => $brand ){
                $response[] = $this->get_formatted_item_data( $term_id, $brand );			
            }
            return $response;
        }           
		return  $response ;
    }
    
    public function get_formatted_item_data( $term_id, $brand ){

        $brand_image_id = get_term_meta($term_id, 'thumbnail_id', true);
        if($brand_image_id){
           $brand_image    =  current( wp_get_attachment_image_src( $brand_image_id, 'medium' ) );
        }
        $brands_array = array(
            'brand_id' => $term_id,
            'brand_name' => strip_tags( html_entity_decode($brand) ),
            'brand_image' => isset( $brand_image ) ? $brand_image : '',
        );
        
        return $brands_array;

    }

    public function createBrandsPage( $all_brands , $request ){
        $grid_items = array();
		foreach ($all_brands  as $brand ) {
			$grid_items[] = $this->grid_item( $brand , $request);
		}
			$page_id     = 'vendor_home';
			$in_app_page = array(
				'id'      => $page_id,
				'title'   =>  __('Brand', 'wc_brands'),
				'style'   => array( 'backgroundColor' => '#F2F5F8' ),
				'widgets' => $grid_items,
			);

			$in_app_page_json = json_encode( $in_app_page );

			echo $in_app_page_json;
			exit;
    }

    function grid_item( $brand , $request ) {
        //print_r($vendor);
        $hide_brand_image = APPMAKER_WC::$api->get_settings( 'hide_brand_images', 1 );
        if( ! $hide_brand_image ) {
            $image = '';
        }else {
            $image = ! empty( $brand['brand_image'] ) ? $brand['brand_image'] : '';
        }
		$data = array(
			'type'       => 'vendor',
			'attributes' => array(
				'title'        => $brand['brand_name'],
				'image'        => $image,				
				'orders'       => '',
				'store_action' => array(
					'type'   => 'LIST_PRODUCT',
					'params' => array(
						'product_brand' => $brand['brand_id'],
						'title'  => html_entity_decode($brand['brand_name']),
					),
				),
			),
        );
        if( isset( $request['category'] ) ){
            $data['attributes']['store_action']['params']['category'] = $request['category'];

        }
		return $data;
	}

}

new APPMAKER_WC_Brands();
