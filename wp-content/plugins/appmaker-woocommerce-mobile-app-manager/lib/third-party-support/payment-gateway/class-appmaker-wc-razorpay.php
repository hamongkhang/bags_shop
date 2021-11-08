<?php

use Razorpay\Api\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
class APPMAKER_WC_Gateway_RazorPay {

	public function __construct() {
		add_filter( 'appmaker_wc_checkout_redirect_gateways', array( $this, 'sdk_gateways' ) );
		add_filter( 'appmaker_wc_sdk_gateways', array( $this, 'sdk_gateways' ) );
		add_filter( 'appmaker_wc_checkout_response', array( $this, 'sdk_config' ) );
		add_filter( 'appmaker_wc_handle_sdk_razorpay', array( $this, 'handle_sdk' ), 1, 2 );
	}

	public function sdk_gateways( $return ) {
		$return[] = 'razorpay';
		return $return;
	}

	protected function getSessionKey( $orderId ) {
		return "razorpay_order_id.$orderId";
	}

	protected function createRazorpayOrderId( $orderId, $sessionKey, $razorpay ) {
		$api = new Api( $razorpay->getSetting( 'key_id' ), $razorpay->getSetting( 'key_secret' ) );


		$data           = $this->get_order_creation_data( $orderId, $razorpay );
		$razorpay_order = $api->order->create( $data );

		$razorpayOrderId = $razorpay_order['id'];

		WC()->session->set( $sessionKey, $razorpayOrderId );

		return $razorpayOrderId;
	}

	function get_order_creation_data( $order_id, $razorpay ) {
		$order = wc_get_order( $order_id );

		if ( ! isset( $this->payment_action ) ) {
			$razorpay->payment_action = 'capture';
		}

		$data = array(
			'receipt'  => $order_id,
			'amount'   => (int) ( $order->get_total() * 100 ),
			'currency' => get_woocommerce_currency(),
		);

		switch ( $razorpay->payment_action ) {
			case 'authorize':
				$data['payment_capture'] = 0;
				break;

			case 'capture':
			default:
				$data['payment_capture'] = 1;
				break;
		}
		return $data;
	}

	public function sdk_config( $return ) {
	    if(is_wp_error($return)){
	        return $return;
        }
		if ( isset( $return['sdk'] ) && 'razorpay' === $return['sdk'] ) {
			global $woocommerce;
			$orderId  = $return['order_id'];
			$gateways = WC()->payment_gateways()->payment_gateways();
			$razorpay = $gateways['razorpay'];
			$order    = wc_get_order( $orderId );

			$productinfo = "Order $orderId";
			$sessionKey  = $this->getSessionKey( $orderId );
			WC()->session->set( WC_Razorpay::SESSION_KEY, $orderId );
			try {
				$razorpayOrderId = $woocommerce->session->get( $sessionKey );

				// If we don't have an Order
				// or the if the order is present in session but doesn't match what we have saved
				if ( ( $razorpayOrderId === null ) or
				     ( ( $razorpayOrderId and ( $this->verifyOrderAmount( $razorpayOrderId, $orderId, $razorpay ) ) === false ) )
				) {
					$razorpayOrderId = $this->createRazorpayOrderId(
					$orderId, $sessionKey, $razorpay );
				}
			} catch ( Exception $e ) {
				return new WP_Error( 'failure',$e->getMessage() );
			}

			$return['sdk_config'] = array(
				'key'         => $razorpay->getSetting( 'key_id' ),
				'name'        => get_bloginfo( 'name' ),
				'amount'      => $order->get_total() * 100,
				'currency'    => get_woocommerce_currency(),
				'description' => $productinfo,
				'prefill'     => array(),
				'notes'       => array(
					'woocommerce_order_id' => $orderId,
				),
				'order_id'    => $razorpayOrderId,
			);

			if ( method_exists( $order, 'get_billing_first_name' ) ) {
				$return['sdk_config']['prefill'] = array(
					'name'    => method_exists( $order,'get_billing_first_name' ) ? $order->get_billing_first_name() : $order->billing_first_name . ' ' . $order->get_billing_last_name(),
					'email'   => $order->get_billing_email(),
					'contact' => $order->get_billing_phone(),
				);
			} else {
				$return['sdk_config']['prefill'] = array(
					'name'    => $order->billing_first_name . ' ' . $order->billing_last_name,
					'email'   => $order->billing_email,
					'contact' => $order->billing_phone,
				);
			}
		}

		return $return;
	}



