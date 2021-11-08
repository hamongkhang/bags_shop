<?php
/*
* Get latest version
*/
if ( ! function_exists( 'appmaker_wc_feedback_include_init' ) ) {
	function appmaker_wc_feedback_include_init( $base ) {
		global $appmaker_wc_options, $appmaker_wc_active_plugin;
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		$wp_content_dir = defined( 'WP_CONTENT_DIR' ) ? WP_CONTENT_DIR : ABSPATH . 'wp-content';
		$wp_plugins_dir = defined( 'WP_PLUGIN_DIR' ) ? WP_PLUGIN_DIR : $wp_content_dir . '/plugins';

		$appmaker_wc_dir                    = $wp_plugins_dir . '/' . dirname( $base ) . '/plugin.php';
		$appmaker_wc_active_plugin[ $base ] = get_plugin_data( $wp_plugins_dir . '/' . $base );

		require_once( dirname( __FILE__ ) . '/feedback-form.php' );

		if ( ! function_exists( 'appmaker_admin_enqueue_scripts' ) ) {
			function appmaker_admin_enqueue_scripts() {
				global $hook_suffix;
				if ( 'plugins.php' === $hook_suffix ) {
					if ( ! defined( 'DOING_AJAX' ) ) {
						wp_enqueue_style( 'appmaker-modal-css', APPMAKER_WC::plugin_url( 'lib/admin/deactivation-feedback/css/modal.css' ) );
						appmaker_add_deactivation_feedback_dialog_box();
					}
				}
			}
		}
		add_action( 'admin_enqueue_scripts', 'appmaker_admin_enqueue_scripts' );
	}
}
