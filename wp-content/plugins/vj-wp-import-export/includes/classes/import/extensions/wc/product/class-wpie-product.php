<?php

namespace wpie\import\wc\product;

use WC_Product_Factory;
use wpie\import\wc\product\external\WPIE_External_Product;
use wpie\import\wc\product\grouped\WPIE_Grouped_Product;
use wpie\import\wc\product\variable\WPIE_Variable_Product;
use wpie\import\wc\product\simple\WPIE_Simple_Product;
use wpie\import\wc\product\variation\WPIE_Variation_Product;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}
if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php');
}

class WPIE_Product_Import extends \wpie\import\base\WPIE_Import_Base {

        private $unique_keys = array ();
        private $parent_id;
        private $variation_id;

        public function __construct( $wpie_import_option = array (), $import_type = "" ) {

                add_filter( 'wpie_before_post_import', array ( $this, "wpie_before_post_import" ), 10, 3 );

                $this->wpie_import_option = $wpie_import_option;

                $this->import_type = $import_type;

                $activeFile = isset( $this->wpie_import_option[ 'activeFile' ] ) ? $this->wpie_import_option[ 'activeFile' ] : "";

                $importFile = isset( $this->wpie_import_option[ 'importFile' ] ) ? $this->wpie_import_option[ 'importFile' ] : array ();

                $fileData = isset( $importFile[ $activeFile ] ) ? $importFile[ $activeFile ] : "";

                $this->base_dir = $fileData[ 'baseDir' ] ? $fileData[ 'baseDir' ] : "";

                unset( $activeFile, $importFile, $fileData );

                $this->get_product_unique_id();
        }

        private function search_parent_id() {

                $variation_method = wpie_sanitize_field( $this->get_field_value( 'wpie_item_variation_import_method', true ) );

                $parent_key = "";

                $parent_id = 0;

                switch ( $variation_method ) {
                        case "match_unique_field";
                                $parent_key = wpie_sanitize_field( $this->get_field_value( 'wpie_item_product_variation_match_unique_field_parent' ) );
                                break;
                        case "match_group_field";
                                $parent_key = wpie_sanitize_field( $this->get_field_value( 'wpie_item_product_variation_match_group_field' ) );
                                break;
                        case "match_title_field";
                                $parent_key = wpie_sanitize_field( $this->get_field_value( 'wpie_item_variation_import_method_title_field' ) );
                                break;
                        case "match_title_field_no_parent";
                                $parent_key = wpie_sanitize_field( $this->get_field_value( 'wpie_item_variation_import_method_title_field_no_parent' ) );
                                break;
                }

                if ( ! empty( $this->unique_keys ) && in_array( $parent_key, $this->unique_keys ) ) {

                        $parent_id = array_search( $parent_key, $this->unique_keys );
                }

                return $parent_id;
        }

        public function before_item_import( $wpie_import_record = array (), &$existing_item_id = 0, &$is_new_item = true, &$is_search_duplicates = true ) {

                $this->wpie_import_record = $wpie_import_record;

                if ( absint( $existing_item_id ) > 0 ) {
                        return;
                }

                $this->parent_id = 0;

                $this->variation_id = 0;

                $product_type = wpie_sanitize_field( strtolower( trim( $this->get_field_value( 'wpie_item_product_type', false, true ) ) ) );

                if ( trim( $product_type ) === "" || $product_type == "variable" || $product_type == "variation" ) {

                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-wpie-variation-product.php';

                        if ( file_exists( $fileName ) ) {

                                require_once($fileName);
                        }

                        $this->parent_id = $this->search_parent_id();

                        $variation = new \wpie\import\wc\product\variation\WPIE_Variation_Product( $this->wpie_import_option, $this->wpie_import_record, 0, null, false, $this->parent_id );

                        $variation_id = $variation->search_variation();

                        if ( $variation_id !== false && intval( $variation_id ) > 0 ) {

                                $this->variation_id = $existing_item_id = $variation_id;

                                $is_search_duplicates = false;

                                if ( absint( $this->parent_id ) === 0 ) {
                                        $post_data = get_post( $variation_id );

                                        if ( $post_data && isset( $post_data->post_parent ) ) {
                                                $this->parent_id = $post_data->post_parent;
                                        }
                                        unset( $post_data );
                                }
                        } elseif ( absint( $this->parent_id ) > 0 ) {
                                $is_search_duplicates = false;
                        }

                        unset( $fileName, $variation, $variation_id );
                }
        }

        public function wpie_before_post_import( $wpie_final_data = array (), $wpie_import_option = array (), $wpie_import_record = array () ) {

                if ( intval( $this->parent_id > 0 ) ) {

                        $wpie_final_data[ 'post_type' ] = "product_variation";

                        $wpie_final_data[ 'post_parent' ] = intval( $this->parent_id );

                        $wpie_final_data[ 'post_status' ] = 'publish';

                        if ( intval( $this->variation_id ) == 0 && isset( $wpie_final_data[ 'ID' ] ) ) {
                                unset( $wpie_final_data[ 'ID' ] );
                        }
                }

                return $wpie_final_data;
        }

