<?php

class APPMAKER_WC_Admin_hooks {
	/**
	 * Holds the values to be used in the fields callbacks
	 *
	 * @var object
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct() {
		add_action( 'restrict_manage_posts', array( $this, 'admin_posts_filter_restrict_manage_posts' ), 999 );
		add_filter( 'parse_query', array( $this, 'posts_filter' ) );
	}

	public function admin_posts_filter_restrict_manage_posts() {
		$type = 'shop_order';
		if ( isset( $_GET['post_type'] ) ) {
			$type = $_GET['post_type'];
		}
		if ( 'shop_order' === $type ) {
			$checked = isset( $_GET['from_app'] ) ? ( ( $_GET['from_app'] === 1 || $_GET['from_app'] === '1' ) ? 'checked' : '' ) : '';
			echo '<div class="alignleft actions bulkactions" style="margin-top: 2px;font-weight: bold">
				<label for="from_app">App Orders
					<input id="from_app" name="from_app" type="checkbox" value="1" ' . $checked . ' />
				</label>
			</div>';
		}
	}

	public function posts_filter( $query ) {
		global $pagenow;
		$type = 'shop_order';
		if ( isset( $_GET['post_type'] ) ) {
			$type = $_GET['post_type'];
		}
		if ( 'shop_order' == $type && is_admin() && $pagenow == 'edit.php' && isset( $_GET['from_app'] ) && $_GET['from_app'] != '' ) {
			$query->query_vars['meta_key']   = 'from_app';
			$query->query_vars['meta_value'] = ( $_GET['from_app'] == 1 || $_GET['from_app'] == '1' );
		}
	}


}

new APPMAKER_WC_Admin_hooks();
