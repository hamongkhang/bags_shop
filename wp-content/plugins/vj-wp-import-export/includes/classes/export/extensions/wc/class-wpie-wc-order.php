<?php


namespace wpie\export\wc\order;

use wpie\export\wc\product;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if ( file_exists( WPIE_EXPORT_CLASSES_DIR . '/class-wpie-post.php' ) ) {

        require_once(WPIE_EXPORT_CLASSES_DIR . '/class-wpie-post.php');
}

class WPIE_WC_Order extends \wpie\export\post\WPIE_Post {

        private $order_items;
        private $order_item_meta;
        private $tax_rate;
        private $order_refunds;
        private $order_notes;
        private $max_items;
        private $is_single_row;
        private $fill_empty_colunm;
        private $order_item_data;

        public function __construct() {
                
        }

        private function get_order_item_standard_fields() {
                return array(
                        'title' => __( "Item", 'vj-wp-import-export' ),
                        'data' => array(
                                array(
                                        'name' => 'Product ID',
                                        'type' => 'wc-order',
                                        'field_type' => 'item',
                                        'field_key' => '_product_id'
                                ),
                                array(
                                        'name' => 'SKU',
                                        'type' => 'wc-order',
                                        'field_type' => 'item',
                                        'field_key' => '_product_sku'
                                ),
                                array(
                                        'name' => 'Product Name',
                                        'type' => 'wc-order',
                                        'field_type' => 'item',
                                        'field_key' => '_product_title'
                                ),
                                array(
                                        'name' => 'Product Variation Details',
                                        'type' => 'wc-order',
                                        'field_type' => 'item',
                                        'field_key' => '_product_variation',
                                        'isFiltered' => false
                                ),
                                array(
                                        'name' => 'Original Product Title',
                                        'type' => 'wc-order',
                                        'field_type' => 'item',
                                        'field_key' => 'original_product_title',
                                        'isFiltered' => false
                                ),
                                array(
                                        'name' => 'Is Variation',
                                        'type' => 'wc-order',
                                        'field_type' => 'item',
                                        'field_key' => 'is_variation',
                                        'isFiltered' => false
                                ),
                                array(
                                        'name' => 'Variation Attributes',
                                        'type' => 'wc-order',
                                        'field_type' => 'item',
                                        'isFiltered' => false,
                                        'field_key' => 'variation_attributes'
                                ),
                                array(
                                        'name' => 'Quantity',
                                        'type' => 'wc-order',
                                        'field_type' => 'item',
                                        'field_key' => '_qty'
                                ),
                                array(
                                        'name' => 'Item Cost',
                                        'type' => 'wc-order',
                                        'field_type' => 'item',
                                        'field_key' => '_line_subtotal'
                                ),
                                array(
                                        'name' => 'Item Total',
                                        'type' => 'wc-order',
                                        'field_type' => 'item',
                                        'field_key' => '_line_total'
                                ),
                                array(
                                        'name' => 'Item Tax',
                                        'type' => 'wc-order',
                                        'field_type' => 'item',
                                        'field_key' => '_line_subtotal_tax'
                                ),
                                array(
                                        'name' => 'Item Tax Total',
                                        'type' => 'wc-order',
                                        'field_type' => 'item',
                                        'field_key' => '_line_tax'
                                ),
                                array(
                                        'name' => 'Item Tax Data',
                                        'type' => 'wc-order',
                                        'field_type' => 'item',
                                        'field_key' => '_line_tax_data'
                                ),
                                array(
                                        'name' => 'Item Meta',
                                        'type' => 'wc-order',
                                        'isFiltered' => false,
                                        'field_type' => 'item',
                                        'field_key' => 'meta'
                                )
                        )
                );
        }

