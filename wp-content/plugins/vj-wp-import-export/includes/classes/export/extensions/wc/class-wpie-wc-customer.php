<?php


namespace wpie\export\wc;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

class WPIE_WC_Customer {

        public function __construct() {
                add_filter( 'wpie_export_users_where', array( $this, 'set_customer_only' ), 10, 2 );
        }

        public function get_billing_fields() {
                return [
                        'billing_first_name', 'billing_last_name', 'billing_company',
                        'billing_address_1', 'billing_address_2', 'billing_city',
                        'billing_postcode', 'billing_country', 'billing_state',
                        'billing_email', 'billing_phone'
                ];
        }

        public function get_shipping_fields() {
                return [
                        'shipping_first_name', 'shipping_last_name', 'shipping_company',
                        'shipping_address_1', 'shipping_address_2', 'shipping_city',
                        'shipping_postcode', 'shipping_country', 'shipping_state'
                ];
        }

        public function get_customer_fields() {

                $billing_fields = $this->get_billing_fields();

                $shipping_fields = $this->get_shipping_fields();

                return array_merge( $billing_fields, $shipping_fields );
        }

        public function pre_process_fields( &$export_fields = [], $export_type = [] ) {

                $meta_list = $this->get_customer_fields();

                $export_fields[ 'meta' ] = array_diff( $export_fields[ 'meta' ], $meta_list );

                $export_fields[ 'address_fields' ] = [
                        'title' => __( "Address Data", 'vj-wp-import-export' ),
                        'isFiltered' => true,
                        'data' => $this->format_fields( $meta_list )
                ];

                unset( $meta_list );
        }

        public function format_fields( $fields_data = [] ) {

                $fields = [];

                if ( !empty( $fields_data ) ) {

                        foreach ( $fields_data as $field_key ) {

                                $fields[] = array(
                                        'name' => $this->fix_title( $field_key ),
                                        'type' => "wpie_cf",
                                        'metaKey' => $field_key
                                );
                        }
                }
                return $fields;
        }

        private function fix_title( $field_title = "" ) {

                return ucwords( trim( str_replace( "_", " ", $field_title ) ) );
        }

        public function set_customer_only( $where = [], $join = [] ) {

                global $wpieExportTemplate, $wpdb;

                $cause = " AND ";

                if ( empty( $where ) ) {
                        $cause = "";
                }

                $only_customer_with_purchase = isset( $wpieExportTemplate[ 'wpie_export_only_customer_with_purchase' ] ) && intval( $wpieExportTemplate[ 'wpie_export_only_customer_with_purchase' ] ) === 1 ? true : false;

                $customer_subquery = $wpdb->prepare( "SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value != %s", '_customer_user', '0' );

                $customers_capabilities = "";

                if ( $only_customer_with_purchase === false ) {
                        $customers_capabilities = " OR {$wpdb->users}.ID IN ( SELECT DISTINCT user_id FROM {$wpdb->usermeta} WHERE meta_key = 'wp_capabilities' AND meta_value REGEXP 'customer' ) ";
                }

                $where[] = " $cause {$wpdb->users}.ID IN (" . $customer_subquery . ") " . $customers_capabilities;

                return $where;
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
