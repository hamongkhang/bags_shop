<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_Order_date {

	public function __construct() {
		add_filter( 'appmaker_wc_checkout_fields', array( $this, 'checkout_fields_response_fix' ), 10, 2 );
	}

	/**
	 * @param $return
	 *
	 * @return array
	 */
	public function checkout_fields_response_fix( $return, $section ) {

		$time_delay_hours = get_option( 'orddd_lite_minimumOrderDays' );
		$min_date = date( 'd-m-Y',strtotime( '+' . $time_delay_hours . ' hours' ) );
		$additional_fields = array();
		if ( $section === 'order' ) {
			$delivery_enabled    = orddd_lite_common::orddd_lite_is_delivery_enabled();
			$is_delivery_enabled = 'yes';
			if ( $delivery_enabled === 'no' ) {
				$is_delivery_enabled = 'no';
			}
			if ( $is_delivery_enabled === 'yes' ) {
				$validate_wpefield = false;
				if ( get_option( 'orddd_lite_date_field_mandatory' ) === 'checked' ) {
					$validate_wpefield = true;
				}
				$options = array(
				'minDate'     => $min_date,
                'minimumDate'     => $min_date,
				);
				$additional_fields['e_deliverydate'] = array(
					'type'        => 'datepicker',
					'label'       => __( get_option( 'orddd_lite_delivery_date_field_label' ), 'order-delivery-date' ),
					'required'    => $validate_wpefield,
					'options'     => $options,
					'minDate'     => $min_date,
					'minimumDate' => $min_date,
					'placeholder' => __( get_option( 'orddd_lite_delivery_date_field_placeholder' ), 'order-delivery-date' ),
				);
			}
		}

		return array_merge( $additional_fields, $return );
	}
}

new APPMAKER_WC_Order_date();
