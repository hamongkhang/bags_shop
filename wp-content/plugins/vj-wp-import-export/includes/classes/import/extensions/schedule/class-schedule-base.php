<?php


namespace wpie\import\schedule;

use \wpie\import\WPIE_Import;
use \wpie\import\upload\validate\WPIE_Upload_Validate;
use \wpie\import\chunk\WPIE_Chunk;
use wpie\import\record\WPIE_Record;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import.php');
}

class Schedule_Base extends WPIE_Import {

        protected $id = 0;
        protected $options = [];
        protected $username = "";

        protected function generate_template( $type = "schedule_import", $status = "draft" ) {

                $this->id = parent::wpie_generate_template( $this->options, $type, $status, "", 0, $this->username );
        }

        protected function delete_template() {

                global $wpdb;

                $wpdb->query( $wpdb->prepare( "DELETE FROM " . $wpdb->prefix . "wpie_template WHERE id = %d", $this->id ) );
        }

        protected function get_template_options() {

                global $wpdb;

                return $wpdb->get_var( $wpdb->prepare( "SELECT options FROM " . $wpdb->prefix . "wpie_template where `id` = %d", $this->id ) );
        }

        protected function update_template_options() {

                $options = $this->get_template_options();

                if ( (!$options) && empty( $options ) ) {
                        return false;
                }

                $this->options = maybe_unserialize( $options );
        }

        protected function validate_upload() {

                if ( $this->update_template_options() === false ) {
                        return false;
                } elseif ( $this->validate_file() === false ) {
                        return false;
                } elseif ( $this->generate_chunks() === false ) {
                        return false;
                } elseif ( $this->reset_template() === false ) {
                        return false;
                }

                return true;
        }

        protected function validate_file() {

                if ( !file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-upload-validate.php' ) ) {
                        return false;
                }

                require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-upload-validate.php');

                $data_parser = new WPIE_Upload_Validate();

                $delim = isset( $this->options[ "wpie_csv_delimiter" ] ) ? wpie_sanitize_field( $this->options[ "wpie_csv_delimiter" ] ) : ",";

                $is_first_row_title = isset( $this->options[ "wpie_file_first_row_is_title" ] ) ? wpie_sanitize_field( $this->options[ "wpie_file_first_row_is_title" ] ) : 1;

                $file = isset( $this->options[ "activeFile" ] ) ? wpie_sanitize_field( $this->options[ "activeFile" ] ) : false;

                $activeSheet = isset( $this->options[ "activeSheet" ] ) ? wpie_sanitize_field( $this->options[ "activeSheet" ] ) : false;

                $activeFormat = isset( $this->options[ "activeFormat" ] ) ? wpie_sanitize_field( $this->options[ "activeFormat" ] ) : false;

                $data = $data_parser->wpie_parse_upload_data( $this->options, $delim, $is_first_row_title, $file, $this->id, $activeSheet, $activeFormat );

                unset( $data_parser, $delim, $file );

                if ( is_wp_error( $data ) ) {
                        return false;
                }
                return true;
        }

        protected function generate_chunks() {

                if ( !file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-csv-chunk.php' ) ) {
                        return false;
                }
                require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-csv-chunk.php');

                $chunk = new WPIE_Chunk();

                $chunk->process_data( $this->options );

                unset( $chunk );

                return true;
        }

        protected function reset_template() {

                if ( !file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-record.php' ) ) {
                        return false;
                }

                require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-record.php');

                $records = new WPIE_Record();

                $parse_data = $records->auto_fetch_records_by_template( $this->options );

                if ( is_wp_error( $parse_data ) ) {
                        unset( $records );
                        return false;
                } else {

                        if ( isset( $parse_data[ 'count' ] ) && absint( $parse_data[ 'count' ] ) > 0 ) {

                                global $wpdb;

                                $wpdb->update(
                                        $wpdb->prefix . "wpie_template",
                                        [
                                                "status" => "background",
                                                'last_update_date' => current_time( 'mysql' ),
                                                'process_log' => maybe_serialize( [ "total" => absint( $parse_data[ 'count' ] ) ] )
                                        ],
                                        [ 'id' => $this->id ]
                                );
                        }
                }
                unset( $records, $parse_data );
                return true;
        }

        protected function finalyze_data( $type = "import-draft" ) {
                return parent::wpie_finalyze_template_data( $type );
        }

}
