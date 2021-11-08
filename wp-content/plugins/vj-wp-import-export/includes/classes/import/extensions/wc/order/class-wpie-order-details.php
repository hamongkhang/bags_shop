<?php

namespace wpie\import\wc\order\details;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

class WPIE_Order_Details {

        public function __construct( $item = null, $is_new_item = false ) {

                if ( ! $item ) {
                        return;
                }

                $order_status = $item->post_status;

                $order_statuses = wc_get_order_statuses();

                $is_update_status = false;

                if ( ! isset( $order_statuses[ $order_status ] ) ) {
                        $order_status = 'wc-pending';
                        $is_update_status = true;
                }

                if ( $is_new_item ) {

                        $order_data = array (
                                'ID'            => $item->ID,
                                'post_title'    => 'Order &ndash; ' . date_i18n( 'F j, Y @ h:i A', strtotime( $item->post_date ) ),
                                'post_password' => $this->get_order_password(),
                                'post_status'   => $order_status,
                        );

                        $order_id = wp_update_post( $order_data, true );

                        unset( $order_data, $order_id );
                } elseif ( $is_update_status ) {

                        $order_data = array (
                                'ID'          => $item->ID,
                                'post_status' => $order_status,
                        );

                        $order_id = wp_update_post( $order_data, true );

                        unset( $order_data, $order_id );
                }

                unset( $order_status, $order_statuses, $is_update_status );
        }

        private function get_order_password() {

                if ( function_exists( "wc_generate_order_key" ) ) {
                        return wc_generate_order_key();
                } elseif ( function_exists( "wp_generate_password" ) ) {
                        return 'wc_' . apply_filters( 'woocommerce_generate_order_key', 'order_' . wp_generate_password( 13, false ) );
                } else {
                        return uniqid( 'order_' );
                }
        }

        public function __destruct() {

                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}