        public function pre_process_fields( &$export_fields = array(), $export_type = array() ) {

                if ( isset( $export_fields[ 'taxonomy' ] ) ) {
                        unset( $export_fields[ 'taxonomy' ] );
                }
                if ( isset( $export_fields[ 'image' ] ) ) {
                        unset( $export_fields[ 'image' ] );
                }
                if ( isset( $export_fields[ 'attachment' ] ) ) {
                        unset( $export_fields[ 'attachment' ] );
                }
                if ( isset( $export_fields[ 'author' ] ) ) {
                        unset( $export_fields[ 'author' ] );
                }

                $export_fields[ 'standard' ] = array(
                        'title' => __( "Standard", 'vj-wp-import-export' ),
                        "isDefault" => true,
                        'data' => array(
                                array(
                                        'name' => 'Order ID',
                                        'type' => 'id',
                                        'isDefault' => true
                                ),
                                array(
                                        'name' => 'Order Key',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_order_key',
                                        'isDefault' => true
                                ),
                                array(
                                        'name' => 'Order Date',
                                        'type' => 'date',
                                        'isDate' => true,
                                        'isDefault' => true
                                ),
                                array(
                                        'name' => 'Completed Date',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_completed_date',
                                        'isDate' => true,
                                        'isDefault' => true
                                ),
                                array(
                                        'name' => 'Title',
                                        'type' => 'title',
                                        'isDefault' => true
                                ),
                                array(
                                        'name' => 'Order Status',
                                        'type' => 'status',
                                        'isDefault' => true
                                ),
                                array(
                                        'name' => 'Order Currency',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_order_currency',
                                        'isDefault' => true
                                ),
                                array(
                                        'name' => 'Payment Method Title',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_payment_method_title',
                                        'isDefault' => true
                                ),
                                array(
                                        'name' => 'Order Total',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_order_total',
                                        'isDefault' => true
                                )
                        )
                );
                $export_fields[ 'customer' ] = array(
                        'title' => __( "Customer", 'vj-wp-import-export' ),
                        'data' => array(
                                array(
                                        'name' => 'Customer User ID',
                                        'type' => "wpie_cf",
                                        'metaKey' => "_customer_user"
                                ),
                                array(
                                        'name' => 'Customer Note',
                                        'type' => 'post_excerpt',
                                )
                        )
                );

                $billing_fields = $this->available_billing_field_data();

                if ( !empty( $billing_fields ) ) {
                        foreach ( $billing_fields as $key ) {

                                if ( $key == "_customer_user_email" ) {
                                        $export_fields[ 'customer' ][ 'data' ] [] = array(
                                                'name' => $key,
                                                'type' => "wc-order",
                                                'field_type' => 'customer',
                                                'field_key' => $key
                                        );
                                } else {
                                        $export_fields[ 'customer' ][ 'data' ] [] = array(
                                                'name' => $key,
                                                'type' => "wpie_cf",
                                                'metaKey' => $key
                                        );
                                }
                        }
                }

                $shipping_fields = $this->available_shipping_field_data();

                if ( !empty( $shipping_fields ) ) {
                        foreach ( $shipping_fields as $key ) {
                                $export_fields[ 'customer' ][ 'data' ] [] = array(
                                        'name' => $key,
                                        'type' => "wpie_cf",
                                        'metaKey' => $key
                                );
                        }
                }
                unset( $billing_fields, $shipping_fields );

                $export_fields[ 'other' ] = array(
                        'title' => __( "Other", 'vj-wp-import-export' ),
                        'data' => array(
                                array(
                                        'name' => 'Prices Include Tax',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_prices_include_tax'
                                ),
                                array(
                                        'name' => 'Customer Ip Address',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_customer_ip_address'
                                ),
                                array(
                                        'name' => 'Customer User Agent',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_customer_user_agent'
                                ),
                                array(
                                        'name' => 'Created Via',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_created_via'
                                ),
                                array(
                                        'name' => 'Order Version',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_order_version'
                                ),
                                array(
                                        'name' => 'Payment Method',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_payment_method'
                                ),
                                array(
                                        'name' => 'Cart Discount Tax',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_cart_discount_tax'
                                ),
                                array(
                                        'name' => 'Order Shipping Tax',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_order_shipping_tax'
                                ),
                                array(
                                        'name' => 'Recorded Sales',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_recorded_sales'
                                ),
                                array(
                                        'name' => 'Order Stock Reduced',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_order_stock_reduced'
                                ),
                                array(
                                        'name' => 'Recorded Coupon Usage Counts',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_recorded_coupon_usage_counts'
                                ),
                                array(
                                        'name' => 'Transaction Id',
                                        'type' => 'wpie_cf',
                                        'metaKey' => '_transaction_id'
                                )
                        )
                );

                $export_fields[ 'meta' ] = array_diff(
                        $export_fields[ 'meta' ],
                        array(
                                "_order_key", "_completed_date", "_order_currency", "_payment_method_title", "_order_total",
                                "_customer_user", "_billing_first_name", "_billing_last_name", "_billing_company", "_billing_address_1",
                                "_billing_address_2", "_billing_city", "_billing_postcode", "_billing_country", "_billing_state",
                                "_billing_email", "_customer_user_email", "_billing_phone", "_shipping_first_name", "_shipping_last_name",
                                "_shipping_company", "_shipping_address_1", "_shipping_address_2", "_shipping_city", "_shipping_postcode",
                                "_shipping_country", "_shipping_state", "_prices_include_tax", "_customer_ip_address", "_customer_user_agent",
                                "_created_via", "_order_version", "_payment_method", "_cart_discount_tax", "_order_shipping_tax",
                                "_recorded_sales", "_order_stock_reduced", "_recorded_coupon_usage_counts", "_transaction_id"
                        )
                );

                $export_fields[ 'item_standard' ] = $this->get_order_item_standard_fields();

                if ( file_exists( WPIE_EXPORT_CLASSES_DIR . '/extensions/wc/class-wpie-wc-product.php' ) ) {

                        require_once(WPIE_EXPORT_CLASSES_DIR . '/extensions/wc/class-wpie-wc-product.php');
                }

                $product = new \wpie\export\wc\product\WPIE_WC_Product( false );

                $product_fields = $product->product_metas();

                $export_fields[ 'order_item_product' ] = array(
                        'title' => __( "Item => Product Data", 'vj-wp-import-export' ),
                        'isExported' => false,
                        'data' => $product->format_fields( $product_fields, 'wc-order-item', "wc-order-item-wpie_cf" )
                );

                $export_fields[ 'order_item_attribute' ] = array(
                        'title' => __( "Item => Attributes", 'vj-wp-import-export' ),
                        'isExported' => false,
                        'data' => $product->get_attribute_fields( 'wc-order-item-wc-product-attr', array( 'product', 'product_variation' ) )
                );

                $post_meta_list = array_diff( $this->get_meta_keys( array( 'product', 'product_variation' ) ), $product_fields );

                $post_meta = array();

                if ( !empty( $post_meta_list ) ) {

                        foreach ( $post_meta_list as $index => $meta_key ) {
                                $post_meta[] = array(
                                        'name' => $meta_key,
                                        'type' => 'wc-order-item-wpie_cf',
                                        'metaKey' => $meta_key
                                );
                        }
                }

                $export_fields[ 'orer_item_meta' ] = array(
                        'title' => __( "Item => Custom Fields", 'vj-wp-import-export' ),
                        'isExported' => false,
                        'data' => $post_meta
                );

                unset( $post_meta_list, $post_meta, $product, $product_fields );

                $taxonomy = apply_filters( 'wpie_pre_post_taxonomy_fields', $this->get_taxonomies_by_post_type( array( 'product', 'product_variation' ), "wc-order-item-wpie_tax", false ) );

                $export_fields[ 'order_item_taxonomy' ] = array(
                        'title' => __( "Item => Taxonomy", 'vj-wp-import-export' ),
                        'isExported' => false,
                        'data' => $taxonomy
                );

                unset( $taxonomy );

                $export_fields[ 'taxes' ] = array(
                        'title' => __( "Taxes & Shipping", 'vj-wp-import-export' ),
                        'isFiltered' => false,
                        'data' => array(
                                array(
                                        'name' => 'Rate Label (per tax)',
                                        'type' => 'wc-order',
                                        'field_type' => 'taxes',
                                        'field_key' => 'tax_order_item_name'
                                ),
                                array(
                                        'name' => 'Rate Code (per tax)',
                                        'type' => 'wc-order',
                                        'field_type' => 'taxes',
                                        'field_key' => 'tax_rate_code'
                                ),
                                array(
                                        'name' => 'Rate Percentage (per tax)',
                                        'type' => 'wc-order',
                                        'field_type' => 'taxes',
                                        'field_key' => 'tax_rate'
                                ),
                                array(
                                        'name' => 'Amount (per tax)',
                                        'type' => 'wc-order',
                                        'field_type' => 'taxes',
                                        'field_key' => 'tax_amount'
                                ),
                                array(
                                        'name' => 'Total Tax Amount',
                                        'type' => 'wc-order',
                                        'field_type' => 'taxes',
                                        'field_key' => '_order_tax'
                                ),
                                array(
                                        'name' => 'Shipping Name',
                                        'type' => 'wc-order',
                                        'field_type' => 'shipping',
                                        'field_key' => 'shipping_order_item_name'
                                ),
                                array(
                                        'name' => 'Shipping Method',
                                        'type' => 'wc-order',
                                        'field_type' => 'shipping',
                                        'field_key' => 'shipping_order_item_method'
                                ),
                                array(
                                        'name' => 'Shipping Cost',
                                        'type' => 'wc-order',
                                        'field_type' => 'shipping',
                                        'field_key' => '_order_shipping'
                                ),
                                array(
                                        'name' => 'Shipping Taxes',
                                        'type' => 'wc-order',
                                        'field_type' => 'shipping',
                                        'field_key' => '_order_shipping_taxes'
                                ),
                                array(
                                        'name' => 'Shipping Meta',
                                        'type' => 'wc-order',
                                        'field_type' => 'shipping',
                                        'field_key' => 'meta'
                                )
                        )
                );

                $export_fields[ 'fees' ] = array(
                        'title' => __( "Fees & Discounts", 'vj-wp-import-export' ),
                        'isFiltered' => false,
                        'data' => array(
                                array(
                                        'name' => 'Discount Amount (per coupon)',
                                        'type' => 'wc-order',
                                        'field_type' => 'coupons',
                                        'field_key' => 'discount_amount',
                                ),
                                array(
                                        'name' => 'Coupons Used',
                                        'type' => 'wc-order',
                                        'field_type' => 'coupons',
                                        'field_key' => '_coupons_used',
                                ),
                                array(
                                        'name' => 'Total Discount Amount',
                                        'type' => 'wc-order',
                                        'field_type' => 'coupons',
                                        'field_key' => '_cart_discount'
                                ),
                                array(
                                        'name' => 'Fee Name',
                                        'type' => 'wc-order',
                                        'field_type' => 'fees',
                                        'field_key' => 'item_fee_name'
                                ),
                                array(
                                        'name' => 'Fee Amount (per surcharge)',
                                        'type' => 'wc-order',
                                        'field_type' => 'fees',
                                        'field_key' => 'fee_line_total'
                                ),
                                array(
                                        'name' => 'Total Fee Amount',
                                        'type' => 'wc-order',
                                        'field_type' => 'fees',
                                        'field_key' => '_total_fee_amount'
                                ),
                                array(
                                        'name' => 'Fee Taxes',
                                        'type' => 'wc-order',
                                        'field_type' => 'fees',
                                        'field_key' => '_fee_tax_data'
                                ),
                        )
                );

                $export_fields[ 'notes' ] = array(
                        'title' => __( "Notes", 'vj-wp-import-export' ),
                        'isFiltered' => false,
                        'data' => array(
                                array(
                                        'name' => 'Note Content',
                                        'type' => 'wc-order',
                                        'field_type' => 'notes',
                                        'field_key' => 'comment_content'
                                ),
                                array(
                                        'name' => 'Note Date',
                                        'type' => 'wc-order',
                                        'field_type' => 'notes',
                                        'isDate' => true,
                                        'field_key' => 'comment_date'
                                ),
                                array(
                                        'name' => 'Note Visibility',
                                        'type' => 'wc-order',
                                        'field_type' => 'notes',
                                        'field_key' => 'visibility'
                                ),
                                array(
                                        'name' => 'Note User Name',
                                        'type' => 'wc-order',
                                        'field_type' => 'notes',
                                        'field_key' => 'comment_author'
                                ),
                                array(
                                        'name' => 'Note User Email',
                                        'type' => 'wc-order',
                                        'field_type' => 'notes',
                                        'field_key' => 'comment_author_email'
                                ),
                        )
                );

                $export_fields[ 'refund' ] = array(
                        'title' => __( "Refunds", 'vj-wp-import-export' ),
                        'isFiltered' => false,
                        'data' => array(
                                array(
                                        'name' => 'Refund Name',
                                        'type' => 'wc-order',
                                        'field_type' => 'refunds',
                                        'field_key' => 'refund_name'
                                ),
                                array(
                                        'name' => 'Refund Amounts',
                                        'type' => 'wc-order',
                                        'field_type' => 'refunds',
                                        'field_key' => 'refund_amount'
                                ),
                                array(
                                        'name' => 'Refund Reason',
                                        'type' => 'wc-order',
                                        'field_type' => 'refunds',
                                        'field_key' => 'refund_reason'
                                ),
                                array(
                                        'name' => 'Refund Date',
                                        'type' => 'wc-order',
                                        'field_type' => 'refunds',
                                        'isDate' => true,
                                        'field_key' => 'refund_date'
                                ),
                                array(
                                        'name' => 'Refund Author Email',
                                        'type' => 'wc-order',
                                        'field_type' => 'refunds',
                                        'field_key' => 'refund_author_email'
                                )
                        )
                );
        }

