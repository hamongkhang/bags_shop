<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
class APPMAKER_WC_Paymes {
    public function __construct() {
        add_filter( 'appmaker_wc_payment_gateway_paymes_fields', array( $this, 'fields' ) );

    }
    public function fields() {

        $fields = array(
            'paymes_ccname'   => array(
                'type'     => 'text',
                'label'    => 'Kart Sahibi',
                'required' => true,
            ),
            'paymes_ccNo'   => array(
                'type'     => 'text',
                'label'    => 'Kart Numarası',
                'required' => true,
            ),
            'paymes_expdate'   => array(
                'type'     => 'text',
                'label'    => 'Ay',
                'placeholder' => 'AA',
                'required' => true,               
            ),
            'paymes_expdate_year'  => array(
                'type'        => 'text',
                'label'       => 'Yıl',
                'placeholder' => 'YY',
                'required'    => true,
            ),
            'paymes_cvv'    => array(
                'type'     => 'password',
                'label'    => 'CVC Kodu',
                'placeholder' => 'CVC',
                'required' => true,
            ),
        );
        $return = APPMAKER_WC_Dynamic_form::get_fields( $fields, 'payment' );

        return $return;
    }
}
new APPMAKER_WC_Paymes();
