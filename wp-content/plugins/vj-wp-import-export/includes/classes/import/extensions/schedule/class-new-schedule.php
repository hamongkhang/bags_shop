<?php

namespace wpie\import\schedule;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/extensions/schedule/class-schedule-base.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/extensions/schedule/class-schedule-base.php');
}

class New_Schedule extends Schedule_Base {

        public function __construct( $options = [] ) {

                $this->options = $options;
        }

        public function save_schedule() {

                $interval = isset( $this->options[ 'wpie_import_interval' ] ) ? wpie_sanitize_field( $this->options[ 'wpie_import_interval' ] ) : "";

                if ( ! empty( $interval ) ) {

                        $schedules = wp_get_schedules();

                        if ( ! isset( $schedules[ $interval ] ) ) {

                                return new \WP_Error( 'wpie_import_error', __( 'Cron interval is missing', 'vj-wp-import-export' ) );
                        }

                        $import_id = isset( $this->options[ "wpie_import_id" ] ) ? absint( wpie_sanitize_field( $this->options[ "wpie_import_id" ] ) ) : 0;

                        $template_options = [];

                        $process_log = "";

                        if ( $import_id > 0 ) {

                                $template_data = $this->get_template_by_id( $import_id );

                                if ( $template_data ) {

                                        $template_options = maybe_unserialize( $template_data->options );

                                        $process_log = isset( $template_data->process_log ) ? $template_data->process_log : "";
                                }
                        }

                        if ( ! empty( $template_options ) ) {
                                $new_options = array_merge( $this->options, $template_options );
                        } else {
                                $new_options = $this->options;
                        }

                        $new_options = $this->copy_template( $new_options );

                        $scheduled_id = parent::wpie_generate_template( $new_options, 'schedule_import_template', 'completed' );

                        global $wpdb;

                        if ( ! empty( $process_log ) ) {

                                $wpdb->update( $wpdb->prefix . "wpie_template", [ 'process_log' => $process_log ], [ 'id' => $scheduled_id ] );
                        }

                        $start_time = isset( $this->options[ 'wpie_interval_start_time' ] ) ? wpie_sanitize_field( $this->options[ 'wpie_interval_start_time' ] ) : "";

                        if ( ! empty( $start_time ) ) {

                                $format = 'Y-m-d H:i:s';

                                $import_time = date( $format, strtotime( $start_time ) );

                                $import_time = strtotime( get_gmt_from_date( $import_time, $format ) );
                        } else {
                                $import_time = time();
                        }


                        $event = wp_schedule_event( $import_time, $interval, 'wpie_cron_schedule_import', [ $scheduled_id ] );

                        if ( ! $event ) {

                                $wpdb->delete( $wpdb->prefix . "wpie_template", [ 'id' => $scheduled_id ] );

                                return new \WP_Error( 'wpie_import_error', __( 'Cron event not set', 'vj-wp-import-export' ) );
                        }


                        unset( $scheduled_id, $import_id, $template_options, $new_options, $schedules );

                        return true;
                }
                return new \WP_Error( 'wpie_import_error', __( 'Invalid Cron data', 'vj-wp-import-export' ) );
        }

        private function copy_template( $options = [] ) {

                $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/schedule/class-template-copy.php';

                if ( ! file_exists( $fileName ) ) {

                        return false;
                }
                require_once($fileName);

                $tempalte = new Template_Copy( $options );

                $new_options = $tempalte->options;

                unset( $tempalte, $fileName );

                return $new_options;
        }

}
