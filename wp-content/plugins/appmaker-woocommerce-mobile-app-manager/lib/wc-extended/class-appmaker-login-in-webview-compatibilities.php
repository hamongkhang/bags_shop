<?php
/**
 * APPMAKER_LOGINWEB_Compatibilities. Compatibilities with other themes or plugins.
 *
 * @package AppmakerCheckout
 */

defined( 'ABSPATH' ) || exit;


/**
 * AAPPMAKER_LOGINWEB_Compatibilities class.
 */
class APPMAKER_LOGINWEB_Compatibilities {
	
	public function __construct() {
        //https://wordpress.org/plugins/oa-social-login/ - support
		add_action('oa_social_login_filter_registration_redirect_url', array( $this, 'oa_social_login_redirect') , 2 , 2 );
		add_action('oa_social_login_filter_login_redirect_url', array( $this, 'oa_social_login_redirect') , 2 , 2 );		

        //https://wordpress.org/plugins/ultimate-member/
        add_action( 'um_on_login_before_redirect', array($this, 'ultimate_member_login_redirect'), 10, 1 );		
        add_action( 'um_registration_after_auto_login', array($this, 'ultimate_member_login_redirect'), 10, 1 );

        add_filter( 'login_redirect', array($this, 'appmaker_wp_login_redirect'),10, 3 );
        
        //https://wordpress.org/plugins/added-to-cart-popup-woocommerce/        
        add_action( 'plugins_loaded', array( $this, 'remove_scripts' ), 999 );  

        //digits
        add_filter('digits_login_redirect',array( $this, 'oa_social_login_redirect') , 2 , 1 );

        //http://xootix.com/easy-login-for-woocommerce
        add_filter( 'xoo_el_login_redirect', array($this, 'oa_social_login_redirect'),2, 2 );
        add_filter( 'xoo_el_register_redirect', array($this, 'oa_social_login_redirect'),2, 2 );
        if(class_exists('OTP\MoOTP') ){
            add_filter('wp_redirect' ,  array($this ,'miniorange_login_redirect' ), 99, 2 );
        }
        
    }

    public function miniorange_login_redirect($redirect_to , $status ) {
        $url = site_url();
        $regex = '(^'.$url.'$)';
        $is_match = preg_match($regex, $redirect_to, $url);
        if(  strpos( $redirect_to, 'open_in_webview' ) != true  && $is_match && strpos( $redirect_to, 'rest_route' ) != true) {
            $user = wp_get_current_user();
            if( $user ) {
                $obj = new APPMAKER_Login_In_Webview();
                $redirect_to = $obj->login_redirect( $redirect_to, $user );
            }           
        }
        return $redirect_to;        
    }

    public function remove_scripts(){

        if( class_exists('Xoo_CP_Public') ) {
            remove_action( 'wp_footer', array( Xoo_CP_Public::get_instance(), 'get_popup_markup' ) );    
            remove_action( 'wp_enqueue_scripts', array( Xoo_CP_Public::get_instance(), 'enqueue_scripts' ) );  
        }
        return true;
    }

    public function appmaker_wp_login_redirect( $redirect_to, $requested_redirect_to, $user ) {

        $obj = new APPMAKER_Login_In_Webview();
        $redirect_to = $obj->login_redirect( $redirect_to, $user );
        return $redirect_to;
    }

    public function oa_social_login_redirect( $url , $user = false ) {

        $obj = new APPMAKER_Login_In_Webview();
        $url = $obj->login_redirect( $url, $user );
        return $url;
    }

    public function ultimate_member_login_redirect( $user_id ) {
        if( !empty( $user_id ) ) {
           $user = get_userdata($user_id);
           $obj = new APPMAKER_Login_In_Webview();
           $url = $obj->login_redirect(false,$user);
           wp_safe_redirect($url);
           exit;
        }
        return $user_id;
     }
 
}
new APPMAKER_LOGINWEB_Compatibilities();
