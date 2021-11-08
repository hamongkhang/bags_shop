<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_Woo_Brands extends APPMAKER_WC_REST_Posts_Abstract_Controller
{

    protected $type;
    protected $namespace = 'appmaker-wc/v1';
    
    public function __construct()
    {
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
        
        add_filter('appmaker_wc_product_filters', array($this, 'brand_filter'), 10, 1);   
        add_filter('appmaker_wc_product_widgets', array($this, 'brands_related_products'), 10, 3);    
    }

    public function brands_related_products( $return, $product_local, $data ) {

        global $product_obj,$product;
        $product_obj = $product_local;
        $product     = $product_local;
        $product_id = $product->get_id();
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
        $cats_array = array(0);
        $terms = wp_get_post_terms( $product->id, 'product_brand' );
        foreach ( $terms as $term ) {

            $cats_array[] = $term->term_id;
        }
        $args =  array(
            'post_type' => 'product',
            'posts_per_page' => 4,
            'post__not_in' => array( $product_id ),   // exclude current product
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_brand',
                    'field' => 'id',
                    'terms' => $cats_array
                ),
            )
        );
        
        //$products                    = new WP_Query( $args );
        $products           = get_posts($args);  
        $related_products   = array();     
        if( $products ){

            foreach( $products as $key => $brand_product ) {
                $related_products[] = $brand_product->ID;
            }
            if( $related_products ) {
                $related_products = APPMAKER_WC::$api->APPMAKER_WC_REST_Products_Controller->get_products_data($related_products);
                foreach ( $tabs as $key => $tab ) {
                    if( $key == 'related_products' ) {
                        $title = APPMAKER_WC::$api->get_settings( 'product_tab_field_title_related_products');
                        if ( ! empty( $related_products ) ) {
                            $return[$key] = array(
                                'type'       => 'product_scroller',
                                'expandable' => false,
                                'expanded'   => true,
                                'title'      => empty($title)? __( 'Related products', 'woocommerce' ) : $title,
                                'products'   => $related_products,
                            );
                        }
                    }
                }
            }

        }

        return $return;

    }
    
    public function brand_filter($return)
    {
        $posts=array();
        if(isset($_REQUEST['category'])){
            $args = array(
                'post_type'             => 'product',
                'post_status'           => 'publish',
                'ignore_sticky_posts'   => 1,
                'posts_per_page'        => -1,
                'tax_query'             => array(
                    array(
                        'taxonomy'      => 'product_cat',
                        'field' => 'term_id', //This is optional, as it defaults to 'term_id'
                        'terms'         => $_REQUEST['category'],
                        'operator'      => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
                    ),
                    array(
                        'taxonomy'      => 'product_visibility',
                        'field'         => 'slug',
                        'terms'         => 'exclude-from-catalog', // Possibly 'exclude-from-search' too
                        'operator'      => 'NOT IN'
                    )
                )
            );
            $query = new WP_Query($args);
            $i=0;
            while( $query->have_posts()): $query->the_post();

                {
                    $posts[$i]=$query->post;
                    $i++;
                }

            endwhile;

            $brands_list = array();
        $result_brands=array();
        foreach($posts as $post => $product){
           $product_id=$product->ID;
            $product_brands = wp_get_post_terms($product_id, 'product_brand');
            foreach($product_brands as $brand) $result_brands[] = $brand->term_id;
        }
        $result_brands= array_unique($result_brands);
        foreach ($result_brands as $brand) {
                $brands_list[]= get_term($brand);
        }
        }else {
            $brands_list = get_terms('product_brand');
        }


        if ( ! empty( $brands_list ) && is_array( $brands_list ) ) {
            $return['items']['product_brand'] = array(
                'id'     => 'product_brand',
                'type'   => 'checkbox',
                'label'  => __( 'Brands' ),
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
				'title'   =>  __('Brand', 'woocommerce-brands'),
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
new APPMAKER_WC_Woo_Brands();