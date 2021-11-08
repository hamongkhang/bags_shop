<?php

namespace wpie\import\wc\order\refunds;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}
if ( file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php' ) ) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php');
}

class WPIE_Order_Refunds extends \wpie\import\base\WPIE_Import_Base {

        public function __construct( $wpie_import_option = array(), $wpie_import_record = array(), $item_id = 0, $is_new_item = true, &$addon_error = false, &$addon_log = array() ) {

                $this->wpie_import_option = $wpie_import_option;

                $this->wpie_import_record = $wpie_import_record;

                $this->item_id = $item_id;

                $this->is_new_item = $is_new_item;

                $this->addon_error = &$addon_error;

                $this->addon_log = &$addon_log;

                $this->prepare_refunds();
        }

        private function prepare_refunds() {

                $refund_amount = wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_refund_amount' ) );

                if ( ! empty( $refund_amount ) ) {

                        global $wpdb;

                        $delimiter = wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_refund_delim' ) );

                        $refund_amount = explode( $delimiter, $refund_amount );

                        $refund_reason = wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_refund_reason' ) );

                        $refund_reason = ! empty( $refund_reason ) ? explode( $delimiter, $refund_reason ) : array();

                        $refund_date = wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_refund_date' ) );

                        $refund_date = ! empty( $refund_date ) ? explode( $delimiter, $refund_date ) : array();

                        $refund_post_name = wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_refund_name' ) );

                        $refund_post_name = ! empty( $refund_post_name ) ? explode( $delimiter, $refund_post_name ) : array();

                        foreach ( $refund_amount as $_key => $_amount ) {

                                $refund_name = isset( $refund_post_name[ $_key ] ) ? $refund_post_name[ $_key ] : "";

                                $refund_id = 0;

                                if ( ! empty( $refund_name ) ) {

                                        $_post = $wpdb->get_row(
                                                $wpdb->prepare(
                                                        "SELECT ID FROM " . $wpdb->posts . "
                                WHERE
                                    `post_type` = 'shop_order_refund'
                                    AND `post_name` = %s
                                    AND `post_parent` = %d
                                LIMIT 1
                                ", $refund_name, $this->item_id
                                                )
                                        );


                                        if ( $_post && isset( $_post->ID ) ) {
                                                $refund_id = $_post->ID;
                                        }
                                        unset( $_post );
                                }

                                $r_date_created = isset( $refund_date[ $_key ] ) ? $refund_date[ $_key ] : "";
                                if ( empty( trim( $r_date_created ) ) || strtotime( $r_date_created ) === false ) {
                                        $r_date_created = current_time( 'mysql' );
                                }
                                $args = array(
                                        'amount' => abs( floatval( $_amount ) ),
                                        'reason' => isset( $refund_reason[ $_key ] ) ? $refund_reason[ $_key ] : "",
                                        'order_id' => $this->item_id,
                                        'refund_id' => $refund_id,
                                        'line_items' => array(),
                                        'date_created' => $r_date_created
                                );

                                $refund = wc_create_refund( $args );

                                if ( $refund instanceOf \WC_Order_Refund ) {

                                        $customer_id = false;

                                        if ( wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_refund_issued_by' ) ) == 'existing' ) {
                                                $customer_id = $this->get_customer_id_by( wpie_sanitize_field( $this->get_field_value( 'wpie_item_order_item_refund_match_by' ) ) );
                                        }

                                        if ( $customer_id ) {

                                                wp_update_post( array( 'ID' => $refund->get_id(),
                                                        'post_author' => $customer_id ) );

                                                update_post_meta( $refund->get_id(), '_refunded_by', $customer_id );
                                        } else {

                                                wp_update_post( array( 'ID' => $refund->get_id(),
                                                        'post_author' => 0 ) );

                                                delete_post_meta( $refund->get_id(), '_refunded_by' );
                                        }
                                }
                                unset( $args, $refund );
                        }

                        unset( $delimiter, $refund_reason, $refund_date );
                }

                unset( $refund_amount );
        }

        private function get_customer_id_by( $match_by = "" ) {

                $user_id = false;

                switch ( $match_by ) {

                        case "id":

                                $customer_id = absint( wpie_sanitize_field( $this->get_field_value( 'wpie_item_refund_customer_id' ) ) );

                                if ( $customer_id > 0 ) {
                                        $user = get_user_by( 'id', absint( $customer_id ) );

                                        if ( $user ) {
                                                $user_id = $duplicate_id;
                                        }
                                        unset( $user );
                                }

                                unset( $customer_id );

                                break;

                        case "email":

                                $email = wpie_sanitize_field( $this->get_field_value( 'wpie_item_refund_customer_email' ) );

                                if ( ! empty( $email ) ) {
                                        $user = get_user_by( 'email', $email );

                                        if ( $user ) {
                                                $user_id = $user->ID;
                                        }
                                        unset( $user );
                                }

                                unset( $email );

                                break;
                        case "login":

                                $user_login = wpie_sanitize_field( $this->get_field_value( 'wpie_item_refund_customer_login' ) );

                                if ( ! empty( $user_login ) ) {
                                        $user = get_user_by( 'login', $user_login );

                                        if ( $user ) {
                                                $user_id = $user->ID;
                                        }
                                        unset( $user );
                                }

                                unset( $user_login );

                                break;

                        case "cf":

                                $meta_key = wpie_sanitize_field( $this->get_field_value( 'wpie_item_refund_customer_meta_key' ) );

                                $meta_val = wpie_sanitize_field( $this->get_field_value( 'wpie_item_refund_customer_meta_val' ) );

                                $user_query = array(
                                        'meta_query' => array(
                                                0 => array(
                                                        'key' => $meta_key,
                                                        'value' => $meta_val,
                                                        'compare' => '='
                                                )
                                        )
                                );

                                $user_data = new \WP_User_Query( $user_query );

                                unset( $user_query );

                                if ( ! empty( $user_data->results ) ) {
                                        foreach ( $user_data->results as $user ) {
                                                $user_id = $user->ID;
                                                break;
                                        }
                                } else {
                                        $user_data_found = $wpdb->get_results( $wpdb->prepare( "SELECT SQL_CALC_FOUND_ROWS " . $wpdb->users . ".ID FROM " . $wpdb->users . " INNER JOIN " . $wpdb->usermeta . " ON (" . $wpdb->users . ".ID = " . $wpdb->usermeta . ".user_id) WHERE 1=1 AND ( (" . $wpdb->usermeta . ".meta_key = %s AND " . $wpdb->usermeta . ".meta_value = %s) ) GROUP BY " . $wpdb->users . ".ID ORDER BY " . $wpdb->users . ".ID ASC LIMIT 0, 1", $meta_key, $meta_val ) );

                                        if ( ! empty( $user_data_found ) ) {
                                                foreach ( $user_data_found as $user ) {
                                                        $user_id = $user->ID;
                                                        break;
                                                }
                                        }
                                        unset( $user_data_found );
                                }
                                unset( $meta_key, $meta_val, $user_data );
                                break;
                }
                return $user_id;
        }

        public function __destruct() {

                parent::__destruct();

                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
