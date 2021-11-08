<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_finaluser extends APPMAKER_WC_REST_Controller {

    public function __construct()
    {
        add_filter("appmaker_wc_forget_password", array($this,'finaluser_forget_password'),2,1);
       
	}
	public function finaluser_forget_password($request){
		global $wpdb;
		$email_body = get_option( 'ep_finaluser_forget_email');
		$forget_email_subject = get_option( 'ep_finaluser_forget_email_subject');			
					
		$admin_mail = get_option('admin_email');	
		if( get_option( 'admin_email_ep_finaluser' )==FALSE ) {
			$admin_mail = get_option('admin_email');						 
		}else{
			$admin_mail = get_option('admin_email_ep_finaluser');								
		}						
		$wp_title = get_bloginfo();

		$user_info = get_user_by( 'email',$request['email'] );
		if(isset($user_info->ID) ){

		
        $random_password = wp_generate_password( 12, false );
					// Get user data by field and data, other field are ID, slug, slug and login
		
			
		$update_user = wp_update_user( array (
					'ID' => $user_info->ID, 
					'user_pass' => $random_password
				)
		);
        
		$email_body = str_replace("[user_name]", $user_info->display_name, $email_body);
		$email_body = str_replace("[iv_member_user_name]", $user_info->user_login, $email_body);	
		$email_body = str_replace("[iv_member_password]", $random_password, $email_body); 
				
		$cilent_email_address =$user_info->user_email; //trim(get_post_meta($post_id, 'iv_form_modal_client_email', true));
						
		
				
		$auto_subject=  $forget_email_subject; 
								
		$headers = array("From: " . $wp_title . " <" . $admin_mail . ">", "Content-Type: text/html");
		$h = implode("\r\n", $headers) . "\r\n";
		wp_mail($cilent_email_address, $auto_subject, $email_body, $h);

		return array(
			'status'  => true,
			'message' => __( 'Link for password reset has been emailed to you. Please check your email.', 'appmaker-woocommerce-mobile-app-manager' ),
		);
			
		}

	}
}
new APPMAKER_WC_finaluser();