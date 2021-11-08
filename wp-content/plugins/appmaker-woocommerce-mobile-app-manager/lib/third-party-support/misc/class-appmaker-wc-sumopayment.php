<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly


class APPMAKER_WC_SUMO_PAYMENT {

    /**
     * Function __construct.
     */
    public function __construct() {

        add_filter( 'appmaker_wc_product_fields', array( $this, 'product_fields' ), 2, 2 );
        add_filter( 'appmaker_wc_dependency__sumo_pp_chosen_payment_plan', array( $this, 'payment_plans_dependency' ), 10, 2 );

        add_filter( 'appmaker_wc_dependency__sumo_pp_deposited_amount', array( $this, 'payment_deposit_dependency' ), 10, 2 );
        // add_action( 'appmaker_wc_before_add_to_cart',array($this,'add_checkbox_value'),1,1 );
        add_filter( 'appmaker_wc_cart_items', array( $this, 'sumopayment_cart' ), 1, 1 );
        add_filter( 'appmaker_wc_order_review', array( $this, 'sumopayment_order_review' ), 1, 1 );
        add_filter( 'woocommerce_rest_prepare_shop_order', array( $this, 'sumopayment_order_detail' ), 1, 3 );
        add_filter( 'appmaker_wc_add_to_cart_validate', array( $this, 'payment_fixed_depo' ), 2, 2 );
    }
     public function payment_fixed_depo($return,$params){
         
		if(method_exists('SUMO_PP_Product_Manager','get_product_props' )){
            $product_props = SUMO_PP_Product_Manager::get_product_props($_POST['product_id'] );
        }else
            $product_props = SUMO_PP_Product_Manager::get_props($_POST['product_id'] );
            
        $product = wc_get_product( $_POST['product_id'] );
        $type_of_payment = $product_props['payment_type'];
        if( $type_of_payment === 'pay-in-deposit' && 'user-defined' === $product_props['deposit_type'] ) {
            if ( ! empty( $product_props['product_price'] ) ) {
                if ( 'sale-price' === get_option( SUMO_PP_PLUGIN_PREFIX . 'calc_deposits_r_payment_plans_price_based_on', 'sale-price' ) ) {
                    $product_props['product_price'] = $product->get_sale_price();
                } else {
                    $product_props['product_price'] = $product->get_regular_price();
                }
            }
            $deposit_amount_range = SUMO_PP_Product_Manager::get_user_defined_deposit_amount_range( $product_props );
            $min = $deposit_amount_range['min'];
            $max = $deposit_amount_range['max'];
            if($params['_sumo_pp_deposited_amount'] < $min || $params['_sumo_pp_deposited_amount'] > $max) {
               return  new WP_Error( 'error_add', __( 'Cannot add item to cart', 'appmaker-woocommerce-mobile-app-manager' ), array( 'status' => 405 ) );
            }
        }

    }

    public function sumopayment_order_detail( $response, $post, $request ) {
        // print_r($response->data['line_items']);exit;

         $order = wc_get_order( ( $response->data['id'] ) );
        foreach ( $order->get_items() as $item ) {
            $product = $item->get_data();
            if ( $product['variation_id'] != 0 ) {
                $product_id = $product['variation_id'];
            } else {
                $product_id = $product['product_id'];
            }
            foreach ( $item->get_formatted_meta_data() as $id => $meta_data ) {
                $product_name = "\n" . $meta_data->display_key . ' : ' . html_entity_decode( strip_tags( $meta_data->display_value ) );
                foreach ( $response->data['line_items'] as $key => $item ) {
                    // echo $product_id;echo $item['product_id']."\n";
                    if ( $product_id == $item['product_id'] ) {
                        $response->data['line_items'][ $key ]['quantity'] .= $product_name;
                    }
                }
            }
        }

         return $response;
    }

    public function sumopayment_order_review( $return ) {
        // print_r($return['products']);exit;
        foreach ( $return['products'] as $key => $product ) {
            // print_r($product['sumopaymentplans']);
            if(!empty($product['sumopaymentplans'])){
                 $total_payment     = "\n" . 'Total payable: ' . APPMAKER_WC_Helper::get_display_price( $product['sumopaymentplans']['total_payable_amount'] ) . "\n";
                 $next_payment_date = 'Next payment date: ' . $product['sumopaymentplans']['next_payment_date'] . "\n";

                 $return['products'][ $key ]['quantity'] .= $total_payment;
                 $return['products'][ $key ]['quantity'] .= $next_payment_date;
            if ( $product['sumopaymentplans']['payment_product_props']['payment_type'] === 'payment-plans' ) {
                $next_installment_amount                 = 'Next installment amount: ' . APPMAKER_WC_Helper::get_display_price( $product['sumopaymentplans']['next_installment_amount'] ) . "\n";
                $return['products'][ $key ]['quantity'] .= $next_installment_amount;
            }
        }
    }
         return $return;
    }

