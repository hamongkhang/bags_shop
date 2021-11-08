<?php
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly
class APPMAKER_WC_Gateway_Simpl
{

    public function __construct()
    {
        add_filter('appmaker_wc_checkout_redirect_gateways', array($this, 'sdk_gateways'));
        add_filter('appmaker_wc_sdk_gateways', array($this, 'sdk_gateways'));
        add_filter('appmaker_wc_checkout_response', array($this, 'sdk_config'));
        add_filter('appmaker_wc_handle_sdk_getsimpl', array($this, 'handle_sdk'), 1, 2);
    }

    public function sdk_gateways($return)
    {
        $return[] = 'getsimpl';
        return $return;
    }

    public function sdk_config($return)
    {
        if (is_wp_error($return)) {
            return $return;
        }
        if (isset($return['sdk']) && 'getsimpl' === $return['sdk']) {
            $sympl = new WC_GetSimpl();
            $orderId = $return['order_id'];
            $order = new WC_Order($orderId);
            $return['sdk_config'] = array(
                'email' => $order->get_billing_email(),
                'phone' => $order->get_billing_phone(),
                'amount' => self::convert_ruppes_to_paise($order->get_total()),
                'currency' => get_woocommerce_currency(),
                'order_id' => $orderId,
                'client_id' => $sympl->client_id
            );
        }
        return $return;
    }


    public function handle_sdk($return, $request)
    {
        $simpl = new WC_GetSimpl();
        $transaction_token  = $request['transaction_token'];
        $order_id = $request['order_id'];
        $order = false;

        if ( ! empty( $order_id ) ) {
            $order = new WC_Order($order_id);
        }

        if ( empty( $order ) || empty( $transaction_token ) ) {
            return new WP_Error( 'error', "An error occurred while processing this payment" );
        }

        $req_body = array();
        $req_body['transaction_token'] = $transaction_token;
        $req_body['amount_in_paise'] = self::convert_ruppes_to_paise($order->get_total());
        $req_body['order_id'] = $order_id;
        $items = $order->get_items();
        $req_body['items'] = array();
        foreach ($items as $item) {
            $temp_item = array();
            $temp_item['display_name'] = $item->get_name();
            $temp_item['quantity'] = $item->get_quantity();
            $temp_item['unit_price_in_paise'] = self::convert_ruppes_to_paise($item->get_total());
            array_push($req_body['items'], $temp_item);
        }
        $billing_address['line1'] = $order->get_billing_address_1();
        $billing_address['line2'] = $order->get_billing_address_2();
        $billing_address['city'] = $order->get_billing_city();
        $billing_address['state'] = $order->get_billing_state();
        $billing_address['pincode'] = $order->get_billing_postcode();
        $req_body['billing_address'] = $billing_address;
        $shipping_address['line1'] = $order->get_shipping_address_1();
        $shipping_address['line2'] = $order->get_shipping_address_2();
        $shipping_address['city'] = $order->get_shipping_city();
        $shipping_address['state'] = $order->get_shipping_state();
        $shipping_address['pincode'] = $order->get_shipping_postcode();
        $req_body['shipping_address'] = $shipping_address;
        $req_body['shipping_amount_in_paise'] = self::convert_ruppes_to_paise($order->get_shipping_total());
        $args = array(
            'body' => json_encode($req_body),
            'headers' => array(
                'Authorization'=> $simpl->client_secret,
                'Content-Type'=> 'application/json'
            )
        );
        $response = wp_remote_post( esc_url_raw($simpl->endpoint."/api/v1.1/transactions"), $args );

        $result = wp_remote_retrieve_body( $response );
        $encode_result = json_decode($result);
        if ($encode_result->success == true) {
            $order->payment_complete($encode_result->data->transaction->transaction_id);
            return array( 'status' => true, 'order_id' => $order_id );
        } else if($encode_result->success == false) {
            $order->update_status('failed');
            return new WP_Error( 'error', "An error occurred while processing this payment", array( 'order_id' => $order_id ) );
        }
    }

    public static function convert_ruppes_to_paise($ruppes)
    {
        $splited_ruppee = explode(".", $ruppes);
        if (count($splited_ruppee) == 2) {
            return ((intval($splited_ruppee[0]) * 100) + intval($splited_ruppee[1]));
        }
        return (intval($splited_ruppee[0]) * 100);
    }
}

new APPMAKER_WC_Gateway_Simpl();