        private function available_billing_field_data() {

                $keys = array(
                        '_billing_first_name', '_billing_last_name', '_billing_company',
                        '_billing_address_1', '_billing_address_2', '_billing_city',
                        '_billing_postcode', '_billing_country', '_billing_state',
                        '_billing_email', '_customer_user_email', '_billing_phone'
                );

                return apply_filters( 'wpie_export_order_billing_fields', $keys );
        }

        private function available_shipping_field_data() {

                $keys = array(
                        '_shipping_first_name', '_shipping_last_name', '_shipping_company',
                        '_shipping_address_1', '_shipping_address_2', '_shipping_city',
                        '_shipping_postcode', '_shipping_country', '_shipping_state'
                );

                return apply_filters( 'wpie_export_order_shipping_fields', $keys );
        }

        public function init_export_process( $post_data = array(), $template_options = array(), $export_id = 0 ) {

                $max_items = isset( $template_options[ 'wpie_order_item_count' ] ) ? $template_options[ 'wpie_order_item_count' ] : 0;

                if ( intval( $max_items ) > 0 ) {
                        $this->max_items[ "line_item" ] = $max_items;
                } else {
                        $this->order_items_count( "line_item", $post_data );

                        $template_options[ 'wpie_order_item_count' ] = $this->max_items[ "line_item" ];

                        if ( $export_id > 0 ) {

                                global $wpdb;

                                $wpdb->update( $wpdb->prefix . "wpie_template", array( 'options' => maybe_serialize( $template_options ) ), array( 'id' => absint( $export_id ) ) );
                        }
                }

                $this->order_item_data = array();

                $this->is_single_row = isset( $template_options[ 'wpie_order_item_sigle_row' ] ) && absint( $template_options[ 'wpie_order_item_sigle_row' ] ) == 1 ? false : true;

                $this->fill_empty_colunm = isset( $template_options[ 'wpie_order_item_fill_empty' ] ) && absint( $template_options[ 'wpie_order_item_fill_empty' ] ) == 1 ? true : false;
        }

