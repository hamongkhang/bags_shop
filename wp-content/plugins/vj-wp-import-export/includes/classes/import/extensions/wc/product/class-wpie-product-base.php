<?php


namespace wpie\import\wc\product\base;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php');
}

abstract class WPIE_Product_Base extends \wpie\import\base\WPIE_Import_Base {

        /**
         * @var WC_Product
         */
        protected $product;
        protected $product_type;
        protected $product_properties = array();
        private $is_virtual;
        private $is_downloadable;
        private $is_featured;
        private $product_parent;

        public function __construct( $wpie_import_option = array(), $wpie_import_record = array(), $item_id = "", $item = null, $is_new_item = false, $product_parent = 0 ) {

                $this->wpie_import_option = $wpie_import_option;

                $this->wpie_import_record = $wpie_import_record;

                $this->item_id = $item_id;

                $this->item = $item;

                $this->is_new_item = $is_new_item;

                $this->product_parent = $product_parent;

                $this->is_virtual = strtolower( trim( wpie_sanitize_field( $this->get_field_value( 'wpie_item_meta_virtual', false, true ) ) ) ) === "yes";

                $this->is_downloadable = strtolower( trim( wpie_sanitize_field( $this->get_field_value( 'wpie_item_meta_downloadable', false, true ) ) ) ) === "yes";

                $this->is_featured = strtolower( trim( wpie_sanitize_field( $this->get_field_value( 'wpie_item_meta_featured', false, true ) ) ) ) === "yes";
        }

        public function set_product( $product ) {
                $this->product = $product;
        }

        protected function get_product() {
                return $this->product;
        }

        public function import_data() {
                $this->set_properties();
                $this->save();
        }

        protected function is_virtual() {
                return $this->is_virtual;
        }

        protected function get_product_id() {
                return $this->item_id;
        }

        protected function is_downloadable() {
                return $this->is_downloadable;
        }

        protected function is_featured() {
                return $this->is_featured;
        }

        private function set_properties() {

                $this->prepare_properties();

                $this->product->set_props( $this->product_properties );
        }

        protected function prepare_properties() {

                $this->prepare_general_properties();

                $this->prepare_inventory_properties();

                $this->prepare_shipping_properties();

                $this->prepare_linked_products();

                $this->prepare_attributes_properties();

                $this->prepare_advanced_properties();
        }

