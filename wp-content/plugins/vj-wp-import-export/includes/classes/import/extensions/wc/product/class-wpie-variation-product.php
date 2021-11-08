<?php


namespace wpie\import\wc\product\variation;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-wpie-product-base.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-wpie-product-base.php');
}

class WPIE_Variation_Product extends \wpie\import\wc\product\base\WPIE_Product_Base {

        protected $product_type = 'product_variation';
        private $parent_id;

        public function __construct( $wpie_import_option = array(), $wpie_import_record = array(), $item_id = "", $item = null, $is_new_item = false, $parent_id = 0 ) {

                $this->parent_id = $parent_id;

                parent::__construct( $wpie_import_option, $wpie_import_record, $item_id, $item, $is_new_item );
        }

        public function import_data() {

                $this->set_description();

                parent::import_data();

                $set_default = wpie_sanitize_field( $this->get_field_value( 'wpie_item_first_variation_as_default', true ) );

                if ( intval( $set_default ) === 1 ) {
                        $this->set_default_attribute();
                }

                $set_gallery = wpie_sanitize_field( $this->get_field_value( 'wpie_item_set_image_parent_gallery', true ) );

                if ( intval( $set_gallery ) === 1 ) {
                        $this->set_image_parent_gallery();
                }

                unset( $set_default, $set_gallery );
        }

