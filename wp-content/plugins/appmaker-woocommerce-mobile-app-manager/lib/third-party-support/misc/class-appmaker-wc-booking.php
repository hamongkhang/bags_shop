<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_bookings {
    public $product;

    public function __construct() {

        add_filter( 'appmaker_wc_product_fields', array( $this, 'product_fields_response_fix' ),2,2);
        add_filter( 'appmaker_wc_add_to_cart_validate', array( $this, 'check_date_field' ), 2, 2 );
        add_filter( 'appmaker_wc_cart_items_response',array($this,'show_booking_details_cart'),2,1 );
        add_filter("woocommerce_rest_prepare_shop_order", array($this,'show_booking_details_order'),2,3);

    }

    public function show_booking_details_order($response,$post,$request){
        //print_r($response->data);exit;
        global $wpdb;
        foreach($response->data['line_items'] as $key => $item){
            $id=$item['id'];
            $booking_ids = WC_Booking_Data_Store::get_booking_ids_from_order_item_id( $id );
            if ( $booking_ids ) {
                foreach ($booking_ids as $booking_id) {
                    $booking = new WC_Booking($booking_id);
                   $date = $booking->get_start_date()  ;
                   $response->data['line_items'][$key]['name']= $response->data['line_items'][$key]['name'].' - '.$date;
                }
            }
        }

        return $response;
    }

    public function show_booking_details_cart($return){

        foreach($return['products'] as $key =>$field){
            $date = $field['booking']['date'];
            $time = $field['booking']['time'];
            $date_label = __( 'Date', 'woocommerce-bookings' );
            $time_label = __('Time', 'woocommerce-bookings');
            if(!empty($date)) {
                $return['products'][$key]['variation_string']=$date_label . ' : ' . $date;
            }
            if(!empty($time)){
                $return['products'][$key]['variation_string'] .= '  &  '. $time_label.' : '.$time;
            }

        }
        return $return;
    }

    public function check_date_field( $return, $params )
    {
        $date= $params['date'];
        if(!empty($date)) {
            //$date= str_replace(' GMT 0530 (IST)','',$date);
            preg_match("/([a-zA-z]{3}[\s][\d]{2}[\s][\d]{4}[\s][\d\:]+)[\s](.*)/i", $date, $new_date);
            $new_date = $new_date[1];
            $timezone = $new_date[2];
            $date = new DateTime($new_date, new DateTimeZone($timezone));
            $date = $date->format('d-m-Y');
        }
      // $date=date("d-m-Y",strtotime($date));
//        $time= current_time( 'timestamp' );
//        $time=date('g:i a',$time);
//
//        $_POST['wc_bookings_field_start_date_time']=$time;

       // $time = date('c', strtotime($date.' '.$params['time']));
        $date = explode('-', $date);

      $_POST['wc_bookings_field_start_date_day']=$date[0];
      $_POST['wc_bookings_field_start_date_month']=$date[1];
      $_POST['wc_bookings_field_start_date_year']= $date[2];

      $_POST['wc_bookings_field_duration']=$params['wc_bookings_field_duration'];
      $_POST['wc_bookings_field_start_date_time'] = $params['time'];

        return $return;
    }

    public function product_fields_response_fix($fields,$product)
    {

        $min_date=date('d-m-Y');
        $next_date = date('d-m-Y', strtotime($min_date. ' + 1 days'));
        $field = array();
        $options = array();
        if ($product->is_type('booking')) {

            $first_block_time     = $product->get_first_block_time();
            $timestamp            = strtotime($next_date);
            $from                 = strtotime( $first_block_time ? $first_block_time : 'midnight', $timestamp );

            $base_interval=$interval = $product->get_duration();
            if ( 'hour' === $product->get_duration_unit() ) {
                $interval      = $interval * 60;
                $base_interval = $base_interval * 60;
            }
            $to                   = strtotime( '+ 1 day', $from ) + $interval;
            $to                   = strtotime( 'midnight', $to ) - 1;
            $blocks     = $product->get_blocks_in_range( $from,$to, array( $interval, $base_interval ), 0 );
            //unset($blocks[0]);

            foreach ($blocks as $key => $timestamp){
                $time_block = date('H:i:s', $timestamp);
                $options[$time_block] = $time_block;
            }

            if ( 'customer' ==$product->get_duration_type() ) {

              $field[ 'wc_bookings_field_duration']= array(
                    'label' => __( 'Duration', 'woocommerce-bookings' ),
                    'type' => 'number',
                    'min' =>$product->get_min_duration(),
                    'max' =>$product->get_max_duration(),
                    'step'  => 1,
                    'value'=>$product->get_min_duration(),
                    'required'=>true,

                );

               }

                $field['date'] = array(
                        'type' => 'datepicker',
                        'label' => __( 'Date', 'woocommerce-bookings' ),
                        'required' => true,
                        'minDate'=>$min_date,
                        'default' =>$min_date,
                        'placeholder'=>$min_date,

                );

              if('hour' === $product->get_duration_unit() || 'minute' === $product->get_duration_unit()) {

                  $field['time'] = array(
                      'type' => 'select',
                      'label' =>__('Time', 'woocommerce-bookings'),
                      'required' => true,
                      'options'=>$options,
                  );

              }
            }
            if(!empty($fields) && !empty($field)) {
                $field = APPMAKER_WC_Dynamic_form::get_fields($field, 'product');
                $fields['items'] = array_merge($fields['items'], $field['items']);
                $fields['order'] = array_merge($fields['order'], $field['order']);
                $fields['dependencies'] = array_merge($fields['dependencies'], $field['dependencies']);
                return $fields;
            }else if(!empty($field)){
                $fields = APPMAKER_WC_Dynamic_form::get_fields($field, 'product');
                return $fields;
            }
            else{
    
                return $fields;
            }
    }
}
new APPMAKER_WC_bookings();

