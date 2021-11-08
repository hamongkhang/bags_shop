<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
class APPMAKER_WC_Gateway_paypal_pro {
    public function __construct() {
        add_filter( 'appmaker_wc_payment_gateway_paypal_pro_fields', array( $this, 'fields' ) );

    }
    public function fields() {


        $years = array();
        for ( $i = 0, $today = (int) date( 'y', time() ) , $today1 = (int) date( 'Y', time() ); $i < 8; $i++ ) {
            $years[ $today++ ] = '' . $today1++;
        }
        $fields = array(
            'paypal_pro-card-number'   => array(
                'type'     => 'text',
                'label'    => __( 'Card Number', 'woocommerce-gateway-paypal-pro' ),
                'required' => true,
            ),
            'paypal_pro-card-expiry'   => array(
                'type'     => 'text',
                'label'    => __( 'Expiry (MM/YY)', 'woocommerce-gateway-paypal-pro' ),
                'required' => true,
                'maxLength' => 16,
            ),
           /* 'pnp_expdatemonth' => array(
                'type'     => 'select',
                'label'    => __( 'Expiration Date', 'woocommerce-gateway-paypal-pro' ),
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
            ),*/
          /*  'pnp_expdateyear'  => array(
                'type'        => 'select',
                'label'       => __( 'Year', 'pnp_direct_patsatech' ),
                'placeholder' => 'Year',
                'required'    => true,
                'options'   => $years,

            ),*/
            'paypal_pro-card-cvc'    => array(
                'type'     => 'number',
                'label'    => __( 'Card Code', 'woocommerce-gateway-paypal-pro' ),
                'required' => true,
            ),
        );
        $return = APPMAKER_WC_Dynamic_form::get_fields( $fields, 'payment' );

        return $return;
    }
}
new APPMAKER_WC_Gateway_paypal_pro();
