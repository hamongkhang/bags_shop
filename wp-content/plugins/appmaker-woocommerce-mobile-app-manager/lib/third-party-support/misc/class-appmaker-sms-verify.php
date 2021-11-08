<?php

class APPMAKER_WC_SMS_Verify {

	public function __construct() {
		add_action( 'appmaker_wc_before_review', array( $this, 'sms_verify_six' ), 10, 1 );
		add_filter( 'appmaker_wc_checkout_fields', array( $this, 'checkout_fields_response_fix' ), 10, 2 );
	}

	function sms_verify_six( $request ) {
		$key  = 'shipping';
		$data = array();
		if ( ! isset( $request['ship_to_different_address'] ) || ( isset( $request['ship_to_different_address'] ) && ( $request['ship_to_different_address'] === false || $request['ship_to_different_address'] === 'false' || $request['ship_to_different_address'] == 0 ) ) ) {
			$key = 'billing';
		}
		if ( isset( $request[ $key . '_email' ] ) ) {
			$data['user_email'] = $request[ $key . '_email' ];
		} else {
			$data['user_email'] = '';
		}
		if ( isset( $request[ $key . '_phone' ] ) ) {
			$data['user_phone'] = $request[ $key . '_phone' ];
		} else {
			$data['user_phone'] = '';
		}
		$match = preg_match( SmsAlertConstants::PATTERN_PHONE, $data['user_phone'] );
		if ( smsalert_get_option( 'buyer_checkout_otp', 'smsalert_general' ) == 'on' && $match ) {
			SmsAlertUtility::checkSession();
			SmsAlertUtility::initialize_transaction( FormSessionVars::WC_CHECKOUT );
			$_SESSION['phone_number_mo'] = $data['user_phone'];
			SmsAlertcURLOTP::smsalert_send_otp_token( false, '', $data['user_phone'] );
		}
	}


	/**
	 * @param $return
	 * @param $section
	 *
	 * @return array
	 */
	public function checkout_fields_response_fix( $return, $section ) {
		$additional_fields = array();
		if ( $section === 'order' &&  smsalert_get_option( 'buyer_checkout_otp', 'smsalert_general' ) == 'on' ) {
			$additional_fields['order_verify'] = array(
				'type'         => 'number',
				'label'        => __( 'Verify Code' ),
				'required'     => true,
				'placeholder'  => __( 'Enter Verification Code' ),
				'keyboardType' => 'numeric',
			);
		}

		return array_merge( $additional_fields, $return );
	}
}

new APPMAKER_WC_SMS_Verify();
