<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
class APPMAKER_WC_Gateway_rede {
    public function __construct() {
        add_filter( 'appmaker_wc_payment_gateway_rede_credit_fields', array( $this, 'fields' ) );
        add_action( 'appmaker_wc_before_checkout',array($this, 'Validation_checkout' ),10,1);

    }

    public function  Validation_checkout($return){
        $expiry_date = $return['rede_credit_expiry'];
        if(preg_match( '/^\d{2}\/\d{4}$/',$expiry_date ) ){
            $expiry_date = explode( '/',$expiry_date );
            $return['rede_credit_expiry'] = $expiry_date[0]." / ".$expiry_date[1];
            $_POST['rede_credit_expiry'] = $return['rede_credit_expiry'] ;
        }
        return $return ;

    }


    public function fields() {
      //APPMAKER_WC::$api->APPMAKER_WC_REST_Checkout_Form_Controller->set_shipping_method(($_POST['shipping_methods']));

      $cart = APPMAKER_WC::$api->APPMAKER_WC_REST_Cart_Controller->cart_items();
      $order_total = $cart['order_total'];
      $WC_Rede_Credit = new WC_Rede_Credit();
      $installments = $WC_Rede_Credit->get_installments($order_total);

        $fields = array(
            'rede_credit_number'   => array(
                'type'     => 'text',
                'label'    => 'Número do cartão',
                'required' => true,
                'maxLength' => 22,
                'placeholder' => '____ ____ ____ ____',
            ),
            'rede_credit_installments'   => array(
                'type'     => 'select',
                'label'    => 'Parcelas',
                'required' => true,
                'options'   =>array(),
            ),
            'rede_credit_holder_name'   => array(
                'type'     => 'text',
                'label'    => 'Nome impresso no cartão',
                'required' => true,
            ),
            'rede_credit_expiry'   => array(
                'type'     => 'text',
                'label'    => 'Validade do cartão',
                'placeholder'=>'MM / AAAA',
                'required' => true,
            ),
            'rede_credit_cvc'    => array(
                'type'     => 'number',
                'label'    =>'Código de segurança',
                'required' => true,
                'placeholder' => 'CVC',
            ),
        );

        if (!empty($installments)){
            foreach ($installments as $installment) {
                $fields['rede_credit_installments']['options'][$installment['num']] = $installment['label'];
            }
        }
        $return = APPMAKER_WC_Dynamic_form::get_fields( $fields, 'payment' );

        return $return;
    }
}
new APPMAKER_WC_Gateway_rede();
