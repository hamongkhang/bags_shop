<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly


class APPMAKER_WC_wpgdpr_compliance
{
    public function __construct()
    {
        add_action( 'appmaker_wc_before_checkout',array($this, 'action_before_checkout' ),10,1);
        add_filter( 'appmaker_wc_registration_validate', array($this,'action_before_registration'),10,1 );

    }

   public function action_before_checkout($request)
    {
        $_POST['wpgdprc']=1;
    }

    public function action_before_registration($return)
    {
        $_POST['wpgdprc']=1;
        return $return;
    }


}
new APPMAKER_WC_wpgdpr_compliance();