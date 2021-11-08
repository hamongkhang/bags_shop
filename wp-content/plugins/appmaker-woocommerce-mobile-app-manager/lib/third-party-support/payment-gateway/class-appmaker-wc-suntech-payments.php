<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
class APPMAKER_WC_Gateway_suntech {

    public function __construct() {
        add_filter( 'appmaker_wc_payment_gateway_suntech_atm_fields', array( $this, 'suntech_atm_fields' ) );
        add_action( 'appmaker_wc_before_checkout', array($this,'add_checkbox_value'),1 );
        add_filter('woocommerce_rest_prepare_shop_order',array($this,'bank_account_details_atm'),2,3);
       // add_filter('appmaker_suntech_atm_shipment',array($this,'shipment_value'),1,1);
        add_filter('appmaker_wc_payment_gateway_suntech_buysafe_fields', array( $this, 'suntech_buysafe_fields' ) );
    }

    public function bank_account_details_atm($response,$post,$request){

       //$shipment_value =  get_post_meta( $post->ID, 'shipment_value',true);
       // if($shipment_value == 'ship') {
            // $obj= new  WC_Gateway_SunTech_Response('suntech_atm');

           $settings = get_option('woocommerce_suntech_atm_settings');
            $payment_method = get_post_meta( $post->ID, '_payment_method', true );
            $due_days = $settings['due_days'];
            //$date = date('d-m-Y');
            //$date = date('d-m-Y', strtotime($date . ' + ' . $due_days . ' days'));
        $date = date('Y/m/d', mktime(0, 0, 0, date("m"), date("d") + $due_days, date("Y")));
            if ($payment_method === 'suntech_atm') {
                $args  = array(
                    'post_id' =>$post->ID,
                    'approve' => 'approve',
                    'type'    => '',
                );

                remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ) );

                $comments = get_comments( $args );
                $account_number = '';
                foreach ( $comments as $comment ) {
                    preg_match( '/(.*): ([0-9]{9,15})(.*)/i', $comment->comment_content, $note );                    
                    if ( is_array( $note ) && ! empty( $note ) ) {
                        $account_number = $note[2];
                    }else{
                        preg_match('/([\d]){7,}/i',$comment->comment_content, $note );
                        if ( is_array( $note ) && ! empty( $note ) ) {
                            $account_number = $note[0];
                        }
                    }

                    //echo $comment->comment_content;exit;
                }
                $response->data['account_details'][]=array(
                    'bank_name' => WC_Gateway_Suntech_Base::trans('Taishin Bank(Code : 812)'),
                    'sort_code' => WC_Gateway_Suntech_Base::trans('0687'),
                    'account_number' => WC_Gateway_Suntech_Base::trans($account_number),
                    'iban' => WC_Gateway_Suntech_Base::trans($date)
                );
                $response->data['account_details_title'] = array(
                    'account_details'=>WC_Gateway_Suntech_Base::trans('BACS Details : '),
                    'bank_name' => WC_Gateway_Suntech_Base::trans('Bank '),
                    'sort_code' => WC_Gateway_Suntech_Base::trans('Branch Code'),
                    'account_number' => WC_Gateway_Suntech_Base::trans('Account'),
                    'iban' => WC_Gateway_Suntech_Base::trans('Payment Deadline ')

                );
            }

        return $response;
    }

//    public function add_meta_shipment_value($return){
//        if (is_wp_error($return)) {
//            return $return;
//        }
//        if(isset($return['order_id']) && !empty($return['order_id']) && $_POST['shipment'] == 'ship') {
//            $order_id = $return['order_id'];
//            add_post_meta($order_id , 'shipment_value', 'ship');
//        }
//        return $return;
//    }

    public function add_checkbox_value($request){
        $shipment = 'ship';
        if($request['shipment'] == true) {
            $_POST['shipment'] = $shipment;
            //add_filter( 'appmaker_wc_checkout_response', array($this,'add_meta_shipment_value'),2,1 );
        }
    }

    public function suntech_atm_fields() {
        $atm_obj = new WC_Gateway_Suntech_ATM();
        $show = 0;
        if($atm_obj->get_option('shipment')==='yes')
            $show = 1;
        $fields['shipment'] = array(
              'type'=>'checkbox',
              'label'=>$atm_obj->trans('CVS Pick-up'),
              'value'=>'ship',
              'show'=>$show,
        );
        $return = APPMAKER_WC_Dynamic_form::get_fields( $fields, 'payment' );

        return $return;
    }

    public function suntech_buysafe_fields(){
        $buysafe = new WC_Gateway_Suntech_BuySafe();
        $installments = $buysafe->get_option('installments');
        $show = 0;
        if(!empty($installments)){
            $fields['installments'] = array(
                'type' => 'select',
                'label' => $buysafe->trans('Please Select Installment Plan'),
                'options' => array(),
            );
            $fields['installments']['options'][1] = $buysafe->trans('Lump Sum');
            foreach($installments as $id => $installment){
                $fields['installments']['options'][ $installment ] = $buysafe->trans($installment . " Installments");
            }
        }

        if($buysafe->get_option('shipment')) {
            $show = 1;
            $fields['shipment'] = array(
                'type' => 'checkbox',
                'label' => $buysafe->trans('CVS Pick-up'),
                'value' => 'ship',
                'show' => $show,
            );
        }
        $return = APPMAKER_WC_Dynamic_form::get_fields( $fields, 'payment' );

        return $return;
    }

}
new APPMAKER_WC_Gateway_suntech();