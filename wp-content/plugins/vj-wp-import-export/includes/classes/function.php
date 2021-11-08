<?php

if( !defined( 'ABSPATH' ) )
{
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if( !function_exists( "wpie_sanitize_field" ) )
{

        function wpie_sanitize_field( $var )
        {
                if( empty( $var ) )
                {
                        return $var;
                }
                if( is_array( $var ) )
                {
                        return array_map( 'wpie_sanitize_field', $var );
                }
                else
                {
                        return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
                }
        }

}
if( !function_exists( "wpie_sanitize_textarea" ) )
{

        function wpie_sanitize_textarea( $var )
        {
                if( empty( $var ) )
                {
                        return $var;
                }
                return implode( "\n", array_map( 'wpie_sanitize_field', explode( "\n", $var ) ) );
        }

}

if( !function_exists( "wpie_plugin_updater" ) )
{

        function wpie_plugin_updater()
        {

                if( file_exists( WPIE_CLASSES_DIR . '/class-wpie-license-manager.php' ) )
                {
                        require_once(WPIE_CLASSES_DIR . '/class-wpie-license-manager.php');
                }

                $license = new \wpie\license\WPIE_License_Manager(
                        WPIE_PLUGIN_API,
                        WPIE_PLUGIN_FILE,
                        array( 'version' => WPIE_PLUGIN_VERSION, 'license_db_key' => "wpie_license", 'author' => 'vjinfotech' )
                );

                $license->init();

                unset( $license );
        }

}
add_action( 'init', 'wpie_plugin_updater', 0 );

add_action( 'init', 'wpie_remove_draft_entries' );

if( !function_exists( "wpie_remove_draft_entries" ) )
{

        function wpie_remove_draft_entries()
        {

                global $wpdb;

                $current_time = date( 'Y-m-d H:i:s', strtotime( '-1 hour', strtotime( current_time( "mysql" ) ) ) );

                $wpdb->query( $wpdb->prepare( "DELETE  FROM {$wpdb->prefix}wpie_template WHERE opration = 'import-draft' AND last_update_date < %s", $current_time ) );

                unset( $current_time );
        }

}

if( !function_exists( "wpie_get_export_id" ) )
{

        function wpie_get_export_id()
        {

                global $wpie_export_id;

                return empty( $wpie_export_id ) ? 0 : absint( $wpie_export_id );
        }

}
if( !function_exists( "wpie_load_vendor_autoloader" ) )
{

        function wpie_load_vendor_autoloader()
        {
                $file = "";

                if( version_compare( phpversion(), "7.3", '<' ) )
                {
                        $file = 'vendor_deprecated/autoload.php';
                }
                else
                {
                        $file = 'vendor/autoload.php';
                }

                if( file_exists( WPIE_PLUGIN_DIR . $file ) )
                {
                        require_once(WPIE_PLUGIN_DIR . $file );
                }
        }

}
