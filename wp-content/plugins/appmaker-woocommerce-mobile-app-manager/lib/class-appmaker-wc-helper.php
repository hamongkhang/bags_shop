<?php

class APPMAKER_WC_Helper {
	/**
	 * Return display price for app
	 *
	 * @param double $price Price.
	 *
	 * @return string
	 */
	static function get_display_price( $price ) {
		$price = wc_price( $price );
		$price = strip_tags( $price );
		$price = preg_replace( '/&nbsp;/', ' ', $price );
		$price = html_entity_decode( $price,ENT_QUOTES, 'UTF-8' );

		return $price;
	}


	static function get_display_price_from_html( $price ) {
		$price = strip_tags( $price );
		$price = preg_replace( '/&nbsp;/', ' ', $price );
		$price = html_entity_decode( $price );

		return $price;
	}



	/**
	 * Function that returns an array containing the IDs of the products that are on sale.
	 *
	 * @since 2.0
	 * @access public
	 *
	 * @return array
	 */
	static function wc_get_product_ids_on_sale( $language ) {
		$sale_products = wc_get_product_ids_on_sale();
		$query_args = array(
			'posts_per_page'	=> '12',
			'no_found_rows' 	=> '1',
			'post_type'         => 'product',
			'post_status' 		=> 'publish',
			'fields'            => 'id=>parent',
			'meta_query'        => WC()->query->get_meta_query(),
			'post__in'          => array_merge( array( 0 ), wc_get_product_ids_on_sale() )
		);
		$products = new WP_Query( $query_args );
		foreach($products->posts as $sale_pro){
			$sale_pro_ids[] = $sale_pro->ID;
		}
		$sale_products = apply_filters( 'appmaker_pre_build_product_scroller', $sale_pro_ids, $language );
		return $sale_products;
	}

	/**
	 * Function that returns an array containing the IDs of the featured products.
	 *
	 * @since 2.1
	 * @access public
	 *
	 * @param int $limit
	 *
	 * @return array
	 */
	static function wc_get_featured_product_ids( $language, $limit = 10 ) {

		$featured_products = wc_get_featured_product_ids();
		$featured_products = apply_filters( 'appmaker_pre_build_product_scroller', $featured_products, $language );
		return $featured_products;
	}


	public static function get_recent_products( $language ) {
		$atts = array(
			'per_page' => '12',
			'columns'  => '4',
			'orderby'  => 'date',
			'order'    => 'desc',
		);
		extract( $atts );
		$meta_query = WC()->query->get_meta_query();
		$args       = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $per_page,
			'orderby'             => $orderby,
			'order'               => $order,
			'meta_query'          => $meta_query,
			'fields'              => 'id=>parent',
			'tax_query'           => array(),
			'suppress_filters'    => 0,
		);

