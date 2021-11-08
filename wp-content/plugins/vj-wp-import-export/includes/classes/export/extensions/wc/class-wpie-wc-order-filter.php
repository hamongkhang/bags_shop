<?php

namespace wpie\export\wc\filter;

use wpie\export\post\WPIE_Post;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

class WPIE_WC_Order_Filter extends WPIE_Post {

        private $_join = [];
        private $_where = [];

        public function apply_order_filter( $data = [], $filter = [] ) {

                if ( empty( $filter[ 'element' ] ) || strpos( $filter[ 'element' ], "wc-order" ) !== 0 ) {
                        return $data;
                }

                global $wpdb;
                $filter[ 'condition' ] = isset( $filter[ 'condition' ] ) ? $filter[ 'condition' ] : "";

                if ( isset( $filter[ 'field_type' ] ) && $filter[ 'field_type' ] === "item" ) {

                        if ( isset( $filter[ 'field_key' ] ) ) {
                                switch ( $filter[ 'field_key' ] ) {
                                        case '_product_sku':
                                                $product_filter = $filter;
                                                $product_filter[ 'metaKey' ] = "_sku";
                                                $product_filter[ 'element' ] = "wpie_cf";
                                                $this->filter_by_product( $product_filter );
                                                break;
                                        case '_product_title':
                                                $product_filter = $filter;
                                                $product_filter[ 'element' ] = "post_title";
                                                $this->filter_by_product( $product_filter );
                                                break;
                                        default:
                                                $item_alias = 'order_item' . time();
                                                $item_meta_alias = 'order_itemmeta' . time();
                                                if ( $filter[ 'condition' ] === 'is_empty' ) {
                                                        $this->_join[] = " LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS $item_alias ON ({$wpdb->posts}.ID = $item_alias.order_id) ";
                                                        $this->_join[] = " LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS $item_meta_alias ON ($item_alias.order_item_id = $item_meta_alias.order_item_id AND $item_meta_alias.meta_key = '{$filter[ 'field_key' ]}') ";
                                                        $this->_where[] = "$item_meta_alias.meta_id " . $this->add_filter_rule( $filter, true, $item_meta_alias );
                                                } else {

                                                        $this->_join[] = " INNER JOIN {$wpdb->prefix}woocommerce_order_items AS $item_alias ON ({$wpdb->posts}.ID = $item_alias.order_id) ";
                                                        $this->_join[] = " INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS $item_meta_alias ON ($item_alias.order_item_id = $item_meta_alias.order_item_id) ";
                                                        $this->_where[] = "$item_meta_alias.meta_key = '{$filter[ 'field_key' ]}' AND $item_meta_alias.meta_value " . $this->add_filter_rule( $filter, false, $item_meta_alias );
                                                }
                                                break;
                                }
                        }
                } elseif ( strpos( $filter[ 'element' ], "wc-order-item" ) === 0 ) {
                        $this->filter_by_product( $filter );
                }

                if ( ! empty( $this->_join ) ) {
                        $data[ 'item_join' ] = isset( $data[ 'item_join' ] ) ? array_merge( $data[ 'item_join' ], $this->_join ) : $this->_join;
                }
                if ( ! empty( $this->_where ) ) {
                        $data[ 'item_where' ] = isset( $data[ 'item_where' ] ) ? array_merge( $data[ 'item_where' ], $this->_where ) : $this->_where;
                }

                return $data;
        }

        private function filter_by_product( $filter ) {

                $filter[ 'element' ] = preg_replace( '%^wc-order-item-%', '', $filter[ 'element' ] );

                $this->export_type = array( "product", "product_variation" );

                $this->parse_rule( $filter );

                $query = array(
                        'post_type'      => array( 'product', 'product_variation' ),
                        'post_status'    => array_keys( get_post_stati() ),
                        'orderby'        => "ID",
                        'order'          => 'ASC',
                        'fields'         => 'ids',
                        'offset'         => 0,
                        'posts_per_page' => -1
                );

                add_filter( 'posts_where', array( $this, 'posts_where' ), 10, 1 );

                add_filter( 'posts_join', array( $this, 'posts_join' ), 10, 1 );

                $post_result = new \WP_Query( $query );

                remove_filter( 'posts_where', array( $this, 'posts_where' ), 10, 1 );

                remove_filter( 'posts_join', array( $this, 'posts_join' ), 10, 1 );

                $ids = $post_result->posts;

                if ( empty( $ids ) ) {
                        $ids = [ -1 ];
                }
                global $wpdb;
                $clause = isset( $filter[ 'clause' ] ) ? $filter[ 'clause' ] : "";
                $ids_str = implode( ",", $ids );
                $item_alias = 'order_item_' . time();
                $item_meta_alias = 'order_itemmeta_' . time();
                $this->_join[] = " INNER JOIN {$wpdb->prefix}woocommerce_order_items AS $item_alias ON ({$wpdb->posts}.ID = $item_alias.order_id) ";
                $this->_join[] = " INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS $item_meta_alias ON ($item_alias.order_item_id = $item_meta_alias.order_item_id) ";
                $this->_where[] = "($item_meta_alias.meta_key = '_product_id' OR $item_meta_alias.meta_key = '_variation_id') AND $item_meta_alias.meta_value IN ($ids_str)" . " " . $clause . " ";


                unset( $query, $post_result, $ids );
        }

}
