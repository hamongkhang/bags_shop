<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_Jne {
	public static $function_kecamatan;
	public static $function_kabupaten;

	public static function className() {
		return 'APPMAKER_WC_Jne';
	}

	public static function init() {
		add_filter( 'appmaker_wc_checkout_fields', array( self::className(), 'indonesia_field' ), 10, 2 );
		add_filter( 'appmaker_wc_states_response', array( self::className(), 'states_response' ), 10, 1 );
		add_action( 'appmaker_wc_before_checkout', array( self::className(), 'before_checkout' ), 10, 1 );
		add_action( 'appmaker_wc_dynamic_form_response', array( self::className(), 'dynamic_form_response' ), 10, 3 );
		add_filter( 'appmaker_wc_dependency_billing_kota', array( self::className(), 'kota_dependency' ), 10, 2 );
		add_filter( 'appmaker_wc_dependency_shipping_kota', array( self::className(), 'kota_dependency' ), 10, 2 );

		add_filter( 'appmaker_wc_dependency_billing_city', array( self::className(), 'city_dependency' ), 10, 2 );
		add_filter( 'appmaker_wc_dependency_shipping_city', array( self::className(), 'city_dependency' ), 10, 2 );
	}

	public static function dynamic_form_response( $response, $fields, $section ) {
		if ( 'billing' === $section || 'shipping' === $section ) {
			$state_key = array_search( $section . '_state',$response['order'] );
			$kota_key = array_search( $section . '_kota',$response['order'] );
			$city_key = array_search( $section . '_city',$response['order'] );
			unset( $response['order'][ $kota_key ] );
			unset( $response['order'][ $city_key ] );
			array_splice( $response['order'],$state_key,0,$section . '_kota' );
			array_splice( $response['order'],$state_key + 1,0,$section . '_city' );
		}
		return $response;
	}

	public static function before_checkout( $request ) {
		$states_c  = WC()->countries->get_shipping_country_states();
		if ( isset( $request['billing_state'] ) || isset( $request['shipping_state'] )  ) {
			foreach ( $states_c['ID'] as $state_key => $state ) {
				if ( isset( $request['billing_state'] ) && $request['billing_state'] == $state  ) {
					$_POST['billing_state'] = $state_key;
				}
				if ( isset( $request['shipping_state'] ) && $request['shipping_state'] == $state  ) {
					$_POST['billing_state'] = $state_key;
				}
			}
		}
	}
	public static function states_response( $return ) {
		if ( isset( $return['ID'] ) && isset( $return['ID']['items'] ) ) {
			$states_c  = WC()->countries->get_shipping_country_states();
			$return['ID']['items'] = array();
			foreach ( $states_c['ID'] as $state_key => $state ) {
				$return['ID']['items'][ $state ] = html_entity_decode( $state );
			}
		}
		return $return;
	}
	public static function kota_dependency( $dependency, $key ) {
		if ( 'billing_kota' === $key ) {
			$dependency = array( 'on' => 'billing_state' );
		} elseif ( 'shipping_kota' === $key ) {
			$dependency = array( 'on' => 'shipping_state' );
		}
		return $dependency;
	}

	public static function city_dependency( $dependency, $key ) {
		if ( 'billing_city' === $key ) {
			$dependency = array( 'on' => 'billing_kota' );
		} elseif ( 'shipping_city' === $key ) {
			$dependency = array( 'on' => 'shipping_kota' );
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
	public static function indonesia_field( $fields, $section ) {
		if ( 'billing' === $section || 'shipping' === $section ) {
			$fields[ $section . '_kota' ]['type'] = 'dependent-select';
			$fields[ $section . '_kota' ]['options'] = self::shipping_get_kota();
			$fields[ $section . '_kota' ]['dependent'] = true;

			$fields[ $section . '_city' ]['type'] = 'dependent-select';
			$fields[ $section . '_city' ]['options'] = self::shipping_get_kecamatan();
			$fields[ $section . '_city' ]['dependent'] = true;
		}

		return $fields;
	}



	public static function shipping_get_kota() {
		$datakota = WC_JNE()->shipping->get_datakota();
		$result = array();
		if ( count( $datakota ) ) {
			if ( is_array( $datakota ) ) {
				foreach ( $datakota as $nama_provinsi => $data_kota ) {
					$result[ $nama_provinsi ] = array(
						'items' => array(),
					);
					foreach ( $data_kota as $nama_kota => $data_kecamatan ) {
						$result[ $nama_provinsi ]['items'][ $nama_kota ] = $nama_kota;
					}
				}
			}
		}
		return $result;
	}


	public static function shipping_get_kecamatan() {
		$datakota = WC_JNE()->shipping->get_datakota();
		$result = array();

		if ( count( $datakota ) ) {
			if ( is_array( $datakota ) ) {
				foreach ( $datakota as $nama_provinsi => $data_kota ) {
					foreach ( $data_kota as $nama_kota => $data_kecamatan ) {
						$result[ $nama_kota ] = array(
							'items' => array(),
						);
						foreach ( $data_kecamatan as $nama_kecamatan => $data_harga ) {
							$result[ $nama_kota ]['items'][ $nama_kecamatan ] = $nama_kecamatan;
						}
					}
				}
			}
		}
		return $result;
	}
}
