<?php


namespace wpie\import\wc\order\item;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}
if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php');
}

class WPIE_Order_Product_Item extends \wpie\import\base\WPIE_Import_Base {

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

                $this->order->remove_order_items( 'line_item' );

                $this->prepare_line_item();
        }

        private function prepare_line_item() {

                $delimiter = wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_product_delim' ) );

                $product_name = $this->get_field_value( 'wpie_item_order_item_product_name' );

                if ( !$this->is_new_item ) {
                        $current_product = $this->order->get_items();
                } else {
                        $current_product = array();
                }

                if ( !empty( $product_name ) ) {

                        if ( empty( $delimiter ) ) {
                                $delimiter = "|";
                        }

                        $product_name = explode( $delimiter, $product_name );

                        $product_price = wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_product_price' ) );

                        if ( !empty( $product_price ) ) {
                                $product_price = explode( $delimiter, $product_price );
                        }

                        $product_quantity = wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_product_quantity' ) );

                        if ( !empty( $product_quantity ) ) {
                                $product_quantity = explode( $delimiter, $product_quantity );
                        }

                        $product_sku = wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_product_sku' ) );

                        if ( !empty( $product_sku ) ) {
                                $product_sku = explode( $delimiter, $product_sku );
                        }

                        $is_variation = wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_is_variation' ) );
                        if ( !empty( $is_variation ) ) {
                                $is_variation = explode( $delimiter, $is_variation );
                        } else {
                                $is_variation = [];
                        }

                        $original_product_title = wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_original_product_title' ) );
                        if ( !empty( $original_product_title ) ) {
                                $original_product_title = explode( $delimiter, $original_product_title );
                        } else {
                                $original_product_title = [];
                        }

                        $variation_attributes = wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_variation_attributes' ) );

                        if ( !empty( $variation_attributes ) ) {
                                $variation_attributes = explode( $delimiter, $variation_attributes );
                        } else {
                                $variation_attributes = [];
                        }
                        $item_metas = $this->get_field_value( 'wpie_item_order_item_meta' );

                        $item_metas = empty( $item_metas ) ? [] : explode( $delimiter, $item_metas );

                        foreach ( $product_name as $key => $name ) {

                                if ( empty( trim( $name ) ) ) {
                                        continue;
                                }

                                $sku = isset( $product_sku[ $key ] ) ? $product_sku[ $key ] : "";

                                $quantity = isset( $product_quantity[ $key ] ) ? absint( $product_quantity[ $key ] ) : 1;

                                $price = isset( $product_price[ $key ] ) ? $product_price[ $key ] : 0;

                                $_is_variation = isset( $is_variation[ $key ] ) ? absint( $is_variation[ $key ] ) : 0;

                                $_product_title = isset( $original_product_title[ $key ] ) ? $original_product_title[ $key ] : "";

                                $_variation_attributes = isset( $variation_attributes[ $key ] ) ? $variation_attributes[ $key ] : "";

                                $item_meta = isset( $item_metas[ $key ] ) ? $item_metas[ $key ] : "";

                                $subtotal = $this->getSubtotal( $price, $quantity );

                                $product_id = 0;

                                $product = false;

                                $item_id = false;

                                if ( !empty( $sku ) ) {
                                        $product_id = $this->get_product_by_meta( "_sku", $sku );
                                }
                                if ( absint( $product_id ) === 0 ) {

                                        $product_id = $this->find_product( $_product_title, $_is_variation, $_variation_attributes );
                                }

                                if ( absint( $product_id ) > 0 ) {

                                        if ( !empty( $current_product ) ) {

                                                foreach ( $current_product as $order_item_id => $order_item ) {

                                                        $item_product_id = $order_item[ 'product_id' ] ? $order_item[ 'product_id' ] : 0;

                                                        if ( $item_product_id == $product_id ) {

                                                                $item_id = $order_item_id;

                                                                break;
                                                        }

                                                        unset( $item_product_id );
                                                }
                                        }

                                        $product = \wc_get_product( $product_id );
                                }

                                $metas = $this->getItemMeta( $item_meta );

                                $line_item = array(
                                        'name' => $name,
                                        'tax_class' => "",
                                        'product_id' => $product && $product->is_type( 'variation' ) ? $product->get_parent_id() : $product_id,
                                        'variation_id' => $product && $product->is_type( 'variation' ) ? $product->get_id() : 0,
                                        'variation' => $product && $product->is_type( 'variation' ) ? $product->get_attributes() : array(),
                                        'subtotal' => $subtotal,
                                        'total' => $subtotal,
                                        'quantity' => $quantity,
                                        'meta' => $metas,
                                );

                                if ( $item_id ) {
                                        $this->update_product( $item_id, $line_item, $product );
                                } else {
                                        $item_id = $this->add_product( $line_item, $product );
                                }

                                if ( intval( $item_id ) > 0 && !empty( $metas ) ) {
                                        $this->updateMeta( $item_id, $metas );
                                }
                        }
                }
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

        private function getSubtotal( $price, $quantity ) {

                if ( floatval( $quantity ) == 0 || floatval( $price ) == 0 ) {
                        return 0;
                }

                return wc_format_decimal( $price ) * absint( $quantity );
        }

        private function update_product( $item_id = 0, $line_item = array(), $product ) {

                $item = new \WC_Order_Item_Product( $item_id );

                if ( isset( $line_item[ 'quantity' ] ) ) {
                        $item->set_quantity( $line_item[ 'quantity' ] );
                }
                if ( isset( $line_item[ 'total' ] ) ) {
                        $item->set_total( floatval( $line_item[ 'total' ] ) );
                }
                if ( isset( $line_item[ 'total_tax' ] ) ) {
                        $item->set_total_tax( floatval( $line_item[ 'total_tax' ] ) );
                }
                if ( isset( $line_item[ 'subtotal' ] ) ) {
                        $item->set_subtotal( floatval( $line_item[ 'subtotal' ] ) );
                }
                if ( isset( $line_item[ 'subtotal_tax' ] ) ) {
                        $item->set_subtotal_tax( floatval( $line_item[ 'subtotal_tax' ] ) );
                }

                $item->save();
        }

        private function add_product( $line_item = [], $product ) {

                if ( $product === false ) {
                        return $this->manually_add_product( $line_item );
                }

                $item = new \WC_Order_Item_Product();

                $item->set_props( $line_item );

                $item->set_backorder_meta();

                $item->set_order_id( $this->item_id );

                $id = $item->save();

                $this->order->add_item( $item );

                return $id;
        }

        private function manually_add_product( $item = [] ) {

                $item_id = wc_add_order_item( $this->item_id, array(
                        'order_item_name' => $item[ 'name' ],
                        'order_item_type' => 'line_item'
                        ) );

                if ( $item_id === false ) {
                        return $item_id;
                }

                $item_qty = isset( $item[ 'quantity' ] ) ? absint( $item[ 'quantity' ] ) : 0;

                $tax_class = isset( $item[ 'tax_class' ] ) ? $item[ 'tax_class' ] : "";

                $subtotal = isset( $item[ 'subtotal' ] ) ? $item[ 'subtotal' ] : 0;

                $subtotal_tax = isset( $item[ 'subtotal_tax' ] ) ? $item[ 'subtotal_tax' ] : 0;

                $total = isset( $item[ 'total' ] ) ? $item[ 'total' ] : 0;

                $total_tax = isset( $item[ 'total_tax' ] ) ? $item[ 'total_tax' ] : 0;

                wc_add_order_item_meta( $item_id, '_qty', wc_stock_amount( $item_qty ) );

                wc_add_order_item_meta( $item_id, '_tax_class', '' );

                wc_add_order_item_meta( $item_id, '_line_subtotal', wc_format_decimal( $subtotal ) );

                wc_add_order_item_meta( $item_id, '_line_total', wc_format_decimal( $total ) );

                wc_add_order_item_meta( $item_id, '_line_subtotal_tax', wc_format_decimal( $subtotal_tax ) );

                wc_add_order_item_meta( $item_id, '_line_tax', wc_format_decimal( $total_tax ) );

                wc_add_order_item_meta( $item_id, '_line_tax_data', [
                        'total' => $total_tax,
                        'subtotal' => $total_tax
                ] );

                unset( $item_qty, $tax_class, $subtotal, $subtotal_tax, $total, $total_tax );

                return $item_id;
        }

        private function updateMeta( $id, $metas ) {
                if ( intval( $id ) < 1 || empty( $metas ) ) {
                        return;
                }

                foreach ( $metas as $key => $value ) {
                        \wc_update_order_item_meta( $id, $key, $value );
                }
        }

        private function get_product_by_meta( $meta_key = "_sku", $meta_val = "" ) {

                if ( empty( $meta_val ) ) {
                        return 0;
                }
                global $wpdb;

                $product_id = $wpdb->get_var(
                        $wpdb->prepare(
                                "SELECT posts.ID
				FROM $wpdb->posts AS posts
				LEFT JOIN $wpdb->postmeta AS postmeta ON ( posts.ID = postmeta.post_id )
				WHERE posts.post_type IN ( 'product', 'product_variation' )
					AND posts.post_status != 'trash'
					AND postmeta.meta_key = %s
					AND postmeta.meta_value = %s
				LIMIT 1",
                                $meta_key,
                                $meta_val
                        )
                );

                unset( $meta_key, $meta_val );

                return $product_id;
        }

        private function find_product( $product_title = "", $is_variation = 0, $attributes = "" ) {

                if ( empty( $product_title ) ) {
                        return 0;
                }

                global $wpdb;

                $product_id = 0;

                $_post = $wpdb->get_var(
                        $wpdb->prepare(
                                "SELECT ID FROM " . $wpdb->posts . "
                                                WHERE
                                                    post_type = 'product'
                                                    AND ID != 0
                                                    AND  ( `post_title` = %s OR `post_title` = %s )
                                                LIMIT 1
                                ", wpie_sanitize_field( $product_title ), $product_title
                        )
                );

                $post_id = 0;

                if ( $_post && absint( $_post ) > 0 ) {
                        $post_id = absint( $_post );
                }

                if ( absint( $post_id ) > 0 ) {

                        if ( $is_variation === 1 ) {
                                $product_id = $this->get_variation_by_attribute( $post_id, $attributes );
                        } else {
                                $product_id = $post_id;
                        }
                }


                return $product_id;
        }

        private function get_variation_by_attribute( $parent_id = 0, $attributes = "" ) {

                if ( empty( $parent_id ) || empty( $attributes ) ) {
                        return 0;
                }

                $attributes = json_decode( $attributes );

                if ( !empty( $attributes ) ) {

                        global $wpdb;

                        $join = "";

                        $where = " where ";

                        foreach ( $attributes as $key => $value ) {

                                if ( empty( $value ) ) {
                                        continue;
                                }

                                $attribute = $key;

                                $value2 = wc_sanitize_taxonomy_name( $value );

                                $rand = uniqid() . "__" . rand( 1, 999 );

                                $join .= " JOIN {$wpdb->prefix}postmeta as `pm_{$rand}` ON p.ID = `pm_{$rand}`.`post_id`";

                                $where .= " `pm_{$rand}`.`meta_key` = 'attribute_{$attribute}' AND ( `pm_{$rand}`.`meta_value` LIKE '{$value}' OR `pm_{$rand}`.`meta_value` LIKE '{$value2}' ) AND ";

                                unset( $value );
                        }

                        $query = 'SELECT p.ID FROM ' . $wpdb->prefix . 'posts as p ' . $join . $where . ' p.post_parent =' . $parent_id;

                        unset( $join, $where );

                        return $wpdb->get_var( $query );
                }

                return 0;
        }

        public function __destruct() {

                parent::__destruct();

                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
