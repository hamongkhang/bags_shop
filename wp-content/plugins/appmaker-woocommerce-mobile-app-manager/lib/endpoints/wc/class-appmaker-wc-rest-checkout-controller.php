<?php
/**
 * REST API Checkout controller
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * REST API Checkout controller
 *
 */
class APPMAKER_WC_REST_Checkout_Controller extends APPMAKER_WC_REST_Controller {

    /**
     * Endpoint namespace.
     *
     * @var string
     */
    protected $namespace = 'appmaker-wc/v1';

    /**
     * Route base.
     *
     * @var string
     */
    protected $rest_base = 'checkout';


    /**
     * Register the routes for products.
     */
    public function register_routes() {

        register_rest_route( $this->namespace, '/' . $this->rest_base, array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'checkout' ),
                'permission_callback' => array( $this, 'api_permissions_check' ),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/sdk/(?P<id>[a-z\-\_]+)', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'handle_sdk' ),
                'permission_callback' => array( $this, 'api_permissions_check' ),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

    }

    /**
     * Override die ajax handler
     *
     * @param mixed $function Current function.
     *
     * @return string
     */
    public function wp_die_ajax_handler( $function ) {
        return '__return_true';
    }

    /**
     * Function to Override order receive url.
     *
     * @param string $url Url.
     * @param WC_Order $order Order object.
     *
     * @return string
     */
    public function override_order_receive_url( $url, $order ) {
        $this->order = $order;
        $order->add_order_note( __( 'Order from App', 'appmaker-woocommerce-mobile-app-manager' ) );

        return add_query_arg( array( 'app_order_id_value' => APPMAKER_WC_Helper::get_id( $order ) ), $url );
    }

    /**
     * Function to Override order receive url.
     *
     * @param string $url Url.
     * @param WC_Order $order Order object.
     *
     * @return string
     */
    public function override_order_cancel_url( $url ) {

        $order = $this->order;
        return add_query_arg( array( 'app_order_id_value' => APPMAKER_WC_Helper::get_id( $order ) ), $url );
    }

