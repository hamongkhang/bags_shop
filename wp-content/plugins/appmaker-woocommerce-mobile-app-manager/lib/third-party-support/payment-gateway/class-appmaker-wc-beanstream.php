<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
class APPMAKER_WC_Gateway_beanstream {
    public function __construct() {

        add_filter( 'appmaker_wc_payment_gateway_beanstream_fields', array( $this, 'fields' ) );

    }
    public function fields(){
        $installed_methods=WC()->payment_gateways()->payment_gateways();

        $defaults = $installed_methods['beanstream']->get_payment_method_defaults();

        $fields = array(
            'wc-beanstream-account-number'   => array(
                'type'              => 'number',
                'label'             => esc_html__( 'Card Number', 'woocommerce-plugin-framework' ),
                'id'                => 'wc-' . $installed_methods['beanstream']->get_id_dasherized() . '-account-number',
                'name'              => 'wc-' . $installed_methods['beanstream']->get_id_dasherized() . '-account-number',
                'maxLength' => 20,
                'placeholder'       => '•••• •••• •••• ••••',
                'required' => true,
                'custom_attributes' => array(
                    'autocomplete'   => 'cc-number',
                    'autocorrect'    => 'no',
                    'autocapitalize' => 'no',
                    'spellcheck'     => 'no',
                ),
                'value' => $defaults['account-number'],
            ),
            'wc-beanstream-expiry'   => array(
                'type'              => 'text',
                'label'             => esc_html__( 'Expiration (MM/YY)', 'woocommerce-plugin-framework' ),
                'required' => true,
                'id'                => 'wc-' . $installed_methods['beanstream']->get_id_dasherized() . '-expiry',
                'name'              => 'wc-' . $installed_methods['beanstream']->get_id_dasherized() . '-expiry',
                'placeholder'       => esc_html__( 'MM / YY', 'woocommerce-plugin-framework' ),
                'custom_attributes' => array(
                    'autocomplete'   => 'cc-exp',
                    'autocorrect'    => 'no',
                    'autocapitalize' => 'no',
                    'spellcheck'     => 'no',
                ),
                'value' => $defaults['expiry'],

            ),

            /*'card-csc'    => array(
                'type'     => 'number',
                'label'             => esc_html__( 'Card Security Code', 'woocommerce-plugin-framework' ),
                'required' => true,
                'maxlength'         => 4,
                'placeholder'       => esc_html__( 'CSC', 'woocommerce-plugin-framework' ),
            ),*/
        );
        if ($installed_methods['beanstream']->csc_enabled() ) {
            $fields['wc-beanstream-csc']=array(
                'type'     => 'number',
                'label'             => esc_html__( 'Card Security Code', 'woocommerce-plugin-framework' ),
                'required' => true,
                'id'                => 'wc-' . $installed_methods['beanstream']->get_id_dasherized() . '-csc',
                'name'              => 'wc-' .  $installed_methods['beanstream']->get_id_dasherized() . '-csc',
                'maxlength'         => 4,
                'custom_attributes' => array(
                    'autocomplete'   => 'off',
                    'autocorrect'    => 'no',
                    'autocapitalize' => 'no',
                    'spellcheck'     => 'no',
                ),
                'value' => $defaults['csc'],
                'placeholder'       => esc_html__( 'CSC', 'woocommerce-plugin-framework' ),
            );
        }
        $return = APPMAKER_WC_Dynamic_form::get_fields( $fields, 'payment' );

        return $return;
    }
}
new APPMAKER_WC_Gateway_beanstream();