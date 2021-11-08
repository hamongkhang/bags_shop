<?php
/**
 * REST API Notifications controller
 *
 * Handles requests to the /appmaker_notifications endpoint.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * REST API Notifications controller class.
 *
 */
class APPMAKER_WC_REST_Notifications_Controller extends APPMAKER_WC_REST_Posts_Abstract_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'appmaker-wc/v1';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'inAppPages/dynamic/push-notification-history';

	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = 'notification';


	/**
	 * Initialize product actions.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Register the routes for notifications.
	 */
	public function register_routes() {

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_notifications' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

	}

	/**
	 * Get a collection of posts.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_notifications( $request ) {
		$logged_in_user_id = get_current_user_id();
		$options           = array(
			'post_type'      => 'notification',
			'post_status'    => 'publish',
			'posts_per_page' => 40,
			'order'=>'DESC',
			'orderby'=>'ID',
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => 'user_id',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => 'user_id',
					'compare' => 'IS NULL',
				),
			),
		);

		if ( 0 !== $logged_in_user_id ) {
			$options['meta_query'][] = array(
				'key'   => 'user_id',
				'value' => $logged_in_user_id,
			);
		}

		$posts = get_posts( $options );

		$data = array();
		foreach ( $posts as $post ) {
			$image_url = get_post_meta( $post->ID, 'image_url' );
			$user_id   = get_post_meta( $post->ID, 'user_id' );
			$action    = get_post_meta( $post->ID, 'action' );
			$item_data = array(
				'view'     => 'simple',
				'type'     => 'notification',
				'template' => 'template-5',
				'title'    => $post->post_title,
				'message'  => $post->post_content, 
				'timeStamp'   => $post->post_date,
				'style'    => array(
					'title'   => array(),
					'content' => array(),
				),
				'action'   => ! empty( $action[0] ) ? json_decode( $action[0], true ) : '',
			);
			if ( ! empty( $image_url[0] ) ) {
				$item_data['imgUri'] = $image_url[0];
			}
			if ( ! empty( $item_data['action'] ) ) {
				$item_data['action'] = array(
					'type'   => APPMAKER_WP_WC_Converter::get_type( $item_data['action']['action']['value'] ),
					'params' =>  $item_data['action']['action_value'],
				);
			} else {
				$item_data['action'] = array(
					'type'   => 'NO_ACTION',
					'params' =>  '',
				);
			}
			$data[] = $item_data;
		}

		$notfication_response = array(
			'id'      => 'posts',
			'title'   => 'Notifications',
			'widgets' => $data,	
			'pages'   => 1,
		);
		$response             = rest_ensure_response( $notfication_response );
		return $response;
	}

}
