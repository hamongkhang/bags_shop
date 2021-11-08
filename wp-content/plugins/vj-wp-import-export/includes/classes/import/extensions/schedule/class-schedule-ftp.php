<?php


namespace wpie\import\schedule;

use \wpie\import\upload\ftp\WPIE_FTP_SFTP;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/extensions/schedule/class-schedule-base.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/extensions/schedule/class-schedule-base.php');
}

class Schedule_Ftp extends Schedule_Base {

        public function __construct( $options = [], $username = "" ) {

                $this->options = $options;

                $this->username = $username;

                parent::generate_template();

                $this->process_upload_files();
        }

        private function process_upload_files() {

                if ( $this->downlod_file() === false || $this->validate_upload() === false ) {

                        $this->delete_template();

                        return false;
                }
                return true;
        }

        private function downlod_file() {
                $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/ftp-sftp/class-ftp-sftp.php';

                if ( !file_exists( $fileName ) ) {
                        return false;
                }

                require_once($fileName);

                $upload = new WPIE_FTP_SFTP();

                $ftp_details = isset( $this->options [ "wpie_ftp_details" ] ) ? wpie_sanitize_field( $this->options [ "wpie_ftp_details" ] ) : '';

                if ( !empty( $ftp_details ) ) {
                        $ftp_details = json_decode( wp_unslash( $ftp_details ), true );

                        if ( is_array( $ftp_details ) && !empty( $ftp_details ) ) {
                                $file_list = $upload->download( $ftp_details, $this->id );

                                if ( !is_wp_error( $file_list ) ) {
                                        unset( $upload, $file );
                                        return $file_list;
                                }
                        }
                }
                unset( $upload, $ftp_details );

                return false;
        }

}
