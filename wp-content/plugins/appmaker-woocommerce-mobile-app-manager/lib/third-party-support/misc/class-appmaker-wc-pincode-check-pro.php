<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_Pincode_Check {

	public function __construct() {
		add_filter( 'appmaker_wc_product_fields', array( $this, 'product_fields' ), 2, 2 );
		add_filter( 'appmaker_wc_add_to_cart_validate', array( $this, 'check_pincode' ), 2, 2 );
	}
	//picodecheck_ajax_submit_check
	public function check_pincode( $return, $params ) {
		global $table_prefix, $wpdb;		
		$pincode = sanitize_text_field( $params['pin_code'] );
		$user_id = get_current_user_id();		
		if( !isset( $params['pin_code']) && $user_id != 0 ) {
			$pincode = get_user_meta( $user_id, 'shipping_postcode', true );
			$params['pin_code'] = $pincode;
		}
		if(!isset($params['pin_code']) &&  isset( $_COOKIE['valid_pincode'] ) ) {
			$params['pin_code'] = $_COOKIE['valid_pincode'];
			$pincode = $params['pin_code'];
		}	
		$product_id = sanitize_text_field( $params['product_id'] );
		$phen_pincodes_list = get_post_meta( $product_id, 'phen_pincode_list' );
		$phen_pincode_list = $phen_pincodes_list[0];
		$star_pincode = substr( $pincode, 0, 3 ) . '*';
		$safe_zipcode = $pincode;
		if ( count( $phen_pincode_list ) == 0 ) {		
			$pincode      = substr( $pincode, 0, 3 );
			$table_pin_codes = $table_prefix . 'check_pincode_pro';
			$table_name =  $table_prefix."check_pincode_p"; //  in pincode plugin free version
			if ( $safe_zipcode ) {
				if( $wpdb->get_var("SHOW TABLES LIKE '$table_pin_codes'") == $table_pin_codes ) {
					$count = $wpdb->get_var( $wpdb->prepare( "select COUNT(*) from $table_pin_codes where `pincode` = %s ", $safe_zipcode ) );
					if ( $count == 0 ) {
						$count = $wpdb->get_var( $wpdb->prepare( "select COUNT(*) from $table_pin_codes where `pincode` LIKE %s ", $wpdb->esc_like( $pincode ) . '*%' ) );
					}				
				}elseif( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name ) {
					//pincode free version plugin fix						
					$count = $wpdb->get_var( $wpdb->prepare( "select COUNT(*) from $table_name where `pincode` = %s" , $safe_zipcode ) );
					setcookie("valid_pincode",$safe_zipcode,time() + (10 * 365 * 24 * 60 * 60),"/");					
				}
				
				if ( $count == 0 ) {
					$return = new WP_Error( 'cart_pin_error', 'Sorry, This item is not available at this pincode' );
				}
			} else {
				$return = new WP_Error( 'cart_pin_error', 'Sorry, This item is not available at this pincode' );
			}
		} else {
			$phen_pincode_list = $phen_pincodes_list[0];
			if ( ! ( array_key_exists( $wpdb->esc_like( $pincode ), $phen_pincode_list ) || array_key_exists( $star_pincode, $phen_pincode_list ) ) ) {
				$return = new WP_Error( 'cart_pin_error', 'Sorry, This item is not available at this pincode' );
			}
		}
		$this->set_pincode( $safe_zipcode );

		return $return;
	}

	public function set_pincode( $safe_zipcode ) {
		$user_ID  = get_current_user_id();
		
		if( empty( $user_ID ) ) {
			$customer = new WC_Customer();
			$customer->set_shipping_postcode( $safe_zipcode );
			$user_ID = get_current_user_id();
		}		
		if ( isset( $user_ID ) && 0 !== $user_ID ) {
			update_user_meta( $user_ID, 'shipping_postcode', $safe_zipcode ); //for setting shipping postcode
		}
		setcookie("valid_pincode",$safe_zipcode,time() + ( 24 * 60 * 60 ),"/");
	}

	/**
	 * @param array $fields
	 * @param WC_Product $product
	 *
	 * @return array|mixed|void
	 */
	public function product_fields( $fields, $product ) {
			$user_id = get_current_user_id();
			$default_value = '';
		if ( $user_id !== 0 ) {
			$default_value = get_user_meta( $user_id, 'shipping_postcode', true );
		}
		if( isset( $_COOKIE['valid_pincode'] ) ) {
			$default_value = $_COOKIE['valid_pincode'];
		}		
			$field = array(
			'pin_code' => array(
				'type'     => 'text',
				'label'    => 'Pincode',
	  			'default_value' => $default_value,
				'default'   => $default_value,
				'required' => true,
				'keyboardType' => 'numeric',
			),
		);
		$fields = APPMAKER_WC_Dynamic_form::get_fields( $field, 'product' );
		return $fields;
	}

}

new APPMAKER_WC_Pincode_Check();
