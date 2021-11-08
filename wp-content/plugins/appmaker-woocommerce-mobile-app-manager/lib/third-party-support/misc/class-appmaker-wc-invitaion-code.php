<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_Inviataion_Code {

	public function __construct() {
		add_filter( 'appmaker_wc_registration_validate', array( $this, 'reg_field_validate' ), 2, 2 );
		add_filter( 'appmaker_wc_login_with_provider_validate', array( $this, 'reg_social_validate' ), 2, 3 );
	}

	public function reg_social_validate( $return, $params, $user ) {
		if ( isset( $params['invitation_code'] ) ) {
			return $this->reg_field_validate( $return, $params );
		} elseif ( empty( $user ) ) {
			return array( 'status' => false, 'ask_invite' => true );
		} else {
			return $return;
		}
	}

	public function reg_field_validate( $return, $params ) {
		$options = get_option( 'register_plus_redux_auto_login_options' );
		if ( ! empty( $options['enable_invitation_code'] ) ) {
			if ( empty( $_POST['invitation_code'] ) && ! empty( $options['require_invitation_code'] ) ) {
				$return = new WP_Error(
					'empty_invitation_code',
					'Por favor ingrese un código de invitación.'
				);
			} elseif ( ! empty( $_POST['invitation_code'] ) ) {
				$invitation_code      = $_POST['invitation_code'];
				$invitation_code_bank = $options['invitation_code_bank'];
				if ( empty( $options['invitation_code_case_sensitive'] ) ) {
					$invitation_code = strtolower( $_POST['invitation_code'] );
					if ( ! is_array( $invitation_code_bank ) ) {
						$invitation_code_bank = array();
					}
					foreach ( $invitation_code_bank as $k => $v ) {
						$invitation_code_bank[ $k ] = strtolower( $v );
					}
				}
				if ( ! in_array( $invitation_code, $invitation_code_bank ) ) {

					$return = new WP_Error(
						'invitation_code_mismatch',
						'EL CÓDIGO ES INCORRECTO.'
					);
				}
			}
		}

		return $return;
	}

}

new APPMAKER_WC_Inviataion_Code();
