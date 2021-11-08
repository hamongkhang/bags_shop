<?php


namespace wpie\export\bg;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if ( file_exists( WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export.php' ) ) {

        require_once(WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export.php');
}

class WPIE_BG extends \wpie\export\WPIE_Export {

        private $process_lock = false;

        public function __construct() {

                add_action( 'init', array( $this, 'unlock_export_process' ), 200 );
        }

        public function init() {

                add_action( 'shutdown', array( $this, 'unlock_process' ) );

                add_action( 'init', array( $this, 'init_process' ), 100 );

                add_filter( 'wpie_add_export_extension_process_btn', array( $this, 'add_bg_export_btn' ), 10, 1 );
        }

        public function init_bg_export( $export_type = "" ) {

                $template = $this->get_bg_template_id( $export_type );

                if ( $template !== false && isset( $template->id ) && absint( $template->id ) > 0 ) {

                        $export_type = isset( $template->opration_type ) ? $template->opration_type : "post";

                        $opration = isset( $template->opration ) ? $template->opration : "export";

                        $process_log = $this->init_export( $export_type, $opration, $template );

                        unset( $export_type, $process_log );
                }
                unset( $template );
        }

        public function get_bg_template_id( $export_type = "" ) {

                global $wpdb;

                if ( empty( $export_type ) ) {

                        $export_type = "'export','schedule_export'";
                } else {
                        $export_type = "'" . $export_type . "'";
                }

                $template = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "wpie_template where `opration` in (" . $export_type . ") and status LIKE '%background%' and process_lock = 0 ORDER BY `id` ASC limit 1" );

                if ( isset( $template->id ) && absint( $template->id ) > 0 ) {

                        return $template;
                }
                unset( $template );

                return false;
        }

        public function add_bg_export_btn( $files = array() ) {

                $fileName = WPIE_EXPORT_CLASSES_DIR . '/extensions/bg/wpie_bg_btn.php';

                if ( !in_array( $fileName, $files ) ) {

                        $files[] = $fileName;
                }

                return $files;
        }

        public function unlock_export_process() {

                global $wpdb;

                $current_time = date( 'Y-m-d H:i:s', strtotime( '-1 hour', strtotime( current_time( "mysql" ) ) ) );

                $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}wpie_template SET process_lock = 0 WHERE process_lock = 1 and last_update_date < %s", $current_time ) );

                unset( $current_time );
        }

        public function unlock_process() {

                if ( $this->process_lock ) {
                        update_option( "wpie_bg_and_cron_processing_lock", 0 );
                }
        }

        public function init_process() {
                $this->init_bg_process();
        }

        public function init_bg_process( $export_type = "" ) {

                $cron_method = "";

                $wpie_bg_and_cron_processing = get_option( "wpie_bg_and_cron_processing" );

                if ( $wpie_bg_and_cron_processing && !empty( $wpie_bg_and_cron_processing ) ) {
                        $wpie_bg_and_cron_processing = maybe_unserialize( $wpie_bg_and_cron_processing );

                        $cron_method = isset( $wpie_bg_and_cron_processing[ 'method' ] ) ? $wpie_bg_and_cron_processing[ 'method' ] : "";

                        if ( $cron_method === "external" && isset( $_GET[ 'wpie_cron_token' ] ) ) {

                                $respons = [
                                        "status" => "success",
                                        "plugin" => "WP Import Export",
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

                                        $template = $this->get_bg_template_id( $export_type );

                                        if ( $template !== false && isset( $template->id ) && absint( $template->id ) > 0 ) {
                                                $this->process_lock = true;

                                                update_option( "wpie_bg_and_cron_processing_lock", 1 );

                                                $this->init_bg_export( $export_type );

                                                update_option( "wpie_bg_and_cron_processing_lock", 0 );

                                                $respons[ "message" ] = "WP Import Export : Export #" . $template->id . " Processing";

                                                echo json_encode( $respons );

                                                die();
                                        }
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
                        $this->init_bg_export( $export_type );
                }
        }

}
