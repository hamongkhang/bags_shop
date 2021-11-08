<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_SUMO_POINTS_AND_REWARDS extends APPMAKER_WC_REST_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'appmaker-wc/v1';
    private $options;
    public $plugin = 'appmaker_wc';
	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'cart/redeem';

	private $rewardCart = null;

	public function __construct() {
		parent::__construct();
		add_filter( 'appmaker_wc_cart_meta_response', array( $this, 'updateCartMeta' ),2,1 );
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/add', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'redeemPoints' ),
				'permission_callback' => array( $this, 'api_permissions_check' ),
			),

		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/remove', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'removePoints' ),
				'permission_callback' => array( $this, 'api_permissions_check' ),
			),

        ) );

        register_rest_route( $this->namespace, '/sumo/rewards', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_rewards_data' ),
				'permission_callback' => array( $this, 'api_permissions_check' ),
			),
		) );

        register_rest_route( $this->namespace, '/sumo/user/register', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'sumo_user_register' ),
				'permission_callback' => array( $this, 'api_permissions_check' ),
			),
		) );

        $this->options = get_option( $this->plugin . '_settings' );
        add_filter( 'appmaker_wc_account_page_response', array( $this, 'Display_Total_points' ) );
        add_action('appmaker_wc_user_registered', array($this,'sumo_user_register') , 2 , 2);
        add_filter('appmaker_wc_referral', array( $this,'get_sumo_referral_details') , 2, 2);
	}

    public function get_sumo_referral_details( $return, $request ) {
        $url = site_url();
        $user = wp_get_current_user();
        $return = array( 'url' => '' , 
                    'code' => '',
                    'status' => true,
                    'provider' => 'sumo'
                );
        if( $user && is_user_logged_in() ) {
            $username = $user->user_login;
            $url   = $url.'/?ref='.$username;
            $return['url'] = $url;
            $return['code'] = $username;
            $return['count'] = get_user_meta( $user->ID , 'referral_link_count_value' , true ) ; 
            if( class_exists('RS_Points_Data') ) {  
                $PointData          = new RS_Points_Data( $user->ID ) ;
                $AvailablePoints    = $PointData->total_available_points() ;
                $return['points']   = $AvailablePoints;
            }                         
        }        
        
        return $return;
    }

    public function sumo_user_register( $user_id,$request ) {
        //$return = array();
        if( isset($request['ref']) && class_exists('RSFunctionForReferralSystem') ) {
           $_GET['ref'] = $request['ref'];
           $obj = new RSFunctionForReferralSystem();
           $obj->set_cookie_for_referral();
           //$obj->award_points_for_referral_account_signup($user_id);
           do_action( 'user_register', $user_id );
        }        
       // return $return;
    }

    public function get_rewards_data( $request ) {
        $return = array();
        $UserId = get_current_user_id();
        if( $UserId && class_exists('RS_Points_Data') ) {
            $title              = __( 'Total Points' , SRP_LOCALE );
            $PointData          = new RS_Points_Data( $UserId ) ;
            $AvailablePoints    = $PointData->total_available_points() ;
            $return             = array( 'points' => $AvailablePoints );
        }
        return $return;
    }

    public function Display_Total_points($return){
	   /* $user_id = get_current_user_id();
        list( $singular, $plural ) = explode( ':', get_option( 'wc_points_rewards_points_label' ) );
        $return['points'] = array(
            'title'  => $singular.' : '.WC_Points_Rewards_Manager::get_users_points( $user_id ),
            'icon'   => array(
                'android' => 'credit-card',
                'ios'     => 'ios-cash',
            ),

        );*/

        $base_url = site_url();
        $url      =  wc_get_page_permalink( 'myaccount' );
        if( empty( $url ) ){
            $url = $base_url.'/my-account';
        }        
        $api_key = $this->options['api_key'];
        $user_id = get_current_user_id();
        $access_token = apply_filters( 'appmaker_wc_set_user_access_token', $user_id );       
        $url = base64_encode($url);
        $url = $base_url.'/?rest_route=/appmaker-wc/v1/user/redirect/&url='.$url.'&api_key='.$api_key.'&access_token='.$access_token.'&user_id='.$user_id;
        $title = get_option( 'rs_my_rewards_title' );
        $rewards = array('rewards'=>array(
            'title'  => $title,
            'icon'   => array(
                'android' => 'credit-card',
                'ios'     => 'ios-cash',
            ),
            'action' => array(
                'type' => 'OPEN_IN_WEB_VIEW',
                'params' => array( 'url' => $url),
            ),
          ),
        );       
        $return = array_slice($return, 0, 1, true) +
        $rewards +
        array_slice($return, 1, count($return)-1, true);
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

	public function redeemPoints( $request ) {

        
	 	if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
			define( 'WOOCOMMERCE_CART', true );
        }
        if($request['redeem_points']){
            $redeem_points = $request['redeem_points']; 
        }else if(isset($_POST['redeem_points'])){
            $redeem_points = $_POST['redeem_points'];
        }else{
            $redeem_points = 0;
        }
		$_POST['rs_apply_coupon_code']        = 'Apply Reward Points';
        $_POST['rs_apply_coupon_code_field'] = $redeem_points;
        $UserId  = get_current_user_id() ;
        if( class_exists('RS_Points_Data') ) {

            $pointsData      = new RS_Points_Data( $UserId ) ;
            $points          = $pointsData->total_available_points();
            $maxredeemederr  = get_option( 'rs_redeem_max_error_message' );
            $min_error       = do_shortcode( get_option( "rs_minimum_redeem_point_error_message" ) );
            if ( $points > 0 ) {
                $MinUserPoints = (get_user_meta( $UserId , 'rsfirsttime_redeemed' , true ) != '1') ? get_option( "rs_first_time_minimum_user_points" ) : get_option( "rs_minimum_user_points_to_redeem" ) ;
                if ( $points < $MinUserPoints ) {
                    return new WP_Error( 'error',$min_error );
                }else if($redeem_points >  $points){
                    return new WP_Error( 'error',$maxredeemederr );
                }else{
                    add_filter('fprewardsystem_page_redirect', '__return_false' );
                    RSRedeemingFrontend::redeem_point_for_user();	
                }
            }   
        }            	

		return APPMAKER_WC::$api->APPMAKER_WC_REST_Cart_Controller->cart_items();

	}

	public function updateCartMeta( $meta ) {

		if ( class_exists( 'RS_Points_Data' ) ) {
            if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
                define( 'WOOCOMMERCE_CART', true );
            }
    
            if ( is_user_logged_in() ) {
                $cart = WC()->session->get( 'cart', null );
                if ( is_null( $cart ) || empty( $cart ) ) {
                    WC()->cart->empty_cart( false );
                    WC()->session->set( 'cart', null );
                    WC()->cart->get_cart_from_session();
                }
            }
            WC()->cart->check_cart_items();
            WC()->cart->persistent_cart_update();
            $auto_redeem = get_option('rs_enable_disable_auto_redeem_points');
            $is_redeem_module_enabled = get_option( 'rs_redeeming_activated' );
            $meta['header_message'] = '';
            $UserId  = get_current_user_id() ;   
            $PointsData      = new RS_Points_Data( $UserId ) ;
            $Points          = $PointsData->total_available_points() ;
            if($is_redeem_module_enabled == 'yes' && 'yes' == $auto_redeem && $Points > 0 ){
                   add_filter('fprewardsystem_page_redirect', '__return_false' );
                   RSRedeemingFrontend::redeem_points_for_user_automatically();	
                   $auto_redeem_msg = get_option( 'rs_automatic_success_coupon_message' , 'AutoReward Points Successfully Added' );
                   if(!empty($auto_redeem_msg)){
                    $meta['header_message'] .= strip_tags(html_entity_decode($auto_redeem_msg ));  	
                   }
            }

			ob_start();
			RSPointExpiry::available_points_for_user();
			$data                   = ob_get_clean();
            $meta['header_message'] .= strip_tags(html_entity_decode($data ));  	
                           

           
            if(empty($UserId)){
                ob_start();
                RSFrontendAssets::message_for_guest();
                $guest_msg = ob_get_clean();
                if($guest_msg){
                    $guest_msg = str_replace('My account','',$guest_msg);
                    $meta['header_message'] .= strip_tags(html_entity_decode($guest_msg ));
                }
            }else{
                if ( get_option( 'woocommerce_calc_taxes' ) == 'yes' && get_option( 'rs_show_hide_message_notice_for_redeeming' ) == '1' ) {
                    $tax_msg = get_option( 'rs_msg_for_redeem_when_tax_enabled' ) ;
                    $meta['header_message'] .= strip_tags(html_entity_decode($tax_msg )). "\n";
                 }  
               if( class_exists('RSProductPurchaseFrontend') ){
                    ob_start();
                    RSProductPurchaseFrontend::messages_and_validation_for_product_purcahse();
                    $product_message = ob_get_clean();                                   
                    $product_message = str_replace("<br>", "\n", $product_message );                
                    if($product_message){
                        $meta['header_message'] .= strip_tags(html_entity_decode($product_message )). "\n";
                    }
                }
                if( class_exists('RSFrontendAssets') ){
                    ob_start();
                    RSFrontendAssets::complete_message_for_purchase();                
                    $cart_complete_message =  ob_get_clean();                
                    if($cart_complete_message){
                       $meta['header_message'] .= strip_tags(html_entity_decode($cart_complete_message )). "\n";
                    }
                }                 
                    
                $PointPriceValue = array() ;
                $PointPriceType  = array() ; 
                $PointsData      = new RS_Points_Data( $UserId ) ;
                $Points          = $PointsData->total_available_points() ;
                $UserInfo        = get_user_by( 'id' , $UserId ) ;
                $Username        = $UserInfo->user_login ;
                $AutoRedeem      = 'auto_redeem_' . strtolower( $Username ) ;
                $AppliedCoupons  = WC()->cart->get_applied_coupons() ;
                foreach ( WC()->cart->cart_contents as $item ) {
                    $ProductId         = ! empty( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : $item[ 'product_id' ] ;
                    $PointPriceType[]  = check_display_price_type( $ProductId ) ;
                    $CheckIfEnable     = calculate_point_price_for_products( $ProductId ) ;
                    if ( ! empty( $CheckIfEnable[ $ProductId ] ) )
                        $PointPriceValue[] = $CheckIfEnable[ $ProductId ] ;
                }
                if ( $Points > 0 ) {
                    $MinUserPoints = (get_user_meta( $UserId , 'rsfirsttime_redeemed' , true ) != '1') ? get_option( "rs_first_time_minimum_user_points" ) : get_option( "rs_minimum_user_points_to_redeem" ) ;
                    if ( $Points >= $MinUserPoints ) {
                        if ( srp_cart_subtotal() >= get_option( 'rs_minimum_cart_total_points' ) ) {
                            if ( ! in_array( $AutoRedeem , $AppliedCoupons ) ) {
                                if ( ! srp_check_is_array( $PointPriceValue ) && ! in_array( '2' , $PointPriceType ) ) {

                                    $placeholder = get_option( 'rs_show_hide_redeem_placeholder' ) == '1' ? get_option( 'rs_redeem_field_placeholder' ) : '' ;
                                    $redeem_block_text = strip_tags( html_entity_decode( $placeholder ) );
                                    //preg_match_all( '/([0-9,.]+)/', $redeem_block_text, $values );
                                    if($is_redeem_module_enabled == 'yes' && 'no' == $auto_redeem ){

                                        $meta['redeem_block'] = array(
                                            'points'        => '',
                                            'value'         => $Points ,
                                            'message'       => $redeem_block_text,
                                            'display_input' => true,
                                        );    
                                    }                                                              
                                }
                            }
                        }else{
                            if ( get_option( 'rs_show_hide_minimum_cart_total_error_message' ) == '1' ) {
                                $CurrencyValue = srp_formatted_price( round_off_type_for_currency( get_option( 'rs_minimum_cart_total_points' ) ) ) ;
                                $ReplacedMsg   = str_replace( "[carttotal]" , $CurrencyValue , get_option( 'rs_min_cart_total_redeem_error' ) ) ;
                                $FinalMsg      = str_replace( "[currencysymbol]" , "" , $ReplacedMsg ) ;
                                $meta['header_message'] .= strip_tags( html_entity_decode($FinalMsg) ). "\n";
                            }
                        }
                    }else {
                        if ( get_user_meta( $UserId , 'rsfirsttime_redeemed' , true ) != '1' ) {
                            if ( get_option( 'rs_show_hide_first_redeem_error_message' ) == '1' ) {
                                $ReplacedMsg = str_replace( "[firstredeempoints]" , get_option( 'rs_first_time_minimum_user_points' ) , get_option( 'rs_min_points_first_redeem_error_message' ) ) ;
                                $meta['header_message'] .= strip_tags( html_entity_decode($ReplacedMsg) ). "\n";
                            }
                        } else {
                            if ( get_option( 'rs_show_hide_after_first_redeem_error_message' ) == '1' ) {
                                $ReplacedMsg = str_replace( "[points_after_first_redeem]" , get_option( 'rs_minimum_user_points_to_redeem' ) , get_option( 'rs_min_points_after_first_error' ) ) ;
                                $meta['header_message'] .= strip_tags( html_entity_decode($ReplacedMsg) ). "\n";
                            }
                        }
                    }
                }else {
                    if ( get_option( 'rs_show_hide_points_empty_error_message' ) == '1' && ! srp_check_is_array( $PointPriceValue ) ) {
                        $empty_msg = get_option( 'rs_current_points_empty_error_message' ) ;
                        $meta['header_message'] .= strip_tags( html_entity_decode($empty_msg) ). "\n";
                    }
                }
            }	

			if ( ! empty( $meta['coupons_applied'] ) ) {

				$coupon_discounted = $meta['coupon_discounted'];
				$total_amount = 0;
				foreach ( $coupon_discounted as $key => $coupon ) {
					$total_amount += $coupon['discount'];
					if ( strstr( $coupon['coupon'], 'sumo_' ) ) {
						$meta['redeem_applied'] = $coupon;
						if ( ! defined( 'WOOCOMMERCE_CART' ) ) {
							//	$coupon_name = 'points';
							//	$meta['coupon_discounted'][ $key ]['coupon'] = $coupon_name;
							//	$meta['coupons_applied'][ $key ] = $coupon_name;
						} else {
							unset( $meta['coupon_discounted'][ $key ] );
							unset( $meta['coupons_applied'][ $key ] );
						}
                        if($is_redeem_module_enabled == 'yes' && 'no' == $auto_redeem ){
                            $meta['redeem_block'] = array(
                                'points'        => '',
                                'value'         => '',
                                'message'       => '',
                                'display_input' => true,
                            );
                        }

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
		}

		return $meta;
	}
}

new APPMAKER_WC_SUMO_POINTS_AND_REWARDS();
