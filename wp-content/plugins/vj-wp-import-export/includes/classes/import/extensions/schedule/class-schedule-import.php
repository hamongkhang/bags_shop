<?php


namespace wpie\import\schedule;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

class Schedule_Import {

        public function process_schedule( $template_id = 0 ) {

                if ( absint( $template_id ) < 1 ) {
                        return false;
                }
                global $wpdb;

                $template = $wpdb->get_row( $wpdb->prepare( "SELECT `options`,`username` FROM " . $wpdb->prefix . "wpie_template where `id` = %d", absint( $template_id ) ) );

                if ( (!$template) && empty( $template ) ) {

                        wp_clear_scheduled_hook( 'wpie_cron_schedule_import', [ absint( $template_id ) ] );

                        return false;
                }
                $username = $template->username;

                $options = maybe_unserialize( $template->options );

                $upload_method = ( isset( $options[ 'wpie_file_upload_method' ] ) && !empty( $options[ 'wpie_file_upload_method' ] )) ? strtolower( trim( wpie_sanitize_field( $options[ 'wpie_file_upload_method' ] ) ) ) : "";

                if ( $upload_method === "wpie_import_url_file_upload" ) {

                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/schedule/class-schedule-url.php';
                        if ( file_exists( $fileName ) ) {

                                require_once($fileName);

                                new Schedule_Url( $options ,$username );
                        }
                } elseif ( $upload_method === "wpie_import_existing_file_upload" ) {
                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/schedule/class-schedule-existing-file.php';
                        if ( file_exists( $fileName ) ) {

                                require_once($fileName);

                                new Schedule_Existing_File( $options ,$username);
                        }
                } elseif ( $upload_method === "wpie_import_ftp_file_upload" ) {
                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/schedule/class-schedule-ftp.php';
                        if ( file_exists( $fileName ) ) {

                                require_once($fileName);

                                new Schedule_Ftp( $options, $username );
                        }
                } else {
                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/schedule/class-schedule-local.php';
                        if ( file_exists( $fileName ) ) {

                                require_once($fileName);

                                new Schedule_Local( $options, $template_id,$username );
                        }
                }
        }

}
