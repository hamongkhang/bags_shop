<?php

class APPMAKER_WC_Coupon {

	/**
	 * Start up
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_action( 'woocommerce_coupon_options_usage_restriction', array( $this, 'app_coupon_option' ) );
			add_action( 'woocommerce_coupon_options_save', array( $this, 'app_coupon_option_save' ) );

		}
		add_filter( 'woocommerce_coupon_is_valid', array( $this, 'app_woocommerce_coupon_code_validate' ), 1, 2 );
		add_filter( 'woocommerce_coupon_error', array( $this, 'app_woocommerce_coupon_error' ), 1, 3 );

	}

	/**
	 * @return object
	 */
	public function app_coupon_option() {
		// Individual use
		woocommerce_wp_checkbox( array(
			'id'          => 'appmaker_wc_app_only_coupon',
			'label'       => __( 'App only coupon', 'woocommerce' ),
			'description' => __( 'Check this box if the coupon only applied from app.', 'woocommerce' ),
		) );

	}

	public function app_coupon_option_save( $post_id ) {
		$appmaker_wc_app_only_coupon = isset( $_POST['appmaker_wc_app_only_coupon'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, 'appmaker_wc_app_only_coupon', $appmaker_wc_app_only_coupon );
	}

	/**
	 * @param $valid
	 * @param WC_Coupon $coupon
	 *
	 * @return bool
	 */
	public function app_woocommerce_coupon_code_validate( $valid, $coupon ) {
		if ( get_post_meta( $this->get_coupon_id( $coupon ), 'appmaker_wc_app_only_coupon', 'no' ) == 'yes' &&  ! defined( 'APPMAKER_WC_REQUEST' ) && !isset($_COOKIE['from_app_set']) ) {
			return false;
		}

		return $valid;
	}

	/**
	 * @param $err
	 * @param $err_code
	 * @param WC_Coupon $coupon
	 */
	public function app_woocommerce_coupon_error( $err, $err_code, $coupon ) {
		if ( get_post_meta( $this->get_coupon_id( $coupon ), 'appmaker_wc_app_only_coupon', 'no' ) == 'yes' && $err_code == WC_Coupon::E_WC_COUPON_INVALID_FILTERED && ! defined( 'APPMAKER_WC_REQUEST' ) ) {
			return __( 'Sorry, this coupon only valid for app users.', 'woocommerce' );
		}

		return $err;
	}

	/**
	 * To make compatible with old and new version of woocommerce
	 *
	 * @param WC_Coupon $coupon
	 *
	 * @return int
	 */
	public function get_coupon_id( $coupon ) {
		if ( method_exists( $coupon,'get_id' ) ) {
			return $coupon->get_id();
		} else {
			return $coupon->id;
		}
	}
}

new APPMAKER_WC_Coupon();
