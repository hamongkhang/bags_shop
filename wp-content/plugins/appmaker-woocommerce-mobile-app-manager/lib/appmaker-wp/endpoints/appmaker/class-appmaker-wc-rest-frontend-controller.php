<?php

//$this->options = get_option( 'appmaker_option' );
class APPMAKER_WC_REST_FRONTEND_Controller extends APPMAKER_WC_REST_Controller {

	protected $type;

	protected $isRoot = false;


	public function __construct( $type ) {
		parent::__construct();
		$this->type      = $type;
		$this->rest_base = "$this->type";
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<key>[a-zA-Z0-9\-\_]+)', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'api_permissions_check' ),
				'args'                => array(
					'context' => $this->get_context_param( array( 'default' => 'view' ) ),
				),
			),

			'schema' => array( $this, 'get_public_item_schema' ),
		) );

	}


	/**
	 * Get a single meta
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_item( $request ) {
		$key = $this->getSafeKey( $request['key'] . '_app' );

		$option = get_option( $key );

		if ( $this->type == 'navigationMenu' && ! $option ) {
			if ( class_exists( 'APPMAKER_WC' ) ) {
				$option = APPMAKER_WC_Converter::convert_navMenu_data( APPMAKER_WC::$api->APPMAKER_WC_REST_BACKEND_NAV_Controller->get_default_menu() );
			} elseif ( class_exists( 'APPMAKER_WC' ) ) {
				$option = APPMAKER_WC_Converter::convert_navMenu_data( APPMAKER_WC::$api->APPMAKER_WC_REST_BACKEND_NAV_Controller->get_default_menu() );
			}
		} elseif ( ! $option ) {
			return new WP_Error( 'rest_invalid_key', __( 'Key is not invalid.' ), array( 'status' => 404 ) );
		}


		$item = array( 'key' => $request['key'], 'data' => $option );


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
		$response = rest_ensure_response( $item['data'] );


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
	 * Get the User's schema, conforming to JSON Schema
	 *
	 * @return array
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'meta',
			'type'       => 'object',
			'properties' => array(),
		);


		return $this->add_additional_fields_schema( $schema );
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
}
