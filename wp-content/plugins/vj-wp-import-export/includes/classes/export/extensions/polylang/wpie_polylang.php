<?php


if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

class WPIE_Polylang_Export_Extension {

        public function __construct() {

                if ( defined( 'POLYLANG_VERSION' ) || defined( 'POLYLANG' ) ) {

                        add_filter( 'wpie_add_export_extension_files', array( $this, 'get_polylang_tab_view' ), 10, 1 );

                        add_filter( 'wpie_prepare_post_fields', array( $this, 'prepare_polylang_addon' ), 10, 2 );

                        add_filter( 'wpie_prepare_taxonomy_fields', array( $this, 'prepare_polylang_addon' ), 10, 2 );

                        add_filter( 'wpie_prepare_export_addons', array( $this, 'prepare_polylang_addon' ), 10, 2 );

                        add_filter( 'wpie_exclude_post_taxonomy_fields', array( $this, 'exclude_taxonomies' ), 10, 1 );
                }
        }

        public function prepare_polylang_addon( $addons = [], $export_type = "post" ) {

                if ( in_array( $export_type, [ "shop_coupon", "users", "shop_customer", "comments", "product_reviews", "shop_order" ] ) ) {
                        return $addons;
                }


                $fileName = WPIE_EXPORT_CLASSES_DIR . '/extensions/polylang/class-wpie-polylang.php';

                $class = '\wpie\export\polylang\WPIE_Polylang_Export';

                if ( file_exists( $fileName ) ) {

                        require_once($fileName);
                }

                if ( $class != "" && !in_array( $class, $addons ) ) {
                        $addons[] = $class;
                }

                unset( $class, $fileName );

                return $addons;
        }

        public function get_polylang_tab_view( $files = array() ) {

                $fileName = WPIE_EXPORT_CLASSES_DIR . '/extensions/polylang/wpie_polylang_tab.php';

                if ( !in_array( $fileName, $files ) ) {

                        $files[] = $fileName;
                }

                return $files;
        }

        public function exclude_taxonomies( $taxonomies = [] ) {

                $taxonomies[] = "post_translations";
                $taxonomies[] = "language";

                return $taxonomies;
        }

}

new WPIE_Polylang_Export_Extension();
