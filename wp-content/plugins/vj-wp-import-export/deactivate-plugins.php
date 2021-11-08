<?php

if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if ( ! function_exists( "wpie_auto_deactivate_plugins" ) ) {

        function wpie_auto_deactivate_plugins() {

                $plugins = [];

                if ( is_plugin_active( 'woo-import-export/woo-import-export.php' ) ) {
                        $plugins[] = 'woo-import-export/woo-import-export.php';
                }
                if ( is_plugin_active( 'wp-import-export-lite/wp-import-export-lite.php' ) ) {
                        $plugins[] = 'wp-import-export-lite/wp-import-export-lite.php';
                }
                if ( ! empty( $plugins ) ) {
                        deactivate_plugins( $plugins );
                }
        }

}
