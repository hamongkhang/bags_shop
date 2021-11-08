<?php


if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

class WPIE_Polylang_Import_Extension {

        public function __construct() {

                if ( defined( 'POLYLANG_VERSION' ) || defined( 'POLYLANG' ) ) {

                        add_filter( 'wpie_pre_post_field_mapping_section', array( $this, "get_polylang_tab_view" ), 10, 2 );

                        add_filter( 'wpie_pre_term_field_mapping_section', array( $this, "get_polylang_tab_view" ), 10, 2 );

                        add_filter( 'wpie_pre_attribute_field_mapping_section', array( $this, "get_polylang_tab_view" ), 10, 2 );

                        add_filter( 'wpie_import_addon', array( $this, "polylang_addon_init" ), 10, 2 );

                        add_action( 'registered_taxonomy', array( $this, 'registered_taxonomy' ), 10, 2 );

                        add_filter( 'wpie_exclude_taxonomies', array( $this, 'exclude_taxonomies' ) );
                }
        }

        public function get_polylang_tab_view( $sections = array(), $wpie_import_type = "" ) {

                if ( $wpie_import_type == "shop_coupon" ) {
                        return $sections;
                }

                $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/polylang/wpie-polylang-tab.php';

                if ( file_exists( $fileName ) ) {

                        require_once($fileName);

                        if ( function_exists( "wpie_import_get_polylang_tab" ) ) {
                                $sections = wpie_import_get_polylang_tab( $sections, $wpie_import_type );
                        }
                }
                unset( $fileName );

                return $sections;
        }

        public function polylang_addon_init( $addons = array(), $wpie_import_type = "" ) {

                if ( in_array( $wpie_import_type, [ "shop_coupon", "users", "shop_customer", "comments", "product_reviews", "shop_order" ] ) ) {
                        return $addons;
                }

                if ( !in_array( '\wpie\import\polylang\WPIE_Polylang_Import', $addons ) ) {

                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/polylang/class-wpie-polylang.php';

                        if ( file_exists( $fileName ) ) {

                                require_once($fileName);
                        }
                        unset( $fileName );

                        $addons[] = '\wpie\import\polylang\WPIE_Polylang_Import';
                }

                return $addons;
        }

        /**
         * FIXME maybe in Polylang or in PLLWC
         *
         * @since 0.1
         *
         * @param string       $taxonomy Taxonomy slug.
         * @param string|array $object   Object type or array of object types.
         */
        public function registered_taxonomy( $taxonomy, $object = null ) {

                if ( function_exists( "\PLL" ) && is_array( $object ) && 'product' === reset( $object ) && 0 === strpos( $taxonomy, 'pa_' ) ) {
                        PLL()->model->cache->clean( 'taxonomies' );
                }
        }

        /**
         * Excludes taxonomies from the WP Import Export interface
         *
         * @since 3.5
         *
         * @param array $taxonomies Array of excluded taxonomies.
         * @return array
         */
        public function exclude_taxonomies( $taxonomies ) {
                return array_merge( $taxonomies, [ 'language', 'post_translations' ] );
        }

}

new WPIE_Polylang_Import_Extension();
