<?php


if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

class WPIE_WPML_Import_Extension {

        public function __construct() {

                if ( class_exists( 'SitePress' ) ) {

                        add_filter( 'wpie_pre_post_field_mapping_section', array( $this, "get_wpml_tab_view" ), 10, 2 );

                        add_filter( 'wpie_pre_term_field_mapping_section', array( $this, "get_wpml_tab_view" ), 10, 2 );

                        add_filter( 'wpie_pre_attribute_field_mapping_section', array( $this, "get_wpml_tab_view" ), 10, 2 );

                        add_filter( 'wpie_import_addon', array( $this, "wpml_addon_init" ), 10, 2 );
                }
        }

        public function get_wpml_tab_view( $sections = array(), $wpie_import_type = "" ) {

                if ( $wpie_import_type == "shop_coupon" ) {
                        return $sections;
                }

                $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/wpml/wpie-wpml-tab.php';

                if ( file_exists( $fileName ) ) {

                        require_once($fileName);

                        if ( function_exists( "wpie_import_get_wpml_tab" ) ) {
                                $sections = wpie_import_get_wpml_tab( $sections, $wpie_import_type );
                        }
                }
                unset( $fileName );

                return $sections;
        }

        public function wpml_addon_init( $addons = [], $wpie_import_type = "" ) {

                if ( in_array( $wpie_import_type, [ "shop_coupon", "users", "shop_customer", "comments", "product_reviews", "shop_order" ] ) || !class_exists( 'SitePress' ) ) {
                        return $addons;
                }

                if ( $wpie_import_type === "taxonomies" ) {

                        $class = '\wpie\import\wpml\WPML_Taxonomy';

                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/wpml/class-wpml-taxonomy.php';
                } elseif ( $wpie_import_type === "product_attributes" ) {

                        $class = '\wpie\import\wpml\WPML_Attribute';

                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/wpml/class-wpml-attribute.php';
                } else {

                        $class = '\wpie\import\wpml\WPML_Post';

                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/wpml/class-wpml-post.php';
                }

                if ( !in_array( $class, $addons ) ) {

                        if ( file_exists( $fileName ) ) {

                                require_once($fileName);
                        }

                        $addons[] = $class;
                }

                unset( $fileName, $class );

                return $addons;
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}

new WPIE_WPML_Import_Extension();
