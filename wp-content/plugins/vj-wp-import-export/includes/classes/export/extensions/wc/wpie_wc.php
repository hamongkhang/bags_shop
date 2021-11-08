<?php


if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

class WPIE_WC_Export_Extension {

        public function __construct() {

                global $woocommerce;

                $plugins = get_option( 'active_plugins' );

                if ( !function_exists( 'is_plugin_active_for_network' ) ) {
                        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
                }

                $wc_plugin = 'woocommerce/woocommerce.php';

                if ( in_array( $wc_plugin, $plugins ) || is_plugin_active_for_network( $wc_plugin ) ) {

                        if ( class_exists( 'Woocommerce' ) ) {
                                $this->init_wc();
                        } else {
                                add_action( 'woocommerce_loaded', array( $this, 'init_wc' ) );
                        }
                }

                unset( $plugins, $wc_plugin );
        }

        public function init_wc() {

                add_filter( 'wpie_prepare_user_fields', array( $this, 'prepare_wc_addon' ), 10, 2 );

                add_filter( 'wpie_prepare_post_fields', array( $this, 'prepare_wc_addon' ), 10, 2 );

                add_filter( 'wpie_prepare_export_addons', array( $this, 'prepare_wc_addon' ), 10, 2 );

                add_filter( 'wpie_export_advance_option_files', array( $this, 'add_order_advance_option' ), 10, 1 );

                add_filter( 'wpie_apply_post_filter', array( $this, 'wpie_apply_post_filter' ), 10, 3 );
        }

        public function prepare_wc_addon( $addons = array(), $export_type = array( "post" ) ) {

                $class = "";

                $fileName = "";

                if ( in_array( "product", $export_type ) ) {

                        $fileName = WPIE_EXPORT_CLASSES_DIR . '/extensions/wc/class-wpie-wc-product.php';

                        $class = '\wpie\export\wc\product\WPIE_WC_Product';
                } elseif ( in_array( "shop_coupon", $export_type ) ) {

                        $fileName = WPIE_EXPORT_CLASSES_DIR . '/extensions/wc/class-wpie-wc-coupon.php';

                        $class = '\wpie\export\wc\coupon\WPIE_WC_Coupon';
                } elseif ( in_array( "shop_order", $export_type ) ) {

                        $fileName = WPIE_EXPORT_CLASSES_DIR . '/extensions/wc/class-wpie-wc-order.php';

                        $class = '\wpie\export\wc\order\WPIE_WC_Order';
                } elseif ( in_array( "shop_customer", $export_type ) ) {

                        $fileName = WPIE_EXPORT_CLASSES_DIR . '/extensions/wc/class-wpie-wc-customer.php';

                        $class = '\wpie\export\wc\WPIE_WC_Customer';
                }

                if ( $fileName != "" && file_exists( $fileName ) ) {

                        require_once($fileName);
                }

                if ( $class != "" && !in_array( $class, $addons ) ) {
                        $addons[] = $class;
                }

                unset( $class, $fileName );

                return $addons;
        }

        public function add_order_advance_option( $files = [] ) {

                $fileName = WPIE_EXPORT_CLASSES_DIR . '/extensions/wc/wc-advance_option.php';

                if ( !in_array( $fileName, $files ) ) {
                        $files[] = $fileName;
                }

                return $files;
        }

        public function wpie_apply_post_filter( $data = [], $export_type = [], $filter = [] ) {

                if ( !empty( $export_type ) ) {
                        if ( in_array( "shop_order", $export_type ) ) {
                                return $this->apply_order_filter( $data, $filter );
                        } elseif ( in_array( "product", $export_type ) ) {
                                return $this->apply_product_filter( $data, $filter );
                        }
                }

                return $data;
        }

        public function apply_order_filter( $data = [], $filter = [] ) {

                $fileName = WPIE_EXPORT_CLASSES_DIR . '/extensions/wc/class-wpie-wc-order-filter.php';

                if ( !file_exists( $fileName ) ) {
                        return $data;
                }

                require_once $fileName;

                $order_filter = new wpie\export\wc\filter\WPIE_WC_Order_Filter();

                $new_data = $order_filter->apply_order_filter( $data, $filter );

                unset( $order_filter );

                return $new_data;
        }

        public function apply_product_filter( $data = [], $filter = [] ) {

                $fileName = WPIE_EXPORT_CLASSES_DIR . '/extensions/wc/class-wpie-wc-product-filter.php';

                if ( !file_exists( $fileName ) ) {
                        return $data;
                }

                require_once $fileName;

                $product_filter = new wpie\export\wc\filter\WPIE_WC_Product_Filter();

                $new_data = $product_filter->apply_product_filter( $data, $filter );

                unset( $product_filter );

                return $new_data;
        }

}

new WPIE_WC_Export_Extension();