    public function sumopayment_cart( $return ) {
        // print_r($return['products']);exit;
    
        foreach ( $return['products'] as $key => $product ) {
            // print_r($product['sumopaymentplans']);
            if(!empty($product['sumopaymentplans'])){
                $total_payment     = 'Total payable: ' . APPMAKER_WC_Helper::get_display_price( $product['sumopaymentplans']['total_payable_amount'] ) . "\n";
                $next_payment_date = 'Next payment date: ' . $product['sumopaymentplans']['next_payment_date'] . "\n";

                $return['products'][ $key ]['variation_string'] .= $total_payment;
                $return['products'][ $key ]['variation_string'] .= $next_payment_date;
            if ( $product['sumopaymentplans']['payment_product_props']['payment_type'] === 'payment-plans' ) {
                $next_installment_amount                         = 'Next installment amount: ' . APPMAKER_WC_Helper::get_display_price( $product['sumopaymentplans']['next_installment_amount'] ) . "\n";
                $return['products'][ $key ]['variation_string'] .= $next_installment_amount;
            }
        }
     }
         return $return;
    }
    
    public function get_payment_plans(){

        global $product;
		if(method_exists('SUMO_PP_Product_Manager','get_product_props' )){
            $product_props = SUMO_PP_Product_Manager::get_product_props($product );
        }else
            $product_props = SUMO_PP_Product_Manager::get_props($product );
       
        $payment_plans = array();
        if( $product_props['payment_type'] == 'payment-plans' && !empty($product_props['selected_plans']))  {
            $payment_plans = $product_props['selected_plans'];
        }
        return $payment_plans;
    }

