<?php

namespace wpie\import\upload\dropbox;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-upload.php' ) ) {
        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-upload.php');
}

class WPIE_Dropbox extends \wpie\import\upload\WPIE_Upload {

        public function __construct() {
                
        }

        public function download_dropbox_file( $file_url = "", $wpie_import_id = 0 ) {

                if ( ! is_dir( WPIE_UPLOAD_IMPORT_DIR ) || ! wp_is_writable( WPIE_UPLOAD_IMPORT_DIR ) ) {

                        return new \WP_Error( 'wpie_import_error', __( 'Uploads folder is not writable', 'vj-wp-import-export' ) );
                }

                $fileName = basename( parse_url( $file_url, PHP_URL_PATH ) );

                $newfiledir = parent::wpie_create_safe_dir_name( $fileName );

                wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir );

                wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original" );

                wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/parse" );

                wp_mkdir_p( WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/parse/chunks" );


                $filePath = WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original/" . $fileName;

                chmod( WPIE_UPLOAD_IMPORT_DIR . "/" . $newfiledir . "/original/", 0755 );

                $response = wp_safe_remote_get( $file_url, array( 'timeout' => 3000, 'stream' => true, 'filename' => $filePath ) );

                if ( is_wp_error( $response ) ) {

                        if ( file_exists( $filePath ) ) {
                                unlink( $filePath );
                        }

                        unset( $wpie_import_id, $file_url, $fileName, $newfiledir, $filePath );

                        return $response;
                }

                if ( 200 != wp_remote_retrieve_response_code( $response ) ) {

                        if ( file_exists( $filePath ) ) {
                                unlink( $filePath );
                        }

                        unset( $wpie_import_id, $file_url, $fileName, $newfiledir, $filePath );

                        return new \WP_Error( 'http_404', trim( wp_remote_retrieve_response_message( $response ) ) );
                }

                $content_md5 = wp_remote_retrieve_header( $response, 'content-md5' );

                if ( $content_md5 ) {

                        $md5_check = verify_file_md5( $filePath, $content_md5 );

                        if ( is_wp_error( $md5_check ) ) {

                                if ( file_exists( $filePath ) ) {
                                        unlink( $filePath );
                                }

                                unset( $wpie_import_id, $file_url, $fileName, $newfiledir, $filePath, $md5_check, $content_md5 );

                                return $md5_check;
                        }
                        unset( $md5_check );
                }

                unset( $file_url, $filePath, $content_md5, $response );

                return parent::wpie_manage_import_file( $fileName, $newfiledir, $wpie_import_id );
        }

}
