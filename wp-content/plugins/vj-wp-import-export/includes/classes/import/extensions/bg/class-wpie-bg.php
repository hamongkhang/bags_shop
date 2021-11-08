<?php


namespace wpie\import\bg;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import.php');
}

class WPIE_BG_Import extends \wpie\import\WPIE_Import {

        private $process_lock = false;

        public function __construct() {

                add_action( 'init', array( $this, 'wpie_bg_import' ), 100 );

                add_action( 'shutdown', array( $this, 'unlock_process' ) );

                add_action( 'init', array( $this, 'wpie_bg_unlock_import' ), 200 );

                add_filter( 'wpie_add_import_extension_process_btn_files', array( $this, 'wpie_add_bg_process_btn' ), 10, 1 );
        }

        public function wpie_add_bg_process_btn( $files = array() ) {

                $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/bg/wpie_bg_btn.php';

                if ( !in_array( $fileName, $files ) ) {

                        $files[] = $fileName;
                }

                return $files;
        }

        public function wpie_bg_unlock_import() {

                global $wpdb;

                $current_time = date( 'Y-m-d H:i:s', strtotime( '-1 hour', strtotime( current_time( "mysql" ) ) ) );

                $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}wpie_template SET process_lock = 0 WHERE process_lock = 1 and last_update_date < %s", $current_time ) );

                unset( $current_time );
        }

        public function wpie_bg_import() {
                $wpie_bg_and_cron_processing = get_option( "wpie_bg_and_cron_processing" );

                $cron_method = "";

                if ( $wpie_bg_and_cron_processing && !empty( $wpie_bg_and_cron_processing ) ) {
                        $wpie_bg_and_cron_processing = maybe_unserialize( $wpie_bg_and_cron_processing );

                        $cron_method = isset( $wpie_bg_and_cron_processing[ 'method' ] ) ? $wpie_bg_and_cron_processing[ 'method' ] : "";

                        if ( $cron_method === "external" && isset( $_GET[ 'wpie_cron_token' ] ) ) {

                                $respons = [
                                        "status" => "success",
                                        "plugin" => "WP Import Export"
                                ];

                                $user_token = wpie_sanitize_field( $_GET[ 'wpie_cron_token' ] );

                                if ( empty( $user_token ) ) {

                                        $respons = [
                                                "status" => "error",
                                                "message" => "WP Import Export : Empty Token"
                                        ];

                                        echo json_encode( $respons );

                                        die();
                                }

                                $is_processing = get_option( "wpie_bg_and_cron_processing_lock", 0 );

                                if ( intval( $is_processing ) === 1 ) {
                                        $respons[ "message" ] = "WP Import Export : Processing";
                                        echo json_encode( $respons );

                                        die();
                                }

                                $site_token = isset( $wpie_bg_and_cron_processing[ 'token' ] ) ? $wpie_bg_and_cron_processing[ 'token' ] : "";

                                if ( intval( $site_token ) === intval( $user_token ) ) {

                                        $id = $this->get_bg_template_id();

                                        if ( $id && absint( $id ) > 0 ) {
                                                $this->process_lock = true;

                                                update_option( "wpie_bg_and_cron_processing_lock", 1 );

                                                parent::wpie_import_process_data( $id );

                                                update_option( "wpie_bg_and_cron_processing_lock", 0 );

                                                $respons[ "message" ] = "WP Import Export : Import #" . $id . " Processing";

                                                echo json_encode( $respons );

                                                die();
                                        } else {
                                                $respons[ "message" ] = "No pending schedules";

                                                echo json_encode( $respons );

                                                die();
                                        }
                                        unset( $id );
                                } else {
                                        $respons = [
                                                "status" => "error",
                                                "message" => "WP Import Export : Empty Token"
                                        ];

                                        echo json_encode( $respons );

                                        die();
                                }
                        }
                }

                if ( $cron_method !== "external" ) {
                        $this->wpie_bg_import_init();
                }
        }

        public function unlock_process() {

                if ( $this->process_lock ) {
                        update_option( "wpie_bg_and_cron_processing_lock", 0 );
                }
        }

        public function wpie_bg_import_init() {
                $id = $this->get_bg_template_id();

                if ( $id && absint( $id ) > 0 ) {

                        parent::wpie_import_process_data( $id );
                }
                unset( $id );
        }

        public function get_bg_template_id() {

                global $wpdb;

                $id = $wpdb->get_var( "SELECT `id` FROM " . $wpdb->prefix . "wpie_template where `opration` in ('import','schedule_import') and status LIKE '%background%' and process_lock = 0 ORDER BY `id` ASC limit 0,1" );

                return $id;
        }

}
