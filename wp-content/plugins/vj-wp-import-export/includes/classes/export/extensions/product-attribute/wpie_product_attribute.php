<?php

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

class WPIE_Product_Attributes_Export_Extension {

        public function __construct() {
                add_filter( 'wpie_export_engine_init', array ( $this, 'wpie_export_engine_init' ), 10, 3 );
        }

        public function wpie_export_engine_init( $export_engine = "", $export_type = "", $template_data = "" ) {

                if ( $export_type == "product_attributes" ) {

                        $fileName = WPIE_EXPORT_CLASSES_DIR . "/extensions/product-attribute/class-wpie-product-attribute.php";

                        if ( file_exists( $fileName ) ) {

                                require_once($fileName);
                        }

                        unset( $fileName );

                        $export_engine = '\wpie\export\WPIE_Product_Attributes_Export';
                }
                return $export_engine;
        }

}

new WPIE_Product_Attributes_Export_Extension();