        protected function prepare_general_properties() {

                if ( $this->is_update_meta( "_regular_price" ) ) {

                        $this->product_properties[ 'price' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_meta_regular_price' ) );

                        $this->product_properties[ 'regular_price' ] = $this->product_properties[ 'price' ];
                }

                if ( $this->is_update_meta( "_sale_price" ) ) {
                        $this->product_properties[ 'sale_price' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_meta_sale_price' ) );
                }

                if ( $this->is_update_meta( "_sale_price_dates_from" ) ) {

                        $date_on_sale_from = wpie_sanitize_field( $this->get_field_value( 'wpie_item_meta_sale_price_dates_from' ) );

                        if ( !empty( $date_on_sale_from ) && strtotime( $date_on_sale_from ) !== false ) {
                                $date_on_sale_from = \wc_string_to_timestamp( \get_gmt_from_date( gmdate( 'Y-m-d H:i:s', ( strtotime( $date_on_sale_from ) ) ) ) );
                        } else {
                                $date_on_sale_from = null;
                        }

                        $this->product_properties[ 'date_on_sale_from' ] = $date_on_sale_from;

                        unset( $date_on_sale_from );
                }

                if ( $this->is_update_meta( "_sale_price_dates_to" ) ) {

                        $date_on_sale_to = wpie_sanitize_field( $this->get_field_value( 'wpie_item_meta_sale_price_dates_to' ) );

                        if ( !empty( $date_on_sale_to ) && strtotime( $date_on_sale_to ) !== false ) {
                                $date_on_sale_to = \wc_string_to_timestamp( \get_gmt_from_date( gmdate( 'Y-m-d H:i:s', ( strtotime( $date_on_sale_to ) ) ) ) );
                        } else {
                                $date_on_sale_to = null;
                        }

                        $this->product_properties[ 'date_on_sale_to' ] = $date_on_sale_to;

                        unset( $date_on_sale_to );
                }

                if ( $this->is_update_meta( "_virtual" ) ) {
                        $this->product_properties[ 'virtual' ] = $this->is_virtual();
                }

                if ( $this->is_update_meta( "_downloadable" ) ) {
                        $this->product_properties[ 'downloadable' ] = $this->is_downloadable();
                }

                if ( $this->is_update_taxonomy( "product_visibility" ) ) {
                        $this->product_properties[ 'featured' ] = $this->is_featured();

                        $this->product_properties[ 'catalog_visibility' ] = wpie_sanitize_field( strtolower( trim( $this->get_field_value( 'wpie_item_meta_visibility', false, true ) ) ) );
                }
                $this->prepare_downloadable_properties();

                $this->prepare_tax_properties();
        }

        private function prepare_downloadable_properties() {

                if ( $this->is_downloadable() ) {

                        $download_limit = absint( wpie_sanitize_field( $this->get_field_value( 'wpie_item_meta_download_limit' ) ) );

                        if ( !$download_limit ) {
                                $download_limit = "";
                        }
                        if ( $this->is_update_meta( "_download_limit" ) ) {
                                $this->product_properties[ 'download_limit' ] = $download_limit;
                        }
                        unset( $download_limit );

                        $download_expiry = absint( wpie_sanitize_field( $this->get_field_value( 'wpie_item_meta_download_expiry' ) ) );

                        if ( !$download_expiry ) {
                                $download_expiry = "";
                        }
                        if ( $this->is_update_meta( "_download_expiry" ) ) {
                                $this->product_properties[ 'download_expiry' ] = $download_expiry;
                        }
                        unset( $download_expiry );

                        $files_data = $this->get_field_value( 'wpie_item_meta_downloadable_files' );

                        if ( !empty( $files_data ) ) {

                                $file_paths = array();

                                $url_delim = $this->get_field_value( 'wpie_item_downloadable_files_delim' );

                                if ( $url_delim == "" ) {
                                        $url_delim = ",";
                                }

                                $file_urls = explode( $url_delim, $files_data );

                                unset( $url_delim );

                                $file_names_data = $this->get_field_value( 'wpie_item_meta_downloadable_file_name' );

                                $file_names = array();

                                if ( !empty( $file_names_data ) ) {

                                        $name_delim = $this->get_field_value( 'wpie_item_downloadable_file_name_delim' );

                                        if ( $name_delim == "" ) {
                                                $name_delim = ",";
                                        }

                                        $file_names = explode( $name_delim, $file_names_data );

                                        unset( $name_delim );
                                }
                                unset( $file_names_data );

                                if ( !empty( $file_urls ) ) {
                                        foreach ( $file_urls as $key => $path ) {
                                                $file_paths[ md5( $path ) ] = array(
                                                        'download_id' => md5( $path ),
                                                        'name' => ((!empty( $file_names[ $key ] )) ? $file_names[ $key ] : basename( $path )),
                                                        'file' => $path
                                                );
                                        }
                                        if ( $this->is_update_meta( "_downloadable_files" ) ) {
                                                $this->product_properties[ 'downloads' ] = $file_paths;
                                        }
                                }

                                unset( $file_urls, $file_names, $file_paths );
                        }
                        unset( $files_data );
                }
        }

        private function prepare_tax_properties() {

                if ( $this->is_update_meta( "_tax_status" ) ) {

                        $tax_status = wpie_sanitize_field( strtolower( trim( $this->get_field_value( 'wpie_item_meta_tax_status', false, true ) ) ) );

                        $this->product_properties[ 'tax_status' ] = $tax_status != '' ? $tax_status : null;
                }

                if ( $this->is_update_meta( "_tax_class" ) ) {

                        $tax_class = strtolower( trim( $this->get_field_value( 'wpie_item_meta_tax_class', false, true ) ) );

                        $tax_class = strtolower( $tax_class == 'standard' ? '' : $tax_class );

                        $this->product_properties[ 'tax_class' ] = $tax_class != '' ? $tax_class : null;
                }
                unset( $tax_status, $tax_class );
        }

        protected function prepare_inventory_properties() {

                $this->prepare_sku();

                if ( $this->is_update_meta( "_manage_stock" ) ) {
                        $this->product_properties[ 'manage_stock' ] = wpie_sanitize_field( strtolower( trim( $this->get_field_value( 'wpie_item_meta_manage_stock', false, true ) ) ) ) === "yes";
                }
                if ( $this->is_update_meta( "_backorders" ) ) {

                        $backorders = wpie_sanitize_field( strtolower( trim( $this->get_field_value( 'wpie_item_meta_backorders', false, true ) ) ) );

                        if ( !empty( $backorders ) ) {

                                $backorders = preg_replace( "/\s+/", "", $backorders );

                                if ( strpos( $backorders, 'notify' ) !== false ) {
                                        $backorders = "notify";
                                } elseif ( strpos( $backorders, 'yes' ) !== false ) {
                                        $backorders = "yes";
                                } elseif ( strpos( $backorders, 'no' ) !== false ) {
                                        $backorders = "no";
                                }
                        }
                        $this->product_properties[ 'backorders' ] = ($backorders != '') ? wpie_sanitize_field( $backorders ) : null;

                        unset( $backorders );
                }
                if ( $this->is_update_meta( "_stock_status" ) ) {

                        $stock_status = wpie_sanitize_field( strtolower( trim( $this->get_field_value( 'wpie_item_meta_stock_status', false, true ) ) ) );

                        if ( !empty( $stock_status ) ) {

                                $stock_status = preg_replace( "/\s+/", "", $stock_status );

                                if ( strpos( $stock_status, 'back' ) !== false || strpos( $stock_status, 'order' ) !== false ) {
                                        $stock_status = "onbackorder";
                                } elseif ( strpos( $stock_status, 'out' ) !== false ) {
                                        $stock_status = "outofstock";
                                } elseif ( strpos( $stock_status, 'instock' ) !== false ) {
                                        $stock_status = "instock";
                                }
                        }
                        $this->product_properties[ 'stock_status' ] = $stock_status;

                        unset( $stock_status );
                }
                if ( $this->is_update_meta( "_stock" ) ) {
                        $this->product_properties[ 'stock_quantity' ] = wc_stock_amount( trim( wpie_sanitize_field( $this->get_field_value( 'wpie_item_meta_stock' ) ) ) );
                }
                if ( $this->is_update_meta( "_low_stock_amount" ) ) {
                        $this->product_properties[ 'low_stock_amount' ] = absint( trim( wpie_sanitize_field( $this->get_field_value( 'wpie_item_meta_low_stock_amount' ) ) ) );
                }
                if ( $this->is_update_meta( "_sold_individually" ) ) {
                        $this->product_properties[ 'sold_individually' ] = wpie_sanitize_field( strtolower( trim( $this->get_field_value( 'wpie_item_meta_sold_individually', false, true ) ) ) ) === "yes";
                }
        }

        private function prepare_sku() {

                if ( $this->is_update_meta( "_sku" ) ) {
                        $this->product_properties[ 'sku' ] = $this->generate_sku();
                }
        }

        private function generate_sku() {

                $new_sku = wpie_sanitize_field( trim( $this->get_field_value( 'wpie_item_meta_sku' ) ) );

                if ( trim( $new_sku ) == "" ) {

                        $new_sku = $this->sku_rand( $this->get_product_id() );
                }
                return wpie_sanitize_field( $new_sku );
        }

        protected function sku_rand( $id = 0 ) {

                $disable_auto_sku = absint( trim( wpie_sanitize_field( trim( $this->get_field_value( 'wpie_item_auto_generate_sku', true ) ) ) ) );

                if ( $disable_auto_sku !== 1 ) {
                        return substr( md5( $id . time() ), 0, 12 );
                }
                return "";
        }

        protected function prepare_shipping_properties() {

                if ( $this->is_update_taxonomy( "product_shipping_class" ) ) {
                        $shipping_class = $this->get_shipping_class();

                        if ( empty( $shipping_class ) ) {
                                $shipping_class = -1;
                        }
                        $this->product_properties[ 'shipping_class_id' ] = absint( $shipping_class );

                        unset( $shipping_class );
                }
                $this->prepare_dimensions();
        }

        private function get_shipping_class() {

                $shipping_class_type = wpie_sanitize_field( $this->get_field_value( 'wpie_item_product_shipping_class_logic' ) );

                if ( $shipping_class_type == "defined" ) {
                        $shipping_class = wpie_sanitize_field( strtolower( trim( $this->get_field_value( 'wpie_item_product_shipping_class' ) ) ) );
                } else {
                        $shipping_class = wpie_sanitize_field( strtolower( trim( $this->get_field_value( 'wpie_item_product_shipping_class_as_specified_data' ) ) ) );
                }
                unset( $shipping_class_type );

                if ( $shipping_class != '' ) {

                        $term = array();

                        if ( !is_numeric( $shipping_class ) ) {

                                $term = $this->is_term_exists( $shipping_class, 'product_shipping_class' );
                        } elseif ( absint( $shipping_class ) > 0 ) {

                                $term = $this->is_term_exists( absint( $shipping_class ), 'product_shipping_class' );
                        }

                        if ( !empty( $term ) && !is_wp_error( $term ) ) {
                                if ( isset( $term[ 'term_id' ] ) ) {
                                        $shipping_class = absint( $term[ 'term_id' ] );
                                }
                        } else {
                                $term = wp_insert_term( $shipping_class, 'product_shipping_class' );
                                if ( !empty( $term ) && !is_wp_error( $term ) ) {
                                        $shipping_class = absint( $term[ 'term_id' ] );
                                }
                        }
                        unset( $term );
                }

                if ( empty( $shipping_class ) || $shipping_class == 0 ) {
                        $shipping_class = '';
                }

                return $shipping_class;
        }

        private function prepare_dimensions() {

                if ( $this->is_virtual() ) {

                        if ( $this->is_update_meta( "_weight" ) ) {
                                $this->product_properties[ 'weight' ] = "";
                        }
                        if ( $this->is_update_meta( "_length" ) ) {
                                $this->product_properties[ 'length' ] = "";
                        }
                        if ( $this->is_update_meta( "_width" ) ) {
                                $this->product_properties[ 'width' ] = "";
                        }
                        if ( $this->is_update_meta( "_height" ) ) {
                                $this->product_properties[ 'height' ] = "";
                        }
                } else {

                        if ( $this->is_update_meta( "_weight" ) ) {
                                $this->product_properties[ 'weight' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_meta_weight' ) );
                        }
                        if ( $this->is_update_meta( "_length" ) ) {
                                $this->product_properties[ 'length' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_meta_length' ) );
                        }
                        if ( $this->is_update_meta( "_width" ) ) {
                                $this->product_properties[ 'width' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_meta_width' ) );
                        }
                        if ( $this->is_update_meta( "_height" ) ) {
                                $this->product_properties[ 'height' ] = wpie_sanitize_field( $this->get_field_value( 'wpie_item_meta_height' ) );
                        }
                }
        }

        private function prepare_linked_products() {

                if ( $this->is_update_meta( "_upsell_ids" ) ) {

                        $this->product_properties[ 'upsell_ids' ] = $this->get_linked_products_by_data( $this->get_product_id(), wpie_sanitize_field( $this->get_field_value( 'wpie_item_meta_upsell_ids' ) ), '_upsell_ids' );
                }

                if ( $this->is_update_meta( "_crosssell_ids" ) ) {
                        $this->product_properties[ 'cross_sell_ids' ] = $this->get_linked_products_by_data( $this->get_product_id(), wpie_sanitize_field( $this->get_field_value( 'wpie_item_meta_crosssell_ids' ) ), '_crosssell_ids' );
                }
        }

        private function get_linked_products_by_data( $product_id = 0, $data = "", $type = "" ) {

                $products = array();

                if ( !empty( $data ) ) {
                        $product_data = explode( ',', $data );

                        if ( !empty( $product_data ) ) {

                                foreach ( $product_data as $key ) {

                                        $linked_product = false;

                                        $query = new \WP_Query( array(
                                                'post_type' => array( 'product',
                                                        'product_variation' ),
                                                'fields' => "ids",
                                                'posts_per_page' => 1,
                                                'meta_query' => array(
                                                        array(
                                                                'key' => '_sku',
                                                                'value' => $key,
                                                        )
                                                )
                                                ) );

                                        if ( $query->have_posts() ) {
                                                $linked_product = $query->posts;
                                        }

                                        wp_reset_postdata();

                                        if ( !$linked_product && is_numeric( $key ) ) {

                                                $query = new \WP_Query( array(
                                                        'post_type' => array( 'product',
                                                                'product_variation' ),
                                                        'post__in' => array( $key )
                                                        ) );

                                                if ( $query->have_posts() ) {
                                                        $linked_product = $query->posts;
                                                }
                                                wp_reset_postdata();
                                        }

                                        if ( !$linked_product ) {
                                                $query = new \WP_Query( array(
                                                        'name' => $key,
                                                        'post_type' => 'product',
                                                        'post_status' => 'publish',
                                                        'posts_per_page' => 1
                                                        ) );

                                                if ( $query->have_posts() ) {
                                                        $linked_product = $query->posts;
                                                }
                                                wp_reset_postdata();
                                        }

                                        if ( $linked_product ) {

                                                $new_product_id = isset( $linked_product[ 0 ] ) ? $linked_product[ 0 ] : "";
                                                if ( $product_id == $new_product_id ) {
                                                        continue;
                                                }
                                                $products[] = $new_product_id;
                                        }
                                        unset( $query, $linked_product );
                                }
                        }
                        unset( $product_data );
                }
                unset( $data );
                return $products;
        }

        private function prepare_attributes_properties() {

                if ( !$this->is_update_field( "attributes" ) ) {
                        return true;
                }

                $attributes = $this->get_attributes_properties();

                $this->product_properties[ 'attributes' ] = $attributes;

                unset( $attributes );
        }

        protected function get_attributes_properties() {

                $attributes_data = $this->get_attr_data();

                if ( !empty( $attributes_data[ 'attribute_names' ] ) ) {

                        $attributes = \WC_Meta_Box_Product_Data::prepare_attributes( $attributes_data );
                } else {
                        $attributes = array();
                }
                
                $attributes = $this->addExistingAttributes( $attributes );

                return $attributes;
        }
        
         private function addExistingAttributes( $attributes ) {

                $update_method = wpie_sanitize_field( $this->get_field_value( 'wpie_item_update', true ) );

                $is_update_attr = wpie_sanitize_field( $this->get_field_value( 'wpie_item_update_attributes', true ) );

                if ( $this->is_new_item || empty( $attributes ) || strtolower( trim( $is_update_attr ) ) === "all" || $update_method == 'all' ) {
                        return $attributes;
                }

                $productAttr = $this->get_product()->get_attributes();

                if ( empty( $productAttr ) ) {
                        return $attributes;
                }

                $attr = $productAttr;

                foreach ( $attributes as $attrbiute ) {

                        $name = $attrbiute->get_name();

                        $isExisting = false;

                        foreach ( $productAttr as $pa_key => $pa_attrbiute ) {
                                $pa_name = $pa_attrbiute->get_name();

                                if ( $pa_name === $name ) {
                                        $attr[ $pa_key ] = $attrbiute;
                                        $isExisting      = true;
                                        break;
                                }
                        }

                        if ( !$isExisting ) {
                                $attr[] = $attrbiute;
                        }
                }

                return $attr;
        }

        protected function get_attr_data() {

                $data = [
                        'attribute_names' => [],
                        'attribute_values' => [],
                        'attribute_visibility' => [],
                        'attribute_variation' => [],
                        'attribute_position' => []
                ];

                $max_length = apply_filters( 'wpie_max_woo_attribute_term_length', 200 );

                $attributes = [];

                $includes = [];

                $excludes = [];

                $is_update_attr = wpie_sanitize_field( $this->get_field_value( 'wpie_item_update_attributes', true ) );

                if ( $is_update_attr == "includes" ) {
                        if ( !empty( wpie_sanitize_field( $this->get_field_value( 'wpie_item_update_attributes_includes_data', true ) ) ) ) {
                                $includes = explode( ",", wpie_sanitize_field( $this->get_field_value( 'wpie_item_update_attributes_includes_data', true ) ) );
                        }
                } elseif ( $is_update_attr == "excludes" ) {
                        if ( !empty( wpie_sanitize_field( $this->get_field_value( 'wpie_item_update_attributes_excludes_data', true ) ) ) ) {
                                $excludes = explode( ",", wpie_sanitize_field( $this->get_field_value( 'wpie_item_update_attributes_excludes_data', true ) ) );
                        }
                }

                $attr_names = wpie_sanitize_field( $this->get_field_value( 'wpie_product_attr_name' ) );

                $attr_slugs = wpie_sanitize_field( $this->get_field_value( 'wpie_attr_slug' ) );

                if ( empty( $attr_names ) && !empty( $attr_slugs ) ) {
                        $attr_names = $attr_slugs;
                }
                if ( !empty( $attr_names ) ) {

                        $attr_values = wpie_sanitize_field( $this->get_field_value( 'wpie_product_attr_value' ) );

                        $attr_slugs_as_specified = wpie_sanitize_field( $this->get_field_value( 'wpie_attr_slug_as_specified_data' ) );

                        $attr_in_variations = wpie_sanitize_field( $this->get_field_value( 'wpie_attr_in_variations' ) );

                        $attr_in_variations_as_specified = wpie_sanitize_field( $this->get_field_value( 'wpie_attr_in_variations_as_specified_data' ) );

                        $attr_is_visible = wpie_sanitize_field( $this->get_field_value( 'wpie_attr_is_visible' ) );

                        $attr_is_visible_as_specified = wpie_sanitize_field( $this->get_field_value( 'wpie_attr_is_visible_as_specified_data' ) );

                        $attr_is_taxonomy = wpie_sanitize_field( $this->get_field_value( 'wpie_attr_is_taxonomy' ) );

                        $attr_is_taxonomy_as_specified = wpie_sanitize_field( $this->get_field_value( 'wpie_attr_is_taxonomy_as_specified_data' ) );

                        $attr_is_auto_create_term = wpie_sanitize_field( $this->get_field_value( 'wpie_attr_is_auto_create_term' ) );

                        $attr_is_auto_create_term_as_specified = wpie_sanitize_field( $this->get_field_value( 'wpie_attr_is_auto_create_term_as_specified_data' ) );

                        $attr_position = wpie_sanitize_field( $this->get_field_value( 'wpie_attr_position' ) );

                        $attr_position_as_specified = wpie_sanitize_field( $this->get_field_value( 'wpie_attr_position_as_specified_data' ) );

                        $attribute_position_count = 0;

                        foreach ( $attr_names as $key => $name ) {

                                $values = isset( $attr_values[ $key ] ) ? $attr_values[ $key ] : "";

                                $_slug = isset( $attr_slugs[ $key ] ) ? $attr_slugs[ $key ] : "";

                                if ( strtolower( trim( $_slug ) ) === "as_specified" ) {

                                        $_slug = isset( $attr_slugs_as_specified[ $key ] ) ? $attr_slugs_as_specified[ $key ] : 0;
                                }

                                if ( empty( $name ) && !empty( $name ) && strtolower( trim( $_slug ) ) !== "auto" ) {
                                        $name = $_slug;
                                }
                                if ( empty( $name ) ) {
                                        continue;
                                }

                                $attribute_position_count++;

                                if ( !empty( $includes ) && !in_array( $name, $includes ) ) {
                                        continue;
                                }
                                if ( !empty( $excludes ) && in_array( $name, $excludes ) ) {
                                        continue;
                                }

                                if ( empty( $name ) || strtolower( trim( $_slug ) ) === "auto" ) {
                                        $_slug = $name;
                                }

                                $in_variations = isset( $attr_in_variations[ $key ] ) && !empty( $attr_in_variations[ $key ] ) ? strtolower( trim( $attr_in_variations[ $key ] ) ) : "";

                                if ( $in_variations == "as_specified" ) {

                                        $in_variations = isset( $attr_in_variations_as_specified[ $key ] ) ? $attr_in_variations_as_specified[ $key ] : 0;
                                }

                                $in_variations = (strtolower( trim( $in_variations ) ) == "yes" || intval( $in_variations ) === 1) ? 1 : 0;

                                $is_visible = isset( $attr_is_visible[ $key ] ) && !empty( $attr_is_visible[ $key ] ) ? strtolower( trim( $attr_is_visible[ $key ] ) ) : "";

                                if ( $is_visible == "as_specified" ) {

                                        $is_visible = isset( $attr_is_visible_as_specified[ $key ] ) ? $attr_is_visible_as_specified[ $key ] : 0;
                                }
                                $is_visible = (strtolower( trim( $is_visible ) ) == "yes" || intval( $is_visible ) === 1) ? 1 : 0;

                                $is_taxonomy = isset( $attr_is_taxonomy[ $key ] ) && !empty( $attr_is_taxonomy[ $key ] ) ? strtolower( trim( $attr_is_taxonomy[ $key ] ) ) : "";

                                if ( $is_taxonomy == "as_specified" ) {

                                        $is_taxonomy = isset( $attr_is_taxonomy_as_specified[ $key ] ) ? $attr_is_taxonomy_as_specified[ $key ] : 0;
                                }

                                $attr_slug = wc_attribute_taxonomy_name( $_slug );

                                if ( strtolower( trim( $is_taxonomy ) ) === "yes" || intval( $is_taxonomy ) === 1 ) {

                                        $attr_val_delim = ( empty( $values ) || strpos( $values, '~|~' )) === false ? '|' : '~|~';

                                        $_terms = empty( $values ) ? [] : array_map( 'strip_tags', explode( $attr_val_delim, $values ) );

                                        $is_auto_create_term = isset( $attr_is_auto_create_term[ $key ] ) ? $attr_is_auto_create_term[ $key ] : "";

                                        if ( strtolower( trim( $is_auto_create_term ) ) === "as_specified" ) {

                                                $is_auto_create_term = isset( $attr_is_auto_create_term_as_specified[ $key ] ) ? $attr_is_auto_create_term_as_specified[ $key ] : 0;
                                        }

                                        if ( strtolower( trim( $is_auto_create_term ) ) === "yes" || intval( $is_auto_create_term ) === 1 ) {
                                                $attr_slug = $this->create_product_attribute( $name, $_slug );
                                        }

                                        $_values = array();

                                        if ( !empty( $_terms ) && taxonomy_exists( $attr_slug ) ) {

                                                foreach ( $_terms as $_term ) {

                                                        $_term = substr( $_term, 0, $max_length );

                                                        $term = get_term_by( 'slug', $_term, $attr_slug, ARRAY_A );

                                                        if ( empty( $term ) && !is_wp_error( $term ) ) {

                                                                $term = $this->is_term_exists( $_term, $attr_slug );

                                                                if ( empty( $term ) || !is_wp_error( $term ) ) {

                                                                        $term = $this->is_term_exists( htmlspecialchars( $_term ), $attr_slug );

                                                                        if ( (empty( $term ) || !is_wp_error( $term ) ) ) {

                                                                                $term = wp_insert_term(
                                                                                        $_term, $attr_slug
                                                                                );

                                                                                if ( !is_wp_error( $term ) ) {

                                                                                        $termId = isset( $term[ 'term_id' ] ) ? absint( $term[ 'term_id' ] ) : false;

                                                                                        do_action( 'wpie_add_new_post_term', $termId );
                                                                                }
                                                                        }
                                                                }
                                                        } else {
                                                                $termId = isset( $term[ 'term_id' ] ) ? absint( $term[ 'term_id' ] ) : false;

                                                                $termId = apply_filters( 'wpie_get_term_by', $termId, $attr_slug );
                                                        }


                                                        if ( !is_wp_error( $term ) ) {
                                                                $_values[] = isset( $term[ 'term_id' ] ) ? intval( $term[ 'term_id' ] ) : 0;
                                                        }
                                                        unset( $term );
                                                }

                                                $values = array_unique( array_map( 'intval', $_values ) );

                                                unset( $_values );
                                        }
                                } else {
                                        $attr_slug = preg_replace( '/^pa\_/', '', $attr_slug );
                                }

                                $attribute_position = isset( $attr_position[ $key ] ) && !empty( $attr_position[ $key ] ) ? strtolower( trim( $attr_position[ $key ] ) ) : "";

                                if ( $attribute_position === "as_specified" ) {

                                        $attribute_position = isset( $attr_position_as_specified[ $key ] ) ? strtolower( trim( $attr_position_as_specified[ $key ] ) ) : "";
                                }

                                $attribute_position = ($attribute_position === "auto" || $attribute_position === "") ? $attribute_position_count : intval( $attribute_position );

                                $attributes[ $attr_slug ] = array(
                                        'name' => $attr_slug,
                                        'value' => $values,
                                        'is_visible' => $is_visible,
                                        'in_variation' => $in_variations,
                                        'position' => $attribute_position
                                );

                                unset( $values, $in_variations, $is_visible, $is_taxonomy, $attr_slug, $attribute_position );
                        }

                        unset( $attr_values, $attr_in_variations, $attr_is_visible, $attr_is_taxonomy, $attr_is_auto_create_term, $attribute_position_count );
                }

                unset( $max_length, $attr_names );

                if ( $is_update_attr != "all" && $this->get_product() && !$this->get_product() instanceof \WC_Product_Variation ) {

                        $product_attr = array();

                        $attr = $this->get_product()->get_attributes();

                        if ( !empty( $attr ) ) {
                                foreach ( $attr as $name => $attribute ) {
                                        if ( !empty( $includes ) && !in_array( $name, $includes ) ) {
                                                continue;
                                        }
                                        if ( !empty( $excludes ) && in_array( $name, $excludes ) ) {
                                                continue;
                                        }

                                        $product_attr[ $name ] = array(
                                                'name' => $name,
                                                'value' => $attribute->get_options(),
                                                'is_visible' => $attribute->get_visible(),
                                                'in_variation' => $attribute->get_variation(),
                                                'position' => $attribute->get_position()
                                        );
                                }
                        }

                        $attributes = array_merge( $product_attr, $attributes );

                        unset( $attr, $product_attr );
                }
                unset( $includes, $excludes );

                if ( !empty( $attributes ) ) {
                        foreach ( $attributes as $parse_attr ) {
                                $data[ 'attribute_names' ][] = $parse_attr[ 'name' ];
                                $data[ 'attribute_values' ][] = $parse_attr[ 'value' ];
                                $data[ 'attribute_visibility' ][] = empty( $parse_attr[ 'is_visible' ] ) ? null : true;
                                $data[ 'attribute_variation' ][] = empty( $parse_attr[ 'in_variation' ] ) ? null : true;
                                $data[ 'attribute_position' ][] = $parse_attr[ 'position' ];
                        }
                }

                unset( $attributes );

                return $data;
        }

        private function create_product_attribute( $attribute_name = "", $slug = "", $prefix = 1 ) {

                if ( strlen( $attribute_name ) >= 28 ) {
                        $attribute_name = substr( $attribute_name, 0, 28 );
                }

                $slug = $prefix > 1 ? $slug . "-" . $prefix : $slug;

                $slug = preg_replace( '/^pa\_/', '', wc_sanitize_taxonomy_name( $slug ) );

                if ( taxonomy_exists( $slug ) ) {
                        return $slug;
                } elseif ( wc_check_if_attribute_name_is_reserved( $slug ) ) {

                        $prefix++;
                        return $this->create_product_attribute( $attribute_name, $slug, $prefix );
                }

                $is_exist = false;
                if ( function_exists( "wc_get_attribute_taxonomies" ) ) {
                        if ( in_array( $slug, wc_get_attribute_taxonomies(), true ) ) {
                                $is_exist = true;
                        }
                }

                if ( $is_exist === false ) {
                        wc_create_attribute(
                                array(
                                        'name' => $attribute_name,
                                        'slug' => $slug,
                                        'type' => 'select',
                                        'order_by' => 'menu_order',
                                        'has_archives' => false,
                                )
                        );
                }

                $slug = wc_attribute_taxonomy_name( $slug );

                // Register the taxonomy now so that the import works!
                $new_taxonomy = register_taxonomy(
                        $slug,
                        apply_filters( 'woocommerce_taxonomy_objects_' . $slug, array(
                        'product' ) ),
                        apply_filters(
                                'woocommerce_taxonomy_args_' . $slug,
                                array(
                                        'hierarchical' => true,
                                        'show_ui' => false,
                                        'query_var' => true,
                                        'rewrite' => false,
                                )
                        )
                );

                return isset( $new_taxonomy->name ) ? $new_taxonomy->name : $slug;
        }

        private function prepare_advanced_properties() {

                if ( $this->is_update_meta( "_purchase_note" ) ) {
                        $this->product_properties[ 'purchase_note' ] = $this->get_field_value( 'wpie_item_meta_purchase_note' );
                }

                $reviews_allowed = $this->get_field_value( 'wpie_item_comment_status', false, true );

                $reviews_allowed = empty( $reviews_allowed ) ? false : in_array( strtolower( trim( $reviews_allowed ) ), [ "yes", "open" ] );

                $this->product_properties[ 'reviews_allowed' ] = $reviews_allowed;

                $this->product_properties[ 'menu_order' ] = absint( $this->get_field_value( 'wpie_item_order' ) );

                $total_sales = get_post_meta( $this->item_id, 'total_sales', true );

                if ( empty( $total_sales ) ) {
                        update_post_meta( $this->item_id, 'total_sales', '0' );
                }

                unset( $total_sales );

                if ( $this->is_new_item ) {

                        update_post_meta( $this->item_id, '_wc_review_count', '0' );

                        update_post_meta( $this->item_id, '_wc_rating_count', '0' );

                        update_post_meta( $this->item_id, '_wc_average_rating', '0' );
                }
        }

        protected function save() {

                do_action( 'woocommerce_admin_process_product_object', $this->product );

                $this->product->save();

                do_action( 'woocommerce_process_product_meta_' . $this->product->get_type(), $this->product->get_id() );

                wc_delete_product_transients( $this->product->get_id() );
        }

        protected function is_update_meta( $field = "" ) {

                if ( empty( $field ) ) {
                        return false;
                }
                if ( $this->is_new_item ) {
                        return true;
                }

                $update_method = wpie_sanitize_field( $this->get_field_value( 'wpie_item_update', true ) );

                if ( $update_method == 'all' ) {
                        return true;
                } elseif ( $update_method == 'price_stock' ) {

                        if ( !in_array( $field, [ '_regular_price', '_sale_price', '_stock', '_manage_stock', '_stock_status', '_low_stock_amount' ] ) ) {
                                return false;
                        }

                        if ( absint( $this->get_field_value( "is_update_item_product_price_data", true ) ) === 1 ) {

                                if ( in_array( $field, [ '_regular_price', '_sale_price' ] ) && absint( $this->get_field_value( "is_update_item_product_price" . $field, true ) ) === 1 ) {
                                        return true;
                                }
                        }
                        if ( absint( $this->get_field_value( "is_update_item_product_stock_data", true ) ) === 1 ) {

                                if ( in_array( $field, [ '_stock', '_manage_stock', '_stock_status', '_low_stock_amount' ] ) && absint( $this->get_field_value( "is_update_item_product_stock" . $field, true ) ) === 1 ) {
                                        return true;
                                }
                        }

                        return false;
                }


                if ( absint( $this->get_field_value( "is_update_item_cf", true ) ) !== 1 ) {
                        return false;
                }

                $exclude_metas = array();

                $includes_metas = array();

                $item_cf_option = wpie_sanitize_field( $this->get_field_value( 'wpie_item_update_cf', true ) );

                if ( $item_cf_option == "all" ) {
                        return true;
                } elseif ( $item_cf_option == "excludes" ) {

                        $exclude_metas_input = wpie_sanitize_field( $this->get_field_value( 'wpie_item_update_cf_excludes_data' ) );

                        if ( !empty( $exclude_metas_input ) ) {

                                $exclude_metas = explode( ",", $exclude_metas_input );

                                if ( !empty( $exclude_metas ) && in_array( $field, $exclude_metas ) ) {
                                        return false;
                                }
                        }
                        unset( $exclude_metas_input );
                } elseif ( $item_cf_option == "includes" ) {

                        $includes_metas_input = wpie_sanitize_field( $this->get_field_value( 'wpie_item_update_cf_includes_data' ) );

                        if ( !empty( $includes_metas_input ) ) {
                                $includes_metas = explode( ",", $includes_metas_input );

                                if ( !empty( $includes_metas ) && !in_array( $field, $includes_metas ) ) {
                                        return false;
                                }
                        }
                        unset( $includes_metas_input );
                }
                unset( $exclude_metas, $includes_metas, $item_cf_option );

                return true;
        }

        protected function is_update_taxonomy( $field = "" ) {

                if ( empty( $field ) ) {
                        return false;
                }
                if ( $this->is_new_item ) {
                        return true;
                }

                $update_method = wpie_sanitize_field( $this->get_field_value( 'wpie_item_update', true ) );

                if ( $update_method == 'all' ) {
                        return true;
                } elseif ( $update_method == 'price_stock' ) {
                        return false;
                }

                if ( absint( $this->get_field_value( "is_update_item_taxonomies", true ) ) !== 1 ) {
                        return false;
                }

                $tax_includes = array();

                $tax_excludes = array();

                $handle_tax = $this->get_field_value( 'wpie_item_update_taxonomies', true );

                if ( $handle_tax == 'includes' ) {

                        $includes = wpie_sanitize_field( $this->get_field_value( 'wpie_item_update_taxonomies_includes_data' ) );

                        if ( !empty( $includes ) ) {
                                $tax_includes = explode( ",", $includes );
                                if ( !empty( $tax_includes ) && !in_array( $field, $tax_includes ) ) {
                                        return false;
                                }
                        }
                        unset( $includes );
                } elseif ( $handle_tax == 'excludes' ) {

                        $excludes = wpie_sanitize_field( $this->get_field_value( 'wpie_item_update_taxonomies_excludes_data' ) );

                        if ( !empty( $excludes ) ) {
                                $tax_excludes = explode( ",", $excludes );

                                if ( !empty( $tax_excludes ) && in_array( $field, $tax_excludes ) ) {
                                        return false;
                                }
                        }
                        unset( $excludes );
                }

                unset( $tax_includes, $tax_excludes, $handle_tax );

                return true;
        }

        public function __destruct() {

                parent::__destruct();

                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
