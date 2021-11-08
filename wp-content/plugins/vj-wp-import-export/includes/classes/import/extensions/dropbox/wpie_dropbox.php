<?php

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if ( file_exists( WPIE_CLASSES_DIR . '/class-wpie-security.php' ) ) {
        require_once(WPIE_CLASSES_DIR . '/class-wpie-security.php');
}

class WPIE_Dropbox_Extension {

        public function __construct() {

                add_filter( 'wpie_import_upload_sections', array( $this, 'get_dropbox_views' ), 10, 1 );

                add_action( 'wp_ajax_wpie_import_upload_file_from_dropbox', array( $this, 'prepare_dropbox_uploadd' ) );

                add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_dropbox_script' ), 10 );
        }

        public function enqueue_dropbox_script() {

                wp_register_script( 'wpie-import-dropins-admin-js', "https://www.dropbox.com/static/api/2/dropins.js", array( 'jquery' ), WPIE_PLUGIN_VERSION, true );

                wp_register_script( 'wpie-import-upload-dropbox-js', WPIE_IMPORT_ADDON_URL . '/dropbox/wpie-import-dropbox.js', array( 'jquery' ), WPIE_PLUGIN_VERSION, true );

                wp_enqueue_script( 'wpie-import-dropins-admin-js' );

                wp_enqueue_script( 'wpie-import-upload-dropbox-js' );
        }

        public function prepare_dropbox_uploadd() {
                
                \wpie\Security::verify_request( 'wpie_new_import' );

                $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/dropbox/class-dropbox.php';

                if ( file_exists( $fileName ) ) {

                        require_once($fileName);
                }

                $wpie_import_id = isset( $_POST[ 'wpie_import_id' ] ) ? intval( wpie_sanitize_field( $_POST[ 'wpie_import_id' ] ) ) : 0;

                $file_url = isset( $_POST[ "file_url" ] ) ? wpie_sanitize_field( $_POST[ "file_url" ] ) : '';

                $upload = new \wpie\import\upload\dropbox\WPIE_Dropbox();

                $file = $upload->download_dropbox_file( $file_url, $wpie_import_id );

                unset( $upload, $fileName );

                $return_value = array( 'status' => 'error' );

                if ( is_wp_error( $file ) ) {
                        $return_value[ 'message' ] = $file->get_error_message();
                } elseif ( empty( $file ) ) {
                        $return_value[ 'erorr_message' ] = __( 'Failed to upload files', 'vj-wp-import-export' );
                } elseif ( $file == "processing" ) {
                        $return_value[ 'status' ] = 'success';
                        $return_value[ 'message' ] = 'processing';
                } else {

                        $return_value[ 'file_list' ] = isset( $file[ 'file_list' ] ) ? $file[ 'file_list' ] : array();

                        $return_value[ 'file_count' ] = count( $return_value[ 'file_list' ] );

                        $return_value[ 'wpie_import_id' ] = isset( $file[ 'wpie_import_id' ] ) ? $file[ 'wpie_import_id' ] : 0;

                        $return_value[ 'file_name' ] = isset( $file[ 'file_name' ] ) ? $file[ 'file_name' ] : "";

                        $return_value[ 'file_size' ] = isset( $file[ 'file_size' ] ) ? $file[ 'file_size' ] : "";

                        $return_value[ 'status' ] = 'success';
                }

                echo json_encode( $return_value );

                die();
        }

        public function get_dropbox_views( $wpie_sections = array() ) {

                $wpie_sections[ "wpie_import_dropbox_file_upload" ] = array(
                        "label" => __( "Upload From Dropbox", 'vj-wp-import-export' ),
                        "icon"  => 'fab fa-dropbox',
                        "view"  => WPIE_IMPORT_CLASSES_DIR . "/extensions/dropbox/wpie-dropbox-view.php",
                );

                return $wpie_sections;
        }

}

new WPIE_Dropbox_Extension();
