<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_booking_date {

    public function __construct() {
        add_filter( 'appmaker_wc_product_fields', array( $this, 'product_fields_response_fix' ),10,1);
    }

    public function product_fields_response_fix($product)
    {
        $additional_fields['booking_calender'] = array(
            'type'        => 'datepicker',
            'label'       =>  'Booking Date' ,
            'required'    => true,
            'placeholder' =>  'booking_date' ,
        );
        $additional_fields['time_slot'] = array(
            'type'        => 'select',
            'label'       =>  'Booking Time' ,
            'required'    => true,

            'options'  => array(
                '1' => '09:00-11:00',
                '2' => '11:00-13:00',
                '3' => '13:00-15:00',
                '4' => '15:00-17:00',
                '5' => '17:00-19:00',
            ),
            'default'  => 'choose a time',
        );
        $fields = APPMAKER_WC_Dynamic_form::get_fields($additional_fields, 'product');
        return $fields;
    }


}
new APPMAKER_WC_booking_date();