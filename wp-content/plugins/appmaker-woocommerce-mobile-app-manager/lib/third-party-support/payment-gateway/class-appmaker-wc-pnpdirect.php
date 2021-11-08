<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
class APPMAKER_WC_Gateway_PnpDirect {
	public function __construct() {
		add_filter( 'appmaker_wc_payment_gateway_plugnpaydirect_fields', array( $this, 'fields' ) );
		add_action( 'appmaker_wc_before_checkout',array($this, 'Validation_checkout' ),10,1);
	}

	public function  Validation_checkout($return){
		$_POST['plugnpaydirect-card-name'] = $return['pnp_cardname'];
		$_POST['plugnpaydirect-card-number'] = $return['pnp_creditcard'];

		$_POST['plugnpaydirect-card-expiry'] = $return['pnp_expdatemonth'].' / '.$return['pnp_expdateyear'];
		$_POST['plugnpaydirect-card-cvc']    = $return['pnp_cvv'];
        return $return ;

	}
	
	public function fields() {
		$years = array();
		for ( $i = 0, $today = (int) date( 'y', time() ) , $today1 = (int) date( 'Y', time() ); $i < 8; $i++ ) {
			$years[ $today++ ] = '' . $today1++;
		}
		$fields = array(
			'pnp_cardname'   => array(
				'type'     => 'text',
				'label'    => __( 'Card Holder Name', 'pnp_direct_patsatech' ),
				'required' => true,
			),
			'pnp_creditcard'   => array(
				'type'     => 'number',
				'label'    => __( 'Card Number', 'pnp_direct_patsatech' ),
				'required' => true,
				'maxLength' => 16,
			),
			'pnp_expdatemonth' => array(
				'type'     => 'select',
				'label'    => __( 'Expiration Date', 'pnp_direct_patsatech' ),
				'required' => true,
				'options'   => array(
					'01'    => ' 1 - January',
					'02'    => ' 2 - February',
					'03'    => ' 3 - March',
					'04'    => ' 4 - April',
					'05'    => ' 5 - May',
					'06'    => ' 6 - June',
					'07'    => ' 7 - July',
					'08'    => ' 8 - August',
					'09'    => ' 9 - September',
					'10'    => '10 - October',
					'11'    => '11 - November',
					'12'    => '12 - December',
				),
			),
			'pnp_expdateyear'  => array(
				'type'        => 'select',
				'label'       => __( 'Year', 'pnp_direct_patsatech' ),
				'placeholder' => 'Year',
				'required'    => true,
				'options'   => $years,

			),
			'pnp_cvv'    => array(
				'type'     => 'number',
				'label'    => __( 'Card CVV', 'pnp_direct_patsatech' ),
				'required' => true,
			),
		);
		$return = APPMAKER_WC_Dynamic_form::get_fields( $fields, 'payment' );

		return $return;
	}
}
new APPMAKER_WC_Gateway_PnpDirect();
