<?php


namespace wpie\export\wc\product;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if ( file_exists( WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export-base.php' ) ) {

        require_once(WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export-base.php');
}

class WPIE_WC_Product extends \wpie\export\base\WPIE_Export_Base {

        private $product;

        public function __construct() {

                add_filter( 'wpie_export_posts_where', array( $this, 'set_variable_condition' ), 10, 2 );

                add_filter( 'wpie_export_post_meta', array( $this, 'wpie_export_product_meta' ), 10, 3 );
        }

        public function product_metas() {
                return array(
                        '_visibility', '_stock_status', '_downloadable', '_virtual', '_regular_price', '_sale_price', '_purchase_note', '_featured', '_weight', '_length',
                        '_width', '_height', '_sku', '_sale_price_dates_from', '_sale_price_dates_to', '_price', '_sold_individually', '_manage_stock', '_stock', '_upsell_ids', '_crosssell_ids',
                        '_downloadable_files', '_download_limit', '_download_expiry', '_download_type', '_product_url', '_button_text', '_backorders', '_tax_status', '_tax_class', '_product_image_gallery', '_default_attributes',
                        'total_sales', '_product_attributes', '_product_version', '_variation_description', '_wc_rating_count', '_wc_review_count', '_wc_average_rating'
                );
        }

        public function pre_process_fields( &$export_fields = array(), $export_type = array() ) {

                $product_fields = $this->product_metas();

                $export_fields[ 'product_arrtibute' ] = array(
                        'title'      => __( "Product Attribute", 'vj-wp-import-export' ),
                        'isFiltered' => true,
                        'data'       => $this->get_attribute_fields( "wc-product-attr", $export_type )
                );

                $export_fields[ 'meta' ] = array_diff( $export_fields[ 'meta' ], $product_fields );

                $product_fields = array_diff( $product_fields, array( "_download_type", "_product_image_gallery", "_product_attributes" ) );

                $product_fields[] = "_downloadable_file_name";

                $export_fields[ 'product_fields' ] = array(
                        'title'      => __( "Product Data", 'vj-wp-import-export' ),
                        'isFiltered' => true,
                        'data'       => $this->format_fields( $product_fields, 'wc-product', "wpie_cf" )
                );

                unset( $product_fields );

                if ( $export_fields[ 'meta' ] ) {

                        foreach ( $export_fields[ 'meta' ] as $key => $meta )
                                if ( strpos( $meta, 'attribute_' ) === 0 ) {
                                        unset( $export_fields[ 'meta' ][ $key ] );
                                }
                }
        }

        public function get_attribute_fields( $type = "wc-product-attr", $export_type = array() ) {

                global $wp_taxonomies;

                $attributes = $this->get_taxonomies_by_post_type( $export_type, $type, true );

                $product_custom_attribute = $this->get_product_custom_attribute();

                if ( !empty( $product_custom_attribute ) ) {

                        foreach ( $product_custom_attribute as $attribute ) {
                                $attributes[] = array(
                                        'name'    => preg_replace( '%^attribute_%', '', $attribute->meta_key ),
                                        'type'    => $type,
                                        'isTax'   => false,
                                        'metaKey' => $attribute->meta_key
                                );
                        }
                }

                unset( $product_custom_attribute );

                return $attributes;
        }

        private function fix_title( $field_title = "" ) {

                $field_title = ucwords( trim( str_replace( "_", " ", $field_title ) ) );

                return stripos( $field_title, "width" ) === false ? str_ireplace( array( 'id', 'url', 'sku', 'wc' ), array( 'ID', 'URL', 'SKU', 'WC' ), $field_title ) : $field_title;
        }

        private function get_product_custom_attribute() {

                global $wpdb;

                return $wpdb->get_results( $wpdb->prepare( "SELECT DISTINCT meta_key FROM {$wpdb->prefix}postmeta 
						WHERE {$wpdb->prefix}postmeta.meta_key LIKE %s AND {$wpdb->prefix}postmeta.meta_key NOT LIKE %s", 'attribute_%', 'attribute_pa_%' ) );
        }

        public function format_fields( $fields_data = array(), $new_type = 'wc-product', $default_type = "wpie_cf" ) {

                $advanced_fields = array();

                if ( !empty( $fields_data ) ) {

                        foreach ( $fields_data as $field_key ) {

                                if ( strpos( $field_key, 'attribute_pa_' ) === 0 ) {
                                        continue;
                                }

                                switch ( $field_key ) {
                                        case '_upsell_ids':
                                                $advanced_fields[] = array(
                                                        'name'        => 'Up-Sells',
                                                        'type'        => "wc-product",
                                                        'UpCrossSell' => 'sku',
                                                        'metaKey'     => $field_key
                                                );
                                                break;
                                        case '_crosssell_ids':
                                                $advanced_fields[] = array(
                                                        'name'        => 'Cross-Sells',
                                                        'type'        => "wc-product",
                                                        'UpCrossSell' => 'sku',
                                                        'metaKey'     => $field_key
                                                );
                                                break;

                                        default:
                                                $advanced_fields[] = array(
                                                        'name'    => $this->fix_title( $field_key ),
                                                        'type'    => $default_type,
                                                        'metaKey' => $field_key
                                                );
                                                break;
                                }
                        }
                }
                return $advanced_fields;
        }

        private function get_product( $product_id = 0 ) {

                if ( !$this->product ) {

                        $this->product = wc_get_product( $product_id );
                }
                return $this->product;
        }

        public function process_item_taxonomy_id( $taxonomy_id = 0, $field_type = "", $field_name = "", $field_option = array(), $item = null ) {

                $taxName = isset( $field_option[ 'taxName' ] ) ? $field_option[ 'taxName' ] : "";

                if ( !in_array( $taxName, array( 'product_shipping_class', 'product_visibility', 'product_type' ) ) && $item->post_type == 'product_variation' ) {

                        $taxonomy_id = isset( $item->post_parent ) && absint( $item->post_parent ) > 0 ? absint( $item->post_parent ) : $taxonomy_id;
                }

                unset( $taxName );

                return $taxonomy_id;
        }

        public function pre_process_data( &$export_data = array(), $field_type = "", $field_name = "", $field_option = array(), $item = null ) {
                $this->product = null;
        }

        public function process_item_taxonomy( &$export_data = array(), $field_type = "", $field_name = "", $field_option = array(), $item = null ) {

                if ( $field_type ) {

                        $taxName = isset( $field_option[ 'taxName' ] ) ? $field_option[ 'taxName' ] : "";

                        switch ( $taxName ) {

                                case "product_visibility":

                                        $product = $this->get_product( $item->ID );

                                        $export_data[ $field_name ] = $product->get_catalog_visibility();

                                        unset( $product );

                                        break;

                                case "product_type":

                                        if ( $item->post_type == 'product_variation' ) {
                                                $export_data[ $field_name ] = "variation";
                                        }

                                        break;
                        }
                        unset( $taxName );
                }
        }

        public function process_addon_data( &$export_data = array(), $field_type = "", $field_name = "", $field_option = array(), $item = null, $site_date_format = "" ) {

                global $wp_taxonomies;

                if ( $field_type ) {

                        $is_php = isset( $field_option[ 'isPhp' ] ) ? wpie_sanitize_field( $field_option[ 'isPhp' ] ) == 1 : false;

                        $php_func = isset( $field_option[ 'phpFun' ] ) ? wpie_sanitize_field( $field_option[ 'phpFun' ] ) : "";

                        $date_type = isset( $field_option[ 'dateType' ] ) ? wpie_sanitize_field( $field_option[ 'dateType' ] ) : "";

                        $date_format = isset( $field_option[ 'dateFormat' ] ) ? wpie_sanitize_field( $field_option[ 'dateFormat' ] ) : "";

                        switch ( $field_type ) {

                                case 'wpie_cf':

                                        $metaKey = isset( $field_option[ 'metaKey' ] ) ? $field_option[ 'metaKey' ] : "";

                                        switch ( $metaKey ) {

                                                case '_sale_price_dates_from':
                                                case '_sale_price_dates_to':

                                                        $_dates = $this->get_date_field( $date_type, $export_data[ $field_name ], $date_format );

                                                        $export_data[ $field_name ] = apply_filters( 'wpie_export_post_meta', $this->apply_user_function( $_dates, $is_php, $php_func ), $item );

                                                        unset( $_dates );

                                                        break;
                                                case '_tax_class':

                                                        $tax_status = get_post_meta( $item->ID, '_tax_status', true );

                                                        if ( 'taxable' == $tax_status ) {
                                                                $export_data[ $field_name ] = apply_filters( 'wpie_export_post_meta', $this->apply_user_function( 'standard', $is_php, $php_func ), $item );
                                                        }
                                                        unset( $tax_status );

                                                        break;
                                                default:
                                                        break;
                                        }

                                        unset( $metaKey );
                                        break;
                                case "wc-product":

                                        $metaKey = isset( $field_option[ 'metaKey' ] ) ? $field_option[ 'metaKey' ] : "";

                                        $metaValue = get_post_meta( $item->ID, $metaKey, true );

                                        $value = array();

                                        $is_empty = true;

                                        switch ( $metaKey ) {

                                                case '_crosssell_ids':

                                                case '_upsell_ids':

                                                        $indicator = isset( $field_option[ 'indicator' ] ) ? wpie_sanitize_field( $field_option[ 'indicator' ] ) : "sku";

                                                        switch ( $indicator ) {

                                                                case 'sku':

                                                                        if ( !empty( $metaValue ) ) {

                                                                                foreach ( $metaValue as $_id ) {

                                                                                        $_sku_data = get_post_meta( $_id, '_sku', true );

                                                                                        $value[] = $_sku_data;

                                                                                        if ( !empty( $_sku_data ) && $is_empty ) {
                                                                                                $is_empty = false;
                                                                                        }

                                                                                        unset( $_sku_data );
                                                                                }
                                                                        }

                                                                        break;
                                                                case 'id':
                                                                        $value = $metaValue;

                                                                        if ( !empty( $metaValue ) ) {
                                                                                $is_empty = false;
                                                                        }

                                                                        break;
                                                                case 'name':
                                                                        if ( !empty( $metaValue ) ) {

                                                                                foreach ( $metaValue as $_id ) {

                                                                                        $_post = get_post( $_id );

                                                                                        $_post_name = "";

                                                                                        if ( $_post ) {
                                                                                                $_post_name = $_post->post_name;
                                                                                        }

                                                                                        $value[] = $_post_name;

                                                                                        if ( !empty( $_post_name ) && $is_empty ) {
                                                                                                $is_empty = false;
                                                                                        }

                                                                                        unset( $_post );
                                                                                }
                                                                        }

                                                                        break;
                                                                default:
                                                                        break;
                                                        }
                                                        unset( $indicator );

                                                        break;
                                        }

                                        if ( !$is_empty ) {
                                                $value = implode( "|", $value );
                                        } else {
                                                $value = "";
                                        }

                                        $export_data[ $field_name ] = apply_filters( 'wpie_export_post_meta', $this->apply_user_function( $value, $is_php, $php_func ), $item );

                                        unset( $metaKey, $metaValue, $value );

                                        break;
                                case "wc-product-attr":

                                        unset( $export_data[ $field_name ] );

                                        $product_attributes = ($item->post_type == "product") ? get_post_meta( $item->ID, '_product_attributes', true ) : get_post_meta( $item->post_parent, '_product_attributes', true );

                                        if ( !is_array( $product_attributes ) ) {
                                                $product_attributes = array();
                                        }

                                        $isTax = isset( $field_option[ 'isTax' ] ) && $field_option[ 'isTax' ] == 1 ? true : false;

                                        $value = array();

                                        if ( $isTax ) {

                                                $taxName = isset( $field_option[ 'taxName' ] ) ? wpie_sanitize_field( $field_option[ 'taxName' ] ) : "";

                                                if ( $item->post_type == "product" ) {

                                                        $taxonomies = get_the_terms( $item->ID, $taxName );

                                                        if ( !is_wp_error( $taxonomies ) && !empty( $taxonomies ) ) {

                                                                foreach ( $taxonomies as $taxonomy ) {

                                                                        $value[] = $taxonomy->name;
                                                                }
                                                        }
                                                } else {
                                                        $value[] = get_post_meta( $item->ID, "attribute_" . $taxName, true );
                                                }
                                        } else {

                                                $taxName = isset( $field_option[ 'metaKey' ] ) ? str_replace( 'attribute_', '', $field_option[ 'metaKey' ] ) : "";

                                                if ( $item->post_type == "product" ) {

                                                        $value[] = isset( $product_attributes[ $taxName ] ) && isset( $product_attributes[ $taxName ][ "value" ] ) ? $product_attributes[ $taxName ][ "value" ] : "";
                                                } else {

                                                        $value[] = get_post_meta( $item->ID, "attribute_" . $taxName, true );
                                                }
                                        }

                                        $final_array_val = [];

                                        if ( is_array( $value ) && !empty( $value ) ) {

                                                foreach ( $value as $_val ) {

                                                        if ( (is_string( $_val ) || is_numeric( $_val ) ) && !empty( $_val ) ) {
                                                                $final_array_val[] = $_val;
                                                        }
                                                }
                                        }

                                        $product_attributes[ $taxName ] = isset( $product_attributes[ $taxName ] ) ? $product_attributes[ $taxName ] : array();

                                        $attr_name = (isset( $product_attributes[ $taxName ][ 'name' ] ) && !empty( $product_attributes[ $taxName ][ 'name' ] )) ? $product_attributes[ $taxName ][ 'name' ] : $taxName;

                                        $export_data[ $field_name . "_name" ] = !empty( $attr_name ) ? str_replace( 'pa_', '', $attr_name ) : "";

                                        $export_data[ $field_name . "_value" ] = apply_filters( 'wpie_export_product_attribute_value', $this->apply_user_function( !empty( $final_array_val ) ? implode( '~|~', $value ) : "", $is_php, $php_func ), $item );

                                        $export_data[ $field_name . "_is_variation" ] = isset( $product_attributes[ $taxName ][ 'is_variation' ] ) && !empty( $product_attributes[ $taxName ][ 'is_variation' ] ) ? "yes" : "no";

                                        $export_data[ $field_name . "_is_visible" ] = isset( $product_attributes[ $taxName ][ 'is_visible' ] ) && !empty( $product_attributes[ $taxName ][ 'is_visible' ] ) ? "yes" : "no";

                                        $export_data[ $field_name . "_is_taxonomy" ] = $isTax ? "yes" : "no";

                                        $export_data[ $field_name . "_position" ] = isset( $product_attributes[ $taxName ][ 'position' ] ) && !empty( $product_attributes[ $taxName ][ 'position' ] ) ? $product_attributes[ $taxName ][ 'position' ] : "";

                                        break;
                                default :
                                        break;
                        }

                        unset( $is_php, $php_func, $date_type, $date_format );
                }
        }

        public function set_variable_condition( $post_where = [], $post_join = [] ) {

                global $wpdb, $wpieExportTemplate;

                $cause = " AND ";

                $child_query = " ";

                if ( empty( $post_where ) ) {
                        $cause = "";
                }

                $variation_options = isset( $wpieExportTemplate[ 'wpie_product_variation_options' ] ) ? $wpieExportTemplate[ 'wpie_product_variation_options' ] : "all";

                if ( "parent" === $variation_options ) {
                        $post_where[] = " $cause $wpdb->posts.post_type != 'product_variation' ";
                        return $post_where;
                } elseif ( "child" === $variation_options ) {

                        $term_taxonomy_id = "";

                        $term = $this->is_term_exists( "variable", "product_type" );

                        if ( is_array( $term ) && isset( $term[ 'term_taxonomy_id' ] ) ) {
                                $term_taxonomy_id = $term[ 'term_taxonomy_id' ];
                        }
                        unset( $term );

                        if ( !empty( $term_taxonomy_id ) ) {

                                $child_query = " AND $wpdb->posts.ID NOT IN( 
                                                                        SELECT object_id
                                                                        FROM {$wpdb->term_relationships}
                                                                        WHERE term_taxonomy_id IN ($term_taxonomy_id)
                                                        ) ";
                        }
                }


                $cause = " AND ";

                if ( empty( $post_where ) ) {
                        $cause         = "";
                        $product_where = " {$wpdb->posts}.post_type = 'product' AND (({$wpdb->posts}.post_status <> 'trash' AND {$wpdb->posts}.post_status <> 'auto-draft'))";
                } else {
                        $product_where = implode( ' ', array_unique( $post_where ) );
                }

                if ( empty( $post_join ) ) {
                        $product_join = "";
                } else {
                        $product_join = implode( ' ', array_unique( $post_join ) );
                }

                $post_where[] = " $cause $wpdb->posts.post_type = 'product' 
                                        AND $wpdb->posts.ID NOT IN(
                                                SELECT o.ID FROM $wpdb->posts o
                                                LEFT OUTER JOIN $wpdb->posts r
                                                ON o.post_parent = r.ID
                                                WHERE r.post_status = 'trash' AND o.post_type = 'product_variation'
                                        )
                                        {$child_query}
                                        OR ($wpdb->posts.post_type = 'product_variation' 
                                                AND $wpdb->posts.post_parent IN (
                                                        SELECT DISTINCT $wpdb->posts.ID
                                                        FROM $wpdb->posts $product_join
                                                        WHERE $product_where
                                                    )
                                        )";

                unset( $cause, $product_where, $product_join, $child_query );

                return $post_where;
        }

        public function change_export_labels( &$export_labels = array(), $field_type = "", $field_name = "", $field_label = "", $field_option = array() ) {

                if ( $field_type == "wc-product-attr" ) {

                        unset( $export_labels[ $field_name ] );

                        $export_labels[ $field_name . "_name" ] = 'Attribute Name (' . $field_label . ')';

                        $export_labels[ $field_name . "_value" ] = 'Attribute Value (' . $field_label . ')';

                        $export_labels[ $field_name . "_is_variation" ] = 'Attribute In Variations (' . $field_label . ')';

                        $export_labels[ $field_name . "_is_visible" ] = 'Attribute Is Visible (' . $field_label . ')';

                        $export_labels[ $field_name . "_is_taxonomy" ] = 'Attribute Is Taxonomy (' . $field_label . ')';

                        $export_labels[ $field_name . "_position" ] = 'Attribute Position (' . $field_label . ')';
                }
        }

        public function wpie_export_product_meta( $meta_value = "", $meta_key = "", $item = null ) {

                if ( !empty( $meta_key ) ) {

                        switch ( $meta_key ) {

                                case "_downloadable_file_name":

                                        $_files = get_post_meta( $item->ID, "_downloadable_files" );

                                        if ( $_files ) {

                                                $_files = maybe_unserialize( $_files );

                                                $names = array();

                                                foreach ( $_files as $files ) {
                                                        foreach ( $files as $file ) {

                                                                $names[] = isset( $file[ 'name' ] ) ? $file[ 'name' ] : "";
                                                        }
                                                }

                                                $meta_value = implode( ",", $names );

                                                unset( $names );
                                        }

                                        unset( $_files );

                                        break;

                                case "_downloadable_files":
                                        $_files = get_post_meta( $item->ID, $meta_key );

                                        if ( $_files ) {

                                                $_files = maybe_unserialize( $_files );

                                                $names = array();

                                                foreach ( $_files as $files ) {
                                                        foreach ( $files as $file ) {

                                                                $names[] = isset( $file[ 'file' ] ) ? $file[ 'file' ] : "";
                                                        }
                                                }

                                                $meta_value = implode( ",", $names );

                                                unset( $names );
                                        }

                                        unset( $_files );

                                        break;
                                case "_children":

                                        $_children = get_post_meta( $item->ID, $meta_key );

                                        if ( $_children ) {

                                                $_children = maybe_unserialize( $_children );

                                                $names = array();

                                                if ( isset( $_children[ 0 ] ) && is_array( $_children[ 0 ] ) && !empty( $_children[ 0 ] ) ) {

                                                        foreach ( $_children[ 0 ] as $_child ) {

                                                                $names[] = get_the_title( $_child );
                                                        }

                                                        $meta_value = implode( "||", $names );

                                                        unset( $names );
                                                }
                                        }

                                        unset( $_children );

                                        break;
                        }
                }
                return $meta_value;
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
