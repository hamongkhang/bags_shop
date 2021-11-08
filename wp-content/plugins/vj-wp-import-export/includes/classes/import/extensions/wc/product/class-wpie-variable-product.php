<?php

namespace wpie\import\wc\product\variable;

use wpie\import\wc\product\variation\WPIE_Variation_Product;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-wpie-product-base.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-wpie-product-base.php');
}

class WPIE_Variable_Product extends \wpie\import\wc\product\base\WPIE_Product_Base {

        protected $product_type = 'variable';

         public function import_data() {

                parent::import_data();

                $variation_method = wpie_sanitize_field( $this->get_field_value( 'wpie_item_variation_import_method', true ) );

                if ( $variation_method == "attributes" ) {
                        $this->link_all_variations();
                } else if ( $variation_method == "match_group_field" || $variation_method == "match_title_field_no_parent" ) {
                        $this->create_child_based_on_parent();
                }

                $set_default = wpie_sanitize_field( $this->get_field_value( 'wpie_item_first_variation_as_default', true ) );

                if ( intval( $set_default ) == 1 ) {
                        $this->set_default_attribute();
                }

                unset( $variation_method, $set_default );
        }

        private function set_default_attribute() {

                $is_set_default_attr = false;

                $product = \wc_get_product( $this->item_id );

                $default_attributes = $product->get_default_attributes( "edit" );

                if ( ! empty( $default_attributes ) ) {
                        return true;
                }

                $product_variation = $product->get_children();

                if ( ! empty( $product_variation ) ) {

                        foreach ( $product_variation as $child_id ) {

                                $child = \wc_get_product( $child_id );

                                $product->set_default_attributes( $child->get_attributes() );

                                $is_set_default_attr = true;

                                unset( $child );

                                break;
                        }
                }

                if ( ! $is_set_default_attr ) {

                        $attributes = wc_list_pluck( array_filter( $product->get_attributes(), 'wc_attributes_array_filter_variation' ), 'get_slugs' );

                        if ( ! empty( $attributes ) ) {

                                $possible_attributes = array_reverse( wc_array_cartesian( $attributes ) );

                                foreach ( $possible_attributes as $possible_attribute ) {

                                        $product->set_default_attributes( $possible_attribute );

                                        $is_set_default_attr = true;

                                        break;
                                }
                                unset( $possible_attributes );
                        }
                        unset( $attributes );
                }

                if ( $is_set_default_attr ) {
                        $product->save();
                }
                unset( $is_set_default_attr );
        }

        private function link_all_variations() {

                $attributes = wc_list_pluck( array_filter( $this->product->get_attributes(), 'wc_attributes_array_filter_variation' ), 'get_slugs' );

                if ( ! empty( $attributes ) ) {

                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-wpie-variation-product.php';

                        if ( file_exists( $fileName ) ) {

                                require_once($fileName);
                        }

                        $existing_attributes = array ();

                        if ( ! $this->is_new_item ) {

                                $existing_variations = array_map( 'wc_get_product', $this->product->get_children() );

                                if ( ! empty( $existing_variations ) ) {
                                        foreach ( $existing_variations as $existing_variation ) {
                                                $existing_attributes[] = $existing_variation->get_attributes();
                                        }
                                }
                        }

                        $possible_attributes = array_reverse( wc_array_cartesian( $attributes ) );

                        foreach ( $possible_attributes as $possible_attribute ) {

                                if ( in_array( $possible_attribute, $existing_attributes ) ) {
                                        continue;
                                }

                                $variation = new \WC_Product_Variation();

                                $variation->set_parent_id( $this->item_id );

                                $variation->set_attributes( $possible_attribute );

                                $variation_id = $variation->save();

                                do_action( 'product_variation_linked', $variation_id );

                                $item = \get_post( $variation_id );

                                $variation_data = new \wpie\import\wc\product\variation\WPIE_Variation_Product( $this->wpie_import_option, $this->wpie_import_record, $variation_id, $item, true, $this->item_id );

                                $variation_product = \wc_get_product( $variation_id );

                                $variation_data->set_product( $variation_product );

                                $variation_data->prepare_link_all_variation_properties();

                                unset( $variation, $variation_data, $variation_id, $item );
                        }

                        unset( $possible_attributes, $existing_attributes );
                }

                unset( $attributes );

                $data_store = $this->product->get_data_store();

                $data_store->sort_all_product_variations( $this->item_id );
        }

        private function create_child_based_on_parent() {

                $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-wpie-variation-product.php';

                if ( file_exists( $fileName ) ) {

                        require_once($fileName);
                }

                $variation_id = false;

                if ( ! $this->is_new_item ) {

                        $_variation = new \wpie\import\wc\product\variation\WPIE_Variation_Product( $this->wpie_import_option, $this->wpie_import_record, 0, null, false, $this->item_id );

                        $_variation_id = $_variation->is_variation_exist();

                        if ( $_variation_id !== false && intval( $_variation_id ) > 0 ) {

                                $variation_id = $_variation_id;
                        }
                }

                if ( $variation_id === false ) {

                        $product = wc_get_product( $this->item_id );

                        $variation_post = array (
                                'post_title'  => $product->get_title(),
                                'post_name'   => 'product-' . $this->item_id . '-variation',
                                'post_status' => 'publish',
                                'post_parent' => $this->item_id,
                                'post_type'   => 'product_variation',
                                'guid'        => $product->get_permalink()
                        );

                        $variation_id = \wp_insert_post( $variation_post );

                        if ( is_wp_error( $variation_id ) ) {
                                return;
                        }

                        if ( absint( $this->existing_item_id ) === 0 ) {

                                $metas = get_post_meta( $this->item_id );

                                if ( ! empty( $metas ) ) {
                                        foreach ( $metas as $meta_key => $meta_value ) {
                                                $single_val = is_array( $meta_value ) && isset( $meta_value[ 0 ] ) ? $meta_value[ 0 ] : "";
                                                if ( empty( $single_val ) ) {
                                                        continue;
                                                }
                                                update_post_meta( $variation_id, $meta_key, $single_val );
                                        }
                                }

                                $sku = get_post_meta( $this->item_id, "_sku" );

                                if ( ! empty( $sku ) ) {

                                        $new_sku = $this->sku_rand( $this->item_id );

                                        update_post_meta( $this->item_id, "_sku", $new_sku );
                                }

                                $price = wpie_sanitize_field( $this->get_field_value( 'wpie_item_meta_regular_price' ) );

                                if ( ! empty( $price ) ) {
                                        update_post_meta( $variation_id, "_price", wc_format_decimal( $price ) );
                                }
                        }
                }

                $item = get_post( $variation_id );

                $variation = new \wpie\import\wc\product\variation\WPIE_Variation_Product( $this->wpie_import_option, $this->wpie_import_record, $variation_id, $item, true, $this->item_id );

                $variation_product = \wc_get_product( $variation_id );

                $variation->set_product( $variation_product );

                $variation->import_data();

                unset( $fileName, $product, $variation_post, $variation_id, $item, $variation, $variation_product );
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
