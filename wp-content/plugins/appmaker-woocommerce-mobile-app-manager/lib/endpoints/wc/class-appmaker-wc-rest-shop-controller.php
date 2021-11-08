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
class APPMAKER_WC_REST_SHOP_Controller extends APPMAKER_WC_REST_Posts_Abstract_Controller {
   
	protected $namespace = 'appmaker-wc/v1';
	protected $rest_base = 'inAppPages/dynamic';
    
    public function __construct() {
		parent::__construct();	
	}

	public function register_routes() {
        /**
         * Register the routes for products.
         */
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base .'/shop',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_category_grid' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),	
				'schema' => array( $this, 'get_public_item_schema' ),			
			)
		);
	}
	
    public function get_category_grid( $request ) {
        $this->get_product_categories();
    }
    /**
	 * Get Product Categories
	 *
	 * @return object
	 */
	public function get_product_categories() {

		$taxonomy     = 'product_cat';
		$orderby      = 'name';
		$parent		  =  isset($_GET['category_id']) ? $_GET['category_id'] : 0;
		$show_count   = 0;      // 1 for yes, 0 for no
		$pad_counts   = 0;      // 1 for yes, 0 for no
		$hierarchical = 1;      // 1 for yes, 0 for no
		$title        = '';
		$empty        = true;

		$args           = array(
			'taxonomy'     => $taxonomy,
			'orderby'      => $orderby,
			'show_count'   => $show_count,
			'pad_counts'   => $pad_counts,
			'hierarchical' => $hierarchical,
			'hide_empty'   => $empty,
		);
		if($parent !== 0 ){
			$args['child_of']=$parent;
		}
		$all_categories = get_categories( $args );		
		$all_categories = $this->buildTree( $all_categories, array(
			'parent_id_column_name' => 'parent',
			'children_key_name'     => 'children',
			'id_column_name'        => 'term_id',
		), $parent );
		if( empty($all_categories) && isset($_GET['category_id']) && ! empty ( $_GET['category_id'] ) ) {
            $all_categories[] = get_term($_GET['category_id']);
		}
		$this->createCatGrid( $all_categories);
    }
    
    function createCatGrid($category){
		
		$grid_items = array();		
        foreach ( $category  as $category ) {
			$grid_items[] = $this->grid_item( $category );
			$parent_catid = $category->category_parent;
			$term = get_term_by( 'id', $parent_catid, 'product_cat' );
		}
		$shop_page_tab = array(
			'id'=>'shop',
            'title'=>__( 'Shop page', 'woocommerce' ),
            'widgets'=>array(
                array(
				'type'=>'grid',
				'title'=>(empty($term)) ? __( 'Categories', 'woocommerce' ) : $term->name,
				'data'=>$grid_items,
				'meta'=>array(
					'items_per_line'=>'2',
					'show_view_more_button'=>'false',
					'view_more_button_title'=> __('View More', 'appmaker-woocommerce-mobile-app-manager'),
					'action'=>'',
					'action_value'=>''
                   )
                )
            ),
            'hash'=>'7d5e68131a587ef99af3d586820c4d12'
		);
        $shop_page_tab_json = json_encode( $shop_page_tab );
		echo $shop_page_tab_json;
		exit;
    }

    function buildTree( array $elements, $options = array(
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

    function get_alternative_category_image($catid){
		
		$query_args = array(
			'posts_per_page' => 1,
			'post_status' => 'publish',
			'post_type' => 'product',
			'meta_query' => array(
				array(
					'key' => '_thumbnail_id',
					'value' => '',
					'compare' => '!=',
				),
			),
			'tax_query' => array(
				array(
					'taxonomy' => 'product_cat',
					'field' => 'term_id',
					'terms' => $catid,
					'operator' => 'IN',
				),
			),
		);
		// if ( $this->shuffle ) {
		// 	$query_args['orderby'] = 'rand';
		// }
		$products = get_posts( $query_args );
		$product = current( $products );
		$cat_img = get_the_post_thumbnail_url( $product->ID, 'thumbnail' );
		return $cat_img;
		
	}

    function grid_item( $category ) {
		$cat_name = htmlspecialchars_decode($category->name);
        //print_r($category); exit;
		$thumbnail_id = get_woocommerce_term_meta( $category->term_id, 'thumbnail_id', true );
		$cat_img = $this->get_alternative_category_image($category->term_id);

		if ( ! empty( $thumbnail_id ) ) {
			$image_url        = wp_get_attachment_url( $thumbnail_id );

			$image = $image_url;
		}else if(! empty( $cat_img )){
			$image = $cat_img;

		}else {
			$image = site_url().'/wp-content/uploads/woocommerce-placeholder.png';
		}
		if(empty( $category->children )){
		$data = array(
                'image'=> $image,
                'dimensions'=> '',
                'title'=> $cat_name,
                'action'=> array(
                  'type'=> 'LIST_PRODUCT',
                  'params'=>array(
                    'category'=> $category->term_id,
                    'title'=> $cat_name
                  )
                  )
				);
			}else{
				$data = array(
					'image'=> $image,
					'dimensions'=> '',
					'title'=> $cat_name,
					'action'=> array(
					  'type'=> 'OPEN_IN_APP_PAGE',
					  'params'=>array(
						'id' => 'dynamic/shop?category_id=' . $category->term_id
					  )
					  )
					);
				}
		 //print_r($data); exit;
			 return $data;
	}

}
new APPMAKER_WC_REST_SHOP_Controller();
