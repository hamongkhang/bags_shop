<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_XLWUEV {


	public function __construct() {
        remove_action('wp_login', array(XLWUEV_Woocommerce_Confirmation_Email_Public::instance(),'custom_form_login_check') );
        add_filter('appmaker_wc_registration_response', array($this, 'user_register_email_verification') , 10, 1 );
        add_filter('appmaker_wc_login_response', array($this, 'user_login_email_verification') , 10, 1 );
    }

    public function user_register_email_verification( $return ) {

        $is_xlwuev_restrict_user = XlWUEV_Common::get_setting_value( 'wuev-general-settings', 'xlwuev_restrict_user' );
        // $status = false;
        // $user_id = get_current_user_id();
        // if( $user_id ) {
        //     $status = get_user_meta( (int) $user_id, 'wcemailverified', true );
        // }      
        if ( '1' == $is_xlwuev_restrict_user  ) {            
            $registration_message        = strip_tags ( XlWUEV_Common::maybe_parse_merge_tags( XlWUEV_Common::get_setting_value( 'wuev-messages', 'xlwuev_email_registration_message' ) ));
            $registration_message        = str_replace('Resend Confirmation Email','',$registration_message);
            $return = new WP_Error("user_verification",  $registration_message );
        } 
        return $return;

    }

    public function user_login_email_verification( $return ) {

        $is_xlwuev_restrict_user = XlWUEV_Common::get_setting_value( 'wuev-general-settings', 'xlwuev_restrict_user' );
        $status = false;
        $user_id = get_current_user_id();
        if( $user_id ) {
            $status = get_user_meta( (int) $user_id, 'wcemailverified', true );
        }      
        if( 'true' !== $status && '1' == $is_xlwuev_restrict_user) {
           
            $message        = strip_tags (XlWUEV_Common::maybe_parse_merge_tags( XlWUEV_Common::get_setting_value( 'wuev-general-settings', 'xlwuev_email_error_message_not_verified_outside' ) ) );    
            $message        = str_replace('Resend Confirmation Email','',$message);        
            $return         =  new WP_Error("user_login_verification",  $message );
        }
        return $return;

    }
    
}
new APPMAKER_WC_XLWUEV();