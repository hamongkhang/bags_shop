<?php

namespace wpie\export\wc\filter;

use wpie\export\post\WPIE_Post;

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

class WPIE_WC_Product_Filter extends WPIE_Post {

        public function apply_product_filter( $data = [], $filter = [] ) {

                if ( "wc-product-attr" !== $filter[ 'element' ] ) {
                        return $data;
                }

                if ( intval( $filter[ 'isTax' ] ) === 1 ) {
                        $filter[ 'element' ] = "wpie_tax";
                } else {
                        $filter[ 'element' ] = "wpie_cf";
                }

                $this->parse_rule( $filter );

                if ( ! empty( $this->item_join ) ) {
                        $data[ 'item_join' ] = isset( $data[ 'item_join' ] ) ? array_merge( $data[ 'item_join' ], $this->item_join ) : $this->item_join;
                }
                if ( ! empty( $this->item_where ) ) {
                        $data[ 'item_where' ] = isset( $data[ 'item_where' ] ) ? array_merge( $data[ 'item_where' ], $this->item_where ) : $this->item_where;
                }

                return $data;
        }

}
