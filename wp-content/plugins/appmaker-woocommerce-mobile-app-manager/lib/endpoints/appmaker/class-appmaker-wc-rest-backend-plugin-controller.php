<?php

/**
 * Access terms associated with a taxonomy
 */
class APPMAKER_WC_REST_BACKEND_Plugin_Controller extends APPMAKER_WC_REST_BACKEND_Terms_Controller {

	protected $isRoot = true;

	public function __construct() {
		parent::__construct();
		$this->rest_base = 'backend/plugin';
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base .'/configured',
			array(
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'plugin_configured' ),
					'permission_callback' => array( $this, 'api_permissions_check' ),
					'args'                => array(
						'context' => $this->get_context_param( array( 'default' => 'view' ) ),
					),
				),
			)
		);
	}

	public function plugin_configured( $request ) {
		$return = array('status' => false , 'data' => array() );
		$project_id = '';
		if ( isset($request['project_id']) && !empty( $request['project_id'] ) ) {
			$project_id  = $request['project_id'];
		} 		
		if ( !empty( $project_id ) ) {
			$options = get_option('appmaker_wc_settings');
			$options['project_id'] = $project_id;
			update_option('appmaker_wc_settings', $options);
			$return['status'] = true;
			$return['data'] = $options;
		}
		return $return;
	}
}