        private function order_items_count( $item_type = "line_item", $order_ids = array() ) {

                if ( !isset( $this->max_items[ $item_type ] ) ) {

                        global $wpdb;

                        $this->max_items[ $item_type ] = $wpdb->get_var( $wpdb->prepare( "SELECT max(cnt) as line_items_count FROM ( 
					SELECT order_id, COUNT(*) as cnt FROM {$wpdb->prefix}woocommerce_order_items 
						WHERE {$wpdb->prefix}woocommerce_order_items.order_item_type = %s AND {$wpdb->prefix}woocommerce_order_items.order_id IN (" . implode( ",", $order_ids ) . ") GROUP BY order_id) AS T3", $item_type ) );
                }
        }

        public function process_addon_data( &$export_data = array(), $field_type = "", $field_name = "", $field_option = array(), $item = null, $site_date_format = "" ) {

                global $wp_taxonomies, $wpdb;

                if ( $field_type ) {

                        $this->get_order_item( $item->ID );

                        $is_php = isset( $field_option[ 'isPhp' ] ) ? wpie_sanitize_field( $field_option[ 'isPhp' ] ) == 1 : false;

                        $php_func = isset( $field_option[ 'phpFun' ] ) ? wpie_sanitize_field( $field_option[ 'phpFun' ] ) : "";

                        $date_type = isset( $field_option[ 'dateType' ] ) ? wpie_sanitize_field( $field_option[ 'dateType' ] ) : "";

                        $new_field_type = isset( $field_option[ 'field_type' ] ) ? wpie_sanitize_field( $field_option[ 'field_type' ] ) : "";

                        $field_key = isset( $field_option[ 'field_key' ] ) ? wpie_sanitize_field( $field_option[ 'field_key' ] ) : "";

                        $date_format = isset( $field_option[ 'dateFormat' ] ) ? wpie_sanitize_field( $field_option[ 'dateFormat' ] ) : "";

                        if ( $new_field_type ) {

                                switch ( $new_field_type ) {

                                        case "customer":

                                                switch ( $field_key ) {

                                                        case '_customer_user_email':

                                                                $customer_user_id = get_post_meta( $item->ID, '_customer_user', true );

                                                                $customer_email = "";

                                                                if ( $customer_user_id ) {

                                                                        $user = get_user_by( 'id', $customer_user_id );

                                                                        if ( $user ) {
                                                                                $customer_email = $user->user_email;
                                                                        }
                                                                        unset( $user );
                                                                }

                                                                $export_data[ $field_name ] = $this->apply_user_function( $customer_email, $is_php, $php_func );

                                                                unset( $customer_user_id, $customer_email );

                                                                break;
                                                }
                                                break;
                                        case 'item':

                                                unset( $export_data[ $field_name ] );

                                                $item_data = [];

                                                if ( isset( $this->order_items[ $item->ID ][ 'line_item' ] ) && !empty( $this->order_items[ $item->ID ][ 'line_item' ] ) ) {

                                                        foreach ( $this->order_items[ $item->ID ][ 'line_item' ] as $key => $order_item ) {

                                                                $this->get_order_item_meta( $order_item->order_item_id );

                                                                $meta_data = $this->order_item_meta[ $order_item->order_item_id ];

                                                                $item_data[ $field_name . "_" . ($key + 1) ] = "";

                                                                if ( !empty( $meta_data ) ) {

                                                                        switch ( $field_key ) {

                                                                                case '_line_item_id':

                                                                                        $item_data[ $field_name . "_" . ($key + 1) ] = $this->apply_user_function( $order_item->order_item_id, $is_php, $php_func );

                                                                                        break;
                                                                                case 'meta':

                                                                                        $metaList = [];

                                                                                        if ( !empty( $meta_data ) ) {

                                                                                                foreach ( $meta_data as $_meta ) {

                                                                                                        $_metalist_key = isset( $_meta[ 'meta_key' ] ) ? $_meta[ 'meta_key' ] : "";

                                                                                                        if ( substr( $_metalist_key, 0, 1 ) === "_" ) {
                                                                                                                continue;
                                                                                                        }
                                                                                                        $_metalist_val = isset( $_meta[ 'meta_value' ] ) ? $_meta[ 'meta_value' ] : "";

                                                                                                        $metaList[] = $_metalist_key . "==>" . $_metalist_val;
                                                                                                }
                                                                                        }

                                                                                        $item_data[ $field_name . "_" . ($key + 1) ] = empty( $metaList ) ? "" : implode( "~=~", $metaList );

                                                                                        break;

                                                                                case '_product_title':
                                                                                        $item_data[ $field_name . "_" . ($key + 1) ] = isset( $order_item->order_item_name ) ? $order_item->order_item_name : "";
                                                                                        break;

                                                                                case '_product_id':
                                                                                case '_product_sku':

                                                                                        $product_id = '';

                                                                                        $variation_id = '';

                                                                                        if ( $meta_data ) {

                                                                                                foreach ( $meta_data as $meta ) {

                                                                                                        if ( !empty( $meta[ 'meta_value' ] ) ) {

                                                                                                                if ( $meta[ 'meta_key' ] == '_variation_id' ) {
                                                                                                                        $variation_id = $meta[ 'meta_value' ];
                                                                                                                }
                                                                                                                if ( $meta[ 'meta_key' ] == '_product_id' ) {
                                                                                                                        $product_id = $meta[ 'meta_value' ];
                                                                                                                }
                                                                                                        }
                                                                                                }
                                                                                        }

                                                                                        $_product_id = empty( $variation_id ) ? $product_id : $variation_id;

                                                                                        if ( empty( $_product_id ) || intval( $_product_id ) == 0 ) {
                                                                                                $item_data[ $field_name . "_" . ($key + 1) ] = $this->apply_user_function( "", $is_php, $php_func );
                                                                                        } else {
                                                                                                switch ( $field_key ) {

                                                                                                        case '_product_id':

                                                                                                                $item_data[ $field_name . "_" . ($key + 1) ] = $this->apply_user_function( $_product_id, $is_php, $php_func );

                                                                                                                break;

                                                                                                        case '_product_sku':

                                                                                                                $item_data[ $field_name . "_" . ($key + 1) ] = $this->apply_user_function( get_post_meta( $_product_id, '_sku', true ), $is_php, $php_func );

                                                                                                                break;
                                                                                                }
                                                                                        }

                                                                                        unset( $_product_id, $variation_id );

                                                                                        break;
                                                                                case 'is_variation':
                                                                                case 'original_product_title':
                                                                                case 'variation_attributes':

                                                                                        $product_id = '';
                                                                                        $variation_id = '';

                                                                                        if ( $meta_data ) {

                                                                                                foreach ( $meta_data as $meta ) {

                                                                                                        if ( $meta[ 'meta_key' ] == '_variation_id' and!empty( $meta[ 'meta_value' ] ) ) {
                                                                                                                $variation_id = $meta[ 'meta_value' ];
                                                                                                        }
                                                                                                        if ( $meta[ 'meta_key' ] == '_product_id' and!empty( $meta[ 'meta_value' ] ) ) {
                                                                                                                $product_id = $meta[ 'meta_value' ];
                                                                                                        }
                                                                                                }
                                                                                        }

                                                                                        if ( empty( $variation_id ) || intval( $variation_id ) == 0 ) {

                                                                                                if ( $field_key == "original_product_title" && intval( $product_id ) > 0 ) {

                                                                                                        $_product = get_post( intval( $product_id ) );

                                                                                                        $_post_parent_title = "";

                                                                                                        if ( is_object( $_product ) && isset( $_product->post_title ) ) {

                                                                                                                $_post_parent_title = $_product->post_title;
                                                                                                        }

                                                                                                        $_post_parent_title = empty( $_post_parent_title ) ? (isset( $order_item->order_item_name ) ? $order_item->order_item_name : "") : $_post_parent_title;

                                                                                                        $item_data[ $field_name . "_" . ($key + 1) ] = $this->apply_user_function( $_post_parent_title, $is_php, $php_func );

                                                                                                        unset( $_product, $_post_data_title );
                                                                                                } else {
                                                                                                        $item_data[ $field_name . "_" . ($key + 1) ] = $this->apply_user_function( "", $is_php, $php_func );
                                                                                                }
                                                                                        } else {
                                                                                                switch ( $field_key ) {

                                                                                                        case 'is_variation':

                                                                                                                $item_data[ $field_name . "_" . ($key + 1) ] = $this->apply_user_function( 1, $is_php, $php_func );

                                                                                                                break;
                                                                                                        case 'variation_attributes':

                                                                                                                $_product = \wc_get_product( $variation_id );

                                                                                                                $attr_data = "";

                                                                                                                if ( $_product ) {

                                                                                                                        $attr = $_product->get_attributes();

                                                                                                                        if ( !empty( $attr ) ) {
                                                                                                                                $attr_data = wp_json_encode( $attr );
                                                                                                                        }
                                                                                                                }

                                                                                                                $item_data[ $field_name . "_" . ($key + 1) ] = $this->apply_user_function( $attr_data, $is_php, $php_func );

                                                                                                                break;

                                                                                                        case 'original_product_title':

                                                                                                                $_product = get_post( $variation_id );

                                                                                                                $_post_parent_title = "";

                                                                                                                if ( is_object( $_product ) && isset( $_product->post_parent ) ) {

                                                                                                                        $_post_parent = $_product->post_parent;

                                                                                                                        $_product_parent = get_post( $_post_parent );

                                                                                                                        if ( is_object( $_product_parent ) && isset( $_product_parent->post_title ) ) {
                                                                                                                                $_post_parent_title = $_product_parent->post_title;
                                                                                                                        }
                                                                                                                        unset( $_post_parent, $_product_parent );
                                                                                                                }

                                                                                                                $_post_parent_title = empty( $_post_parent_title ) ? (isset( $order_item->order_item_name ) ? $order_item->order_item_name : "") : $_post_parent_title;

                                                                                                                $item_data[ $field_name . "_" . ($key + 1) ] = $this->apply_user_function( $_post_parent_title, $is_php, $php_func );

                                                                                                                unset( $_product, $_post_data_title );

                                                                                                                break;
                                                                                                }
                                                                                        }

                                                                                        unset( $_product_id, $variation_id );

                                                                                        break;

                                                                                case '_product_variation':

                                                                                        $var_data = array();

                                                                                        if ( $meta_data ) {

                                                                                                foreach ( $meta_data as $meta ) {

                                                                                                        if ( strpos( $meta[ 'meta_key' ], "pa_" ) === 0 ) {

                                                                                                                $var_data[] = $meta[ 'meta_value' ];
                                                                                                        }
                                                                                                }
                                                                                        }

                                                                                        if ( !empty( $var_data ) ) {

                                                                                                $item_data[ $field_name . "_" . ($key + 1) ] = $this->apply_user_function( implode( "|", $var_data ), $is_php, $php_func );
                                                                                        } else {

                                                                                                $item_data[ $field_name . "_" . ($key + 1) ] = $this->apply_user_function( "", $is_php, $php_func );
                                                                                        }

                                                                                        unset( $var_data );

                                                                                        break;

                                                                                case '_line_subtotal':

                                                                                        $_line_total = 0;

                                                                                        $_qty = 0;

                                                                                        if ( $meta_data ) {
                                                                                                foreach ( $meta_data as $meta ) {
                                                                                                        if ( $meta[ 'meta_key' ] == '_line_subtotal' ) {
                                                                                                                $_line_total = $meta[ 'meta_value' ];
                                                                                                        }
                                                                                                        if ( $meta[ 'meta_key' ] == '_qty' ) {
                                                                                                                $_qty = $meta[ 'meta_value' ];
                                                                                                        }
                                                                                                }
                                                                                        }

                                                                                        $item_data[ $field_name . "_" . ($key + 1) ] = $this->apply_user_function( ($_qty) ? number_format( $_line_total / $_qty, 2 ) : 0, $is_php, $php_func );

                                                                                        unset( $_line_total, $_qty );

                                                                                        break;

                                                                                default:


                                                                                        if ( $meta_data && is_array( $meta_data ) ) {

                                                                                                $default_item_data = array();

                                                                                                foreach ( $meta_data as $meta ) {
                                                                                                        if ( $meta[ 'meta_key' ] == $field_key ) {
                                                                                                                $default_item_data[] = $meta[ 'meta_value' ];
                                                                                                                break;
                                                                                                        }
                                                                                                }
                                                                                        } else {
                                                                                                $default_item_data = $meta_data;
                                                                                        }
                                                                                        if ( $default_item_data ) {
                                                                                                $default_item_data = implode( "|", $default_item_data );
                                                                                        } else {
                                                                                                $default_item_data = "";
                                                                                        }

                                                                                        $item_data[ $field_name . "_" . ($key + 1) ] = $this->apply_user_function( $default_item_data, $is_php, $php_func );

                                                                                        unset( $default_item_data );

                                                                                        break;
                                                                        }
                                                                }

                                                                unset( $meta_data );
                                                        }
                                                }

                                                if ( $this->is_single_row ) {

                                                        if ( $item_data ) {
                                                                $export_data = array_replace( $export_data, $item_data );
                                                        }

                                                        if ( absint( $this->max_items[ 'line_item' ] ) > 0 ) {

                                                                for ( $i = 1; $i <= absint( $this->max_items[ 'line_item' ] ); $i++ ) {
                                                                        if ( !isset( $export_data[ $field_name . "_" . $i ] ) ) {
                                                                                $export_data[ $field_name . "_" . $i ] = "";
                                                                        }
                                                                }
                                                                unset( $i );
                                                        }
                                                } else {

                                                        $export_data[ $field_name ] = "";

                                                        if ( $item_data && absint( $this->max_items[ 'line_item' ] ) > 0 ) {

                                                                for ( $i = 0; $i < count( $item_data ); $i++ ) {
                                                                        $this->order_item_data[ $i ][ $field_name ] = isset( $item_data[ $field_name . "_" . ($i + 1) ] ) ? $item_data[ $field_name . "_" . ($i + 1) ] : "";
                                                                }

                                                                unset( $i );
                                                        }
                                                }

                                                unset( $item_data );

                                                break;

                                        case 'taxes':

                                                $taxes_data = array();

                                                if ( !empty( $this->order_items[ $item->ID ][ 'tax' ] ) ) {

                                                        foreach ( $this->order_items[ $item->ID ][ 'tax' ] as $key => $order_tax ) {

                                                                $this->get_order_item_meta( $order_tax->order_item_id );

                                                                $meta_data = $this->order_item_meta[ $order_tax->order_item_id ];

                                                                $rate_details = null;

                                                                if ( $meta_data ) {

                                                                        foreach ( $meta_data as $meta ) {

                                                                                if ( $meta[ 'meta_key' ] == 'rate_id' ) {

                                                                                        $rate_id = $meta[ 'meta_value' ];

                                                                                        $this->get_order_tax_rate( $rate_id );

                                                                                        $rate_details = $this->tax_rate[ $rate_id ];

                                                                                        break;
                                                                                }
                                                                        }
                                                                }
                                                                if ( $field_key ) {

                                                                        switch ( $field_key ) {

                                                                                case 'tax_order_item_name':

                                                                                        $taxes_data[] = $order_tax->order_item_name;

                                                                                        break;
                                                                                case 'tax_rate_code':

                                                                                        $rate_name = "";
                                                                                        foreach ( $meta_data as $meta ) {
                                                                                                if ( $meta[ 'meta_key' ] == 'rate_id' ) {
                                                                                                        $rate_id = $meta[ 'meta_value' ];
                                                                                                        $rate_name = $wpdb->get_var( $wpdb->prepare( "SELECT tax_rate_name FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_id = %d", $rate_id ) );
                                                                                                        break;
                                                                                                }
                                                                                        }

                                                                                        $taxes_data[] = $rate_name;

                                                                                        break;
                                                                                case 'tax_rate':

                                                                                        $taxes_data[] = (!empty( $rate_details )) ? $rate_details->tax_rate : '';

                                                                                        break;
                                                                                case 'tax_amount':

                                                                                        $tax_amount = 0;

                                                                                        foreach ( $meta_data as $meta ) {
                                                                                                if ( $meta[ 'meta_key' ] == 'tax_amount' || $meta[ 'meta_key' ] == 'shipping_tax_amount' ) {
                                                                                                        $tax_amount += $meta[ 'meta_value' ];
                                                                                                }
                                                                                        }

                                                                                        $taxes_data[] = $tax_amount;

                                                                                        unset( $tax_amount );

                                                                                        break;
                                                                                case '_order_tax':

                                                                                        $_order_shipping_tax = get_post_meta( $item->ID, '_order_shipping_tax', true );

                                                                                        $_order_tax = get_post_meta( $item->ID, '_order_tax', true );

                                                                                        $_order_shipping_tax = is_numeric( $_order_shipping_tax ) ? floatval( $_order_shipping_tax ) : 0;

                                                                                        $_order_tax = is_numeric( $_order_tax ) ? floatval( $_order_tax ) : 0;

                                                                                        $taxes_data[] = $_order_shipping_tax + $_order_tax;

                                                                                        unset( $_order_shipping_tax, $_order_tax );

                                                                                        break;

                                                                                default:

                                                                                        break;
                                                                        }
                                                                }

                                                                unset( $meta_data, $rate_details );
                                                        }
                                                }

                                                $export_data[ $field_name ] = $this->apply_user_function( implode( "|", $taxes_data ), $is_php, $php_func );

                                                unset( $taxes_data );

                                                break;

                                        case 'shipping':




                                                $shipping_data = array();

                                                if ( !empty( $this->order_items[ $item->ID ][ 'shipping' ] ) ) {

                                                        foreach ( $this->order_items[ $item->ID ][ 'shipping' ] as $order_shipping ) {

                                                                $this->get_order_item_meta( $order_shipping->order_item_id );

                                                                $meta_data = $this->order_item_meta[ $order_shipping->order_item_id ];

                                                                if ( $field_key ) {

                                                                        switch ( $field_key ) {

                                                                                case 'shipping_order_item_name':
                                                                                        $shipping_data[] = $order_shipping->order_item_name;
                                                                                        break;
                                                                                case 'shipping_order_item_method':

                                                                                        $method_title = "";

                                                                                        $method_id = "";

                                                                                        if ( !empty( $meta_data ) ) {
                                                                                                foreach ( $meta_data as $meta ) {
                                                                                                        if ( $meta[ 'meta_key' ] == 'method_id' ) {
                                                                                                                $method_id = $meta[ 'meta_value' ];
                                                                                                                break;
                                                                                                        }
                                                                                                }
                                                                                        }

                                                                                        if ( !empty( $method_id ) ) {

                                                                                                $shipping_methods = \WC()->shipping->get_shipping_methods();

                                                                                                if ( isset( $shipping_methods[ $method_id ] ) && method_exists( $shipping_methods[ $method_id ], "get_method_title" ) ) {
                                                                                                        $method_title = $shipping_methods[ $method_id ]->get_method_title();
                                                                                                }
                                                                                        }
                                                                                        $shipping_data[] = $method_title;
                                                                                        break;
                                                                                case 'meta':

                                                                                        $metaList = [];

                                                                                        if ( !empty( $meta_data ) ) {

                                                                                                foreach ( $meta_data as $_meta ) {

                                                                                                        $_metalist_key = isset( $_meta[ 'meta_key' ] ) ? $_meta[ 'meta_key' ] : "";

                                                                                                        if ( substr( $_metalist_key, 0, 1 ) === "_" ) {
                                                                                                                continue;
                                                                                                        }
                                                                                                        $_metalist_val = isset( $_meta[ 'meta_value' ] ) ? $_meta[ 'meta_value' ] : "";

                                                                                                        $metaList[] = $_metalist_key . "==>" . $_metalist_val;
                                                                                                }
                                                                                        }

                                                                                        $shipping_data[] = empty( $metaList ) ? "" : implode( "~=~", $metaList );

                                                                                        break;
                                                                                case '_order_shipping':

                                                                                        $_order_shipping = "";

                                                                                        if ( $meta_data ) {
                                                                                                foreach ( $meta_data as $meta ) {
                                                                                                        if ( $meta[ 'meta_key' ] == 'cost' ) {
                                                                                                                $_order_shipping = $meta[ 'meta_value' ];
                                                                                                                break;
                                                                                                        }
                                                                                                }
                                                                                        }

                                                                                        $shipping_data[] = $_order_shipping;

                                                                                        unset( $_order_shipping );

                                                                                        break;
                                                                                case '_order_shipping_taxes':

                                                                                        $_order_shipping_taxes = "";

                                                                                        if ( $meta_data ) {
                                                                                                foreach ( $meta_data as $meta ) {
                                                                                                        if ( $meta[ 'meta_key' ] == 'taxes' ) {
                                                                                                                $_order_shipping_taxes = $meta[ 'meta_value' ];
                                                                                                                break;
                                                                                                        }
                                                                                                }
                                                                                        }

                                                                                        $_order_shipping_taxes = maybe_unserialize( $_order_shipping_taxes );

                                                                                        if ( is_array( $_order_shipping_taxes ) && isset( $_order_shipping_taxes[ 'total' ] ) ) {
                                                                                                $_order_shipping_taxes = $_order_shipping_taxes[ 'total' ];
                                                                                        } else {
                                                                                                $shipping_data[] = $_order_shipping_taxes;
                                                                                        }

                                                                                        unset( $_order_shipping_taxes );
                                                                                        break;
                                                                        }
                                                                }
                                                                unset( $meta_data );
                                                        }
                                                }

                                                $export_data[ $field_name ] = $this->apply_user_function( implode( "|", $shipping_data ), $is_php, $php_func );

                                                unset( $shipping_data );

                                                break;

                                        case 'coupons':

                                                $coupon_data = array();

                                                if ( !empty( $this->order_items[ $item->ID ][ 'coupon' ] ) ) {
                                                        foreach ( $this->order_items[ $item->ID ][ 'coupon' ] as $order_coupon ) {
                                                                $this->get_order_item_meta( $order_coupon->order_item_id );

                                                                $meta_data = $this->order_item_meta[ $order_coupon->order_item_id ];

                                                                if ( $field_key ) {

                                                                        switch ( $field_key ) {

                                                                                case '_cart_discount':
                                                                                        $coupon_data[] = get_post_meta( $item->ID, '_cart_discount', true );
                                                                                        break;
                                                                                case '_coupons_used':
                                                                                        $coupon_data[] = $order_coupon->order_item_name;
                                                                                        break;
                                                                                case 'discount_amount':

                                                                                        if ( $meta_data ) {

                                                                                                foreach ( $meta_data as $meta ) {
                                                                                                        if ( $meta[ 'meta_key' ] == 'discount_amount' ) {
                                                                                                                $coupon_data[] = $meta[ 'meta_value' ] * (-1);
                                                                                                                break;
                                                                                                        }
                                                                                                }
                                                                                        }

                                                                                        break;
                                                                        }
                                                                }
                                                                unset( $meta_data );
                                                        }
                                                }

                                                $export_data[ $field_name ] = $this->apply_user_function( implode( "|", $coupon_data ), $is_php, $php_func );

                                                unset( $coupon_data );

                                                break;

                                        case 'fees':

                                                $fees_data = array();

                                                $is_fee_name = false;

                                                if ( !empty( $this->order_items[ $item->ID ][ 'fee' ] ) ) {

                                                        foreach ( $this->order_items[ $item->ID ][ 'fee' ] as $order_fee ) {

                                                                $this->get_order_item_meta( $order_fee->order_item_id );

                                                                $meta_data = $this->order_item_meta[ $order_fee->order_item_id ];

                                                                if ( $field_key ) {

                                                                        switch ( $field_key ) {

                                                                                case 'item_fee_name':

                                                                                        $fees_data[] = isset( $order_fee->order_item_name ) ? $order_fee->order_item_name : "";
                                                                                        break;

                                                                                case 'fee_line_total':

                                                                                        $fee_line_total = "";

                                                                                        if ( $meta_data ) {
                                                                                                foreach ( $meta_data as $meta ) {
                                                                                                        if ( $meta[ 'meta_key' ] == '_line_total' ) {
                                                                                                                $fee_line_total = $meta[ 'meta_value' ];
                                                                                                                break;
                                                                                                        }
                                                                                                }
                                                                                        }
                                                                                        $fees_data[] = $fee_line_total;

                                                                                        unset( $fee_line_total );
                                                                                        break;

                                                                                case '_total_fee_amount':

                                                                                        $total_fee_amount = 0;

                                                                                        if ( $meta_data ) {
                                                                                                foreach ( $meta_data as $meta ) {

                                                                                                        if ( $meta[ 'meta_key' ] == '_line_total' ) {

                                                                                                                $total_fee_amount += $meta[ 'meta_value' ];

                                                                                                                break;
                                                                                                        }
                                                                                                }
                                                                                        }

                                                                                        $fees_data[] = $total_fee_amount;
                                                                                        unset( $total_fee_amount );

                                                                                        break;
                                                                                case '_fee_tax_data':

                                                                                        $fee_tax_data = "";
                                                                                        if ( $meta_data ) {
                                                                                                foreach ( $meta_data as $meta ) {
                                                                                                        if ( $meta[ 'meta_key' ] == '_line_tax_data' ) {
                                                                                                                $fee_tax_data = $meta[ 'meta_value' ];
                                                                                                                break;
                                                                                                        }
                                                                                                }
                                                                                        }

                                                                                        $fees_data[] = $fee_tax_data;
                                                                                        unset( $fee_tax_data );

                                                                                        break;
                                                                        }
                                                                }

                                                                unset( $meta_data );
                                                        }
                                                }

                                                if ( !$is_fee_name ) {
                                                        $export_data[ $field_name ] = $this->apply_user_function( implode( "|", $fees_data ), $is_php, $php_func );
                                                }
                                                unset( $fees_data );

                                                break;

                                        case 'refunds':

                                                $refunds_data = array();

                                                $this->get_order_refunds( $item->ID );

                                                $_is_empty_field = true;
                                                $is_set_empty = false;

                                                if ( !empty( $this->order_refunds[ $item->ID ] ) && $field_key ) {

                                                        $order_refund = $this->order_refunds[ $item->ID ];

                                                        if ( is_array( $order_refund ) && !empty( $order_refund ) ) {

                                                                foreach ( $order_refund as $_refund ) {
                                                                        switch ( $field_key ) {

                                                                                case 'refund_name':
                                                                                        $refunds_data[] = $_refund->post_name;
                                                                                        break;
                                                                                case 'refund_amount':
                                                                                        $refunds_data[] = get_post_meta( $_refund->ID, '_refund_amount', true );
                                                                                        break;
                                                                                case 'refund_date':
                                                                                        $refunds_data[] = date( "Y-m-d h:i:s", get_post_time( 'U', true, $_refund->ID ) );
                                                                                        break;
                                                                                case 'refund_reason':

                                                                                        $refunds_data[] = $_refund->post_excerpt;
                                                                                        $is_set_empty = true;
                                                                                        if ( $_is_empty_field !== false && !empty( $_refund->post_excerpt ) ) {
                                                                                                $_is_empty_field = false;
                                                                                        }
                                                                                        break;
                                                                                case 'refund_author_email':

                                                                                        $user_email = "";

                                                                                        $refund_author = get_userdata( $_refund->post_author );

                                                                                        if ( $refund_author ) {
                                                                                                $user_email = $refund_author->user_email;
                                                                                        }

                                                                                        $refunds_data[] = $user_email;

                                                                                        unset( $user_email, $refund_author );

                                                                                        break;
                                                                                case 'refund_items':

                                                                                        $refund = new \WC_Order_Refund( $_refund->ID );

                                                                                        $refund_items = $refund->get_items( array( "line_item", "fee", "shipping" ) );

                                                                                        $refund_item_data = array();

                                                                                        if ( !empty( $refund_items ) ) {

                                                                                                foreach ( $refund_items as $_refund_item ) {

                                                                                                        if ( $_refund_item instanceof \WC_Order_Item_Product ) {
                                                                                                                $refund_item_data[ "line_item" ][] = array(
                                                                                                                        'product_id' => $_refund_item->get_product_id(),
                                                                                                                        'variation_id' => $_refund_item->get_variation_id(),
                                                                                                                        'quantity' => 1,
                                                                                                                        'tax_class' => '',
                                                                                                                        'subtotal' => 0,
                                                                                                                        'subtotal_tax' => 0,
                                                                                                                        'total' => 0,
                                                                                                                        'total_tax' => 0,
                                                                                                                        'taxes' => array(
                                                                                                                                'subtotal' => array(),
                                                                                                                                'total' => array()
                                                                                                                        )
                                                                                                                );
                                                                                                        } elseif ( $_refund_item instanceof \WC_Order_Item_Fee ) {
                                                                                                                $refund_item_data[ "fee" ][] = array();
                                                                                                        } elseif ( $_refund_item instanceof \WC_Order_Item_Shipping ) {
                                                                                                                $refund_item_data[ "shipping" ][] = array();
                                                                                                        }
                                                                                                }
                                                                                        }

                                                                                        $refunds_data[] = $refund_item_data;

                                                                                        unset( $refund, $refund_items, $refund_item_data );

                                                                                        break;

                                                                                default:
                                                                                        break;
                                                                        }
                                                                }
                                                        }

                                                        unset( $order_refund );
                                                }

                                                $finalRefund = "";
                                                if ( $is_set_empty && $_is_empty_field ) {
                                                        
                                                } else {
                                                        $finalRefund = implode( "|", $refunds_data );
                                                }
                                                $export_data[ $field_name ] = $this->apply_user_function( $finalRefund, $is_php, $php_func );

                                                unset( $refunds_data );

                                                break;

                                        case 'notes':

                                                $notes_data = array();

                                                $this->get_order_notes( $item->ID );

                                                if ( !empty( $this->order_notes[ $item->ID ] ) && $field_key ) {

                                                        foreach ( $this->order_notes[ $item->ID ] as $order_note ) {

                                                                switch ( $field_key ) {

                                                                        case 'visibility':

                                                                                $visibility = get_comment_meta( $order_note->comment_ID, 'is_customer_note', true );

                                                                                $notes_data[] = $visibility ? 'customer' : 'private';

                                                                                unset( $visibility );

                                                                                break;

                                                                        default:
                                                                                $notes_data[] = isset( $order_note->$field_key ) ? $order_note->$field_key : "";
                                                                                break;
                                                                }
                                                        }
                                                }

                                                $export_data[ $field_name ] = $this->apply_user_function( implode( "|", $notes_data ), $is_php, $php_func );

                                                break;

                                        default :
                                                break;
                                }
                        }
                        unset( $is_php, $php_func, $date_type, $new_field_type, $field_key, $date_format );
                }
        }

        private function get_order_item( $order_id = null ) {

                if ( absint( $order_id ) > 0 && !isset( $this->order_items[ $order_id ] ) ) {

                        global $wpdb;

                        $order_items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id = %d", absint( $order_id ) ) );

                        if ( !empty( $order_items ) ) {

                                foreach ( $order_items as $order_item ) {

                                        switch ( $order_item->order_item_type ) {
                                                case 'line_item':
                                                        $this->order_items[ $order_id ][ 'line_item' ][] = $order_item;
                                                        break;
                                                case 'tax':
                                                        $this->order_items[ $order_id ][ 'tax' ][] = $order_item;
                                                        break;
                                                case 'shipping':
                                                        $this->order_items[ $order_id ][ 'shipping' ][] = $order_item;
                                                        ;
                                                        break;
                                                case 'coupon':
                                                        $this->order_items[ $order_id ][ 'coupon' ][] = $order_item;
                                                        break;
                                                case 'fee':
                                                        $this->order_items[ $order_id ][ 'fee' ][] = $order_item;
                                                        break;
                                        }
                                }
                        }
                        unset( $order_items );
                }
        }

        private function get_order_item_meta( $item_id = 0 ) {

                if ( !isset( $this->order_item_meta[ $item_id ] ) ) {

                        global $wpdb;

                        $this->order_item_meta[ $item_id ] = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id = %d", absint( $item_id ) ), ARRAY_A );
                }
        }

        private function get_order_refunds( $post_id = 0 ) {

                if ( !isset( $this->order_refunds[ $post_id ] ) ) {

                        global $wpdb;

                        $this->order_refunds[ $post_id ] = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}posts WHERE post_parent = %d AND post_type = 'shop_order_refund'", $post_id ) );
                }
        }

