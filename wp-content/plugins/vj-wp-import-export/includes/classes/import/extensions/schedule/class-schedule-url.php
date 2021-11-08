<?php


namespace wpie\import\schedule;

use wpie\import\upload\url\WPIE_URL_Upload;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/extensions/schedule/class-schedule-base.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/extensions/schedule/class-schedule-base.php');
}

class Schedule_Url extends Schedule_Base {

        public function __construct( $options = [], $username = "" ) {

                $this->options = $options;

                $this->username = $username;

                parent::generate_template();

                return $this->process_upload_files();
        }

        private function process_upload_files() {

                if ( $this->downlod_file() === false || $this->validate_upload() === false ) {

                        $this->delete_template();

                        return false;
                }
                return true;
        }

        private function downlod_file() {

                $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/url-upload/class-wpie-url-upload.php';

                if ( !file_exists( $fileName ) ) {

                        return false;
                }

                require_once($fileName);

                $upload = new WPIE_URL_Upload();

                $file_url = isset( $this->options[ "wpie_upload_final_file_url" ] ) ? esc_url( urldecode( $this->options[ "wpie_upload_final_file_url" ] ) ) : '';

                if ( !empty( $file_url ) ) {

                        $file = $upload->wpie_download_file_from_url( $this->id, $file_url );

                        if ( !is_wp_error( $file ) ) {
                                unset( $upload, $file_url );
                                return $file;
                        }
                }
                unset( $upload, $file_url );

                return false;
        }

}
