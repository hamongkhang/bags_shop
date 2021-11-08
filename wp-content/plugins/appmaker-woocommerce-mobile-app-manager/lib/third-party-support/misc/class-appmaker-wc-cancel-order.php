<?php
/**
 * Created by IntelliJ IDEA.
 * User: shifa
 * Date: 16/7/18
 * Time: 6:21 PM
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_cancel_order {

    public function __construct() {
        add_filter( 'appmaker_wc_valid_order_statuses_for_cancel', array( $this, 'Add_order_status' ),10,1);
        add_filter( 'appmaker_wc_should_cancel_order',array($this,'cancel_order'),2,2);
    }

    public function Add_order_status($status)
    {
        array_push($status, "processing");
        return $status;
    }

    public function cancel_order($bool=true,$order)
    {
        if(empty($order)){
            return false;
        }
        $_GET['order']        = APPMAKER_WC_Helper::get_property($order,'order_key');
        $_GET['order_id']     = APPMAKER_WC_Helper::get_id( $order );
        $_GET['redirect']     = false;
        $_GET['_wpnonce']     = wp_create_nonce( 'woocommerce-cancel_order' );
        $_GET['cancel_order'] = true;
        if (!is_user_logged_in()) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'wc-cancel-order'), '', array('response' => 403));
        }

        $_REQUEST['_wpnonce']=wp_create_nonce('woocommerce-mark-order-cancell-request-myaccount');
        if (!check_admin_referer('woocommerce-mark-order-cancell-request-myaccount')) {
            wp_die(__('You have taken too long. Please go back and retry.', 'wc-cancel-order'), '', array('response' => 403));
        }

        $order_id = isset($_GET['order_id']) && (int)$_GET['order_id'] ? (int)$_GET['order_id'] :
            '';

        if (!$order_id) {
            die();
        }

        $order = wc_get_order($order_id);
        $order->update_status('cancel-request');
        $mails = WC()->mailer()->get_emails();
        if( isset($mails['WC_Email_Cancel_Request_Order'])) {
            $mails['WC_Email_Cancel_Request_Order']->trigger($order_id);
        } elseif( isset( $mails['Wc_Cancel_Request_Received'] ) ) {
            $mails['Wc_Cancel_Request_Received']->trigger($order_id);
        }       

        return false;

    }


}
new APPMAKER_WC_cancel_order();