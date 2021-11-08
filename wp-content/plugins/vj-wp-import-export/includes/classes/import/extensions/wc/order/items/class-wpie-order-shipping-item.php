<?php


namespace wpie\import\wc\order\item;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}
if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php');
}

class WPIE_Order_Shipping_Item extends \wpie\import\base\WPIE_Import_Base {

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

                $this->order->remove_order_items( 'shipping' );

                $this->prepare_order_shipping();

                $this->order->calculate_shipping();
        }

        private function prepare_order_shipping() {

                $delimiter = wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_shipping_costs_delim' ) );

                $item_shipping_method = wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_shipping_method' ) );

                $total_shipping = 0;

                if ( !empty( $item_shipping_method ) ) {

                        $shipping_methods = WC()->shipping->get_shipping_methods();

                        $shipping_zone_methods = array();

                        if ( class_exists( 'WC_Shipping_Zones' ) ) {

                                $zones = \WC_Shipping_Zones::get_zones();

                                if ( !empty( $zones ) ) {

                                        foreach ( $zones as $zone_id => $zone ) {
                                                if ( !empty( $zone[ 'shipping_methods' ] ) ) {
                                                        foreach ( $zone[ 'shipping_methods' ] as $method ) {
                                                                $shipping_zone_methods[] = $method;
                                                        }
                                                }
                                        }
                                } else {

                                        $zone = new \WC_Shipping_Zone( 0 );

                                        $shipping_zone_methods[] = $zone->get_shipping_methods();

                                        unset( $zone );
                                }

                                unset( $zones );
                        }

                        $shipping_name = wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_shipping_name' ) );

                        if ( !empty( $shipping_name ) ) {
                                $shipping_name = explode( $delimiter, $shipping_name );
                        }

                        $shipping_amount = wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_shipping_amount' ) );

                        if ( !empty( $shipping_amount ) ) {
                                $shipping_amount = explode( $delimiter, $shipping_amount );
                        }

                        $item_metas = $this->get_field_value( 'wpie_item_order_item_shipping_meta' );

                        $item_metas = empty( $item_metas ) ? [] : explode( $delimiter, $item_metas );

                        if ( !$this->is_new_item ) {
                                $current_shipping = $this->order->get_items( 'shipping' );
                        } else {
                                $current_shipping = array();
                        }

                        $item_shipping_method = explode( $delimiter, $item_shipping_method );

                        foreach ( $item_shipping_method as $key => $method ) {

                                if ( empty( $method ) ) {
                                        continue;
                                }

                                $_method = false;

                                $_name = isset( $shipping_name[ $key ] ) ? $shipping_name[ $key ] : "";

                                $_amount = isset( $shipping_amount[ $key ] ) ? $shipping_amount[ $key ] : 0;

                                $item_meta = isset( $item_metas[ $key ] ) ? $item_metas[ $key ] : "";

                                if ( !empty( $shipping_methods ) ) {

                                        foreach ( $shipping_methods as $shipping_method_slug => $shipping_method ) {

                                                if ( $shipping_method_slug == str_replace( " ", "_", strtolower( trim( $method ) ) ) || $shipping_method->method_title == $method ) {
                                                        $_method = $shipping_method;
                                                        break;
                                                }
                                        }
                                }

                                if ( !$_method && !empty( $shipping_zone_methods ) ) {

                                        foreach ( $shipping_zone_methods as $shipping_zone_method ) {

                                                if ( isset( $shipping_zone_method->title ) && $shipping_zone_method->title == $method ) {

                                                        $_method = $shipping_zone_method;

                                                        break;
                                                }
                                        }
                                }

                                if ( $_method ) {

                                        $shipping_rate = new \WC_Shipping_Rate( $_method->id, $_name, $_amount );

                                        $total_shipping += $_amount;

                                        $item_id = false;

                                        if ( !empty( $current_shipping ) ) {

                                                foreach ( $current_shipping as $order_item_id => $order_item ) {
                                                        if ( $shipping_rate->get_method_id() == $order_item->get_method_id() ) {
                                                                $item_id = $order_item_id;
                                                                break;
                                                        }
                                                }
                                        }

                                        if ( $item_id ) {

                                                $this->update_shipping( $item_id, $shipping_rate );
                                        } else {
                                                $item_id = $this->add_shipping( $shipping_rate );
                                        }

                                        $metas = $this->getItemMeta( $item_meta );

                                        if ( intval( $item_id ) > 0 && !empty( $metas ) ) {
                                                $this->updateMeta( $item_id, $metas );
                                        }

                                        unset( $shipping_rate, $item_id, $shipping_item );
                                }

                                unset( $_name, $_amount, $_method );
                        }

                        unset( $shipping_methods, $shipping_zone_methods, $shipping_name, $shipping_amount, $current_shipping, $item_shipping_method );
                }

                $this->order->set_shipping_total( $total_shipping );

                unset( $delimiter, $item_shipping_method, $total_shipping );
        }

        private function update_shipping( $item_id = 0, $shipping_rate = array() ) {

                $item = new \WC_Order_Item_Shipping( $item_id );

                $item->set_shipping_rate( $shipping_rate );

                $shipping_id = $item->save();

                if ( !$shipping_id ) {
                        $this->addon_log[] = "<strong>" . __( 'WARNING', 'vj-wp-import-export' ) . '</strong> : ' . __( 'Unable to update order shipping line.', 'vj-wp-import-export' );
                }

                unset( $item, $shipping_id );
        }

        private function add_shipping( $shipping_rate ) {

                $item = new \WC_Order_Item_Shipping();

                $item->set_order_id( $this->item_id );

                $item->set_shipping_rate( $shipping_rate );

                $shipping_id = $item->save();

                $this->order->add_item( $item );

                unset( $item );

                return $shipping_id;
        }

        private function getItemMeta( $meta = "" ) {

                $metaList = [];

                if ( empty( $meta ) ) {
                        return $metaList;
                }

                $singleMeta = explode( "~=~", $meta );

                foreach ( $singleMeta as $metadata ) {

                        if ( strpos( $metadata, "==>" ) === false ) {
                                continue;
                        }

                        $metainfo = explode( "==>", $metadata );

                        $key = isset( $metainfo[ 0 ] ) ? $metainfo[ 0 ] : "";

                        if ( empty( $key ) ) {
                                continue;
                        }
                        $val = isset( $metainfo[ 1 ] ) ? $metainfo[ 1 ] : "";

                        $metaList[ $key ] = $val;
                }


                return $metaList;
        }

        private function updateMeta( $id, $metas ) {
                if ( intval( $id ) < 1 || empty( $metas ) ) {
                        return;
                }

                foreach ( $metas as $key => $value ) {
                        \wc_update_order_item_meta( $id, $key, $value );
                }
        }

        public function __destruct() {

                parent::__destruct();

                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
