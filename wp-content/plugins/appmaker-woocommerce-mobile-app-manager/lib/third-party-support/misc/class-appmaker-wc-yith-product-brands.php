<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_Yith_Brands  extends APPMAKER_WC_REST_Posts_Abstract_Controller {

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
        add_filter( 'appmaker_wc_product_widgets', array( $this, 'brand_product_list' ), 10, 2 );
        add_filter( 'appmaker_wc_product_tabs', array( $this, 'product_tabs' ), 2, 1 );
        add_filter( 'appmaker_wc_product_filters', array( $this, 'brand_filter' ), 10, 3 );	
        add_filter('appmaker_wc_backend_taxonomy', array($this, 'brand_taxonomy'), 10, 1);
		
    }	

    public function brand_taxonomy( $taxonomy ){

        if($taxonomy && $taxonomy == 'product_brand'){
            $taxonomy = 'yith_product_brand';
        }
        return $taxonomy;
    }    

    public function brand_filter( $return ) {
		$brands_list = get_terms( 'yith_product_brand', array(
			'orderby'    => 'name',
			'order'      => 'ASC',
			'hide_empty' => true,
		) );

		$brands_list = get_terms( 'yith_product_brand' );

		if ( ! empty( $brands_list ) && is_array( $brands_list ) ) {
			$return['items']['yith_product_brand'] = array(
				'id'     => 'yith_product_brand',
				'type'   => 'checkbox',
				'label'  => 'Brands',
				'values' => array(),
			);

			foreach ( $brands_list as $term ) {
				$return['items']['yith_product_brand']['values'][] = array(
					'label' => strip_tags( html_entity_decode( $term->name ) ),
					'value' => $term->slug,
				);
			}
		}

		return $return;
	}

    public function product_tabs( $tabs ) {     
        
        if( ! isset( $tabs['yith_product_brand'] ) ) {                         
 
              $tabs['yith_product_brand'] = array(
                  'title'    => 'Brand',
                  'priority' => 2,
                  'callback' => 'woocommerce_product_description_tab',
              );
        }        
      
      return $tabs; 
    }
    	
	/**
	 * @param $return
	 * @param WC_Product $product
	 * @param $data
	 *
	 * @return mixed
	 */
    public function brand_product_list( $return, $product_local ) {
     
        global $product_obj,$product;
        $product_obj = $product_local;
        $product     = $product_local;
        
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
        
        	
        $terms = get_the_terms( $product->get_id(), 'yith_product_brand' );
        foreach ( $tabs as $key => $tab ) {
            if( $key == 'yith_product_brand') {
            
               if(! empty( $terms ) ){
                    foreach ( $terms as $term ) {			
                        $label = get_option( 'yith_wcbr_brands_label' );
                        $title = $label ? $label : 'Brand: ';
                        
                        $return[$key] = array(
                            'type'  => 'menu',
                            'title' => $title.' '.strip_tags( html_entity_decode($term->name)),
                    
                            'action' => array(
                                'type'   => 'LIST_PRODUCT',
                                'params' => array(
                                    'product_brand' => $term
                                ),
                            )
                        );
                    }
                } else {
                      unset($return[$key]);
                }
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
                    $product_brands = wp_get_post_terms( $product->get_id(), 'yith_product_brand' );
                    foreach($product_brands as $brand) {
                      //  if( 0 != $brand->parent ) {                           
                          $result_brands[] = $brand->term_id;
                       // }
                    }
                }
                $result_brands= array_unique($result_brands);
                foreach ($result_brands as $brand) {
                        $brands_list[]= get_term($brand);
                }
            } else{
                $brands_list = get_terms('yith_product_brand', array('hide_empty' => true));
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
				'title'   =>  get_option( 'yith_wcbr_brands_label' ),
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

new APPMAKER_WC_Yith_Brands();
