<?php


if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

class WPIE_ACF_Import_Extension {

        public function __construct() {



                add_action( 'admin_enqueue_scripts', array( $this, 'wpie_enqueue_wc_scripts' ), 10 );

                add_filter( 'wpie_import_addon', array( $this, "acf_addon_init" ), 10, 2 );

                add_filter( 'wpie_pre_post_field_mapping_section', array( $this, "wpie_acf_fields" ), 10, 2 );

                add_filter( 'wpie_pre_term_field_mapping_section', array( $this, "wpie_acf_fields" ), 10, 2 );

                add_filter( 'wpie_pre_user_field_mapping_section', array( $this, "wpie_acf_fields" ), 10, 2 );

                add_filter( 'wpie_pre_attribute_field_mapping_section', array( $this, "wpie_acf_fields" ), 10, 2 );
        }

        public function wpie_enqueue_wc_scripts() {
                if ( class_exists( "ACF" ) ) {
                        wp_enqueue_script( 'wpie-import-acf-js', WPIE_IMPORT_ADDON_URL . '/acf/wpie-import-acf.min.js', array( 'jquery' ), WPIE_PLUGIN_VERSION );
                }
        }

        public function wpie_acf_fields( $sections = array(), $wpie_import_type = "" ) {

                if ( class_exists( "ACF" ) ) {
                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/acf/wpie-acf-fields.php';

                        if ( file_exists( $fileName ) ) {

                                require_once($fileName);

                                if ( function_exists( "wpie_get_acf_fields" ) ) {
                                        $sections = wpie_get_acf_fields( $sections, $wpie_import_type );
                                }
                        }
                        unset( $fileName );
                }

                return $sections;
        }

        public function acf_addon_init( $addons = array(), $wpie_import_type = "" ) {

                global $acf;

                if ( $acf && isset( $acf->settings ) && isset( $acf->settings[ 'version' ] ) && version_compare( $acf->settings[ 'version' ], '5.0.0' ) >= 0 ) {

                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/acf/class-wpie-acf.php';

                        $class = '\wpie\import\acf\WPIE_ACF';

                        if ( file_exists( $fileName ) ) {

                                require_once($fileName);
                        }

                        if ( !in_array( $class, $addons ) ) {
                                $addons[] = $class;
                        }

                        unset( $class, $fileName );
                }

                return $addons;
        }

}

new WPIE_ACF_Import_Extension();
