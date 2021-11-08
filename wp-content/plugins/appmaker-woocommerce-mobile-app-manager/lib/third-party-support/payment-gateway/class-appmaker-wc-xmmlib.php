<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
class APPMAKER_WC_Gateway_AuthorizeNet_AIM {
	public function __construct() {
		add_filter( 'appmaker_wc_payment_gateway_spyr_authorizenet_aim_fields', array( $this, 'fields' ) );
	}

	public function fields() {
		$years = array();
		for ( $i = 0, $today = (int) date( 'y', time() ), $today1 = (int) date( 'Y', time() ); $i < 8; $i ++ ) {
			$years[ $today ++ ] = '' . $today1 ++;
		}
		$fields = array(
			'spyr_authorizenet_aim-card-number' => array(
				'type'     => 'number',
				'label'    => __( 'Phone number.', 'woocommerce' ),
				'required' => true,
			),
			'spyr_authorizenet_aim-card-cvc'    => array(
				'type'      => 'password',
				'label'     => __( 'Postal code.', 'woocommerce' ),
				'required'  => true,
				'maxLength' => 4,
			),
		);
		$return = APPMAKER_WC_Dynamic_form::get_fields( $fields, 'payment' );

		return $return;
	}
}

new APPMAKER_WC_Gateway_AuthorizeNet_AIM();