	function verifyOrderAmount( $razorpayOrderId, $orderId, $razorpay ) {
		$order = wc_get_order( $orderId );

		$api = new Api( $razorpay->getSetting( 'key_id' ), $razorpay->getsetting('key_secret') );

		$razorpayOrder = $api->order->fetch( $razorpayOrderId );

		$razorpayOrderArgs = array(
			'id'       => $razorpayOrderId,
			'amount'   => (int) $order->get_total() * 100,
			'currency' => get_woocommerce_currency(),
			'receipt'  => (string) $orderId,
		);

		$orderKeys = array_keys( $razorpayOrderArgs );

		foreach ( $orderKeys as $key ) {
			if ( $razorpayOrderArgs[ $key ] !== $razorpayOrder[ $key ] ) {
				return false;
			}
		}

		return true;
	}

	public function handle_sdk( $return, $request ) {

		$order_id = WC()->session->get( WC_Razorpay::SESSION_KEY );
		$gateways = WC()->payment_gateways()->payment_gateways();
        /** @var WC_Razorpay $razorpay */
		$razorpay = $gateways['razorpay'];
		$status   = false;
		$message  = 'order is = ';
        //$_POST['razorpay_payment_id']='rzp_test_0iToq7AHeqyuvN';
		if ( ! empty( $order_id ) && ! empty( $_POST['razorpay_payment_id'] ) ) {
			$razorpay_payment_id = $_POST['razorpay_payment_id'];
			$order               = wc_get_order( $order_id );
			$key_id              = $razorpay->getSetting( 'key_id' );
			$key_secret          = $razorpay->getsetting('key_secret');
			$amount              = $order->get_total() * 100;
			$api                 = new Api( $key_id, $key_secret );
			$payment             = $api->payment->fetch( $razorpay_payment_id );
            //$razorpay->payment_action = 'authorize';$payment['amount']=7600;
			try {
				if ( $razorpay->getSetting('payment_action') === 'authorize' && $payment['amount'] === $amount ) {
					$success = true;
				} else {
					//$sessionKey = $this->getSessionKey($order_id);
					$razorpay_order_id  = $_POST['razorpay_order_id'];
					$razorpay_signature = $_POST['razorpay_signature'];

					$signature = hash_hmac( 'sha256', $razorpay_order_id . '|' . $razorpay_payment_id, $key_secret );
					if ( hash_equals( $signature, $razorpay_signature ) ) {
						$success = true;
					} else {
						$success = false;
						$message = 'PAYMENT_ERROR: Payment failed';
					}
				}
			} catch ( Exception $e ) {
				$success = false;
				$message = 'WOOCOMMERCE_ERROR: Request to Razorpay Failed';
			}
			if ( $success === true ) {
				$status = true;
				// $this->msg['message'] = "Thank you for shopping with us. Your account has been charged and your transaction is successful. We will be processing your order soon. Order Id: $order_id";
				$order->payment_complete();
				$order->add_order_note( "Razorpay payment successful <br/>Razorpay Id: $razorpay_payment_id" );
				WC()->cart->empty_cart();
			} else {
				$message = 'Thank you for shopping with us. However, the payment failed.';
				$order->add_order_note( 'Transaction Declined<br/>' );
				$order->add_order_note( "Payment Failed. Please check Razorpay Dashboard. <br/> Razorpay Id: $razorpay_payment_id" );
				$order->update_status( 'failed' );
			}
		} // We don't have a proper order id

		else {

			if ( $order_id !== null ) {
				$order = wc_get_order( $order_id );
				$order->update_status( 'failed' );
				$order->add_order_note( 'Customer cancelled the payment' );
			}
			$message = 'An error occurred while processing this payment';
		}

		if ( $status ) {
			return array( 'status' => true, 'order_id' => $order_id );
		} else {
			return new WP_Error( 'error', $message, array( 'order_id' => $order_id ) );
		}
	}
}

new APPMAKER_WC_Gateway_RazorPay();
