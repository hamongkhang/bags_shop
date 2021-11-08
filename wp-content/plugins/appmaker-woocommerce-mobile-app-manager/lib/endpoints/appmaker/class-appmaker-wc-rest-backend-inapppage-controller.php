<?php
class APPMAKER_WC_REST_BACKEND_INAPPPAGE_Controller extends APPMAKER_WP_WC_REST_BACKEND_INAPPPAGE_Controller {


	/**
	 * Plugin option slug
	 *
	 * @var string
	 */
	public $plugin = 'appmaker_wc';
	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'appmaker-wc/v1';



	public function get_default_home() {

		$category_grid              = $this->get_product_categories();
		$slider                     = json_decode( '{"data":[{"data":{"title":{"value":"grocery store","display_value":"grocery store","label":"Title","display":true},"image":{"value":{"title":"banner-1","id":1900,"url":"http://demo.shopilder.com/wp-content/uploads/2019/07/banner-1.png","meta":{"width":1080,"height":575}},"display_value":"http://demo.shopilder.com/wp-content/uploads/2019/07/banner-1.png","label":"Image","display":true},"action":{"value":"LIST_PRODUCT","display_value":"Product Category","label":"Action","display":true},"action_value":{"value":"","display_value":"All","label":"Choose Product Category","display":true}},"image":{"value":{"title":"banner-1","id":1900,"url":"http://demo.shopilder.com/wp-content/uploads/2019/07/banner-1.png","meta":{"width":1080,"height":575}},"display_value":"http://demo.shopilder.com/wp-content/uploads/2019/07/banner-1.png","label":"Image","display":true}},{"data":{"title":{"value":"book store","display_value":"book store","label":"Title","display":true},"image":{"value":{"title":"banner-2","id":1901,"url":"http://demo.shopilder.com/wp-content/uploads/2019/07/banner-2.png","meta":{"width":1080,"height":575}},"display_value":"http://demo.shopilder.com/wp-content/uploads/2019/07/banner-2.png","label":"Image","display":true},"action":{"value":"LIST_PRODUCT","display_value":"Product Category","label":"Action","display":true},"action_value":{"value":"","display_value":"All","label":"Choose Product Category","display":true}},"image":{"value":{"title":"banner-2","id":1901,"url":"http://demo.shopilder.com/wp-content/uploads/2019/07/banner-2.png","meta":{"width":1080,"height":575}},"display_value":"http://demo.shopilder.com/wp-content/uploads/2019/07/banner-2.png","label":"Image","display":true}},{"data":{"title":{"value":"electronics store","display_value":"electronics store","label":"Title","display":true},"image":{"value":{"title":"banner-3","id":1902,"url":"http://demo.shopilder.com/wp-content/uploads/2019/07/banner-3.png","meta":{"width":1080,"height":575}},"display_value":"http://demo.shopilder.com/wp-content/uploads/2019/07/banner-3.png","label":"Image","display":true},"action":{"value":"LIST_PRODUCT","display_value":"Product Category","label":"Action","display":true},"action_value":{"value":"","display_value":"All","label":"Choose Product Category","display":true}},"image":{"value":{"title":"banner-3","id":1902,"url":"http://demo.shopilder.com/wp-content/uploads/2019/07/banner-3.png","meta":{"width":1080,"height":575}},"display_value":"http://demo.shopilder.com/wp-content/uploads/2019/07/banner-3.png","label":"Image","display":true}},{"data":{"title":{"value":"fashion","display_value":"fashion","label":"Title","display":true},"image":{"value":{"title":"banner-4","id":1903,"url":"http://demo.shopilder.com/wp-content/uploads/2019/07/banner-4.png","meta":{"width":1080,"height":575}},"display_value":"http://demo.shopilder.com/wp-content/uploads/2019/07/banner-4.png","label":"Image","display":true},"action":{"value":"LIST_PRODUCT","display_value":"Product Category","label":"Action","display":true},"action_value":{"value":"","display_value":"All","label":"Choose Product Category","display":true}},"image":{"value":{"title":"banner-4","id":1903,"url":"http://demo.shopilder.com/wp-content/uploads/2019/07/banner-4.png","meta":{"width":1080,"height":575}},"display_value":"http://demo.shopilder.com/wp-content/uploads/2019/07/banner-4.png","label":"Image","display":true}}],"expanded":true,"title":"Slider title #47431","type":"slider","data_main":{},"key":"widget_key_5322"}' );
		$pre_build_product_scroller = json_decode( '{"data":[{"data":{"action_value":{"value":"BEST_SELLING","display_value":"Best Selling Products","label":"Choose Product Scroller","display":true},"id":""}}],"expanded":true,"title":"Best Selling Products","type":"pre_build_product_scroller","data_main":{"show_view_more_button":"false","view_more_button_title":"View More","view_more_action":"","view_more_action_value":""},"key":"widget_key_32982"}' );
		$banner                     = json_decode( '{"data":[{"data":{"image":{"value":{"title":"Group-214-1","id":2745,"url":"http://demo.shopilder.com/wp-content/uploads/2019/09/Group-214-1.png","meta":{"width":1080,"height":345}},"display_value":"http://demo.shopilder.com/wp-content/uploads/2019/09/Group-214-1.png","label":"Image","display":true},"action":{"value":"LIST_PRODUCT","display_value":"Product Category","label":"Action","display":true},"action_value":{"value":"","display_value":"All","label":"Choose Product Category","display":true}},"image":{"value":{"title":"Group-214-1","id":2745,"url":"http://demo.shopilder.com/wp-content/uploads/2019/09/Group-214-1.png","meta":{"width":1080,"height":345}},"display_value":"http://demo.shopilder.com/wp-content/uploads/2019/09/Group-214-1.png","label":"Image","display":true}}],"expanded":true,"title":"Banner title #9921","type":"banner","data_main":{},"key":"widget_key_29792"}' );
		$text_widget                = json_decode( '{"data":[{"data":{"html":{"value":"<p><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Contact us</strong></p>","display_value":"<p><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Contact us</strong></p>","label":"Html","display":true}}}],"expanded":true,"title":"Text Widget title #74821","type":"html","data_main":{},"key":"widget_key_42142"}' );
		$whatsapp_chat              = json_decode( '{"data":[{"data":{"title":{"value":"Chat on whatsapp","display_value":"Chat on whatsapp","label":"Title","display":true},"image":{"value":{"title":"whatsapp","id":1910,"url":"http://demo.shopilder.com/wp-content/uploads/2019/07/whatsapp.png","meta":{"width":121,"height":121}},"display_value":"http://demo.shopilder.com/wp-content/uploads/2019/07/whatsapp.png","label":"Image","display":true},"action":{"value":"OPEN_URL","display_value":"Open URL","label":"Action","display":true},"action_value":{"value":"https://api.whatsapp.com/send?phone=919778178955&text=Hola%20team%20at%20appmaker","display_value":"https://api.whatsapp.com/send?phone=919778178955&text=Hola%20team%20at%20appmaker","label":"URL","display":true}},"image":{"value":{"title":"whatsapp","id":1910,"url":"http://demo.shopilder.com/wp-content/uploads/2019/07/whatsapp.png","meta":{"width":121,"height":121}},"display_value":"http://demo.shopilder.com/wp-content/uploads/2019/07/whatsapp.png","label":"Image","display":true}},{"data":{"title":{"value":"Call us","display_value":"Call us","label":"Title","display":true},"image":{"value":{"title":"call","id":1911,"url":"http://demo.shopilder.com/wp-content/uploads/2019/07/call.png","meta":{"width":121,"height":121}},"display_value":"http://demo.shopilder.com/wp-content/uploads/2019/07/call.png","label":"Image","display":true},"action":{"value":"OPEN_URL","display_value":"Open URL","label":"Action","display":true},"action_value":{"value":"tel:+919778178955","display_value":"tel:+919778178955","label":"URL","display":true}},"image":{"value":{"title":"call","id":1911,"url":"http://demo.shopilder.com/wp-content/uploads/2019/07/call.png","meta":{"width":121,"height":121}},"display_value":"http://demo.shopilder.com/wp-content/uploads/2019/07/call.png","label":"Image","display":true}}],"expanded":true,"title":"Contact us","type":"menu","data_main":{},"key":"widget_key_31212"}' );
		$widgets                    = array(
			$slider,
			$category_grid,
			$pre_build_product_scroller,
			$banner,
			$text_widget,
			$whatsapp_chat,
		);
		if ( ! empty( $category_grid ) ) {

			$inAppPage = array(
				'parentID' => false,
				'title'    => 'Home',
				'language' => 'default',
				'type'     => 'NORMAL',
				'widgets'  => $widgets,
				'id'       => 'home',
			);
			return json_decode( json_encode( $inAppPage ) );

		} else {

			$json = '{"parentID":false,"title":"Home","language":"default","type":"NORMAL","widgets":[{"data":[{"data":{"title":{"value":"grocery store","display_value":"grocery store","label":"Title","display":true},"image":{"value":{"title":"banner-1","id":1900,"url":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/banner-1.png","meta":{"width":1080,"height":575}},"display_value":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/banner-1.png","label":"Image","display":true},"action":{"value":"LIST_PRODUCT","display_value":"Product Category","label":"Action","display":true},"action_value":{"value":"","display_value":"All","label":"Choose Product Category","display":true}},"image":{"value":{"title":"banner-1","id":1900,"url":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/banner-1.png","meta":{"width":1080,"height":575}},"display_value":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/banner-1.png","label":"Image","display":true}},{"data":{"title":{"value":"book store","display_value":"book store","label":"Title","display":true},"image":{"value":{"title":"banner-2","id":1901,"url":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/banner-2.png","meta":{"width":1080,"height":575}},"display_value":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/banner-2.png","label":"Image","display":true},"action":{"value":"LIST_PRODUCT","display_value":"Product Category","label":"Action","display":true},"action_value":{"value":"","display_value":"All","label":"Choose Product Category","display":true}},"image":{"value":{"title":"banner-2","id":1901,"url":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/banner-2.png","meta":{"width":1080,"height":575}},"display_value":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/banner-2.png","label":"Image","display":true}},{"data":{"title":{"value":"electronics store","display_value":"electronics store","label":"Title","display":true},"image":{"value":{"title":"banner-3","id":1902,"url":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/banner-3.png","meta":{"width":1080,"height":575}},"display_value":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/banner-3.png","label":"Image","display":true},"action":{"value":"LIST_PRODUCT","display_value":"Product Category","label":"Action","display":true},"action_value":{"value":"","display_value":"All","label":"Choose Product Category","display":true}},"image":{"value":{"title":"banner-3","id":1902,"url":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/banner-3.png","meta":{"width":1080,"height":575}},"display_value":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/banner-3.png","label":"Image","display":true}},{"data":{"title":{"value":"fashion","display_value":"fashion","label":"Title","display":true},"image":{"value":{"title":"banner-4","id":1903,"url":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/banner-4.png","meta":{"width":1080,"height":575}},"display_value":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/banner-4.png","label":"Image","display":true},"action":{"value":"LIST_PRODUCT","display_value":"Product Category","label":"Action","display":true},"action_value":{"value":"","display_value":"All","label":"Choose Product Category","display":true}},"image":{"value":{"title":"banner-4","id":1903,"url":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/banner-4.png","meta":{"width":1080,"height":575}},"display_value":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/banner-4.png","label":"Image","display":true}}],"expanded":true,"title":"Slider title #47431","type":"slider","data_main":{},"key":"widget_key_5322"},{"data":[{"data":{"title":{"value":"","display_value":"","label":"Title","display":true},"image":{"value":{"title":"grid-1","id":1905,"url":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/grid-1.png","meta":{"width":540,"height":540}},"display_value":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/grid-1.png","label":"Image","display":true},"action":{"value":"LIST_PRODUCT","display_value":"Product Category","label":"Action","display":true},"action_value":{"value":"","display_value":"All","label":"Choose Product Category","display":true}},"image":{"value":{"title":"grid-1","id":1905,"url":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/grid-1.png","meta":{"width":540,"height":540}},"display_value":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/grid-1.png","label":"Image","display":true}},{"data":{"title":{"value":"","display_value":"","label":"Title","display":true},"image":{"value":{"title":"grid-2","id":1906,"url":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/grid-2.png","meta":{"width":541,"height":540}},"display_value":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/grid-2.png","label":"Image","display":true},"action":{"value":"LIST_PRODUCT","display_value":"Product Category","label":"Action","display":true},"action_value":{"value":"","display_value":"All","label":"Choose Product Category","display":true}},"image":{"value":{"title":"grid-2","id":1906,"url":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/grid-2.png","meta":{"width":541,"height":540}},"display_value":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/grid-2.png","label":"Image","display":true}},{"data":{"title":{"value":"","display_value":"","label":"Title","display":true},"image":{"value":{"title":"grid-3","id":1907,"url":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/grid-3.png","meta":{"width":541,"height":541}},"display_value":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/grid-3.png","label":"Image","display":true},"action":{"value":"LIST_PRODUCT","display_value":"Product Category","label":"Action","display":true},"action_value":{"value":"","display_value":"All","label":"Choose Product Category","display":true}},"image":{"value":{"title":"grid-3","id":1907,"url":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/grid-3.png","meta":{"width":541,"height":541}},"display_value":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/grid-3.png","label":"Image","display":true}},{"data":{"title":{"value":"","display_value":"","label":"Title","display":true},"image":{"value":{"title":"grid-4","id":1908,"url":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/grid-4.png","meta":{"width":540,"height":541}},"display_value":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/grid-4.png","label":"Image","display":true},"action":{"value":"LIST_PRODUCT","display_value":"Product Category","label":"Action","display":true},"action_value":{"value":"","display_value":"All","label":"Choose Product Category","display":true}},"image":{"value":{"title":"grid-4","id":1908,"url":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/grid-4.png","meta":{"width":540,"height":541}},"display_value":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/grid-4.png","label":"Image","display":true}}],"expanded":true,"title":"Categories","type":"grid","data_main":{"items_per_line":{"value":"2","display_value":"2","label":"Items per line","display":true},"show_view_more_button":"false","view_more_button_title":"View More","view_more_action":"","view_more_action_value":""},"key":"widget_key_68452"},{"data":[{"data":{"action_value":{"value":"BEST_SELLING","display_value":"Best Selling Products","label":"Choose Product Scroller","display":true},"id":""}}],"expanded":true,"title":"Best Selling Products","type":"pre_build_product_scroller","data_main":{"show_view_more_button":"false","view_more_button_title":"View More","view_more_action":"","view_more_action_value":""},"key":"widget_key_32982"},{"data":[{"data":{"image":{"value":{"title":"Group-214-1","id":2745,"url":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/09\/Group-214-1.png","meta":{"width":1080,"height":345}},"display_value":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/09\/Group-214-1.png","label":"Image","display":true},"action":{"value":"LIST_PRODUCT","display_value":"Product Category","label":"Action","display":true},"action_value":{"value":"","display_value":"All","label":"Choose Product Category","display":true}},"image":{"value":{"title":"Group-214-1","id":2745,"url":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/09\/Group-214-1.png","meta":{"width":1080,"height":345}},"display_value":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/09\/Group-214-1.png","label":"Image","display":true}}],"expanded":true,"title":"Banner title #9921","type":"banner","data_main":{},"key":"widget_key_29792"},{"data":[{"data":{"html":{"value":"<p><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Contact us<\/strong><\/p>","display_value":"<p><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Contact us<\/strong><\/p>","label":"Html","display":true}}}],"expanded":true,"title":"Text Widget title #74821","type":"html","data_main":{},"key":"widget_key_42142"},{"data":[{"data":{"title":{"value":"Chat on whatsapp","display_value":"Chat on whatsapp","label":"Title","display":true},"image":{"value":{"title":"whatsapp","id":1910,"url":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/whatsapp.png","meta":{"width":121,"height":121}},"display_value":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/whatsapp.png","label":"Image","display":true},"action":{"value":"OPEN_URL","display_value":"Open URL","label":"Action","display":true},"action_value":{"value":"https:\/\/api.whatsapp.com\/send?phone=919778178955&text=Hola%20team%20at%20appmaker","display_value":"https:\/\/api.whatsapp.com\/send?phone=919778178955&text=Hola%20team%20at%20appmaker","label":"URL","display":true}},"image":{"value":{"title":"whatsapp","id":1910,"url":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/whatsapp.png","meta":{"width":121,"height":121}},"display_value":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/whatsapp.png","label":"Image","display":true}},{"data":{"title":{"value":"Call us","display_value":"Call us","label":"Title","display":true},"image":{"value":{"title":"call","id":1911,"url":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/call.png","meta":{"width":121,"height":121}},"display_value":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/call.png","label":"Image","display":true},"action":{"value":"OPEN_URL","display_value":"Open URL","label":"Action","display":true},"action_value":{"value":"tel:+919778178955","display_value":"tel:+919778178955","label":"URL","display":true}},"image":{"value":{"title":"call","id":1911,"url":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/call.png","meta":{"width":121,"height":121}},"display_value":"http:\/\/demo.shopilder.com\/wp-content\/uploads\/2019\/07\/call.png","label":"Image","display":true}}],"expanded":true,"title":"Contact us","type":"menu","data_main":{},"key":"widget_key_31212"}],"id":"home"}';

			return json_decode( $json );

		}
	}

