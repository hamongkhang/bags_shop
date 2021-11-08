<?php


namespace wpie\import\wc\order\item;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}
if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php');
}

class WPIE_Order_Tax_Item extends \wpie\import\base\WPIE_Import_Base {

        /**
         * @var \WC_Order
         */
        private $order;
        private $tax = [];

        public function __construct( $wpie_import_option = array(), $wpie_import_record = array(), $item_id = 0, $is_new_item = true, &$addon_error = false, &$addon_log = array(), $order = null ) {

                $this->wpie_import_option = $wpie_import_option;

                $this->wpie_import_record = $wpie_import_record;

                $this->item_id = $item_id;

                $this->order = $order;

                $this->is_new_item = $is_new_item;

                $this->addon_error = &$addon_error;

                $this->addon_log = &$addon_log;

                if ( !$this->is_new_item ) {
                        $this->tax = $this->order->get_items( 'tax' );
                }
                $this->prepare_order_tax();

                $this->order->calculate_taxes();
        }

        private function prepare_order_tax() {

                $delimiter = wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_taxes_delim' ) );

                $tax_rate = wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_tax_rate' ) );

                $current_tax_items = [];

                if ( !empty( $tax_rate ) ) {

                        $tax_rate_amount = wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_tax_rate_amount' ) );

                        $tax_rate = explode( $delimiter, $tax_rate );

                        if ( !empty( $tax_rate_amount ) ) {
                                $tax_rate_amount = explode( $delimiter, $tax_rate_amount );
                        }

                        $tax_classes = \WC_Tax::get_tax_classes();

                        $current_tax_rates = array();

                        if ( $tax_classes ) {

                                if ( !in_array( '', $tax_classes ) ) {
                                        $tax_classes[] = '';
                                }

                                foreach ( $tax_classes as $class ) {

                                        $_class = \WC_Tax::get_rates_for_tax_class( sanitize_title( $class ) );

                                        if ( !empty( $_class ) ) {

                                                foreach ( $_class as $rate_key => $rate ) {
                                                        $current_tax_rates[ $rate->tax_rate_id ] = $rate;
                                                }
                                        }
                                        unset( $_class );
                                }
                        }


                        foreach ( $tax_rate as $key => $tax_name ) {

                                $_tax_rate_amount = isset( $tax_rate_amount[ $key ] ) ? $tax_rate_amount[ $key ] : 0;

                                $item_id = false;

                                $tax_rate_id = false;

                                if ( !empty( $current_tax_rates ) ) {

                                        foreach ( $current_tax_rates as $_rate_id => $rate ) {

                                                $code = array();

                                                $code[] = isset( $rate->tax_rate_country ) ? $rate->tax_rate_country : "";

                                                $code[] = isset( $rate->tax_rate_state ) ? $rate->tax_rate_state : "";

                                                $code[] = isset( $rate->tax_rate_name ) ? $rate->tax_rate_name : "TAX";

                                                $code[] = isset( $rate->tax_rate_priority ) ? absint( $rate->tax_rate_priority ) : 0;

                                                $new_tax_rate_name = implode( '-', $code );

                                                if ( strtolower( $rate->tax_rate_name ) == strtolower( $tax_name ) || strtolower( $new_tax_rate_name ) == strtolower( $tax_name ) ) {

                                                        $tax_rate_id = $_rate_id;

                                                        break;
                                                }

                                                unset( $code, $new_tax_rate_name );
                                        }
                                }

                                if ( $tax_rate_id !== false ) {

                                        if ( !empty( $this->tax ) ) {
                                                foreach ( $this->tax as $order_item_id => $order_item ) {
                                                        if ( $order_item[ 'name' ] == $tax_name ) {
                                                                $item_id = $order_item_id;
                                                                break;
                                                        }
                                                }
                                        }

                                        if ( $item_id !== false ) {

                                                $current_tax_items[] = $item_id;

                                                $this->update_tax( $item_id, $tax_rate_id );
                                        } else {

                                                $this->add_tax( $tax_rate_id );
                                        }
                                }

                                unset( $_tax_rate_amount, $item_id, $tax_rate_id );
                        }

                        unset( $tax_rate_amount, $tax_classes, $current_tax_rates );
                }

                if ( !empty( $this->tax ) ) {

                        foreach ( $this->tax as $item_id => $item ) {

                                if ( in_array( $item_id, $current_tax_items ) ) {
                                        continue;
                                } else {
                                        $this->order->remove_item( $item_id );
                                }
                        }
                }

                unset( $delimiter, $tax_rate, $current_tax_items );
        }

        private function add_tax( $rate_id = 0 ) {

                $item = new \WC_Order_Item_Tax();

                $item->set_rate( $rate_id );

                $item->set_order_id( $this->item_id );

                $item->save();

                $this->order->add_item( $item );
        }

        private function update_tax( $item_id = 0, $rate_id = 0 ) {

                $item = new \WC_Order_Item_Tax( $item_id );

                $item->set_rate( $rate_id );

                $item->save();

                unset( $item );
        }

        public function __destruct() {

                parent::__destruct();

                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