		if ( method_exists( WC()->query, 'get_tax_query' ) ) {
			$args['tax_query'] = WC()->query->get_tax_query( $args['tax_query'] );
		} else {
			$args['meta_query'][] = WC()->query->visibility_meta_query();
			if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
				$args['meta_query'][] = array(
					'key'     => '_stock_status',
					'value'   => 'instock',
					'compare' => '=',
				);
			}
		}

		$products           = get_posts( $args );
		$product_ids        = array_keys( $products );
		$parent_ids         = array_values( array_filter( $products ) );
		$return_product_ids = array_unique( array_merge( $product_ids, $parent_ids ) );
		$return_product_ids = apply_filters( 'appmaker_pre_build_product_scroller', $return_product_ids, $language );
		return $return_product_ids;
	}


	public static function get_best_selling_products( $language ) {
		$atts = array(
			'per_page' => '20',
			'columns'  => '4',
		);
		extract( $atts );
		$meta_query = WC()->query->get_meta_query();
		$args       = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $per_page,
			'meta_key'            => 'total_sales',
			'orderby'             => 'meta_value_num',
			'meta_query'          => $meta_query,
			'fields'              => 'id=>parent',
			'tax_query'           => array(),
			'suppress_filters'    => 0,
		);

		if ( method_exists( WC()->query, 'get_tax_query' ) ) {
			$args['tax_query'] = WC()->query->get_tax_query( $args['tax_query'] );
		} else {
			$args['meta_query'][] = WC()->query->visibility_meta_query();
			if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
				$args['meta_query'][] = array(
					'key'     => '_stock_status',
					'value'   => 'instock',
					'compare' => '=',
				);
			}
		}

		$products           = get_posts( $args );
		$product_ids        = array_keys( $products );
		$parent_ids         = array_values( array_filter( $products ) );
		$return_product_ids = array_unique( array_merge( $product_ids, $parent_ids ) );
		$return_product_ids = apply_filters( 'appmaker_pre_build_product_scroller', $return_product_ids, $language );
		return $return_product_ids;
	}

	public static function get_top_rated_products( $language ) {
		$args = array(
			'posts_per_page'   => 20,
			'no_found_rows'    => 1,
			'post_status'      => 'publish',
			'post_type'        => 'product',
			'meta_key'         => '_wc_average_rating',
			'orderby'          => 'meta_value_num',
			'order'            => 'DESC',
			'fields'           => 'id=>parent',
			'meta_query'       => WC()->query->get_meta_query(),
			'tax_query'        => WC()->query->get_tax_query(),
			'suppress_filters' => 0,
		);

		$products           = get_posts( $args );
		$product_ids        = array_keys( $products );
		$parent_ids         = array_values( array_filter( $products ) );
		$return_product_ids = array_unique( array_merge( $product_ids, $parent_ids ) );
		$return_product_ids = apply_filters( 'appmaker_pre_build_product_scroller', $return_product_ids, $language );
		return $return_product_ids;
	}

	public static function get_products_by_tax( $taxonomy, $tax_id, $language ) {
		$atts = array(
			'per_page' => '12',
			'columns'  => '4',
			'orderby'  => 'title',
			'order'    => 'asc',
		);
		extract( $atts );
		$meta_query = WC()->query->get_meta_query();
        $orderby = 'rand';
		$args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'orderby'             => $orderby,
			'order'               => $order,
			'posts_per_page'      => $per_page,
			'meta_query'          => $meta_query,
			'fields'              => 'id=>parent',
			'tax_query'           => array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $tax_id,
					'operator' => 'IN',
				),
			),
			'suppress_filters'    => 0,
		);
		if ( method_exists( WC()->query, 'get_tax_query' ) ) {
			$args['tax_query'] = WC()->query->get_tax_query( $args['tax_query'] );
		} else {
			$args['meta_query'][] = WC()->query->visibility_meta_query();
			if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
				$args['meta_query'][] = array(
					'key'     => '_stock_status',
					'value'   => 'instock',
					'compare' => '=',
				);
			}
		}

		$products           = get_posts( $args );
		$product_ids        = array_keys( $products );
		$parent_ids         = array_values( array_filter( $products ) );
		$return_product_ids = array_unique( array_merge( $product_ids, $parent_ids ) );
		if('product_tag' == $taxonomy){
			$return_product_ids = $product_ids;
		}
		$return_product_ids = apply_filters( 'appmaker_pre_build_product_scroller', $return_product_ids, $language );
		return $return_product_ids;
	}

	/**
	 * Parses and formats a MySQL datetime (Y-m-d H:i:s) for ISO8601/RFC3339.
	 *
	 * Requered WP 4.4 or later.
	 * See https://developer.wordpress.org/reference/functions/mysql_to_rfc3339/
	 *
	 * @since 2.6.0
	 *
	 * @param string $date
	 *
	 * @return string|null ISO8601/RFC3339 formatted datetime.
	 */
	static function wc_rest_prepare_date_response( $date ) {
		// Check if mysql_to_rfc3339 exists first!
		if ( ! function_exists( 'mysql_to_rfc3339' ) ) {
			return null;
		}

		// Return null if $date is empty/zeros.
		if ( '0000-00-00 00:00:00' === $date || empty( $date ) ) {
			return null;
		}

		// Return the formatted datetime.
		return mysql_to_rfc3339( $date );
	}

	static function get_id( $object ) {
		if ( method_exists( $object, 'get_id' ) ) {

			return $object->get_id();
		} elseif ( is_object( $object ) ) {
			return $object->id;
		}
	}

	static function get_property( $object, $property ) {
		if ( method_exists( $object, 'get_' . $property ) ) {
			return call_user_func( array( $object, 'get_' . $property ) );
		} else {
			return $object->{$property};
		}
	}


	public static function get_product( $value ) {
		$product = wc_get_product( $value );

		if ( ! empty( $product ) && $product->is_type( 'variation' ) ) {
			$product = method_exists( $product, 'get_parent_id' ) ? $product->get_parent_id() : $product->get_parent();

			return wc_get_product( $product );
		}

		return $product;

	}


	/**
	 * @param WP_Post|string $item
	 * @return array|false
	 */
	public static function get_image_dimensions( $item , $size = false , $thumbnail = false ) {
		if ( is_string( $item ) ) {
			$id   = attachment_url_to_postid( $item );
			$item = get_post( $id );
		}
		if ( ! is_a( $item, 'WP_Post' ) ) {
			return false;
		}
		$size_array = array();
		$meta = wp_get_attachment_metadata( $item->ID );
		if ( empty( $meta ) ) {
			$attachment_path = get_attached_file( $item->ID );
			$attach_data     = wp_generate_attachment_metadata( $item->ID, $attachment_path );
			wp_update_attachment_metadata( $item->ID, $attach_data );
			// Wrap the data in a response object
			$meta = wp_get_attachment_metadata( $item->ID );
		}
		if( isset($meta['sizes']) && is_array($meta['sizes']) && ! empty( $meta['sizes']) && ! empty( $size ) ) {
			foreach( $meta['sizes'] as $meta_size_id => $meta_size) {
				if( $meta_size_id == $size && isset( $meta_size['width'], $meta_size['height'] ) ) {
					$size_array =  array(
						'width'  => $meta_size['width'],
						'height' => $meta_size['height'],
					);
				}
			}
		} 
		if( $size_array ) {
			return $size_array;
		} elseif( isset( $meta['width'], $meta['height'] ) && !$thumbnail ) {

			return array(
				'width'  => $meta['width'],
				'height' => $meta['height'],
			);
		} else {
			return  array(
				'width'  => 300,
				'height' => 300,
			);
		}
	}

}
