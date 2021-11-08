<?php


if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

class WPIE_WC_Import_Extension {

        public function __construct() {

                add_action( 'admin_enqueue_scripts', array( $this, 'wpie_enqueue_wc_scripts' ), 10 );

                add_filter( 'wpie_pre_post_field_mapping_section', array( $this, "wpie_product_field_mapping_section" ), 10, 2 );

                add_filter( 'wpie_pre_user_field_mapping_section', array( $this, "add_customer_options" ), 10, 2 );

                add_filter( 'wpie_import_post_search_existing_item', array( $this, "wpie_import_order_search_existing_item" ), 10, 2 );

                add_filter( 'wpie_import_addon', array( $this, "wc_addon_init" ), 10, 2 );

                add_filter( 'wpie_import_post_update_item_fields', array( $this, "wpie_get_update_fields" ), 10, 2 );
        }

        public function wpie_enqueue_wc_scripts() {

                wp_register_script( 'wpie-import-wc-js', WPIE_IMPORT_ADDON_URL . '/wc/wpie-import-wc.min.js', array( 'jquery' ), WPIE_PLUGIN_VERSION );

                wp_enqueue_script( 'wpie-import-wc-js' );
        }

        public function add_customer_options( $sections = [], $wpie_import_type = "" ) {

                if ( $wpie_import_type == "shop_customer" ) {
                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/wc/customer/wpie-customer-fields.php';

                        if ( file_exists( $fileName ) ) {


                                require_once($fileName);

                                if ( function_exists( "wpie_import_wc_customer_mapping_fields" ) ) {
                                        $sections = wpie_import_wc_customer_mapping_fields( $sections, $wpie_import_type );
                                }
                        }
                        unset( $fileName );
                }
                return $sections;
        }

        public function wpie_product_field_mapping_section( $sections = [], $wpie_import_type = "" ) {

                if ( $wpie_import_type == "product" ) {

                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/wc/product/wpie-product-fields.php';

                        if ( file_exists( $fileName ) ) {

                                require_once($fileName);

                                if ( function_exists( "wpie_import_product_mapping_fields" ) ) {
                                        $sections = wpie_import_product_mapping_fields( $sections, $wpie_import_type );
                                }
                        }
                        unset( $fileName );
                } elseif ( $wpie_import_type == "shop_order" ) {
                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/wc/order/wpie-order-fields.php';

                        if ( file_exists( $fileName ) ) {

                                require_once($fileName);

                                if ( function_exists( "wpie_import_order_mapping_fields" ) ) {
                                        $sections = wpie_import_order_mapping_fields( $sections, $wpie_import_type );
                                }
                        }
                        unset( $fileName );
                } elseif ( $wpie_import_type == "shop_coupon" ) {

                        if ( isset( $sections[ "200" ] ) ) {
                                unset( $sections[ "200" ] );
                        }
                        if ( isset( $sections[ "400" ] ) ) {
                                unset( $sections[ "400" ] );
                        }
                }


                return $sections;
        }

        public function wpie_import_order_search_existing_item( $sections = "", $wpie_import_type = "" ) {

                if ( $wpie_import_type == "shop_order" ) {

                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/wc/order/wpie-order-existing-item-fields.php';

                        if ( file_exists( $fileName ) ) {

                                require_once($fileName);

                                if ( function_exists( "wpie_import_order_search_existing_item" ) ) {
                                        $sections = wpie_import_order_search_existing_item();
                                }
                        }
                        unset( $fileName );
                }
                return $sections;
        }

        public function wpie_get_update_fields( $sections = "", $wpie_import_type = "" ) {

                if ( $wpie_import_type == "shop_order" ) {

                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/wc/order/wpie-order-update-fields.php';

                        if ( file_exists( $fileName ) ) {

                                require_once($fileName);

                                if ( function_exists( "wpie_import_order_update_fields" ) ) {
                                        $sections = wpie_import_order_update_fields();
                                }
                        }
                        unset( $fileName );
                }


                return $sections;
        }

        public function wc_addon_init( $addons = array(), $wpie_import_type = "" ) {

                $fileName = "";

                if ( $wpie_import_type == "product" && !in_array( '\wpie\import\wc\product\WPIE_Product_Import', $addons ) ) {

                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-wpie-product.php';

                        $addons[] = '\wpie\import\wc\product\WPIE_Product_Import';
                } elseif ( $wpie_import_type == "shop_coupon" && !in_array( '\wpie\import\wc\coupon\WPIE_Coupon_Import', $addons ) ) {

                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/wc/coupon/class-wpie-coupon.php';

                        $addons[] = '\wpie\import\wc\coupon\WPIE_Coupon_Import';
                } elseif ( $wpie_import_type == "shop_order" && !in_array( '\wpie\import\wc\order\WPIE_Order_Import', $addons ) ) {

                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/wc/order/class-wpie-order.php';

                        $addons[] = '\wpie\import\wc\order\WPIE_Order_Import';
                } elseif ( $wpie_import_type == "shop_customer" && !in_array( '\wpie\import\wc\WPIE_Customer_Import', $addons ) ) {

                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/wc/customer/class-wpie-customer.php';

                        $addons[] = '\wpie\import\wc\WPIE_Customer_Import';
                }

                if ( !empty( $fileName ) && file_exists( $fileName ) ) {

                        require_once($fileName);
                }

                unset( $fileName );

                return $addons;
        }

        public function __destruct() {
                foreach ( $this as $key => $value ) {
                        unset( $this->$key );
                }
        }

}

new WPIE_WC_Import_Extension();