        /**
         * Function to fix order minimum amount.
         *
         * @param array $return Return.
         *
         * @return mixed
         */
    public function product_fields( $fields, $product ) {

        // print_r(get_option( SUMO_PP_PLUGIN_PREFIX . 'selected_plans' , array() ) );
        // print_r(SUMO_PP_Payment_Plan_Manager::get_props('74'));

		if(method_exists('SUMO_PP_Product_Manager','get_product_props' )){
            $product_props = SUMO_PP_Product_Manager::get_product_props($product->get_id() );
        }else{
			$product_props = SUMO_PP_Product_Manager::get_props($product->get_id() );
		}
            
        // print_r(SUMO_PP_Product_Manager::get_user_defined_deposit_amount_range($product_props));exit;
        $type_of_payment = $product_props['payment_type'];
        $additional_fields = array();
        if ( $type_of_payment ) {

                $additional_fields = array(
                    '_sumo_pp_payment_type' => array(
                        'label'   => 'Choose payment type',
                        'type'    => 'select',
                        'options' => array(
                            'pay_full'       => 'Pay in full',
                            $type_of_payment => ( $type_of_payment == 'pay-in-deposit' ) ? 'pay on deposit' : 'payment with plan',
                        ),
                    ),
                );
                if ( $type_of_payment == 'payment-plans' && ! empty( $product_props['selected_plans'] ) ) {
                    $payment_plans = $product_props['selected_plans'];
                    $plan_options  = array();
                    foreach ( $payment_plans as $keys ) {
                        foreach ( $keys as $plan_id ) {
							// $payment_plan_id = 'Payment_plan_'.$plan_id;
							if(class_exists('SUMO_PP_Payment_Plan_Manager')){

								$payment_plan_data                                 = SUMO_PP_Payment_Plan_Manager::get_props( $plan_id );
							}else{
								$payment_plan_data                                 = SUMO_PP_Plan_Manager::get_props( $plan_id );
							}
                           
                            $plan_post_data                                    = get_post( $plan_id );
                            $plan_options[ $plan_id ]                          = $plan_post_data->post_title;
                            $additional_fields['_sumo_pp_chosen_payment_plan'] = array(
                                'type'      => 'select',
                                'dependent' => true,
                                'label'     => 'payment plans',
                                'options'   => $plan_options,

                            );
                            $plan_desc = $payment_plan_data['plan_description'];
                            $sale_price = $product->get_sale_price();
                            $regular_price = $product->get_regular_price();
                            $price     = empty( $sale_price ) ? $regular_price : $sale_price;
                            if ( 'fixed-price' === $payment_plan_data['plan_price_type'] ) {
                                $initial_payable_amount = floatval( $payment_plan_data['initial_payment'] );
                            } else {
                                $initial_payable_amount = ( $price * floatval( $payment_plan_data['initial_payment'] ) ) / 100;
                            }
                            $initial_payable_amount = APPMAKER_WC_Helper::get_display_price( $initial_payable_amount );
                            $initial_payable        = 'Initial payable: ' . $initial_payable_amount;
                            /*
                            $total_payable_amount = APPMAKER_WC_Helper::get_display_price($price);
                            $total_payable = 'Total payable: '."     ".$total_payable_amount;*/
                            $plan_label                                     = $plan_desc . '     ' . $initial_payable;
                            $additional_fields[ 'payment_plan' . $plan_id ] = array(
                                'type'          => 'checkbox',
                                'dependent'     => true,
                                'label'         => $plan_label,
                                'default_value' => true,
                            );
                            add_filter( 'appmaker_wc_dependency_payment_plan' . $plan_id, array( $this, 'payment_plan_dependency' ), 10, 2 );
                        }
                    }
                } elseif ( $type_of_payment == 'pay-in-deposit' ) {
                    if ( ! empty( $product_props['product_price'] ) ) {
                        if ( 'sale-price' === get_option( SUMO_PP_PLUGIN_PREFIX . 'calc_deposits_r_payment_plans_price_based_on', 'sale-price' ) ) {
                            $product_props['product_price'] = $product->get_sale_price();
                        } else {
                            $product_props['product_price'] = $product->get_regular_price();
                        }
                    }
                    // echo $product_props[ 'deposit_type' ];exit;
                    if ( 'user-defined' === $product_props['deposit_type'] ) {
                        $deposit_amount_range = SUMO_PP_Product_Manager::get_user_defined_deposit_amount_range( $product_props );
                        if ( $deposit_amount_range['min'] ) {
                            $label = 'Enter your Deposit Amount between ' . APPMAKER_WC_Helper::get_display_price( $deposit_amount_range['min'] ) . ' and ' . APPMAKER_WC_Helper::get_display_price( $deposit_amount_range['max'] );
                        } else {
                            $label = 'Enter an amount not less than ' . APPMAKER_WC_Helper::get_display_price( $deposit_amount_range['min'] );
                        }

                        $additional_fields['_sumo_pp_deposited_amount'] = array(
                            'type'      => 'text',
                            'dependent' => true,
                            'label'     => $label,

                        );
                    } else {
                        $fixed_deposit_amount = SUMO_PP_Product_Manager::get_fixed_deposit_amount( $product_props );
                        $label                = 'Pay amount ' . APPMAKER_WC_Helper::get_display_price( $fixed_deposit_amount );
                        $additional_fields['_sumo_pp_deposited_amount'] = array(
                            'type'          => 'hidden',
                            'dependent'     => true,
                            'label'         => $label,
                            'default_value' => $fixed_deposit_amount,

                        );
                        $additional_fields['sumo_pp_deposited_amount'] = array(
                            'type'          => 'checkbox',
                            'dependent'     => true,
                            'label'         => $label,
                            'default_value' => true,

                        );
                        add_filter( 'appmaker_wc_dependency_sumo_pp_deposited_amount', array( $this, 'payment_fixed_deposit_dependency' ), 10, 2 );
                    }
                }
        }
        $fields = APPMAKER_WC_Dynamic_form::get_fields( $additional_fields, 'product' );
        
        return $fields;
    }


    public function payment_plans_dependency( $dependency, $key ) {
        if ( '_sumo_pp_chosen_payment_plan' === $key ) {
            $dependency = array(
                'on'         => '_sumo_pp_payment_type',
                'matchValue' => 'payment-plans',
            );
        }
        return $dependency;
    }

    public function payment_plan_dependency( $dependency, $key ) {
  
        $payment_plans_list = $this->get_payment_plans();
        foreach ( $payment_plans_list as $keys ) {
            foreach ( $keys as $plan_id ) {
                if ( 'payment_plan' . $plan_id === $key ) {
                    $dependency = array(
                        'on'         => '_sumo_pp_chosen_payment_plan',
                        'matchValue' => $plan_id,
                    );
                }
            }
        }
        return $dependency;
    }

    public function payment_fixed_deposit_dependency( $dependency, $key ) {

        if ( 'sumo_pp_deposited_amount' === $key ) {
            $dependency = array(
                'on'         => '_sumo_pp_payment_type',
                'matchValue' => 'pay-in-deposit',
            );
        }
        return $dependency;
    }
    
    public function payment_deposit_dependency( $dependency, $key ) {
        if ( '_sumo_pp_deposited_amount' === $key ) {
            $dependency = array(
                'on'         => '_sumo_pp_payment_type',
                'matchValue' => 'pay-in-deposit',
            );
        }
        return $dependency;
    }

}

new APPMAKER_WC_SUMO_PAYMENT();
