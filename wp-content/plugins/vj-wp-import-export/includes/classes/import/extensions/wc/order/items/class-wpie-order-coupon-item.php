<?php


namespace wpie\import\wc\order\item;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}
if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php');
}

class WPIE_Order_Coupon_Item extends \wpie\import\base\WPIE_Import_Base {

        /**
         * @var \WC_Order
         */
        private $order;

        public function __construct( $wpie_import_option = array(), $wpie_import_record = array(), $item_id = 0, $is_new_item = true, &$addon_error = false, &$addon_log = array(), $order = null ) {

                $this->wpie_import_option = $wpie_import_option;

                $this->wpie_import_record = $wpie_import_record;

                $this->item_id = $item_id;

                $this->order = $order;

                $this->is_new_item = $is_new_item;

                $this->addon_error = &$addon_error;

                $this->addon_log = &$addon_log;

                $this->prepare_order_coupon();

                $this->order->recalculate_coupons();
        }

        private function prepare_order_coupon() {

                $total_discount = 0;

                $delimiter = wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_coupon_delim' ) );

                $item_coupon = wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_coupon' ) );

                if ( !$this->is_new_item ) {
                        $current_coupon = $this->order->get_items( 'coupon' );
                } else {
                        $current_coupon = [];
                }

                $item_list = [];

                if ( !empty( $item_coupon ) ) {

                        $coupon_amount = wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_coupon_amount' ) );

                        if ( !empty( $coupon_amount ) ) {
                                $coupon_amount = explode( $delimiter, $coupon_amount );
                        }

                        $item_coupon = explode( $delimiter, $item_coupon );

                        foreach ( $item_coupon as $key => $code ) {

                                if ( empty( $code ) ) {
                                        continue;
                                }

                                $_amount = isset( $coupon_amount[ $key ] ) ? abs( floatval( $coupon_amount[ $key ] ) ) : 0;

                                $item_id = false;

                                if ( !empty( $current_coupon ) ) {

                                        foreach ( $current_coupon as $order_item_id => $order_item ) {

                                                if ( $order_item->get_code( 'edit' ) == $code ) {

                                                        $item_id = $order_item_id;

                                                        $item_list[] = $order_item_id;

                                                        break;
                                                }
                                        }
                                }

                                $coupon_item = array(
                                        'code' => $code,
                                        'amount' => $_amount
                                );

                                $total_discount += $_amount;

                                if ( $item_id ) {
                                        $this->update_coupon( $item_id, $coupon_item );
                                } else {
                                        $this->add_coupon( $coupon_item );
                                }

                                unset( $_amount, $item_id, $coupon_item );
                        }

                        unset( $coupon_amount );
                }

                if ( !empty( $current_coupon ) ) {

                        foreach ( $current_coupon as $order_item_id => $order_item ) {

                                if ( !in_array( $order_item_id, $item_list ) ) {

                                        $this->remove_item( $order_item_id );

                                        break;
                                }
                        }
                }


                unset( $delimiter, $item_coupon, $item_list, $current_coupon );

                $this->order->set_discount_total( $total_discount );

                $this->order->set_discount_tax( 0 );
        }

        private function update_coupon( $item_id = 0, $coupon = [] ) {

                $item = new \WC_Order_Item_Coupon( $item_id );

                if ( isset( $coupon[ 'code' ] ) ) {
                        $item->set_code( $coupon[ 'code' ] );
                }

                if ( isset( $coupon[ 'amount' ] ) ) {
                        $item->set_discount( floatval( $coupon[ 'amount' ] ) );
                }

                $coupon_id = $item->save();

                if ( !$coupon_id ) {

                        $this->addon_log[] = "<strong>" . __( 'WARNING', 'vj-wp-import-export' ) . '</strong> : ' . __( 'Unable to update order coupon line.', 'vj-wp-import-export' );
                }

                unset( $item, $coupon_id );
        }

        private function add_coupon( $coupon = [] ) {

                $item = new \WC_Order_Item_Coupon();

                $item->set_name( $coupon[ 'code' ] );

                $item->set_code( $coupon[ 'code' ] );

                $item->set_discount( isset( $coupon[ 'amount' ] ) ? abs( floatval( $coupon[ 'amount' ] ) ) : 0  );

                $item->set_order_id( $this->item_id );

                $item->save();

                $this->order->add_item( $item );
        }

        private function remove_item( $item_id ) {

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
