<?php


namespace wpie\import\wc;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php');
}

class WPIE_Customer_Import extends \wpie\import\base\WPIE_Import_Base {

        public function __construct( $wpie_import_option = [], $import_type = "" ) {

                $this->import_type = "shop_customer";

                $this->wpie_import_option = $wpie_import_option;

                add_filter( 'wpie_import_user_role', [ $this, "set_customer_role" ] );
        }

        public function set_customer_role( $role = "" ) {

                return empty( trim( $role ) ) === "" ? "customer" : $role;
        }

        public function before_item_import( $wpie_import_record = array(), &$existing_item_id = 0, &$is_new_item = true, &$is_search_duplicates ) {

                $this->wpie_import_record = $wpie_import_record;
        }

        public function after_item_import( $item_id = 0, $item = null, $is_new_item = false ) {

                $this->item_id = $item_id;

                $this->item = $item;

                $this->is_new_item = $is_new_item;

                if ( $this->is_update_field( 'billing' ) ) {

                        $billingFields = $this->get_billing_fields();

                        foreach ( $billingFields as $key ) {

                                $val = $this->get_field_value( 'wpie_item_' . $key );

                                $this->update_meta( $key, $val );
                        }
                }
                if ( $this->is_update_field( 'shipping' ) ) {

                        $shipping_source = $this->get_field_value( 'wpie_item_customer_shipping_source' );

                        $shippingFields = $this->get_shipping_fields();

                        foreach ( $shippingFields as $key ) {

                                $val_field = ($shipping_source === "import") ? $key : str_replace( "shipping", "billing", $key );

                                $val = $this->get_field_value( 'wpie_item_' . $val_field );

                                $this->update_meta( $key, $val );
                        }
                }
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

        public function __destruct() {

                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