        private function get_order_notes( $post_id = 0 ) {

                if ( !isset( $this->order_notes[ $post_id ] ) ) {

                        $args = array(
                                'post_id' => $post_id,
                                'type' => 'order_note'
                        );

                        remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10 );

                        $this->order_notes[ $post_id ] = get_comments( $args );

                        add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );
                }
        }

        private function get_order_tax_rate( $rate_id = 0 ) {

                if ( !isset( $this->tax_rate[ $rate_id ] ) ) {

                        global $wpdb;

                        $this->tax_rate[ $rate_id ] = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_id = %d", $rate_id ) );
                }
        }

        public function change_export_labels( &$export_labels = array(), $field_type = "", $field_name = "", $field_label = "", $field_option = array() ) {

                if ( $this->is_single_row && $field_type == "wc-order" ) {

                        $new_field_type = isset( $field_option[ 'field_type' ] ) ? wpie_sanitize_field( $field_option[ 'field_type' ] ) : "";

                        if ( $new_field_type == "item" ) {

                                if ( isset( $this->max_items[ 'line_item' ] ) && absint( $this->max_items[ 'line_item' ] ) > 0 ) {

                                        unset( $export_labels[ $field_name ] );

                                        for ( $i = 1; $i <= absint( $this->max_items[ 'line_item' ] ); $i++ ) {
                                                $export_labels[ $field_name . "_" . $i ] = $field_label . " #" . $i;
                                        }

                                        unset( $i );
                                }
                        }

                        unset( $new_field_type );
                }
        }

        public function finalyze_export_process( &$export_data = array(), &$has_multiple_rows = false ) {

                if ( !$this->is_single_row ) {

                        if ( $this->order_item_data ) {

                                $has_multiple_rows = true;

                                $item_data = array();

                                foreach ( $this->order_item_data as $key => $item ) {

                                        if ( $key == 0 || $this->fill_empty_colunm ) {

                                                $data = $export_data;
                                        } else {

                                                $data = array_fill_keys( array_keys( $export_data ), "" );
                                        }

                                        $item_data[] = array_replace( $data, $item );

                                        unset( $data );
                                }

                                $export_data = $item_data;

                                unset( $item_data );
                        }
                        $this->order_item_data = array();
                }
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
