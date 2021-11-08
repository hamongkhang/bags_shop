<?php

/**
 * Access terms associated with a taxonomy
 */
class APPMAKER_WC_REST_BACKEND_Terms_Controller extends APPMAKER_WP_WC_REST_BACKEND_Terms_Controller {
	public $plugin = 'appmaker_wc';
	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'appmaker-wc/v1';
	protected $taxonomy = 'product_cat';
	protected $isRoot = true;
}