        public function after_item_import( $item_id = 0, $item = null, $is_new_item = false ) {

                $this->item_id = $item_id;

                $this->item = $item;

                $this->is_new_item = $is_new_item;

                $current_product_type = wpie_sanitize_field( strtolower( trim( $this->get_field_value( 'wpie_item_product_type', false, true ) ) ) );

                if ( absint( $this->parent_id ) < 1 && $current_product_type === "variation" ) {
                        wp_delete_post( $this->item_id, true );
                        return false;
                }

                if ( $this->parent_id > 0 ) {

                        $product = new \WC_Product_Variation( $this->item_id );

                        if ( ! ( $product && method_exists( $product, "get_id" ) && $product->get_id() > 0 ) ) {

                                if ( $this->is_new_item ) {
                                        wp_delete_post( $this->item_id, true );
                                }
                                return false;
                        }


                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-wpie-variation-product.php';

                        if ( file_exists( $fileName ) ) {

                                require_once($fileName);
                        }

                        $variation = new \wpie\import\wc\product\variation\WPIE_Variation_Product( $this->wpie_import_option, $this->wpie_import_record, $this->item_id, $this->item, $this->is_new_item, $this->parent_id );

                        $variation->set_product( $product );

                        $variation->import_data();
                } else {

                        $product_type = wpie_sanitize_field( $this->get_field_value( 'wpie_item_product_type', false, true ) );

                        $existing_product_type = \WC_Product_Factory::get_product_type( $this->item_id );

                        if ( empty( $product_type ) ) {
                                $product_type = $existing_product_type;
                        }
                        if ( $product_type !== $existing_product_type && $existing_product_type === "variable" ) {
                                //$this->remove_variation();
                        }

                        $product_type = $product_type ? strtolower( trim( $product_type ) ) : 'simple';

                        $className = \WC_Product_Factory::get_product_classname( $this->item_id, $product_type );

                        $product = new $className( $this->item_id );

                        if ( ! ( $product && method_exists( $product, "get_id" ) && $product->get_id() > 0 ) ) {

                                if ( $this->is_new_item ) {
                                        wp_delete_post( $this->item_id );
                                }
                                return false;
                        }

                        $productClass = "";

                        $fileName = "";

                        switch ( $product_type ) {

                                case 'external':

                                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-wpie-external-product.php';

                                        $productClass = '\wpie\import\wc\product\external\WPIE_External_Product';

                                        break;
                                case 'grouped':
                                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-wpie-grouped-product.php';

                                        $productClass = '\wpie\import\wc\product\grouped\WPIE_Grouped_Product';

                                        break;
                                case 'variation':
                                case 'variable':

                                        $this->set_unique_key( $product_type );

                                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-wpie-variable-product.php';

                                        $productClass = '\wpie\import\wc\product\variable\WPIE_Variable_Product';

                                        break;
                                default:
                                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-wpie-simple-product.php';

                                        $productClass = '\wpie\import\wc\product\simple\WPIE_Simple_Product';

                                        break;
                        }


                        if ( ! empty( $fileName ) && file_exists( $fileName ) ) {

                                require_once($fileName);
                        }

                        unset( $fileName, $product_type );

                        if ( class_exists( $productClass ) ) {

                                $product_data = new $productClass( $this->wpie_import_option, $this->wpie_import_record, $item_id, $item, $is_new_item );

                                $product_data->set_product( $product );

                                $product_data->import_data();
                        }
                        unset( $productClass );
                }
        }

        private function remove_variation() {

                $product = wc_get_product( $this->item_id );

                if ( ! ( $product && method_exists( $product, "get_id" ) && $product->get_id() > 0 ) && $product->get_type() === "variable" ) {

                        return false;
                }

                $children = $product->get_children();

                if ( ! empty( $children ) && is_array( $children ) ) {
                        foreach ( $children as $child ) {
                                wp_delete_post( $child );
                        }
                }
        }

        private function set_unique_key( $product_type = "" ) {

                if ( $product_type == "variable" || $product_type == "variation" ) {

                        $unique_key = "";

                        $variation_method = wpie_sanitize_field( $this->get_field_value( 'wpie_item_variation_import_method', true ) );

                        switch ( $variation_method ) {
                                case "match_unique_field";
                                        $unique_key = wpie_sanitize_field( $this->get_field_value( 'wpie_item_product_variation_field_parent' ) );
                                        break;
                                case "match_group_field";
                                        $unique_key = wpie_sanitize_field( $this->get_field_value( 'wpie_item_product_variation_match_group_field' ) );
                                        break;
                                case "match_title_field";
                                        $unique_key = wpie_sanitize_field( $this->get_field_value( 'wpie_item_variation_import_method_title_field' ) );
                                        break;
                                case "match_title_field_no_parent";
                                        $unique_key = wpie_sanitize_field( $this->get_field_value( 'wpie_item_variation_import_method_title_field_no_parent' ) );
                                        break;
                        }

                        if ( ! empty( $unique_key ) ) {
                                $this->set_product_unique_id( $unique_key );
                        }
                        unset( $unique_key );
                }
        }

        private function get_product_unique_id() {

                if ( file_exists( $this->get_product_group_log_dir() . "/log.json" ) ) {

                        $this->unique_keys = json_decode( file_get_contents( $this->get_product_group_log_dir() . "/log.json" ), true );
                }
        }

        private function set_product_unique_id( $unique_key = "" ) {

                $this->unique_keys[ $this->item_id ] = $unique_key;

                $base_dir = $this->get_product_group_log_dir();

                wp_mkdir_p( $base_dir );

                file_put_contents( $base_dir . "/log.json", json_encode( array_unique( $this->unique_keys ) ) );
        }

        private function get_product_group_log_dir() {
                return WPIE_UPLOAD_IMPORT_DIR . "/" . $this->base_dir . "/group";
        }

        public function __destruct() {

                parent::__destruct();

                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
