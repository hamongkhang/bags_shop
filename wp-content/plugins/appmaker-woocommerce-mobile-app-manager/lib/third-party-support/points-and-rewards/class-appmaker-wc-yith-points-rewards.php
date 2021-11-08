<?php
/**
 * Created by IntelliJ IDEA.
 * User: shifa
 * Date: 4/10/19
 * Time: 12:55 PM
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_YITH_POINTS_AND_REWARDS extends APPMAKER_WC_REST_Controller
{

    /**
     * Endpoint namespace.
     *
     * @var string
     */
    protected $namespace = 'appmaker-wc/v1';

    /**
     * Route base.
     *
     * @var string
     */
    protected $rest_base = 'cart/redeem';

    private $rewardCart = null;

    public function __construct()
    {
        parent::__construct();
        add_filter('appmaker_wc_cart_meta_response', array($this, 'updateCartMeta'));
        register_rest_route($this->namespace, '/' . $this->rest_base . '/add', array(
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array($this, 'redeemPoints'),
                'permission_callback' => array($this, 'api_permissions_check'),
            ),

        ));

        register_rest_route($this->namespace, '/' . $this->rest_base . '/remove', array(
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array($this, 'removePoints'),
                'permission_callback' => array($this, 'api_permissions_check'),
            ),

        ));
        add_filter('appmaker_wc_account_page_response', array($this, 'Display_Total_points'));
    }

    public function Display_Total_points($return){
        $title = YITH_WC_Points_Rewards()->get_option( 'my_account_page_label', __( 'My Points', 'yith-woocommerce-points-and-rewards' ) );
        $points   = get_user_meta( get_current_user_id(), '_ywpar_user_total_points', true );
        $points   = ( $points == '' ) ? 0 : $points;
        $mypoints = array('points' => array(
            'title'  => $title.' = '.$points,
            'icon'   => array(
                'android' => 'credit-card',
                'ios'     => 'ios-cash',
            ),
			),
        );
		$return       = array_slice( $return, 0, 4, true ) + $mypoints +
		array_slice( $return, 4, count( $return ) - 3, true );
        return $return;
    }

    public function removePoints( $request ) {
        if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
            define( 'WOOCOMMERCE_CART', true );
        }
        $added = WC()->cart->remove_coupon( $request['coupon'] );
        if ( ! $added ) {
            $return = $this->get_wc_notices_errors();
            if ( ! is_wp_error( $return ) ) {
                $return = new WP_Error( 'invalid_coupon', 'Invalid coupon' );
            }
        } else {
            WC()->cart->persistent_cart_update();
            $return = APPMAKER_WC::$api->APPMAKER_WC_REST_Cart_Controller->cart_items();
        }

        return $return;

    }
    public function redeemPoints( $request )
    {

        if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
            define( 'WOOCOMMERCE_CART', true );
        }
        $_POST['ywpar_rate_method'] = YITH_WC_Points_Rewards()->get_option( 'conversion_rate_method' );
        $_POST['ywpar_max_discount'] = YITH_WC_Points_Rewards_Redemption()->calculate_rewards_discount();
        $_POST['ywpar_points_max'] = YITH_WC_Points_Rewards_Redemption()->get_max_points();
        $_POST['coupon_code'] = $request['coupon'];
        $_POST['ywpar_input_points'] = $request['redeem_points'];
        $min_value_to_reedem = YITH_WC_Points_Rewards_Redemption()->get_conversion_rate_rewards(get_woocommerce_currency());
        $min_value_to_reedem_error_msg = apply_filters('ywpar_min_value_to_reedem_error_msg',__('The minimum value to reedem is ','yith-woocommerce-points-and-rewards') . $min_value_to_reedem['points'] , $min_value_to_reedem['points'] );
        if($_POST['ywpar_points_max'] < $min_value_to_reedem['points']){
            return new WP_Error( 'error',$min_value_to_reedem_error_msg  );
       }
       else if($request['redeem_points'] > $_POST['ywpar_points_max'] || $request['redeem_points'] <$min_value_to_reedem['points'] ){
           return new WP_Error( 'error',$min_value_to_reedem_error_msg  );
       } else {

           $_POST['ywpar_input_points_check'] = 1;
           $_POST['ywpar_input_points_nonce'] = wp_create_nonce('ywpar_input_points_nonce');

           if( ! isset( $_POST['ywpar_rate_method'] ) || ( isset( $_POST['ywpar_rate_method'] ) && $_POST['ywpar_rate_method'] == '' ) ) {
               $_POST['ywpar_rate_method'] = 'fixed';               
           }       

           YITH_WC_Points_Rewards_Redemption()->apply_discount();

           return APPMAKER_WC::$api->APPMAKER_WC_REST_Cart_Controller->cart_items();
       }

    }
    public function updateCartMeta( $meta ) {

            $coupons = WC()->cart->get_applied_coupons();
            $show_cart_messages = true;
            //the messages will not show if the coupon is just applied to cart so the user is redeeming point and the option to earn while redeeming is activated
            if ( YITH_WC_Points_Rewards_Redemption()->check_coupon_is_ywpar( $coupons ) && get_option('ywpar_disable_earning_while_reedeming','no') == 'yes') {
                $show_cart_messages = false;
            }
            $enable_cart_messages = ( YITH_WC_Points_Rewards()->get_option('enabled_cart_message') == 'yes' || YITH_WC_Points_Rewards()->get_option( 'enabled_rewards_cart_message' ) == 'yes' ) ? true : false ;
            $is_user_enabled_yith_premium = true ;
            if ( function_exists('is_user_enabled') &&  ! YITH_WC_Points_Rewards()->is_user_enabled() ) {
                $is_user_enabled_yith_premium = false;
            }
            if ( $enable_cart_messages &&  $is_user_enabled_yith_premium && $show_cart_messages && YITH_WC_Points_Rewards()->is_enabled() ) {
               ob_start();
               YITH_WC_Points_Rewards_Frontend()->print_messages_in_cart();
               $data = ob_get_contents();
               ob_end_clean();
               if( $meta['header_message'] ) {
                $meta['header_message'] .= strip_tags( html_entity_decode( $data));
               }else 
                $meta['header_message'] = strip_tags( html_entity_decode( $data));
           }
          ob_start();
          YITH_WC_Points_Rewards_Frontend()->print_rewards_message_in_cart();
          $redeem_block = ob_get_contents();
          ob_end_clean();
            if ($redeem_block) {
                $redeem_block_text = strip_tags( html_entity_decode( $redeem_block ) );
                preg_match_all( '/([0-9,.]+)/', $redeem_block_text, $values );

                $meta['redeem_block'] = array(
                    'points'        => $values[0][0],
                    'value'         => trim($values[0][1],"."),
                    'message'       => $redeem_block_text,
                    'display_input' =>true
                );
            }

            if ( ! empty( $meta['coupons_applied'] ) ) {

                $coupon_discounted = $meta['coupon_discounted'];
                $total_amount = 0;
                foreach ( $coupon_discounted as $key => $coupon ) {
                    $total_amount += $coupon['discount'];
                    if ( strstr( $coupon['coupon'], 'ywpar_discount_' ) ) {
                        $meta['redeem_applied'] = $coupon;
                        if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
                            //	$coupon_name = 'points';
                            //	$meta['coupon_discounted'][ $key ]['coupon'] = $coupon_name;
                            //	$meta['coupons_applied'][ $key ] = $coupon_name;
                        } else {
                            unset( $meta['coupon_discounted'][ $key ] );
                            unset( $meta['coupons_applied'][ $key ] );
                        }

                        $meta['redeem_block'] = array(
                            'points'        => '',
                            'value'         => '',
                            'message'       => '',
                            'display_input' =>true
                        );

                    }
                    if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
                        //out
                        unset( $meta['coupon_discounted'][ $key ] );
                        unset( $meta['coupons_applied'][ $key ] );
                    } else {
                        //cart

                    }
                }
                if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
                    //	$coupon_name = 'points';
                    $meta['coupons_applied'] = array( 'Discount' );
                    $meta['coupon_discounted'] = array(array(
                        'coupon' => 'Discount',
                        'discount' => $total_amount,
                        'discount_display' => APPMAKER_WC_Helper::get_display_price( $total_amount ),
                    ),
                    );
                } else {

                }
            }


        return $meta;
    }

}
new APPMAKER_WC_YITH_POINTS_AND_REWARDS();