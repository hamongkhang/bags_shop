<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_Epeken {
	public static $function_kecamatan;
	public static $function_kabupaten;
	public static $function_kota;

	public static function className() {
		return 'APPMAKER_WC_Epeken';
	}

	public static function init() {
		add_filter( 'appmaker_wc_dynamic_form_response', array( self::className(), 'indonesia_field' ), 10, 3 );
		add_filter( 'appmaker_wc_dependency_billing_address_2', array(
			self::className(),
			'address_2_dependency',
		), 10, 2 );
		add_filter( 'appmaker_wc_dependency_shipping_address_2', array(
			self::className(),
			'address_2_dependency',
		), 10, 2 );

		add_filter( 'appmaker_wc_dependency_billing_city', array(
			self::className(),
			'city_dependency',
		), 10, 2 );
		add_filter( 'appmaker_wc_dependency_shipping_city', array(
			self::className(),
			'city_dependency',
		), 10, 2 );

		if ( function_exists( 'epeken_get_list_of_kecamatan' ) ) {
			self::$function_kabupaten = 'epeken_get_list_of_kota_kabupaten';
			self::$function_kecamatan = 'epeken_get_list_of_kecamatan';
		} else {
			self::$function_kabupaten = 'get_list_of_kota_kabupaten';
			self::$function_kecamatan = 'get_list_of_kecamatan';
		}

		if( function_exists('epeken_get_list_of_kota') ) {
			self::$function_kota = 'epeken_get_list_of_kota';
		}
	}

	public static function address_2_dependency( $dependency, $key ) {
		if ( 'billing_address_2' === $key ) {
			$dependency = array( 'on' => 'billing_city' );
		} elseif ( 'shipping_address_2' === $key ) {
			$dependency = array( 'on' => 'shipping_city' );
		} 		
		return $dependency;
	}

	public static function city_dependency( $dependency, $key ) {
		if( 'billing_city' === $key ) {
			$dependency = array('on' => 'billing_state' );
		}elseif( 'shipping_city' === $key ) {
			$dependency = array('on' => 'shipping_state' );
		}
		return $dependency;
	}

	/**
	 * @param $fields
	 * @param $section
	 *
	 * @return array|mixed
	 * @internal param array $args
	 *
	 */
	public static function indonesia_field( $response,$fields,$section ) {
		if ( 'billing' === $section || 'shipping' === $section ) {
			$response['items'][ $section . '_address_2' ]['type'] = 'dependent-select';
			$response['items'][ $section . '_address_2' ]['options'] = self::indonesia_country_override();
			$response['items'][ $section . '_address_2' ]['dependent'] = true;

			$response['items'][ $section . '_city' ]['type'] = 'dependent-select';
			$response['items'][ $section . '_city' ]['options'] = self::indonesia_city_override();
			$response['items'][ $section . '_city' ]['dependent'] = true;			
		}

		return $response;
	}

	public static function indonesia_country_override() {
		$countries = call_user_func( self::$function_kabupaten );
		$return    = array();
		foreach ( $countries as $key => $country ) {
			$return[ $key ] = array(
				'items' => array(),
			);
			$states         = call_user_func( self::$function_kecamatan, $key );
			if ( is_array( $states ) ) {
				foreach ( $states as $key1 => $state ) {
					$return[ $key ]['items'][ $key1 ] = $state;
				}
			}
		}

		return $return;
	}

	public function indonesia_city_override() {		
		
		$countries_obj = new WC_Countries();
	    $states = $countries_obj -> get_states('ID');
		$return    = array();
		foreach ( $states as $key => $state) {
			$return[ $key ] = array(
				'items' => array(),
			);
            $cities         = call_user_func( self::$function_kota, $key );
			if ( is_array( $cities ) ) {
				foreach ( $cities as $key1 => $city ) {
					$return[ $key ]['items'][ $key1 ] = $city;
				}
			}			
			
	    }
	    return $return;
    }    
}
