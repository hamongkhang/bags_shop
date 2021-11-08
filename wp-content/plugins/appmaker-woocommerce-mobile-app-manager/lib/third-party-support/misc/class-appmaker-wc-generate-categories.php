<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_generate_categories extends APPMAKER_WC_REST_Posts_Abstract_Controller {

	protected $type;
	protected $namespace     = 'appmaker-wc/v1';
	protected $isRoot        = true;
	protected $inAppPagesKey = '_inAppPages';

	public function __construct() {
		parent::__construct();
		$this->type      = 'inappages';
		$this->rest_base = 'backend';
		/**
		 * Register the routes to get categories and sub-categories
		 */
		// echo '/' . $this->rest_base . '/generate-categories-' . $this->type ; exit;
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/generate-categories-' . $this->type,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'generate_categories' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
			)
		);

	}

	public function generate_categories() {
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
		$show_count   = 0;      // 1 for yes, 0 for no
		$pad_counts   = 0;      // 1 for yes, 0 for no
		$hierarchical = 1;      // 1 for yes, 0 for no
		$title        = '';
		$empty        = 0;

		$args           = array(
			'taxonomy'     => $taxonomy,
			'orderby'      => $orderby,
			'show_count'   => $show_count,
			'pad_counts'   => $pad_counts,
			'hierarchical' => $hierarchical,
			'title_li'     => $title,
			'hide_empty'   => $empty,
		);
		$all_categories = get_categories( $args );
		$all_categories = $this->buildTree( $all_categories );
		$this->createCatPages( $all_categories, true );
	}


	function createCatPages( $category, $is_root = false ) {
		$children = $is_root ? $category : $category->children;
		if ( ! empty( $children ) ) {
			// Create in-app page for this cat here
			$grid_items = array();
			foreach ( $children  as $child_cat ) {
				$grid_items[] = $this->grid_item( $child_cat );
				$this->createCatPages( $child_cat );
			}
			$page_id     = $is_root ? 'product_cat_home' : 'product_cat_' . $category->term_id;
			$in_app_page = array(
				'parentID' => false,
				'title'    => $is_root ? 'Home - Categories' : $category->name,
				'language' => 'default',
				'type'     => 'NORMAL',
				'is_new'   => true,
				'widgets'  => array(
					array(
						'data'      => $grid_items,
						'expanded'  => true,
						'title'     => $is_root ? __( 'Categories', 'woocommerce' ) : $category->name,
						'type'      => 'grid',
						'data_main' => array(
							'items_per_line'         => array(
								'value'         => '2',
								'display_value' => '2',
								'label'         => 'Items per line',
								'display'       => true,
							),
							'show_view_more_button'  => 'false',
							'view_more_button_title' => __('View More', 'appmaker-woocommerce-mobile-app-manager'),
							'view_more_action'       => 'NO_ACTION',
							'view_more_action_value' => '',
						),
						'key'       => 'widget_key_13002',
					),
				),
				'id'       => $page_id,
			);
			// echo $page_id . "\n\n";
			$in_app_page_json = json_encode( $in_app_page );

			$backend_controller = APPMAKER_WC::$api->APPMAKER_WC_REST_BACKEND_INAPPPAGE_Controller;
			$response           = $backend_controller->update_item(
				array(
					'key'        => $page_id,
					'data'       => $in_app_page_json,
					'force_save' => true,
				)
			);
		}
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
		if ( $this->shuffle ) {
			$query_args['orderby'] = 'rand';
		}
		$products = get_posts( $query_args );
		$product = current( $products );
		$cat_img = get_the_post_thumbnail_url( $product->ID, 'thumbnail' );
		return $cat_img;
		
	}

	function grid_item( $category ) {
		$thumbnail_id = get_woocommerce_term_meta( $category->term_id, 'thumbnail_id', true );
		$cat_img = $this->get_alternative_category_image($category->term_id);

		if ( ! empty( $thumbnail_id ) ) {
			$attachment       = get_post( $thumbnail_id );
			$image_url        = wp_get_attachment_url( $thumbnail_id );
			$image_meta       = APPMAKER_WC_Helper::get_image_dimensions( $attachment );
			$has_sub_category = ! empty( $category->children );

			$image = array(
				'value'         => array(
					'title' => $attachment->post_title,
					'id'    => $attachment->ID,
					'url'   => $image_url,
					'meta'  => $image_meta,
				),
				'display_value' => $image_url,
				'label'         => 'Image',
				'display'       => true,
			);
		}else if(! empty( $cat_img )){
			$image = array(
				'value'         => array(
					'title' => 'Placeholder',
					'id'    => 0,
					'url'   => $cat_img,
					'meta'  => array(),
				),
				'display_value' => $cat_img,
				'label'         => 'Image',
				'display'       => true,
			);

		}else {
			$image = array(
				'value'         => array(
					'title' => 'Placeholder',
					'id'    => 0,
					'url'   => 'https://via.placeholder.com/400x400/FFFFFF/000000?text=' . $category->name,
					'meta'  => array(),
				),
				'display_value' => 'https://via.placeholder.com/400x400/FFFFFF/000000?text=' . $category->name,
				'label'         => 'Image',
				'display'       => true,
			);
		}
		$data = array(
			'data'  => array(
				'title'        => array(
					'value'         => $category->name,
					'display_value' => $category->name,
					'label'         => 'Title',
					'display'       => true,
				),
				'image'        => $image,
				'action'       => array(
					'value'         => $has_sub_category ? 'OPEN_IN_APP_PAGE' : 'LIST_PRODUCT',
					'display_value' => $has_sub_category ? 'Open In-App Page' : 'Product Category',
					'label'         => 'Action',
					'display'       => true,
				),
				'action_value' => array(
					'value'         => $has_sub_category ? 'product_cat_' . $category->term_id : $category->term_id,
					'display_value' => $category->name,
					'label'         => $has_sub_category ? 'Open In-App Page' : 'Choose Product Category',
					'display'       => true,
				),
			),
			'image' => $image,
		);
		// print_r($data);
			 return $data;
	}
}
new APPMAKER_WC_generate_categories();
