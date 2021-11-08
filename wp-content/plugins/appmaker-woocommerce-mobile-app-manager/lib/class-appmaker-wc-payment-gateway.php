<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class APPMAKER_WC_Gateway_Appmaker extends WC_Payment_Gateway {
    public static function className(){
        return "APPMAKER_WC_Gateway_Appmaker";
    }

    public static function init(){
        if(isset($_POST['payment_method']) && in_array($_POST['payment_method'],apply_filters("appmaker_wc_checkout_redirect_gateways",array("stripe")))) {
            add_filter('woocommerce_payment_gateways', array(self::className(), 'add_appmaker_gateway'));
            $_POST['payment_method'] = "appmaker_webview";
        }
    }

    function add_appmaker_gateway( $methods ) {
        $methods[] = 'WC_Gateway_Appmaker';
        return $methods;
    }

    /**
     * Constructor for the gateway.
     */
    public function __construct() {
        $this->id                 = 'appmaker_webview';
        $this->method_title       = __( 'Appmaker Webview', 'woocommerce' );
        $this->method_description = __( 'Redirect to checkout page in webview page', 'woocommerce' );
        $this->has_fields         = false;

        // Load the settings
        $this->init_settings();

        // Get settings
        $this->title              = $this->get_option( 'title' );
        $this->description        = $this->get_option( 'description' );
        $this->instructions       = $this->get_option( 'instructions', $this->description );
        $this->enable_for_methods = $this->get_option( 'enable_for_methods', array() );
        $this->enable_for_virtual = $this->get_option( 'enable_for_virtual', 'yes' ) === 'yes' ? true : false;
    }

    /**
     * Check If The Gateway Is Available For Use.
     *
     * @return bool
     */
    public function is_available() {
        return true;
    }


    /**
     * Process the payment and return the result.
     *
     * @param int $order_id
     * @return array
     */
    public function process_payment( $order_id ) {
        $return['result']="success";
        $order = New WC_Order($order_id);
        $return['redirect'] = add_query_arg( 'payment_from_app', '1', $order->get_checkout_payment_url() );
        return $return;
    }

}