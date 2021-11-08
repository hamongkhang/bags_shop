<?php
/**
 * REST API Total Sales controller
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * REST API Total Sales controller class.
 *
 */
class APPMAKER_WC_REST_BACKEND_Report_Controller extends APPMAKER_WC_REST_BACKEND_Terms_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'appmaker-wc/v1/backend/report';

	/**
	 * Route base.
	 *
	 * @var string
	 */

	/**
	 * Register the routes for products.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/sales',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'sales' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/multi-currency',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_sales_data' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Return plugin meta
	 *
	 * @return array
	 */
	public function sales( $request ) {
		$return['sales']                    = array(
			'currecny'      => get_woocommerce_currency(),
			'base_location' => wc_get_base_location(),
		);
		$return['sales']['all']['last_day'] = self::total_sales( 'last_day', false, $request, null );
		$return['sales']['app']['last_day'] = self::total_sales( 'last_day', true, $request , null );
		$return['sales']['android']['last_day'] = self::total_sales( 'last_day', false, $request, 'android' );
		$return['sales']['ios']['last_day'] = self::total_sales( 'last_day', false, $request, 'ios' );
		if ( $request['show_all_time'] == true ) {
			$return['sales']['all']['all_time'] = self::total_sales( 'all_time', false, $request, null );
			$return['sales']['app']['all_time'] = self::total_sales( 'all_time', true, $request, null );
			$return['sales']['android']['all_time'] = self::total_sales( 'all_time', false, $request, 'android' );
		    $return['sales']['ios']['all_time'] = self::total_sales( 'all_time', false, $request, 'ios' );
		}
		if ( $request['show_last_month'] == true ) {
			$return['sales']['all']['last_month'] = self::total_sales( 'last_month', false, $request, null );
			$return['sales']['app']['last_month'] = self::total_sales( 'last_month', true , $request, null );
			$return['sales']['android']['last_month'] = self::total_sales( 'last_month', false, $request, 'android' );
		    $return['sales']['ios']['last_month'] = self::total_sales( 'last_month', false, $request, 'ios' );
		}
		if ( $request['show_last_week'] == true ) {
			$return['sales']['all']['last_week'] = self::total_sales( 'last_week', false, $request , null );
			$return['sales']['app']['last_week'] = self::total_sales( 'last_week', true, $request, null );
			$return['sales']['android']['last_week'] = self::total_sales( 'last_week', false, $request, 'android' );
		    $return['sales']['ios']['last_week'] = self::total_sales( 'last_week', false, $request, 'ios' );
		}
		if ( isset( $request['start_date'] ) && isset( $request['end_date'] ) ) {
			$return['sales']['all']['custom_start_end_date'] = self::total_sales( 'start_end_date', false, $request , null );
			$return['sales']['app']['custom_start_end_date'] = self::total_sales( 'start_end_date', true, $request, null );
			$return['sales']['android']['custom_start_end_date'] = self::total_sales( 'start_end_date', false, $request, 'android' );
		    $return['sales']['ios']['custom_start_end_date'] = self::total_sales( 'start_end_date', false, $request, 'ios' );
			
		}
		return $return;
	}

	public static function total_sales( $duration = 'all_time', $from_app = false, $request = array() , $platform = null ) {
		global $wpdb;
		$multi_currency =  isset( $request['multi_currency'] ) && $request['multi_currency'];
		$query = 'SELECT SUM(meta.meta_value) AS total_sales, COUNT(posts.ID) AS total_orders';

		if ( $multi_currency ) {
			$query .= ', order_currency_meta.meta_value AS order_currency';
		}

		$query .= " FROM {$wpdb->posts} AS posts";

		$query .= " LEFT JOIN {$wpdb->postmeta} AS meta ON meta.meta_key = '_order_total' AND posts.ID = meta.post_id";

		if ( $multi_currency ) {
			$query .= " LEFT JOIN {$wpdb->postmeta} AS order_currency_meta ON order_currency_meta.meta_key = '_order_currency' AND posts.ID = order_currency_meta.post_id";
		}

		if ( $from_app ) {
			$query .= " LEFT JOIN {$wpdb->postmeta} AS app_meta ON app_meta.meta_key = 'from_app' AND posts.ID = app_meta.post_id";
			$query .= ' WHERE';
			$query .= ' app_meta.meta_value IS NOT NULL AND ';
		} else if ( $platform ) {
			$query .= " LEFT JOIN {$wpdb->postmeta} AS app_meta ON app_meta.meta_key = 'appmaker_mobile_platform' AND posts.ID = app_meta.post_id";
			$query .= ' WHERE';
			$query .= " app_meta.meta_value = '".$platform."' AND ";

		} else {
			$query .= ' WHERE';
		}

		if ( $duration === 'last_month' ) {
			$query .= " post_date > '" . date( 'Y-m-d', strtotime( '-30 days' ) ) . "'\n AND ";
		} elseif ( $duration === 'last_day' ) {
			$query .= " post_date > '" . date( 'Y-m-d', strtotime( '-1 days' ) ) . "'\n AND ";
		} elseif ( $duration === 'last_week' ) {
			$query .= " post_date > '" . date( 'Y-m-d', strtotime( '-7 days' ) ) . "'\n AND ";
		} elseif ( $duration === 'start_end_date' ) {
			$start_date = date( 'Y-m-d', strtotime( $request['start_date'] ) );
			$end_date   = date( 'Y-m-d', strtotime( $request['end_date'] ) );
			$query     .= " post_date BETWEEN '" . $start_date . "' AND '" . $end_date . "' \n AND ";
		}

		$query .= " posts.post_type = 'shop_order' AND posts.post_status IN ( '" . implode( "','", array( 'wc-completed', 'wc-processing', 'wc-on-hold' ) ) . "' )";

		if ( $multi_currency ) {
			$query .= ' GROUP BY order_currency';
		}

		if ( $multi_currency ) {
			return apply_filters( 'appmaker_reports_sales_overview_order_totals', $wpdb->get_results( $query ) );
		}
		return apply_filters( 'appmaker_reports_sales_overview_order_totals', $wpdb->get_row( $query ) );
	}
}
