<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_POINTS_AND_REWARDS extends APPMAKER_WC_REST_Controller {

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

	public function __construct() {
		parent::__construct();
		add_filter( 'appmaker_wc_cart_meta_response', array( $this, 'updateCartMeta' ) );
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
        add_filter( 'appmaker_wc_account_page_response', array( $this, 'Display_Total_points' ) );
	}
    public function Display_Total_points($return){
	    $user_id=get_current_user_id();
        list( $singular, $plural ) = explode( ':', get_option( 'wc_points_rewards_points_label' ) );
        $return['points'] = array(
            'title'  => $singular.' = '.WC_Points_Rewards_Manager::get_users_points( $user_id ),
            'icon'   => array(
                'android' => 'credit-card',
                'ios'     => 'ios-cash',
            ),

        );
        return $return;
    }
	public function getRewardCart() {
		if ( $this->rewardCart === null ) {
			$this->rewardCart = new WC_Points_Rewards_Cart_Checkout();
		}
		return $this->rewardCart;
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
		$_POST['wc_points_rewards_apply_discount']        = true;
		$_POST['wc_points_rewards_apply_discount_amount'] = $request['redeem_points'];
		$this->getRewardCart()->maybe_apply_discount();

		return APPMAKER_WC::$api->APPMAKER_WC_REST_Cart_Controller->cart_items();

	}

	public function updateCartMeta( $meta ) {

		if ( class_exists( 'WC_Points_Rewards_Cart_Checkout' ) ) {

			ob_start();
			$this->getRewardCart()->render_earn_points_message();
			$data                   = ob_get_clean();
			$meta['header_message'] = strip_tags(html_entity_decode($data ));

			ob_start();
			$this->getRewardCart()->render_redeem_points_message();
			$redeem_block = ob_get_clean();
			if ( $redeem_block ) {

				$redeem_block_text = strip_tags( html_entity_decode( $redeem_block ) );
				preg_match_all( '/([0-9,.]+)/', $redeem_block_text, $values );

				$meta['redeem_block'] = array(
					'points'        => $values[0][0],
					'value'         => trim($values[0][1],"."),
					'message'       => $redeem_block_text,
					'display_input' => 'yes' === get_option( 'wc_points_rewards_partial_redemption_enabled' ),
				);
			}

			if ( ! empty( $meta['coupons_applied'] ) ) {

				$coupon_discounted = $meta['coupon_discounted'];
				$total_amount = 0;
				foreach ( $coupon_discounted as $key => $coupon ) {
					$total_amount += $coupon['discount'];
					if ( strstr( $coupon['coupon'], 'wc_points_redemption_' ) ) {
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
							'display_input' => 'yes' === get_option( 'wc_points_rewards_partial_redemption_enabled' ),
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
		}

		return $meta;
	}
}

new APPMAKER_WC_POINTS_AND_REWARDS();
