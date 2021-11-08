<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
add_action('init', 'byconsolewooodtSetCookie', 1);

class APPMAKER_WC_Delivery_Date_Time{
    public function __construct() {
        add_filter( 'appmaker_wc_checkout_fields', array( $this, 'checkout_fields_response_fix' ), 10, 2 );
        add_filter('appmaker_wc_validate_checkout',array($this,'Validation_checkout'));
        add_filter('appmaker_wc_validate_checkout_review',array($this,'Validation_checkout'));
    }
    public function  Validation_checkout($return){
        $date = $return['byconsolewooodt_delivery_date'];
        preg_match("/([a-zA-z]{3}[\s][\d]{2}[\s][\d]{4}[\s][\d\:]+)[\s](.*)/i",$date,$new_date);
        $new_date = $new_date[1];
        $timezone = $new_date[2];
        $date = new DateTime($new_date, new DateTimeZone($timezone));
        $timestamp = $date->format('d-m-Y');
        $date=strtotime($timestamp);
       if($date == strtotime(date('d-m-Y'))){
           $timezone = date_default_timezone_get();
           date_default_timezone_set($timezone);
           $current_time = current_time('h:i A');
           $current_time = strtotime($current_time);
           if (strtotime($return['byconsolewooodt_delivery_time']) <= $current_time) {
               return new wp_error('invalid_time', "Please enter a valid time");
           }
       }
       $_POST['byconsolewooodt_delivery_date_alternate'] = $return['byconsolewooodt_delivery_date'];
       $_POST['byconsolewooodt_delivery_type_of_delivery_time_hidden'] = 'exact_time';
       $delivery_type = get_option('byconsolewooodt_order_type');
       if( 'both' !== $delivery_type){
           $_POST['byconsolewooodt_delivery_type'] = $delivery_type;
       }
            return $return ;

    }
    
    public function get_delivery_intervals(){
        $from = get_option('byconsolewooodt_delivery_hours_from');
        $from = strtotime($from);
        $to = get_option('byconsolewooodt_delivery_hours_to');
        $to = strtotime($to);
        $return = array();
        do{
            $time = date('h:i A', $from);
            $return[$time] = $time;
            $from = strtotime("+15 minutes", $from);
        }while($from<=$to);


        /*   $timezone = date_default_timezone_get();
        date_default_timezone_set($timezone);

        $current_time = current_time('h:i A');
        $current_time = strtotime($current_time);

           foreach ($return as $from => $i) {
               if (strtotime($i) <= $current_time) {
                   unset($return[$from]);

               }

           }*/


        return $return;
    }

    public function checkout_fields_response_fix( $return, $section )
    {
        $additional_fields = array();
        $min_date=date('d-m-Y');
        $byconsolewooodt_delivery_time = ! empty( $byconsolewooodt_delivery_widget_cookie_array['byconsolewooodt_widget_time_field'] ) ? $byconsolewooodt_delivery_widget_cookie_array['byconsolewooodt_widget_time_field'] : false;
        // get cookie as array
        $stripped_out_byconsolewooodt_delivery_widget_cookie=stripslashes($_COOKIE['byconsolewooodt_delivery_widget_cookie']);
        $byconsolewooodt_delivery_widget_cookie_array=json_decode($stripped_out_byconsolewooodt_delivery_widget_cookie,true);
        $byconsolewooodt_date_field_text=get_option('byconsolewooodt_date_field_text');
        $byconsolewooodt_time_field_text=get_option('byconsolewooodt_time_field_text');
        $byconsolewooodt_takeaway_lable=get_option('byconsolewooodt_takeaway_lable');
        $byconsolewooodt_delivery_lable=get_option('byconsolewooodt_delivery_lable');
       // $byconsolewooodt_delivery_type = ! empty( $byconsolewooodt_delivery_widget_cookie_array['byconsolewooodt_widget_type_field'] ) ? $byconsolewooodt_delivery_widget_cookie_array['byconsolewooodt_widget_type_field'] : false;
        $delivery_type = get_option('byconsolewooodt_order_type');
        if ( $section === 'order' ) {
            if('both' == $delivery_type){

                $additional_fields['byconsolewooodt_delivery_type'] = array(
                    'type' => 'select',
                    'label' => __('Select order type','byconsole-woo-order-delivery-time'),
                    'placeholder'=>__('Select delivery type','byconsole-woo-order-delivery-time'),
                    'required' => true,
                    'options' => array(
                        'take_away' => $byconsolewooodt_takeaway_lable,
                        'levering' => $byconsolewooodt_delivery_lable,
                    ),
                    'default' => 'choose oder type',
                );

            }           

            $additional_fields['byconsolewooodt_delivery_date'] = array(
                'type' => 'datepicker',
                'label' =>  __('Select date','byconsole-woo-order-delivery-time'),
                'required' => true,
                'minDate'=>$min_date,
                'placeholder' => __($byconsolewooodt_date_field_text,'byconsole-woo-order-delivery-time'),
                'default' =>$min_date,
            );
            $additional_fields['byconsolewooodt_delivery_time'] = array(
                'type' => 'select',
                'label' => __('Select time','byconsole-woo-order-delivery-time'),
                'class'=>array('byconsolewooodt_delivery_time'),
                'required' => true,
                'placeholder' => __($byconsolewooodt_time_field_text,'byconsole-woo-order-delivery-time'),
                'options'=>$this->get_delivery_intervals(),
            );
        }
        return array_merge( $additional_fields, $return );
    }
}
new APPMAKER_WC_Delivery_Date_Time();
