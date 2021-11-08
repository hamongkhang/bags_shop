<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Install Class.
 */
class APPMAKER_WC_Install {
	private static $force = false;
	/**
	 * Updates and callbacks that need to be run per version.
	 *
	 * @var array $updates
	 */
	private static $updates = array(
		'0.0.19' => array(
			array( __CLASS__, 'migrate_woocommerce_v1' ),
		),
		'0.9.0' => array(
			array( __CLASS__, 'migrate_options_to_appmaker_settings' ),
		),
		'1.4.1' => array(
			array( __CLASS__, 'add_meta_into_inapppage_images' ),
		),
	);


	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action( 'appmaker_wc_register_rest_routes', array( __CLASS__, 'check_version' ), 5 );
		if ( current_user_can( 'activate_plugins' ) && isset( $_GET['appmaker_wc_force_migrate'] ) && $_GET['appmaker_wc_force_migrate'] ) {
			add_action( 'appmaker_wc_register_rest_routes', array( __CLASS__, 'add_meta_into_inapppage_images' ) );
			if(isset( $_GET['force'] ) && $_GET['force']){
			    self::add_meta_into_inapppage_images();
            }
		}
	}

	/**
	 * Check WooCommerce version and run the updater is required.
	 *
	 * This check is done on all requests and runs if he versions do not match.
	 */
	public static function check_version() {
		if ( get_option( 'appmaker_wc_version' ) !== APPMAKER_WC::$version ) {
			self::install();
			do_action( 'appmaker_wc_updated' );
		}
	}


	/**
	 * Install WC.
	 */
	public static function install() {
		if ( ! defined( 'APPMAKER_WC_INSTALLING' ) ) {
			define( 'APPMAKER_WC_INSTALLING', true );
		}

		self::update();
		self::update_version();

		// Trigger action.
		do_action( 'appmaker_wc_installed' );
	}

	/**
	 * Execute all update functions
	 */
	private static function update() {
		$current_db_version = get_option( 'appmaker_wc_version', '1.0.0' );
		foreach ( self::$updates as $version => $update_callbacks ) {
			if ( version_compare( $current_db_version, $version, '<' ) ) {
				foreach ( $update_callbacks as $update_callback ) {
					call_user_func( $update_callback );
				}
			}
		}
	}

	/**
	 * Update WC version to current.
	 */
	private static function update_version() {
		delete_option( 'appmaker_wc_version' );
		add_option( 'appmaker_wc_version', APPMAKER_WC::$version, '', false );
	}



	public static function add_meta_into_inapppage_images(){
        $inAppPages = get_option( 'appmaker_wc_inAppPages__inAppPages',array(
            'home' => array(
                'id'    => 'home',
                'label' => 'Home',
                'key'   => 'home',
            ),
        ) );
        if(!empty($inAppPages) && is_array($inAppPages)) {
            foreach ($inAppPages as $key => $meta) {
                // Loop through in-app pages
                $inAppPage = get_option('appmaker_wc_inAppPages_' . $key);
                if (!empty($inAppPage) && isset($inAppPage->widgets) && is_array($inAppPage->widgets)) {
                    // Loop through widgets
                    foreach ($inAppPage->widgets as $widget){
                        if(!empty($widget) && isset($widget->data) && is_array($widget->data)){
                            foreach ($widget->data as $field_key => $data) {
                                if(isset($data->data->image) && !empty($data->data->image) ){ // && !isset($data->data->image->value->meta)
                                    $image = $data->data->image->display_value;
                                    $id =  attachment_url_to_postid($image);
                                    if(!empty($id)){
                                        $response = self::get_media_meta($id);
                                        $data->data->image->value = $response;
                                    }
                                }

                                if(isset($data->image) && !empty($data->image) ){ // && !isset($data->image->value->meta)
                                    $image = $data->image->display_value;
                                    $id =  attachment_url_to_postid($image);
                                    if(!empty($id)){
                                        $response = self::get_media_meta($id);
                                        $data->image->value = $response;
                                    }
                                }
                            }
                        }
                    }
                }
                update_option('appmaker_wc_inAppPages_' . $key, $inAppPage);
            }
        }
    }

    public static function get_media_meta($id){
        $attachment = get_post( $id );
        $meta = wp_get_attachment_metadata($attachment->ID);
        if( empty($meta) ) {
            $attachment_path = get_attached_file($attachment->ID);
            if (!function_exists('wp_generate_attachment_metadata')) {
                require_once(ABSPATH . 'wp-admin/includes/image.php');
            }
            $attach_data = wp_generate_attachment_metadata($attachment->ID, $attachment_path);
            wp_update_attachment_metadata($attachment->ID, $attach_data);
            // Wrap the data in a response object
            $meta = wp_get_attachment_metadata($attachment->ID);
        }
        return array( 'id' => $attachment->ID, 'url' => $attachment->guid, 'meta' => array('width' => $meta['width'],'height' => $meta['height']) );
    }



	public static function migrate_options_to_appmaker_settings() {
		$options = get_option( 'appmaker_wc_settings' , array() );
		$settings = array();
		if ( isset( $options['facebook_id'] ) && ! empty( $options['facebook_id'] ) ) {
			$settings['facebook_id'] = $options['facebook_id'];
		}

		if ( isset( $options['facebook_secret'] ) && ! empty( $options['facebook_secret'] ) ) {
			$settings['facebook_secret'] = $options['facebook_secret'];
		}

		if ( isset( $options['fcm_server_key'] ) && ! empty( $options['fcm_server_key'] ) ) {
			$settings['fcm_server_key'] = $options['fcm_server_key'];
		}

		$options = get_option( 'appmaker_wc_custom_settings', array() );
		$options = array_merge( $options, $settings );
		update_option( 'appmaker_wc_custom_settings', $options );

	}

	public static function migrate_woocommerce_v1() {
		self::migrate_woocommerce_v1_in_app_pages();
		self::migrate_woocommerce_v1_nav_menu();
	}

	public static function migrate_woocommerce_v1_nav_menu() {
		$nav_backend_controller = new APPMAKER_WC_REST_BACKEND_NAV_Controller();
		$new_nav_menu           = get_option( $nav_backend_controller->getSafeKey( 'mainmenu' ) );
		if ( empty( $new_nav_menu ) || self::$force ) {
			global $mobappNavigationSettings;
			$mobappNavigationSettings = get_option( 'mobappNavigationSettings' );
			require_once( 'vendor/appmaker_woocommerce_v1/v1_nav_menu_convert.php' );
			$v1_nav_menu_convert = new v1_nav_menu_convert();
			$menu                = $v1_nav_menu_convert->get_menu();
			if ( ! empty( $menu ) ) {
				$menu    = addslashes( json_encode( $menu ) );
				$request = new WP_REST_Request();
				$request->set_param( 'data', $menu );
				$res = $nav_backend_controller->create_item( $request );
			}
		}
	}

	public static function migrate_woocommerce_v1_in_app_pages() {
		$in_app_page_backend_controller = new APPMAKER_WC_REST_BACKEND_INAPPPAGE_Controller();
		require_once( 'vendor/appmaker_woocommerce_v1/class-wooapp-api-InAppPages.php' );
		$in_app_pages_a         = new WOOAPP_API_InAppPages();
		$v2_n_app_pages_widgets = array();
		foreach ( WOOAPP_API_InAppPages::get_pages() as $key => $in_app_pages ) {
			$new_in_app_page = get_option( $in_app_page_backend_controller->getSafeKey( $key ) );
			if ( empty( $new_in_app_page ) || self::$force ) {
				$v2_n_app_pages_widgets[ $key ] = $in_app_pages_a->get_page_by_name( $key );
			}
		}
		foreach ( $v2_n_app_pages_widgets as $key => $app_pages_widget ) {
			$app_pages_widget = addslashes( json_encode( $app_pages_widget['data'] ) ); // Convert array to object.
			$request          = new WP_REST_Request();
			$request->set_param( 'key', $key );
			$request->set_param( 'data', $app_pages_widget );
			if ( self::$force ) {
				$in_app_page_backend_controller->delete_item( $request );
			}
			$res = $in_app_page_backend_controller->create_item( $request );
		}
	}
}

APPMAKER_WC_Install::init();
