<?php

/**
 * Access terms associated with a taxonomy
 */
class APPMAKER_WC_REST_Backend_Notifications_Controller extends APPMAKER_WC_REST_BACKEND_Terms_Controller {


	public function __construct() {
		parent::__construct();
		$this->rest_base = 'backend/notification';
		add_action( 'init', array( $this, 'push_notification_post_type' ) );

	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'add_notification' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => $this->get_notification_args(),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

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
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_notification' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => array(
						'context' => $this->get_context_param( array( 'default' => 'view' ) ),
					),
				),

				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	public function push_notification_post_type() {
		register_post_type(
			'notification',
			// CPT Options
			array(
				'labels'      => array(
					'name'          => __( 'Notification' ),
					'singular_name' => __( 'Notification' ),
				),
				'public'      => true,
				'has_archive' => true,
				'rewrite'     => array( 'slug' => 'appmaker_notification' ),
				'support'     => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields' ),
			)
		);
	}

	/*
	* Add new notification
	*/
	public function add_notification( $request ) {
		$enable_notifcation_history = APPMAKER_WC::$api->get_settings( 'enable_notifcation_history', false );
		if ( ! $enable_notifcation_history ) {
			return array(
				'status'   => true,
				'disabled' => true,
			);
		}
		$return    = array( 'status' => true );
		$title     = $request['title'];
		$content   = $request['content'];
		$image     = isset( $request['image'] ) ? $request['image'] : false;
		$user_id   = isset( $request['user_id'] ) ? $request['user_id'] : false;
		$action    = $request['action'];
		$post_data = array(
			'post_title'   => $title,
			'post_content' => $content,
			'post_status'  => 'publish',
			'post_type'    => 'notification',
		);

		$post_id = wp_insert_post( $post_data );

		if ( ! empty( $image ) ) {
			add_post_meta( $post_id, 'image_url', $image );
		}

		if ( ! empty( $user_id ) ) {
			add_post_meta( $post_id, 'user_id', $user_id );
		}

		add_post_meta( $post_id, 'action', $action );
		return $return;
	}

	/**
	 * Get all notifications
	 *
	 * @param WP_REST_Request $request Full details about the request
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_notifications( $request ) {
		$data = array();
		$posts = get_posts(
			array(
				'post_type'      => 'notification',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
			)
		);

		foreach ( $posts as $post ) {
			$image_url = get_post_meta( $post->ID, 'image_url' );
			$user_id   = get_post_meta( $post->ID, 'user_id' );
			$action    = get_post_meta( $post->ID, 'action' );
			$data[]    = array(
				'id'      => $post->ID,
				'title'   => $post->post_title,
				'content' => $post->post_content,
				'image'   => ! empty( $image_url[0] ) ? $image_url[0] : '',
				'user_id' => ! empty( $user_id[0] ) ? $user_id[0] : '',
				'action'  => ! empty( $action[0] ) ? json_decode( $action[0], true ) : '',
			);
		}
		$response = rest_ensure_response( $data );
		return $response;
	}

	/**
	 * Get a single notification
	 *
	 * @param WP_REST_Request $request Full details about the request
	 *
	 * @return WP_REST_Request|WP_Error
	 */
	public function get_notification( $request ) {
		$post_id   = $request['id'];
		$image_url = get_post_meta( $post_id, 'image_url' );
		$user_id   = get_post_meta( $post_id, 'user_id' );
		$action    = get_post_meta( $post_id, 'action' );
		$post      = get_post( $post_id );
		$data      = array(
			'id'      => $post->ID,
			'title'   => $post->post_title,
			'content' => $post->post_content,
			'image'   => ! empty( $image_url[0] ) ? $image_url[0] : '',
			'user_id' => ! empty( $user_id[0] ) ? $user_id[0] : '',
			'action'  => ! empty( $action[0] ) ? json_decode( $action[0], true ) : '',
		);
		$response  = rest_ensure_response( $data );
		return $response;
	}

	/**
	 * Trim text.
	 *
	 * @param string $text Text.
	 *
	 * @return string
	 */
	public function trim( $text ) {
		$text = trim( $text );

		return $text;
	}

	/**
	 * Fields required for notification
	 */

	public function get_notification_args() {

		$params          = array();
		$params['title'] = array(
			'description'       => __( 'Title', 'appmaker-woocommerce-mobile-app-manager' ),
			'type'              => 'string',
			'validate_callback' => array( $this, 'trim' ),
			'required'          => apply_filters( 'appmaker_wc_notification_title_required', true ),
		);

		$params['content'] = array(
			'description'       => __( 'Content', 'appmaker-woocommerce-mobile-app-manager' ),
			'type'              => 'string',
			'validate_callback' => array( $this, 'trim' ),
			'required'          => apply_filters( 'appmaker_wc_notification_content_required', true ),
		);

		$params['image'] = array(
			'description'       => __( 'Image', 'appmaker-woocommerce-mobile-app-manager' ),
			'type'              => 'url',
			'validate_callback' => array( $this, 'trim' ),
			'required'          => apply_filters( 'appmaker_wc_notification_image_required', false ),
		);

		$params['action'] = array(
			'description'       => __( 'Action', 'appmaker-woocommerce-mobile-app-manager' ),
			'type'              => 'string',
			'validate_callback' => array( $this, 'trim' ),
			'required'          => apply_filters( 'appmaker_wc_notification_action_required', false ),
		);

		return $params;
	}

}
