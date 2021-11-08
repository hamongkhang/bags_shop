<?php


if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if ( file_exists( WPIE_CLASSES_DIR . '/class-wpie-security.php' ) ) {
        require_once(WPIE_CLASSES_DIR . '/class-wpie-security.php');
}

class WPIE_GDrive_Extension {

        public function __construct() {

                add_filter( 'wpie_import_upload_sections', array( $this, 'get_gd_upload_views' ), 10, 1 );

                add_action( 'wp_ajax_wpie_import_upload_file_from_googledrive', array( $this, 'upload_from_gd' ) );

                add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_gd_scripts' ), 10 );
        }

        public function enqueue_gd_scripts() {

                wp_register_script( 'wpie-import-gdrive-admin-js', "https://apis.google.com/js/api.js", array( 'jquery' ), WPIE_PLUGIN_VERSION, true );

                wp_register_script( 'wpie-import-gdrive-local-admin-js', WPIE_IMPORT_ADDON_URL . '/googledrive/wpie-import-gdrive.js', array( 'jquery' ), WPIE_PLUGIN_VERSION, true );

                wp_enqueue_script( 'wpie-import-gdrive-admin-js' );

                wp_enqueue_script( 'wpie-import-gdrive-local-admin-js' );
        }

        public function get_gd_upload_views( $wpie_sections = array() ) {

                $wpie_sections[ "wpie_import_googledrive_file_upload" ] = array(
                        "label" => __( "Upload From Google Drive", 'vj-wp-import-export' ),
                        "icon"  => 'fab fa-google-drive',
                        "view"  => WPIE_IMPORT_CLASSES_DIR . "/extensions/googledrive/wpie-gdrive-view.php",
                );

                return $wpie_sections;
        }

        public function upload_from_gd() {

                \wpie\Security::verify_request( 'wpie_new_import' );

                $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/googledrive/class-gdrive.php';

                if ( file_exists( $fileName ) ) {

                        require_once($fileName);
                }

                $upload = new \wpie\import\upload\googledrive\WPIE_GDrive();

                $file = $upload->download_gdrive_file();

                unset( $upload, $fileName );

                $return_value = array( 'status' => 'error' );

                if ( is_wp_error( $file ) ) {
                        $return_value[ 'message' ] = $file->get_error_message();
                } elseif ( empty( $file ) ) {
                        $return_value[ 'erorr_message' ] = __( 'Failed to upload files', 'vj-wp-import-export' );
                } elseif ( $file == "processing" ) {
                        $return_value[ 'status' ]  = 'success';
                        $return_value[ 'message' ] = 'processing';
                } else {

                        $return_value[ 'file_list' ] = isset( $file[ 'file_list' ] ) ? $file[ 'file_list' ] : array();

                        $return_value[ 'file_count' ] = count( $return_value[ 'file_list' ] );

                        $return_value[ 'wpie_import_id' ] = isset( $file[ 'wpie_import_id' ] ) ? $file[ 'wpie_import_id' ] : 0;

                        $return_value[ 'file_name' ] = isset( $file[ 'file_name' ] ) ? $file[ 'file_name' ] : "";

                        $return_value[ 'file_size' ] = isset( $file[ 'file_size' ] ) ? $file[ 'file_size' ] : "";

                        $return_value[ 'status' ] = 'success';
                }
                unset( $file );

                echo json_encode( $return_value );

                die();
        }

}

new WPIE_GDrive_Extension();
