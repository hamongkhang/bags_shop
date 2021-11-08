<?php

class APPMAKER_WC_REST_Posts_Controller extends APPMAKER_WP_WC_REST_Posts_Controller {

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
}
