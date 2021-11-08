<?php
/**
 *
 * @wordpress-plugin
 * Plugin Name: Appmaker – Convert WooCommerce to Android & iOS Native Mobile Apps
 * Plugin URI: https://appmaker.xyz
 * Description: This Plugin is used to manage Android and iOS mobile app created for your WooCommerce store
 * Version: 1.35.9
 * Author: Appmaker
 * Author URI: https://appmaker.xyz/woocommerce
 * Text Domain: appmaker-woocommerce-mobile-app-manager
 * Domain Path: /i18n/languages/
 * WC requires at least: 2.6.0
 * WC tested up to: 5.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}




/**
 * Class APPMAKER_WC
 */
class APPMAKER_WC {

	/**
	 * Version
	 *
	 * @var string
	 */
	static $version = '1.35.9';

	/**
	 * Plugin root path
	 *
	 * @var string
	 */
	static $root;

	/**
	 *  API Object instance
	 *
	 * @var APPMAKER_WC_API
	 */
	static $api;

	static $white_labeled = false;

	/**
	 * Initialise plugin
	 */

	public static function init() {
		self::$root = dirname( __FILE__ );
		if ( (isset( $_GET['skip_appamker_wc_plugin'] ) && (1 == $_GET['skip_appamker_wc_plugin'] )) ||  ( ! class_exists( 'WooCommerce' )) ) {
			return true;
		}
		register_deactivation_hook( __FILE__, array( self::name(), 'deactivate' ) );
		register_uninstall_hook( __FILE__, array( self::name(), 'uninstall' ) );
		self::load_plugin_textdomain();
		if ( is_admin() ) {
			require_once dirname( __FILE__ ) . '/lib/admin/class-appmaker-wc-options.php';
			require_once dirname( __FILE__ ) . '/lib/admin/class-appmaker-wc-admin-hooks.php';
			include_once dirname( __FILE__ ) . '/lib/wc-extended/class-appmaker-wc-category-filter.php';
			require_once dirname( __FILE__ ) . '/lib/admin/deactivation-feedback/appmaker-wc-feedback-form.php';
			appmaker_wc_feedback_include_init( plugin_basename( __FILE__ ) );
		}
		require_once dirname( __FILE__ ) . '/lib/class-appmaker-wc-general-helper.php';
		require_once dirname( __FILE__ ) . '/lib/class-appmaker-wc-general-hooks.php';
		require_once dirname( __FILE__ ) . '/lib/appmaker-multi-step-checkout/appmaker-multi-step-checkout.php';

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array(
			self::name(),
			'add_plugin_action_links',
		) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );

		self::load_appmaker_rest_api();
		require_once( self::$root . '/lib/class-appmaker-wc-install.php' );

		require_once dirname( __FILE__ ) . '/lib/wc-extended/class-appmaker-wc-coupon.php';
	}


	public static function load_plugin_textdomain() {
		$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, 'appmaker-woocommerce-mobile-app-manager' );
		unload_textdomain( 'appmaker-woocommerce-mobile-app-manager' );
		load_textdomain( 'appmaker-woocommerce-mobile-app-manager', WP_LANG_DIR . '/appmaker-woocommerce-mobile-app-manager/appmaker-woocommerce-mobile-app-manager-' . $locale . '.mo' );
		load_plugin_textdomain( 'appmaker-woocommerce-mobile-app-manager', false, plugin_basename( dirname( __FILE__ ) ) . '/i18n/languages' );
	}

	/**
	 * Rteurn class name
	 *
	 * @return string
	 */
	public static function name() {
		return 'APPMAKER_WC';
	}

	/**
	 * Load rest api class.
	 */
	public static function load_appmaker_rest_api() {
		require_once( self::$root . '/lib/class-appmaker-wc-api.php' );
		self::$api = APPMAKER_WC_API::get_instance();
	}

	/**
	 * Plugin uninstall hook
	 */
	public static function uninstall() {
		delete_option( 'appmaker_wc_settings' );
		update_option( 'appmaker_wc_settings', false );
		do_action( 'APPMAKER_WC_Plugin_uninstall' );
	}

	/**
	 * Plugin deactivate hook
	 */
	public static function deactivate() {
		do_action( 'APPMAKER_WC_Plugin_deactivate' );
	}

	/**
	 * Add plugin action links
	 *
	 * @param array $links Array of links.
	 *
	 * @return array
	 */
	public static function add_plugin_action_links( $links ) {
		return array_merge(
			array(
				'settings' => '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/admin.php?page=appmaker-wc-admin">Settings</a>',
			),
			$links
		);
	}

	public static function plugin_url( $path ) {
		return plugins_url( $path, __FILE__ );
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param    mixed $links Plugin Row Meta.
	 * @param    mixed $file Plugin Base file.
	 *
	 * @return    array
	 */
	public static function plugin_row_meta( $links, $file ) {
		if ( plugin_basename( __FILE__ ) === $file ) {
			if ( ! self::$white_labeled ) {
				$row_meta = array(
					'docs'       => '<a target="_blank" href="https://appmaker.xyz/docs/">Docs</a>',
					'create_app' => '<a target="_blank" href="https://appmaker.xyz/woocommerce/">Create App</a>',
				);
				return array_merge( $links, $row_meta );
			}
		}

		return (array) $links;
	}

	/**
	 * Returns error notice if WooCommerce is not activates/installed
	 */
	public static function appmaker_woocommerce_error_notice() {
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			add_action( 'admin_notices', array( 'APPMAKER_WC', 'show_admin_message' ) );
		}
	}

	/**
	 * Returns WooCommerce Notice
	 */
	public static function show_admin_message() {
		$class   = 'notice notice-error';
		$message = __( 'Appmaker plugin requires WooCommerce installed and activate to work properly.', 'appmaker-woocommerce-mobile-app-manager' );
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
	}
}

function appmaker_wc_plugin_activated( $plugin ) {
	if ( $plugin == plugin_basename( __FILE__ ) ) {
		do_action( 'APPMAKER_WC_Plugin_activate' );
		wp_safe_redirect( admin_url( 'admin.php?page=appmaker-wc-admin' ) );
		exit;
	}
}



add_action( 'admin_init', array( 'APPMAKER_WC', 'appmaker_woocommerce_error_notice' ) );

add_action( 'activated_plugin', 'appmaker_wc_plugin_activated' );

add_action( 'plugins_loaded', array( 'APPMAKER_WC', 'init' ) );

