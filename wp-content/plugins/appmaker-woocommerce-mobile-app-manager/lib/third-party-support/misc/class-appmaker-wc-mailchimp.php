<?php
/**
 * Created by IntelliJ IDEA.
 * User: shifa
 * Date: 11/12/18
 * Time: 2:54 PM
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
class APPMAKER_WC_mailchimp{
    public function __construct() {

        add_filter( 'appmaker_wc_checkout_fields', array( $this, 'fields' ),2,2 );

    }
    public function fields($return,$section){

        $additional_fields=array();
        $wc_mailchimp_list_id= WC_Mailchimp_Integration::instance(1,1)->get_option( 'wc_mailchimp_list_id', 0 );
        if ( 0 === $wc_mailchimp_list_id ) {
            return false;
        }
        $lists = WC_Mailchimp_Integration::instance(1,1)->fetch_mailchimp_lists();
        if ( ! isset( $lists[ $wc_mailchimp_list_id ] ) ) {
            //List no longer exists on MailChimp
            return false;
        }
        $subscribe_checked = apply_filters( 'woocommerce_mailchimp_integration_subscribe_checked_by_default', 'yes' === WC_Mailchimp_Integration::instance(1,1)->get_option( 'wc_mailchimp_subscribe_checkbox_checked_by_default', 'no' ) );
        if ( $section==='order' && $lists !== false ) {
            do_action( 'woocommerce_mailchimp_integration_before_subscribe_form' );

            $additional_fields['mailchimp-subscribe'] = array(
                'type'=>'checkbox',
                'show'    => 0,
                'link'    => '',
                'value' =>'yes',
                'default'=>$subscribe_checked,
                'label'=>esc_html(WC_Mailchimp_Integration::instance(1,1)->get_option( 'wc_mailchimp_text_label' ) )

            );
            do_action( 'woocommerce_mailchimp_integration_after_subscribe_form' );

        }
        return array_merge( $additional_fields, $return );
    }
}
new APPMAKER_WC_mailchimp();