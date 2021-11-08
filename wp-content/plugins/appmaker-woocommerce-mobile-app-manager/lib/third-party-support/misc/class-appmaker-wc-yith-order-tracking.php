<?php
/**
 * Created by IntelliJ IDEA.
 * User: shifa
 * Date: 11/14/18
 * Time: 1:00 PM
 */


if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly


class APPMAKER_WC_Yith_Order_Tracking {

    public function __construct()
    {
        add_filter('woocommerce_rest_prepare_shop_order', array($this, 'get_tracking_details'), 10, 3);

    }


    public  function get_tracking_details($response, $post, $request){
        global $YWOT_Instance;
        $message = $YWOT_Instance->show_tracking_information ( new WC_Order($post->ID), get_option ( 'ywot_order_tracking_text' ), '' );
        $data            = get_post_custom ( $post->ID);
        $link_tracking = '';
        $ShippingProvider=!empty($data['ywot_carrier_name'][0])?$data['ywot_carrier_name'][0]:$data['ywot_carrier_id'][0];
        if (!empty( $data['ywot_tracking_code'][0]))
        {
            $ShippingProvider = strtolower($ShippingProvider);

            if (strpos($ShippingProvider, 'bluedart') !== false)
            {
                $link_tracking = 'http://www.bluedart.com/servlet/RoutingServlet?handler=tnt&action=awbquery&awb=awb&numbers='. $data['ywot_tracking_code'][0].'&sms=Y';
            }
            else if (strpos($ShippingProvider, 'delhivery') !== false)
            {
                $link_tracking = 'https://www.delhivery.com/track/package/'. $data['ywot_tracking_code'][0];
            }
            else if (strpos($ShippingProvider, 'fedex') !== false)
            {
                $link_tracking = 'https://www.fedex.com/apps/fedextrack/?action=track&trackingnumber='. $data['ywot_tracking_code'][0].'&cntry_code=us';
            }
           else if (strpos($ShippingProvider, 'wowexpress') !== false)
            {
                $link_tracking = 'https://www.wowexpress.in/#TrackOrder';
            }
           else if (strpos($ShippingProvider, 'dhl') !== false)
            {
                $link_tracking = 'http://www.dhl.com/content/g0/en/express/tracking.shtml?brand=DHL&AWB='.$data['ywot_tracking_code'][0];
            }
           else if (strpos($ShippingProvider, 'smsa_express') !== false)
            {
                $link_tracking = 'http://www.smsaexpress.com/Track.aspx?tracknumbers='.$data['ywot_tracking_code'][0];
            }
           else if (strpos($ShippingProvider, 'chunghwa_post') !== false)
           {
               $link_tracking = 'http://postserv.post.gov.tw/pstmail/main_mail.html';
           }
           else if (strpos($ShippingProvider, 'myship_7-11') !== false)
           {
               $link_tracking = 'https://eservice.7-11.com.tw/e-tracking/search.aspx';
           }
           else if (strpos($ShippingProvider, 'pickrr') !== false)
           {
               $link_tracking = 'http://www.pickrr.com/tracking/';
           }
           else{
               $link_tracking = '#';
           }
        }
        if(!empty($message)){

            if($link_tracking !== '#'){
                $response->data['top_notice'][] = array('icon' => array(
                    'android' => 'local-shipping',
                    'ios'     => 'ios-paper-plane-outline',
                ),
                    'message'=>strip_tags($message),
                    'button'=> array(
                        'type'=> 'button',
                        'text'=>'Track',
                        'action'=>array(
                            'type'   => 'OPEN_URL',
                            'params' => array( 'url' =>$link_tracking ),
    
                        )
                    ),
                );
            }else{
                $response->data['top_notice'][] = array('icon' => array(
                    'android' => 'local-shipping',
                    'ios'     => 'ios-paper-plane-outline',
                ),
                    'message'=>strip_tags($message),    
                    'button'=> false,                
                );
            }          

        }
        return $response;
    }


}
new APPMAKER_WC_Yith_Order_Tracking();