	/**
	 * Get Product Categories
	 *
	 * @return object
	 */
	public function get_product_categories() {

		$taxonomy         = 'product_cat';
		$orderby          = 'name';
		$show_count       = 0;      // 1 for yes, 0 for no
		$pad_counts       = 0;      // 1 for yes, 0 for no
		$hierarchical     = 1;      // 1 for yes, 0 for no
		$title            = '';
		$empty            = 0;
		$exclude_category = get_term_by( 'slug', 'uncategorized', 'product_cat' );
		$args             = array(
			'taxonomy'     => $taxonomy,
			'orderby'      => $orderby,
			'show_count'   => $show_count,
			'pad_counts'   => $pad_counts,
			'hierarchical' => $hierarchical,
			'title_li'     => $title,
			'hide_empty'   => $empty,
			'parent'       => 0,
			'hide_empty'   => 0,
			'number'       => 4,
			// 'exclude'      => $exclude_category->term_id,
		);
		if( $exclude_category )
		{
			$args['exclude'] = $exclude_category->term_id;
		}
		$all_categories   = get_categories( $args );
		$all_categories   = $this->buildTree( $all_categories );
		$data             = $this->createCatPages( $all_categories, true );
		return $data;
	}


	function createCatPages( $category, $is_root = false ) {
		$children = $is_root ? $category : $category->children;
		if ( ! empty( $children ) ) {
			// Create in-app page for this cat here
			$grid_items = array();
			foreach ( $children  as $child_cat ) {
				$grid_items[] = $this->grid_item( $child_cat );
			}
			$page_id  = $is_root ? 'product_cat_home' : 'product_cat_' . $category->term_id;
			$gridJson =
					array(
						'data'      => $grid_items,
						'expanded'  => true,
						'title'     => $is_root ? 'Categories' : html_entity_decode($category->name),
						'type'      => 'grid',
						'data_main' => array(
							'items_per_line'         => array(
								'value'         => '2',
								'display_value' => '2',
								'label'         => 'Items per line',
								'display'       => true,
							),
							'show_view_more_button'  => 'false',
							'view_more_button_title' => 'View More',
							'view_more_action'       => 'NO_ACTION',
							'view_more_action_value' => '',
						),
						'key'       => 'widget_key_13002',
					);
			// echo $page_id . "\n\n";
			// $gridJson = json_encode( $in_app_page );

			return $gridJson;
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

	function get_alternative_category_image( $catid ) {

		$cat_img    = '';
		$query_args = array(
			'posts_per_page' => 1,
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'meta_query'     => array(
				array(
					'key'     => '_thumbnail_id',
					'value'   => '',
					'compare' => '!=',
				),
			),
			'tax_query'      => array(
				array(
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => $catid,
					'operator' => 'IN',
				),
			),
		);
		/*
		if ( $this->shuffle ) {
			$query_args['orderby'] = 'rand';
		}*/
		$products = get_posts( $query_args );
		$product  = current( $products );
		if ( ! empty( $product ) ) {
			$cat_img = get_the_post_thumbnail_url( $product->ID, 'thumbnail' );
		}
		return $cat_img;

	}
	function grid_item( $category ) {
		$thumbnail_id = get_woocommerce_term_meta( $category->term_id, 'thumbnail_id', true );
		$cat_img      = $this->get_alternative_category_image( $category->term_id );

		// $has_sub_category = ! empty( $category->children ) ? $category->children : '';

		if ( ! empty( $thumbnail_id ) ) {
			$attachment = get_post( $thumbnail_id );
			$image_url  = wp_get_attachment_url( $thumbnail_id );
			$image_meta = APPMAKER_WC_Helper::get_image_dimensions( $attachment );

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
		} elseif ( ! empty( $cat_img ) ) {
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

		} else {
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
					'value'         => 'LIST_PRODUCT',
					'display_value' => 'Product Category',
					'label'         => 'Action',
					'display'       => true,
				),
				'action_value' => array(
					'value'         => $category->term_id,
					'display_value' => $category->name,
					'label'         => 'Choose Product Category',
					'display'       => true,
				),
			),
			'image' => $image,
		);
		// print_r($data);
			 return $data;
	}
}

