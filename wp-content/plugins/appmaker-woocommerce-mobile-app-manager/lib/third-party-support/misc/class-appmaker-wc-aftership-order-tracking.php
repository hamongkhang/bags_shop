<?php
/**
 * Created by IntelliJ IDEA.
 * User: shifa
 * Date: 11/8/18
 * Time: 10:52 AM
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_Order_Tracking
{
    public $post_type='shop_order';
    public function __construct()
    {
       add_filter("woocommerce_rest_prepare_{$this->post_type}", array($this,'order_tracking'),2,2);

    }
    public function order_tracking($response,$post){

        $options = get_option('aftership_option_name');

        $values['aftership_tracking_provider'] = get_post_meta($response->data['id'], '_aftership_tracking_provider', true);
        $values['aftership_tracking_number'] = get_post_meta($response->data['id'], '_aftership_tracking_number', true);
        if (!$values['aftership_tracking_provider']) {
            return $response;
        }

        if (!$values['aftership_tracking_number']) {
            return $response;
        }
        if (array_key_exists('track_message_1', $options) && array_key_exists('track_message_2', $options)) {
            $track_message_1 = $options['track_message_1'];
            $track_message_2 = $options['track_message_2'];
        } else {
            $track_message_1 = 'Your order was shipped via ';
            $track_message_2 = 'Tracking number is ';
        }
        $response->data['top_notice'][] = array(
           'icon' => array(
                'android' => 'local-shipping',
                'ios'     => 'ios-paper-plane-outline',
            ),
            'message' => $track_message_1.$values['aftership_tracking_provider'].". ".$track_message_2.$values['aftership_tracking_number'].".",
            'button'=> array(
                'type'=> 'button',
                'text'=>'Track',
                'action'=>array(
                    'type'   => 'OPEN_URL',
                    'params' => array( 'url' =>'https://track.aftership.com/'.$values['aftership_tracking_number'].'?' ),

                )
            ),
        );

        return $response;

    }

}
new APPMAKER_WC_Order_Tracking();
