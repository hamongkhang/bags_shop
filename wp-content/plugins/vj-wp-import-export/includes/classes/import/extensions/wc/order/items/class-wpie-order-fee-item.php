<?php


namespace wpie\import\wc\order\item;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}
if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php');
}

class WPIE_Order_Fee_Item extends \wpie\import\base\WPIE_Import_Base {

        /**
         * @var \WC_Order
         */
        private $order;
        private $fee;

        public function __construct( $wpie_import_option = array(), $wpie_import_record = array(), $item_id = 0, $is_new_item = true, &$addon_error = false, &$addon_log = array(), $order = null ) {

                $this->wpie_import_option = $wpie_import_option;

                $this->wpie_import_record = $wpie_import_record;

                $this->item_id = $item_id;

                $this->order = $order;

                $this->is_new_item = $is_new_item;

                $this->addon_error = &$addon_error;

                $this->addon_log = &$addon_log;

                if ( !$this->is_new_item ) {
                        $this->fee = $this->order->get_items( 'fee' );
                } else {
                        $this->fee = [];
                }

                $this->prepare_order_fee();
        }

        private function prepare_order_fee() {

                $delimiter = wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_fees_delim' ) );

                $fee_name = wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_fee' ) );

                $fee_amount = wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_fee_amount' ) );

                $fee_items = [];

                if ( !empty( $fee_name ) ) {

                        $fee_name = explode( $delimiter, $fee_name );

                        if ( !empty( $fee_amount ) ) {
                                $fee_amount = explode( $delimiter, $fee_amount );
                        }

                        foreach ( $fee_name as $key => $name ) {

                                $item_id = false;

                                if ( !empty( $this->fee ) ) {

                                        foreach ( $this->fee as $order_item_id => $order_item ) {
                                                if ( $order_item[ 'name' ] == $name ) {
                                                        $item_id = $order_item_id;

                                                        $fee_items[] = $item_id;
                                                        break;
                                                }
                                        }
                                }

                                $fee_data = array(
                                        'title' => $name,
                                        'total' => isset( $fee_amount[ $key ] ) ? floatval( abs( $fee_amount[ $key ] ) ) : 0,
                                );

                                if ( $item_id ) {

                                        $this->update_fee( $item_id, $fee_data );
                                } else {
                                        $this->add_fee( $fee_data );
                                }
                                unset( $item_id, $fee_data );
                        }
                }

                if ( !empty( $this->fee ) ) {

                        foreach ( $this->fee as $order_item_id => $order_item ) {

                                if ( !in_array( $order_item_id, $fee_items ) ) {

                                        $this->remove_item( $order_item_id );

                                        break;
                                }
                        }
                }

                unset( $delimiter, $fee_name, $fee_amount, $fee_items );
        }

        private function add_fee( $fee = null ) {

                $item = new \WC_Order_Item_Fee();

                $item->set_order_id( $this->item_id );

                $item->set_name( wc_clean( $fee[ 'title' ] ) );

                $item->set_total( isset( $fee[ 'total' ] ) ? floatval( abs( $fee[ 'total' ] ) ) : 0  );

                $item->save();

                $this->order->add_item( $item );

                unset( $item );
        }

        private function update_fee( $item_id = 0, $fee = array() ) {

                $item = new \WC_Order_Item_Fee( $item_id );

                if ( isset( $fee[ 'title' ] ) ) {
                        $item->set_name( wc_clean( $fee[ 'title' ] ) );
                }

                if ( isset( $fee[ 'total' ] ) ) {
                        $item->set_total( floatval( $fee[ 'total' ] ) );
                }

                $item->save();

                unset( $item );
        }

        private function remove_item( $item_id = 0 ) {

                if ( method_exists( $this->order, "remove_item" ) ) {
                        $this->order->remove_item( $item_id );
                } elseif ( function_exists( "wc_delete_order_item" ) ) {
                        wc_delete_order_item( $item_id );
                }
        }

        public function __destruct() {

                parent::__destruct();

                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