        private function set_description() {

                if ( $this->is_update_meta( "_variation_description" ) ) {

                        $this->product_properties[ 'description' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_meta_variation_description' ) );
                }
        }

        private function set_default_attribute() {

                $is_set_default_attr = false;

                $product = \wc_get_product( $this->parent_id );

                $default_attributes = $product->get_default_attributes( "edit" );

                if ( !empty( $default_attributes ) ) {
                        return true;
                }

                $product_variation = $product->get_children();

                if ( !empty( $product_variation ) ) {

                        foreach ( $product_variation as $child_id ) {

                                $child = \wc_get_product( $child_id );

                                $product->set_default_attributes( $child->get_attributes() );

                                $is_set_default_attr = true;

                                unset( $child );

                                break;
                        }
                }

                if ( !$is_set_default_attr ) {

                        $attributes = wc_list_pluck( array_filter( $product->get_attributes(), 'wc_attributes_array_filter_variation' ), 'get_slugs' );

                        if ( !empty( $attributes ) ) {

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

        protected function prepare_properties() {

                $this->prepare_variation_status();

                $this->prepare_general_properties();

                $this->prepare_inventory_properties();

                $this->prepare_shipping_properties();

                $this->prepare_attributes_properties();
        }

        public function prepare_link_all_variation_properties() {

                $this->prepare_variation_status();

                $this->prepare_general_properties();

                $this->prepare_inventory_properties();

                $this->prepare_shipping_properties();

                $this->product->set_props( $this->product_properties );

                $this->save();
        }

        private function prepare_variation_status() {

                $status = strtolower( trim( wpie_sanitize_field( $this->get_field_value( 'wpie_item_variation_enable', false, true ) ) ) ) === "yes" ? 'publish' : 'private';

                $this->get_product()->set_status( $status );
        }

        private function prepare_attributes_properties() {

                $attributes = $this->get_attr_data();

                $parent_attributes = get_post_meta( $this->parent_id, "_product_attributes", true );

                if ( empty( $parent_attributes ) || !is_array( $parent_attributes ) ) {
                        $parent_attributes = array();
                }

                $is_update_parent_attribute = false;

                if ( isset( $attributes[ 'attribute_names' ] ) && !empty( $attributes[ 'attribute_names' ] ) ) {

                        foreach ( $attributes[ 'attribute_names' ] as $key => $attribute ) {

                                $value = isset( $attributes[ 'attribute_values' ][ $key ] ) ? $attributes[ 'attribute_values' ][ $key ] : array();

                                if ( empty( $value ) ) {
                                        continue;
                                }

                                $term_id = (is_array( $value ) && isset( $value[ 0 ] ) ) ? $value[ 0 ] : $value;

                                $is_taxonomy = 0;

                                $term_slug = null;

                                if ( is_numeric( $term_id ) ) {

                                        $term = get_term_by( 'id', intval( $term_id ), $attribute );

                                        if ( is_object( $term ) && isset( $term->slug ) ) {

                                                $is_taxonomy = 1;

                                                $term_slug = $term->slug;

                                                $post_term_ids = wp_get_post_terms( $this->parent_id, $attribute, array( 'fields' => 'ids' ) );

                                                if ( is_array( $post_term_ids ) && !in_array( $term_id, $post_term_ids ) ) {
                                                        wp_set_post_terms( $this->parent_id, array( $term_id ), $attribute, true );
                                                }

                                                unset( $post_term_ids );
                                        }
                                }

                                if ( $term_slug === null ) {
                                        $term_slug = ( is_array( $value ) && isset( $value[ 0 ] )) ? $value[ 0 ] : $value;
                                }

                                if ( !isset( $parent_attributes[ $attribute ] ) || empty( $parent_attributes[ $attribute ] ) ) {

                                        $is_update_parent_attribute = true;

                                        $parent_attributes[ $attribute ] = array(
                                                "name" => $attribute,
                                                "value" => $value,
                                                "is_visible" => isset( $attributes[ 'attribute_visibility' ] ) && isset( $attributes[ 'attribute_visibility' ][ $key ] ) ? $attributes[ 'attribute_visibility' ][ $key ] : "",
                                                "is_taxonomy" => $is_taxonomy,
                                                "is_variation" => 1,
                                                "position" => isset( $attributes[ 'attribute_position' ] ) && isset( $attributes[ 'attribute_position' ][ $key ] ) ? $attributes[ 'attribute_position' ][ $key ] : ""
                                        );
                                }

                                update_post_meta( $this->item_id, 'attribute_' . $attribute, $term_slug );

                                unset( $term, $value, $term_id );
                        }
                }

                if ( $is_update_parent_attribute ) {
                        update_post_meta( $this->parent_id, "_product_attributes", $parent_attributes );
                }

                unset( $attributes );
        }

        public function is_variation_exist() {

                $variation_id = $this->get_variation_by_attribute( $this->parent_id );

                if ( intval( $variation_id ) === 0 ) {
                        $variation_id = $this->get_variation_by_sku();
                }
                return $variation_id;
        }

        public function search_variation() {

                $wpie_duplicate_indicator = empty( $this->get_field_value( 'wpie_existing_item_search_logic', true ) ) ? 'title' : wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic', true ) );

                $variation_id = 0;

                if ( $wpie_duplicate_indicator === "id" ) {
                        $variation_id = $this->get_variation_by_id();
                } elseif ( $wpie_duplicate_indicator === "sku" || $wpie_duplicate_indicator === "cf" ) {
                        $variation_id = $this->get_variation_by_sku();
                }

                if ( absint( $variation_id ) === 0 && absint( $this->parent_id ) > 0 ) {
                        $variation_id = $this->get_variation_by_attribute( $this->parent_id );
                }

                return $variation_id;
        }

        public function get_variation_by_id() {

                $post_id = wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic_id' ) );

                $post_id = absint( $post_id ) > 0 ? absint( $post_id ) : 0;

                if ( $post_id === 0 ) {
                        return 0;
                }

                global $wpdb;

                $variation_id = $wpdb->get_var(
                        $wpdb->prepare(
                                "
                                                SELECT posts.ID
                                                FROM {$wpdb->posts} as posts
                                                WHERE post_type = 'product_variation'
                                                AND post_status NOT IN ('trash','auto-draft' )
                                                AND ID = %d
                                        ",
                                $post_id
                        )
                );

                if ( absint( $variation_id ) > 0 ) {
                        return absint( $variation_id );
                }
                return 0;
        }

        public function get_variation_by_sku() {

                $wpie_duplicate_indicator = empty( $this->get_field_value( 'wpie_existing_item_search_logic', true ) ) ? 'title' : wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic', true ) );

                $sku = "";

                if ( $wpie_duplicate_indicator == "sku" ) {
                        $sku = wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic_sku' ) );
                } elseif ( $wpie_duplicate_indicator == "cf" ) {
                        $meta_key = wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic_cf_key' ) );
                        if ( strpos( trim( strtolower( $meta_key ) ), "sku" ) !== false ) {
                                $sku = wpie_sanitize_field( $this->get_field_value( 'wpie_existing_item_search_logic_cf_value' ) );
                        }
                }

                if ( empty( $sku ) ) {
                        $sku = wpie_sanitize_field( $this->get_field_value( 'wpie_item_meta_sku' ) );
                }

                if ( empty( $sku ) ) {
                        return 0;
                }
                global $wpdb;

                $variation_id = $wpdb->get_var(
                        $wpdb->prepare(
                                "
                                                SELECT posts.ID
                                                FROM {$wpdb->posts} as posts
                                                INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
                                                WHERE posts.post_type IN ( 'product_variation' )
                                                AND posts.post_status NOT IN ('trash','auto-draft' )
                                                AND postmeta.meta_key = '_sku'                                               
                                                AND postmeta.meta_value = %s
                                                ORDER BY posts.ID ASC
                                                LIMIT 0, 1
                                        ",
                                $sku
                        )
                );

                if ( absint( $variation_id ) > 0 ) {
                        return absint( $variation_id );
                }
                return 0;
        }

        private function get_variation_by_attribute( $parent_id = 0 ) {

                $attr_names = wpie_sanitize_field( $this->get_field_value( 'wpie_product_attr_name' ) );

                $attr_slugs = wpie_sanitize_field( $this->get_field_value( 'wpie_attr_slug' ) );

                $attr_values = wpie_sanitize_field( $this->get_field_value( 'wpie_product_attr_value' ) );

                if ( $this->is_empty_array( $attr_names ) && $this->is_empty_array( $attr_values ) ) {
                        return 0;
                }
                if ( empty( $attr_slugs ) ) {
                        $attr_slugs = $attr_names;
                }
                if ( !empty( $attr_slugs ) ) {

                        $attr_slugs_as_specified = wpie_sanitize_field( $this->get_field_value( 'wpie_attr_slug_as_specified_data' ) );

                        $attr_is_taxonomy = wpie_sanitize_field( $this->get_field_value( 'wpie_attr_is_taxonomy' ) );

                        $attr_is_taxonomy_as_specified = wpie_sanitize_field( $this->get_field_value( 'wpie_attr_is_taxonomy_as_specified_data' ) );

                        global $wpdb;

                        $join = "";

                        $where = " where ";

                        foreach ( $attr_slugs as $key => $attribute ) {

                                $value = isset( $attr_values[ $key ] ) ? $attr_values[ $key ] : "";

                                if ( empty( $value ) ) {
                                        continue;
                                }

                                if ( strtolower( trim( $attribute ) ) === "as_specified" ) {

                                        $attribute = isset( $attr_slugs_as_specified[ $key ] ) ? $attr_slugs_as_specified[ $key ] : 0;
                                } else {
                                        $attribute = isset( $attr_names[ $key ] ) ? $attr_names[ $key ] : "";
                                }

                                $is_taxonomy = isset( $attr_is_taxonomy[ $key ] ) ? $attr_is_taxonomy[ $key ] : "";

                                if ( strtolower( trim( $is_taxonomy ) ) === "as_specified" ) {

                                        $is_taxonomy = isset( $attr_is_taxonomy_as_specified[ $key ] ) ? $attr_is_taxonomy_as_specified[ $key ] : "";
                                }

                                if ( strtolower( trim( $is_taxonomy ) ) === "yes" || intval( $is_taxonomy ) === 1 ) {
                                        $attribute = wc_attribute_taxonomy_name( $attribute );
                                } else {
                                        $attribute = preg_replace( '/^pa\_/', '', $attribute );
                                }

                                $value2 = wc_sanitize_taxonomy_name( $value );

                                $rand = uniqid() . "__" . rand( 1, 999 );

                                $join .= " JOIN {$wpdb->prefix}postmeta as `pm_{$rand}` ON p.ID = `pm_{$rand}`.`post_id`";

                                $where .= " `pm_{$rand}`.`meta_key` = 'attribute_{$attribute}' AND ( `pm_{$rand}`.`meta_value` LIKE '{$value}' OR `pm_{$rand}`.`meta_value` LIKE '{$value2}' ) AND ";

                                unset( $value );
                        }

                        $query = 'SELECT p.ID FROM ' . $wpdb->prefix . 'posts as p ' . $join . $where . ' p.post_parent =' . $parent_id;

                        unset( $join, $where, $is_include );

                        $variation_id = $wpdb->get_var( $query );

                        if ( absint( $variation_id ) > 0 ) {
                                return absint( $variation_id );
                        }
                }

                return 0;
        }

        private function is_empty_array( $var = null ) {

                if ( !is_array( $var ) || (is_array( $var ) && count( $var ) === 0 ) ) {
                        return true;
                }
                return strlen( trim( implode( "", $var ) ) ) === 0;
        }

        public function get_variation_id() {
                return $this->variation_id;
        }

        private function set_image_parent_gallery() {

                $gallery = get_post_meta( $this->item_id, "_product_image_gallery", true );

                if ( !empty( $gallery ) ) {

                        $gallery = explode( ",", $gallery );

                        $parent_gallery = get_post_meta( $this->parent_id, "_product_image_gallery", true );

                        if ( empty( $parent_gallery ) ) {
                                $parent_gallery = [];
                        } else {
                                $parent_gallery = explode( ",", $parent_gallery );
                        }

                        $parent_gallery = array_merge( $parent_gallery, $gallery );

                        $parent_gallery = empty( $parent_gallery ) ? "" : (implode( ",", array_unique( $parent_gallery ) ));

                        update_post_meta( $this->parent_id, "_product_image_gallery", $parent_gallery );
                }
        }

        public function __destruct() {

                parent::__destruct();

                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