    /**
     * Checkout route
     *
     * @param array $request Request Object.
     *
     * @return array|bool|mixed|object|  |WP_Error
     */
    public function checkout( $request ) {

        $return = array();
        try {
            define( 'DOING_AJAX', true );
            remove_filter('woocommerce_registration_errors', array('WC_Ncr_Registration_Captcha', 'validate_captcha_wc_registration'), 10);
            if ( isset( $request['ship_to_different_address'] ) && ( $request['ship_to_different_address'] === false || $request['ship_to_different_address'] === 'false' || $request['ship_to_different_address'] == '0' || $request['ship_to_different_address'] === '' ) ) {
                unset( $_POST['ship_to_different_address'] );
                $_POST['shipping_first_name'] = '';
                $_POST['shipping_last_name']  = '';
                $_POST['shipping_company']    = '';
                $_POST['shipping_address_1']  = '';
                $_POST['shipping_address_2']  = '';
                $_POST['shipping_city']       = '';
                $_POST['shipping_postcode']   = '';
                $_POST['shipping_country']    = '';
                $_POST['shipping_state']      = '';

            }
            // shipping method is taken from $_POST['shipping_method] in wc
            
            $_POST['shipping_method'] = $_POST['shipping_methods'];
            do_action( 'appmaker_wc_before_checkout', $request );

            // $_POST['_wpnonce'] = $request['wpnonce'];
            $_POST['_wpnonce'] = wp_create_nonce( 'woocommerce-process_checkout' );
            $_REQUEST['_wpnonce'] = $_REQUEST['woocommerce-process-checkout-nonce'] = wp_create_nonce( 'woocommerce-process_checkout' );

            $terms_page_id = wc_get_page_id( 'terms' );
            if($terms_page_id > 0)
                 $_POST['terms-field'] = $terms_page_id;
            if (!isset( $_POST['terms']) || (isset( $_POST['terms']) && ( $_POST['terms'] != true && $_POST['terms'] !== '1' && $_POST['terms'] !== 'true' ) ) ) {
                unset($_POST['terms']);
                unset($_REQUEST['terms']);
            }
           
            $_POST['post_data'] = http_build_query( $_POST ); // To make sure advanced shipping methods will show
            $validation_response= apply_filters('appmaker_wc_validate_checkout', $request);
            if(is_wp_error($validation_response)){
                return $validation_response;
            }
            $woocommerce_checkout                        = WC()->checkout();
            $woocommerce_checkout->enable_signup         = true;
            $woocommerce_checkout->enable_guest_checkout = ( ( get_option( 'woocommerce_enable_guest_checkout' ) === 'yes' ) || ( get_option( 'woocommerce_enable_signup_and_login_from_checkout' ) === 'no' ) );
            add_filter( 'wp_die_ajax_handler', array( $this, 'wp_die_ajax_handler' ) );
            add_filter( 'woocommerce_get_checkout_order_received_url', array(
                $this,
                'override_order_receive_url',
            ), 10, 2 );
            add_filter( 'woocommerce_get_cancel_order_url_raw', array(
                $this,
                'override_order_cancel_url',
            ), 10, 1 );
            ob_start();
            $woocommerce_checkout->process_checkout();
            $response_json = ob_get_clean();
            $response      = array();
            preg_match( '/({(.*?)})({(.*?)})?/', $response_json, $response );
            $response = json_decode( $response[1], true );

            $notice_errors = $this->get_wc_notices_errors();

            if ( isset( $response['type'] ) && 'sdk' == $response['type'] ) {
                $return = $response;
            } elseif ( isset( $response['result'] ) && 'failure' !== $response['result'] && isset( $response['redirect'] ) ) {
                $order_id = false;               
                preg_match( '/app_order_id_value((?:=)|(?:%3D))([0-9]+)/', $response['redirect'], $order_id );
                if(empty($order_id[0])){
                    preg_match( '/order_id((?:=)|(?:%3D))([0-9]+)/', $response['redirect'], $order_id );
                   } 
                if ( isset( $order_id[2] ) ) {
                    $order_id = $order_id[2];
                } elseif ( ! empty( WC()->session->order_awaiting_payment ) ) {
                    $order_id = WC()->session->order_awaiting_payment;
                }
                if ( false !== $order_id ) {
                    $response['order_id'] = $order_id;
                    $order = wc_get_order( $response['order_id'] );
                    if ( is_a( $order, 'WC_Order' ) ) {
                        if ( ! get_post_meta( APPMAKER_WC_Helper::get_id( $order ), 'from_app' ) ) {
                            $order->add_order_note( __( 'Order from App', 'appmaker-woocommerce-mobile-app-manager' ) );
                            add_post_meta( APPMAKER_WC_Helper::get_id( $order ), 'from_app', true );
                        }
                        if ( ! get_post_meta( APPMAKER_WC_Helper::get_id( $order ), 'appmaker_mobile_platform' ) && isset($request['platform'] ) ) {                            
                            $note = sprintf( __( 'Order from #%s app', 'appmaker-woocommerce-mobile-app-manager' ), $request['platform'] );
                            $order->add_order_note( $note );
                            add_post_meta( APPMAKER_WC_Helper::get_id( $order ), 'appmaker_mobile_platform', $request['platform'] );
                        }
                        $key = method_exists( $order, 'get_order_key' ) ?  $order->get_order_key() : $order->order_key;
                        WC()->session->set( 'last_order_key', $key );
        				WC()->session->set( 'last_order_id', $order_id );
                    }
                }

                $return = $response;
                // WC()->cart->empty_cart( true );
            } elseif ( ! empty( $notice_errors ) ) {
                $return['result'] = 'failure';
            } else {
                if ( isset( $response['messages'] ) ) {
                    $response['messages'] = preg_replace('/\p{C}+/u', "", $response['messages']); // Remove invisibles characters
                    preg_match_all( '/<li.*?>(([\n\s]+)?.*?([\n\s]+)?)<\/li>/i', $response['messages'], $errors );
                    if ( is_array( $errors[1] ) && ! empty( $errors[1] ) ) {
                        foreach ( $errors[1] as $key => $error ) {
                            $return = $this->add_error( $return, trim($error), 'checkout_error_' . $key );
                        }
                    } else {
                        preg_match_all( '/<div>(.*?)<\/div>/i', $response['messages'], $errors );
                        if ( is_array( $errors[1] ) && ! empty( $errors[1] ) ) {
                            foreach ( $errors[1] as $key => $error ) {
                                $return = $this->add_error( $return, $error, 'checkout_error_' . $key );
                            }
                        }
                    }
                } elseif ( ! empty( $notice_errors ) ) {
                    $return = $notice_errors;
                }
            }
        } catch ( Exception $e ) {
            $return = new WP_Error( 'checkout_error', 'Cannot process checkout', $e );
        }

        $return = apply_filters( 'appmaker_wc_checkout_response', $return );

        return $return;
    }

    public function handle_sdk( $request ) {
        $sdk = isset( $request['id'] ) ? $request['id'] : '';
        $return = array();
        $return = apply_filters( 'appmaker_wc_handle_sdk_' . $sdk , $return, $request );
        if ( empty( $return ) ) {
            return new WP_Error( 'error', 'Unable to process checkout' );
        }
        return $return;
    }
}

