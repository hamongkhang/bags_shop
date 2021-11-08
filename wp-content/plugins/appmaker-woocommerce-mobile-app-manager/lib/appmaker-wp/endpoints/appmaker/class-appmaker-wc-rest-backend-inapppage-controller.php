<?php

//$this->options = get_option( 'appmaker_option' );
class APPMAKER_WC_REST_BACKEND_INAPPPAGE_Controller extends APPMAKER_WC_REST_Controller {

	protected $type;

	protected $isRoot = true;
	protected $inAppPagesKey = '_inAppPages';

	public function __construct() {
		parent::__construct();
		$this->type      = 'inAppPages';
		$this->rest_base = "backend/$this->type";
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'api_permissions_check' ),
				'args'                => $this->get_collection_params(),
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_item' ),
				'permission_callback' => array( $this, 'api_permissions_check' ),
				'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<key>[a-zA-Z0-9\-\_]+)', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'api_permissions_check' ),
				'args'                => array(
					'context' => $this->get_context_param( array( 'default' => 'view' ) ),
				),
			),
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_item' ),
				'permission_callback' => array( $this, 'api_permissions_check' ),
				'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
			),
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_item' ),
				'permission_callback' => array( $this, 'api_permissions_check' ),
				'args'                => array(
					'force'    => array(
						'default'     => false,
						'description' => __( 'Required to be true, as resource does not support trashing.' ),
					),
					'reassign' => array(),
				),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );

	}

	/**
	 * Get the query params for collections
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$query_params = array(
			'context' => $this->get_context_param(),

		);

		$query_params['context']['default'] = 'view';


		return $query_params;
	}

	/**
	 * Get all metas
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		$response = get_option( $this->getSafeKey( $this->inAppPagesKey ), array(
			'home' => array(
				'id'    => 'home',
				'label' => 'Home',
				'key'   => 'home'
			)
		) );

		return $response;
	}

	/**
	 * Get a single meta
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_item( $request ) {


		$key = $this->getSafeKey( $request['key'] );

		$option = get_option( $key );
		if ( ! $option && $request['key'] == 'home' ) {
			$option = array(
				'id'      => 'home',
				'title'   => 'Home',
				'widgets' => array()
			);
		}

		if ( ! $option ) {
			return new WP_Error( 'rest_invalid_key', __( 'Key is not invalid.' ), array( 'status' => 404 ) );
		}
		$item     = array( 'key' => $request['key'], 'data' => $option );
		$data     = $this->prepare_item_for_response( $item, $request );
		$response = rest_ensure_response( $data );

		return $response;
	}

	/**
	 * Prepare the item for the REST response.
	 *
	 * @param mixed $item WordPress representation of the item.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response $response
	 */
	public function prepare_item_for_response( $item, $request ) {

		// Wrap the data in a response object
		$response = rest_ensure_response( $item );


		/**
		 * Filter meta data returned from the REST API.
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param object $meta User object used to create response.
		 * @param WP_REST_Request $request Request object.
		 */
		return $response;
	}

	/**
	 * Create a single meta
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function create_item( $request ) {
		if ( ! empty( $request['key'] ) ) {
			$key = $request['key'];
		} else {
			$key = $this->get_random_key();
		}

		$data = stripslashes( $request['data'] );
		$data = json_decode( $data );

		if ( json_last_error() != JSON_ERROR_NONE ) {
			return new WP_Error( 'invalid_json', __( 'Json is invalid.' ), array( 'status' => 400 ) );
		}

		if ( empty( $data ) || ! isset( $data->title ) || ! isset( $data->widgets ) || empty( $data->title ) ) {
			return new WP_Error( 'rest_invalid_request', __( 'Please include all data.' ), array( 'status' => 400 ) );
		}

		$data->id = $key;

		$appData = APPMAKER_WC_Converter::convert_inAppPage_data( $data, $key );


		if ( json_last_error() != JSON_ERROR_NONE ) {
			return new WP_Error( 'invalid_json', __( 'Json is invalid.' ), array( 'status' => 400 ) );
		}

		if ( add_option( $this->getSafeKey( $key ), $data, '', 'no' ) ) {
			add_option( $this->getSafeKey( $key . '_app' ), $appData, '', 'no' );


			$savedKeys = get_option( $this->getSafeKey( $this->inAppPagesKey ), array() );
			$savedKeys = array_merge( $savedKeys, array(
				$key => array(
					'id'    => $appData['id'],
					'label' => $appData['title'],
					'key'   => $key
				)
			) );
			update_option( $this->getSafeKey( $this->inAppPagesKey ), $savedKeys );

			$request->set_param( 'context', 'edit' );
			$item     = array( 'key' => $key, 'data' => $data );
			$response = $this->prepare_item_for_response( $item, $request );
			$response = rest_ensure_response( $response );
			$response->set_status( 201 );
			$response->header( 'Location', rest_url( sprintf( '/%s/%s/%s', $this->namespace, $this->rest_base, $key ) ) );

			return $response;
		}

		return new WP_Error( 'rest_item_exists', __( 'Cannot create existing resource.' ), array( 'status' => 400 ) );

	}

	/**
	 * Update a single meta
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function update_item( $request ) {
		$key    = $request['key'];
		$option = get_option( $this->getSafeKey( $key ) );

		if ( ! $option && $request['key'] == 'home' ) {
			$option = array(
				'id'      => 'home',
				'title'   => 'Home',
				'widgets' => array()
			);
		}

		if ( ! $option ) {
			return new WP_Error( 'rest_invalid_key', __( 'Key is not valid.' ), array( 'status' => 404 ) );
		}

		$data = stripslashes( $request['data'] );
		$data = json_decode( $data );
		if ( json_last_error() != JSON_ERROR_NONE ) {
			return new WP_Error( 'invalid_json', __( 'Json is invalid.' ), array( 'status' => 400 ) );
		}

		if ( ! isset( $data->title ) || ! isset( $data->widgets ) || empty( $data->title ) ) {
			return new WP_Error( 'rest_invalid_request', __( 'Please include all data.' ), array( 'status' => 400 ) );
		}

		$appData = APPMAKER_WC_Converter::convert_inAppPage_data( $data, $key );
		update_option( $this->getSafeKey( $key ), $data );
		update_option( $this->getSafeKey( $key . '_app' ), $appData );
		$request->set_param( 'context', 'edit' );

		$savedKeys         = get_option( $this->getSafeKey( $this->inAppPagesKey ), array() );
		$savedKeys[ $key ] = array( 'id' => $appData['id'], 'label' => $appData['title'], 'key' => $key );
		update_option( $this->getSafeKey( $this->inAppPagesKey ), $savedKeys );

		$item     = array( 'key' => $request['key'], 'data' => $data );
		$response = $this->prepare_item_for_response( $item, $request );
		$response = rest_ensure_response( $response );

		return $response;
	}

	/**
	 * Delete a single meta
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function delete_item( $request ) {
		$key    = $this->getSafeKey( $request['key'] );
		$option = get_option( $key );
		if ( ! $option ) {
			return new WP_Error( 'rest_invalid_key', __( 'Key is not invalid.' ), array( 'status' => 404 ) );
		}
		delete_option( $key );

		$savedKeys = get_option( $this->getSafeKey( $this->inAppPagesKey ), array() );
		unset( $savedKeys[ $request['key'] ] );
		update_option( $this->getSafeKey( $this->inAppPagesKey ), $savedKeys );

		$request->set_param( 'context', 'delete' );
		$item     = array(
			'key'  => $request['key'],
			'data' => $option
		);
		$response = $this->prepare_item_for_response( $item, $request );
		$response = rest_ensure_response( $response );

		return $response;
	}

	/**
	 * Get the User's schema, conforming to JSON Schema
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'meta',
			'type'       => 'object',
			'properties' => array(

				'data' => array(
					'description' => __( 'JSON data .' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),

				),
				'key'  => array(
					'description' => __( 'Key for that data .' ),
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),

				),

			),
		);


		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Prepare links for the request.
	 *
	 * @param WP_Post $meta User object.
	 *
	 * @return array Links for the given meta.
	 */
	protected function prepare_links( $meta ) {
		$links = array(
			'self'       => array(
				'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $meta->ID ) ),
			),
			'collection' => array(
				'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ),
			),
		);

		return $links;
	}

	/**
	 * Determine if the current meta is allowed to make the desired roles change.
	 *
	 * @param integer $meta_id
	 * @param array $roles
	 *
	 * @return WP_Error|boolean
	 */
	protected function check_role_update( $meta_id, $roles ) {
		global $wp_roles;

		foreach ( $roles as $role ) {

			if ( ! isset( $wp_roles->role_objects[ $role ] ) ) {
				return new WP_Error( 'rest_meta_invalid_role', sprintf( __( 'The role %s does not exist.' ), $role ), array( 'status' => 400 ) );
			}

			$potential_role = $wp_roles->role_objects[ $role ];
			// Don't let anyone with 'edit_metas' (admins) edit their own role to something without it.
			// Multisite super admins can freely edit their blog roles -- they possess all caps.
			if ( ! ( is_multisite() && current_meta_can( 'manage_sites' ) ) && get_current_meta_id() === $meta_id && ! $potential_role->has_cap( 'edit_metas' ) ) {
				return new WP_Error( 'rest_meta_invalid_role', __( 'You cannot give resource that role.' ), array( 'status' => rest_authorization_required_code() ) );
			}

			// The new role must be editable by the logged-in meta.

			/** Include admin functions to get access to get_editable_roles() */
			require_once ABSPATH . 'wp-admin/includes/admin.php';

			$editable_roles = get_editable_roles();
			if ( empty( $editable_roles[ $role ] ) ) {
				return new WP_Error( 'rest_meta_invalid_role', __( 'You cannot give resource that role.' ), array( 'status' => 403 ) );
			}
		}

		return true;

	}


}
