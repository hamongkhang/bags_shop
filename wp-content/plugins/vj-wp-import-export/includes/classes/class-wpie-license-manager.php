<?php


namespace wpie\license;

use \WP_Error;

if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

class WPIE_License_Manager {

        private $api_url      = '';
        private $api_data     = array();
        private $name         = '';
        private $slug         = '';
        private $_plugin_file = '';
        private $did_check    = false;
        private $version;
        private $license_db_key;
        private $transient_name;

        function __construct( $_api_url, $_plugin_file, $_api_data = null ) {

                $this->api_url = trailingslashit( $_api_url );

                $this->api_data = urlencode_deep( $_api_data );

                $this->name = plugin_basename( $_plugin_file );

                $this->slug = basename( $_plugin_file, '.php' );

                $this->version = isset( $_api_data[ 'version' ] ) ? wpie_sanitize_field( $_api_data[ 'version' ] ) : 0;

                $this->_plugin_file = $_plugin_file;

                $this->license_db_key = isset( $_api_data[ 'license_db_key' ] ) ? md5( $_api_data[ 'license_db_key' ] ) : "";

                $this->transient_name = md5( 'wpie_' . sanitize_key( $this->name ) . '_plugin_updates' );
        }

        public function init() {

                add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'modify_plugins_transient' ), 10, 1 );

                add_filter( 'plugins_api', array( $this, 'modify_plugin_details' ), 10, 3 );

                if ( is_admin() ) {

                        add_action( 'in_plugin_update_message-' . $this->name, array( $this, 'modify_plugin_update_message' ), 10, 2 );
                }
        }

        function modify_plugin_update_message( $plugin_data, $response ) {

                if ( $this->get_license_key() ) {
                        return;
                }
                $settings = add_query_arg( [ 'page' => "wpie-settings" ], admin_url( "admin.php" ) );

                echo '<br />' . sprintf( __( 'To enable updates, please enter your Purchase Code on the <a href="%s">Updates</a> page.', 'vj-wp-import-export' ), $settings );
        }

        public function modify_plugin_details( $result, $action = null, $args = null ) {

                if ( $action !== 'plugin_information' ) {
                        return $result;
                }

                if ( !isset( $args->slug ) || ( $args->slug != $this->slug ) ) {

                        return $result;
                }

                $response = $this->get_plugin_transient();

                if ( !is_object( $response ) ) {
                        return $result;
                }

                $response->sections = isset( $response->sections ) ? ( array ) $response->sections : array();

                $response->icons = isset( $response->sections ) ? ( array ) $response->icons : array();

                $response->banners = isset( $response->sections ) ? ( array ) $response->banners : array();

                return $response;
        }

        public function modify_plugins_transient( $transient ) {

                if ( !isset( $transient->response ) ) {
                        return $transient;
                }

                $force_check = ($this->did_check === false) ? ( isset( $_GET[ 'force-check' ] ) ? absint( $_GET[ 'force-check' ] ) == 1 : false) : false;

                $update = $this->get_plugin_transient( $force_check );

                if ( is_object( $update ) ) {

                        $res = new \stdClass();

                        $res->slug = $this->slug;

                        $res->plugin = $this->name;

                        $res->new_version = isset( $update->version ) ? $update->version : "";

                        $res->tested = isset( $update->tested ) ? $update->tested : "";

                        $res->url = isset( $update->homepage ) ? $update->homepage : "";

                        $res->icons = ( array ) $update->icons;

                        $res->banners = ( array ) $update->banners;

                        $res->package = "";

                        $license = $this->get_license_key();

                        if ( $license ) {
                                $res->package = $update->update_url . "?vlm_api_action=update_package&license=" . base64_encode( $license ) . "&wp_url=" . home_url() . "&plugin=" . $res->slug;
                        }

                        $res->download_link = $res->package;

                        $transient->response[ $this->name ] = $res;
                }

                $this->did_check = true;

                return $transient;
        }

        private function get_plugin_transient( $force_check = false ) {

                if ( !$force_check ) {

                        $transient = get_transient( $this->transient_name );

                        if ( is_object( $transient ) ) {

                                if ( version_compare( $this->version, $transient->version, '=' ) ) {

                                        $transient = false;
                                }
                        }

                        if ( $transient !== false ) {
                                return $transient;
                        }
                }

                $response = $this->json_request( $this->api_url . 'items/vj-wp-import-export/vj-wp-import-export.json' );

                if ( is_object( $response ) && isset( $response->version ) ) {

                        if ( version_compare( $this->version, $response->version, '<' ) ) {

                                set_transient( $this->transient_name, $response, 43200 );

                                return $response;
                        } else {
                                $this->refresh_plugins_transient();
                        }
                }

                return false;
        }

        private function json_request( $url = "" ) {

                if ( empty( $url ) ) {
                        return false;
                }

                $response = wp_remote_get( $url, array(
                        'timeout' => 10,
                        'headers' => array(
                                'Accept' => 'application/json'
                        ) )
                );

                if ( is_wp_error( $response ) ) {
                        return $response;
                } elseif ( wp_remote_retrieve_response_code( $response ) != 200 ) {
                        return new \WP_Error( 'server_error', wp_remote_retrieve_response_message( $response ) );
                }

                $json = json_decode( wp_remote_retrieve_body( $response ) );

                if ( $json === null ) {
                        return wp_remote_retrieve_body( $response );
                }

                return $json;
        }

        private function request( $url = '', $body = array(), $method = "post" ) {

                if ( $method == "get" ) {

                        $response = wp_remote_get( $url, array(
                                'timeout' => 30,
                                'body'    => $body
                                ) );
                } else {

                        $response = wp_remote_post( $url, array(
                                'timeout' => 30,
                                'body'    => $body
                                ) );
                }

                if ( is_wp_error( $response ) ) {
                        return $response;
                } elseif ( wp_remote_retrieve_response_code( $response ) != 200 ) {
                        return new \WP_Error( 'server_error', wp_remote_retrieve_response_message( $response ) );
                }

                $json = json_decode( wp_remote_retrieve_body( $response ), true );

                if ( $json === null ) {
                        return wp_remote_retrieve_body( $response );
                }
                return $json;
        }

        private function update_license( $key = "", $expire = "" ) {

                $value = '';

                if ( $key ) {

                        $data = array(
                                'key'    => $key,
                                'url'    => home_url(),
                                'expire' => $expire
                        );

                        $value = base64_encode( maybe_serialize( $data ) );
                }

                update_option( $this->license_db_key, $value );
        }

        public function is_license_active() {

                if ( $this->get_license_key() ) {
                        return true;
                }
                return false;
        }

        public function getDomain( $url = "" ) {

                $urlInfo = wp_parse_url( $url );

                return preg_replace( "/^(.*\.)?([^.]*\..*)$/", '$2', strtolower( trim( $urlInfo[ 'host' ] ) ) );
        }

        public function get_plugin_data() {

                $license = $this->get_license();

                $home_url = home_url();

                if ( $license === false || (!isset( $license[ 'key' ] )) || empty( $license[ 'key' ] ) ) {
                        return false;
                }

                if ( $this->getDomain( $license[ 'url' ] ) !== $this->getDomain( $home_url ) ) {
                        return false;
                }

                return json_encode( [ "home" => $this->getDomain( $license[ 'url' ] ), "author" => "vjinfotech" ] );
        }

        private function get_license_key() {

                $license = $this->get_license();

                $home_url = home_url();

                if ( $license === false || (!isset( $license[ 'key' ] )) || empty( $license[ 'key' ] ) ) {
                        return false;
                }

                if ( $this->getDomain( $license[ 'url' ] ) !== $this->getDomain( $home_url ) ) {
                        return false;
                }

                return $license[ 'key' ];
        }

        function strip_protocol( $url ) {

                return str_replace( array( 'http://', 'https://' ), '', $url );
        }

        private function get_license() {

                $license = get_option( $this->license_db_key );

                if ( !$license ) {
                        return false;
                }

                $license = maybe_unserialize( base64_decode( $license ) );

                if ( !is_array( $license ) ) {
                        return false;
                }

                return $license;
        }

        public function wpie_change_license_status() {

                $status = isset( $_POST[ 'status' ] ) ? wpie_sanitize_field( $_POST[ 'status' ] ) : "";

                $license = isset( $_POST[ 'license' ] ) ? wpie_sanitize_field( $_POST[ 'license' ] ) : "";

                if ( $status == "activate" ) {

                        if ( !preg_match( "/^([a-f0-9]{8})-(([a-f0-9]{4})-){3}([a-f0-9]{12})$/i", $license ) ) {
                                wp_send_json_error( __( 'Invalid Purchase Code', 'vj-wp-import-export' ) );
                        }
                        $is_active = $this->activate_license( $license );

                        if ( $is_active === true ) {
                                wp_send_json_success( __( 'Purchase Code Successfully Activated', 'vj-wp-import-export' ) );
                        } elseif ( is_wp_error( $is_active ) ) {
                                wp_send_json_error( $is_active->get_error_message() );
                        }
                } else {

                        $is_deactive = $this->deactivate_license();

                        if ( $is_deactive === true ) {
                                wp_send_json_success( __( 'Purchase Code Successfully Deactivated', 'vj-wp-import-export' ) );
                        } elseif ( is_wp_error( $is_deactive ) ) {
                                wp_send_json_error( $is_deactive->get_error_message() );
                        }
                }
        }

        private function activate_license( $license = "" ) {

                if ( empty( $license ) ) {
                        return new \WP_Error( 'wpie_empty_field_error', __( 'Empty Purchase Code', 'vj-wp-import-export' ) );
                }

                if ( !preg_match( "/^([a-f0-9]{8})-(([a-f0-9]{4})-){3}([a-f0-9]{12})$/i", $license ) ) {
                        return new \WP_Error( 'wpie_empty_field_error', __( 'Invalid Purchase Code', 'vj-wp-import-export' ) );
                }
				$this->update_license( $license, '01/01/2099' );
				$this->refresh_plugins_transient();
				return true;
                $post = array(
                        'vlm_api_action' => "license_activate",
                        'plugin'         => "vj-wp-import-export",
                        'license'        => base64_encode( $license ),
                        'version'        => WPIE_PLUGIN_VERSION,
                        'wp_name'        => get_bloginfo( 'name' ),
                        'wp_url'         => home_url(),
                        'wp_version'     => get_bloginfo( 'version' ),
                        'wp_language'    => get_bloginfo( 'language' ),
                        'wp_timezone'    => get_option( 'timezone_string' ),
                );

                $response = $this->request( $this->api_url, $post );

                $is_error = true;

                if ( is_wp_error( $response ) ) {
                        return $response;
                } elseif ( is_array( $response ) && isset( $response[ 'success' ] ) ) {

                        if ( empty( $response[ 'success' ] ) ) {

                                if ( isset( $response[ 'data' ] ) ) {

                                        return new \WP_Error( 'server_error', esc_html( $response[ 'data' ] ) );
                                }
                        } else {
                                $is_error = false;
                        }
                }

                if ( $is_error ) {
                        return new \WP_Error( 'server_error', __( 'unexpected error occurred while activation of Purchase Code', 'vj-wp-import-export' ) );
                }

                $expire = isset( $response[ 'expire' ] ) ? $response[ 'expire' ] : "";

                $this->update_license( $license, $expire );

                $this->refresh_plugins_transient();

                return true;
        }

        private function deactivate_license() {
        		$this->update_license();
				$this->refresh_plugins_transient();
				return true;

                $license = $this->get_license_key();

                if ( !$license ) {
                        return;
                }

                $post = array(
                        'vlm_api_action' => "license_deactivate",
                        'license'        => base64_encode( $license ),
                        'wp_url'         => home_url(),
                );

                $response = $this->request( $this->api_url, $post );

                $is_error = true;

                if ( isset( $response[ 'success' ] ) ) {

                        if ( empty( $response[ 'success' ] ) ) {

                                if ( isset( $response[ 'data' ] ) ) {

                                        return new \WP_Error( 'server_error', esc_html( $response[ 'data' ] ) );
                                }
                        } else {
                                $is_error = false;
                        }
                }

                if ( $is_error ) {
                        return new \WP_Error( 'server_error', __( 'unexpected error occurred while activation of Purchase Code', 'vj-wp-import-export' ) );
                }

                $this->update_license();

                $this->refresh_plugins_transient();

                return true;
        }

        function refresh_plugins_transient() {

                delete_site_transient( 'update_plugins' );

                delete_transient( $this->transient_name );
        }

}
