<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class APPMAKER_WC_REST_Dynamic_Form_Controller extends APPMAKER_WC_REST_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'appmaker-wc/v1';

	/**
	 * Route base
	 *
	 * @var string
	 */
	protected $rest_base = 'dynamic-form';

	public function __construct() {
		parent::__construct();

		add_filter( 'appmaker_wc_dynamic_get_form_billing_address_get', array( $this, 'billing_address_get' ) );
		add_filter( 'appmaker_wc_dynamic_get_form_shipping_address_get', array( $this, 'shipping_address_get' ) );

		add_filter( 'appmaker_wc_dynamic_get_form_billing_address_submit', array( $this, 'billing_address_submit' ) );
		add_filter( 'appmaker_wc_dynamic_get_form_shipping_address_submit', array( $this, 'shipping_address_submit' ) );

		add_filter( 'appmaker_wc_dynamic_get_form_account_form_get', array( $this, 'account_details_get' ) );
		add_filter( 'appmaker_wc_dynamic_get_form_account_form_submit', array( $this, 'account_details_submit' ) );

	}

	/**
	 * Register the routes for products.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form>[\a-z]+)', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_form' ),
				'permission_callback' => array( $this, 'api_permissions_check' ),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<form>[\a-z]+)', array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'submit_form' ),
				'permission_callback' => array( $this, 'api_permissions_check' ),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );
	}

	public function get_form( $request ) {
		$form = isset( $request['form'] ) ? $request['form'] : 'default';

		return apply_filters( 'appmaker_wc_dynamic_get_form_' . $form . '_get', array( 'items' => array() ) );
	}

	public function submit_form( $request ) {
		$form = isset( $request['form'] ) ? $request['form'] : 'default';

		return apply_filters( 'appmaker_wc_dynamic_get_form_' . $form . '_submit', array( 'message' => 'No Form Defined' ) );
	}

	public function get_address_fields( $load_address = 'billing' ) {

		$current_user = wp_get_current_user();
		$load_address = sanitize_key( $load_address );

		$address = WC()->countries->get_address_fields( get_user_meta( get_current_user_id(), $load_address . '_country', true ), $load_address . '_' );

		// Enqueue scripts.
		wp_enqueue_script( 'wc-country-select' );
		wp_enqueue_script( 'wc-address-i18n' );

		// Prepare values.

		foreach ( $address as $key => $field ) {

			$value = get_user_meta( get_current_user_id(), $key, true );


			if ( ! $value ) {
				switch ( $key ) {
					case 'billing_email' :
					case 'shipping_email' :
						$value = $current_user->user_email;
						break;
					case 'billing_country' :
					case 'shipping_country' :
						$value = WC()->countries->get_base_country();
						break;
					case 'billing_state' :
					case 'shipping_state' :
						$value = WC()->countries->get_base_state();
						break;
				}
			}

			$address[ $key ]['default'] = apply_filters( 'woocommerce_my_account_edit_address_field_value', $value, $key, $load_address );
		}
		$address['wpnonce'] = array(
			'id'    => 'wpnonce',
			'type'  => 'hidden',
			'label' => __( '' ),
			'default'  => wp_create_nonce( 'woocommerce-edit_address' ),
		);

		$address['action'] = array(
			'id'    => 'action',
			'type'  => 'hidden',
			'label' => __( '' ),
			'default'  => 'edit_address',
		);
		return $address;
	}


	/**
	 * Return dynamic form
	 *
	 * @return array|mixed
	 */
	public function billing_address_get() {

		if ( ! is_user_logged_in() ) {
			return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to do this' ), array( 'status' => rest_authorization_required_code() ) );
		}

		$form = $this->get_address_fields( 'billing' );
		return APPMAKER_WC_Dynamic_form::get_fields( $form, 'billing' );
	}

	/**
	 * Return dynamic form
	 *
	 * @return array|mixed
	 */
	public function shipping_address_get() {
		if ( ! is_user_logged_in() ) {
			return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to do this' ), array( 'status' => rest_authorization_required_code() ) );
		}

		$form = $this->get_address_fields( 'shipping' );
		return APPMAKER_WC_Dynamic_form::get_fields( $form, 'shipping' );
	}

	/**
	 * Return dynamic form
	 *
	 * @return array|mixed
	 */
	public function account_details_get() {

		if ( ! is_user_logged_in() ) {
			return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to do this' ), array( 'status' => rest_authorization_required_code() ) );
		}

		$user  = wp_get_current_user();

		$return = array();
		$return['account_first_name'] = array(
			'id'    => 'account_first_name',
			'type'  => 'text',
			'required'  => true,
			'label' => __( 'First name', 'woocommerce' ),
			'default'  => $user->user_firstname,
		);

		$return['account_last_name'] = array(
			'id'    => 'account_last_name',
			'type'  => 'text',
			'required'  => true,
			'label' => __( 'Last name', 'woocommerce' ),
			'default'  => $user->user_lastname,
		);

		$return['account_email'] = array(
			'id'    => 'account_email',
			'type'  => 'text',
			'validate' => array( 'email' ),
			'required'  => true,
			'label' => __( 'Email address', 'woocommerce' ),
			'default'  => $user->user_email,
		);

		$return['password_current'] = array(
			'id'    => 'password_current',
			'type'  => 'password',
			'label' => __( 'Current password (leave blank to leave unchanged)', 'woocommerce' ),
		);

		$return['password_1'] = array(
			'id'    => 'password_1',
			'type'  => 'password',
			'label' => __( 'New password (leave blank to leave unchanged)', 'woocommerce' ),
		);

		$return['password_2'] = array(
			'id'    => 'password_2',
			'type'  => 'password',
			'label' => __( 'Confirm new password', 'woocommerce' ),
		);

		$return['wpnonce'] = array(
			'id'    => 'wpnonce',
			'type'  => 'hidden',
			'label' => __( '' ),
			'default'  => wp_create_nonce( 'save_account_details' ),
		);

		$return['action'] = array(
			'id'    => 'action',
			'type'  => 'hidden',
			'label' => __( '' ),
			'default'  => 'save_account_details',
		);

		return APPMAKER_WC_Dynamic_form::get_fields( $return, 'account' );
	}



	/**
	 * Return dynamic form
	 *
	 * @return array|mixed
	 */
	public function billing_address_submit() {

		if ( ! is_user_logged_in() ) {
			return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to do this' ), array( 'status' => rest_authorization_required_code() ) );
		}

		global $wp;
		$wp->query_vars['edit-address'] = 'billing';
		return $this->address_submit();
	}

	/**
	 * Return dynamic form
	 *
	 * @return array|mixed
	 */
	public function shipping_address_submit() {
		if ( ! is_user_logged_in() ) {
			return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to do this' ), array( 'status' => rest_authorization_required_code() ) );
		}
		global $wp;
		$wp->query_vars['edit-address'] = 'shipping';
		return $this->address_submit();
	}

	/**
	 * Return dynamic form
	 *
	 * @return array|mixed
	 */
	public function address_submit() {
		try {
						
			$saved = $this->save_address();
			$errors = $this->get_wc_notices_errors();
			if ( is_wp_error( $errors ) ) {
				return $errors;
			} elseif ( false === $saved ) {
				return new WP_Error( 'error', 'Unable to process' );
			} else {
				return array( 'message' => __( 'Address changed successfully.', 'woocommerce' ) );
			}
		} catch (Exception $e) {
			return new WP_Error( 'error', $e->getMessage() );
		}
	}



	/**
	 * Return dynamic form
	 *
	 * @return array|WP_Error|bool
	 */
	public function account_details_submit() {

		if ( ! is_user_logged_in() ) {
			return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to do this' ), array( 'status' => rest_authorization_required_code() ) );
		}

		try {
			$saved = $this->save_account_details();
			$errors = $this->get_wc_notices_errors();
			if ( is_wp_error( $errors ) ) {
				return $errors;
			} elseif ( false === $saved ) {
				return new WP_Error( 'error', 'Unable to process' );
			} else {
				return array( 'message' => __( 'Account details changed successfully.', 'woocommerce' ) );
			}
		} catch (Exception $e) {
			return new WP_Error( 'error', $e->getMessage() );
		}
	}

	private function save_account_details() {
		if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
			return  false;
		}

		if ( empty( $_POST['action'] ) || 'save_account_details' !== $_POST['action'] || empty( $_POST['wpnonce'] ) || ! wp_verify_nonce( $_POST['wpnonce'], 'save_account_details' ) ) {
			return false;
		}

		$errors       = new WP_Error();
		$user         = new stdClass();

		$user->ID     = (int) get_current_user_id();
		$current_user = get_user_by( 'id', $user->ID );

		if ( $user->ID <= 0 ) {
			return false;
		}

		$account_first_name = ! empty( $_POST['account_first_name'] ) ? wc_clean( $_POST['account_first_name'] ) : '';
		$account_last_name  = ! empty( $_POST['account_last_name'] ) ? wc_clean( $_POST['account_last_name'] ) : '';
		$account_email      = ! empty( $_POST['account_email'] ) ? sanitize_email( $_POST['account_email'] ) : '';
		$pass_cur           = ! empty( $_POST['password_current'] ) ? $_POST['password_current'] : '';
		$pass1              = ! empty( $_POST['password_1'] ) ? $_POST['password_1'] : '';
		$pass2              = ! empty( $_POST['password_2'] ) ? $_POST['password_2'] : '';
		$save_pass          = true;

		$user->first_name   = $account_first_name;
		$user->last_name    = $account_last_name;

		// Prevent emails being displayed, or leave alone.
		$user->display_name = is_email( $current_user->display_name ) ? $user->first_name : $current_user->display_name;

		// Handle required fields
		$required_fields = apply_filters( 'woocommerce_save_account_details_required_fields', array(
			'account_first_name' => __( 'First Name', 'woocommerce' ),
			'account_last_name'  => __( 'Last Name', 'woocommerce' ),
			'account_email'      => __( 'Email address', 'woocommerce' ),
		) );

		foreach ( $required_fields as $field_key => $field_name ) {
			$value = wc_clean( $_POST[ $field_key ] );
			if ( empty( $value ) ) {
				wc_add_notice( '<strong>' . esc_html( $field_name ) . '</strong> ' . __( 'is a required field.', 'woocommerce' ), 'error' );
			}
		}

		if ( $account_email ) {
			if ( ! is_email( $account_email ) ) {
				wc_add_notice( __( 'Please provide a valid email address.', 'woocommerce' ), 'error' );
			} elseif ( email_exists( $account_email ) && $account_email !== $current_user->user_email ) {
				wc_add_notice( __( 'This email address is already registered.', 'woocommerce' ), 'error' );
			}
			$user->user_email = $account_email;
		}

		if ( ! empty( $pass1 ) && ! wp_check_password( $pass_cur, $current_user->user_pass, $current_user->ID ) ) {
			wc_add_notice( __( 'Your current password is incorrect.', 'woocommerce' ), 'error' );
			$save_pass = false;
		}

		if ( ! empty( $pass_cur ) && empty( $pass1 ) && empty( $pass2 ) ) {
			wc_add_notice( __( 'Please fill out all password fields.', 'woocommerce' ), 'error' );
			$save_pass = false;
		} elseif ( ! empty( $pass1 ) && empty( $pass_cur ) ) {
			wc_add_notice( __( 'Please enter your current password.', 'woocommerce' ), 'error' );
			$save_pass = false;
		} elseif ( ! empty( $pass1 ) && empty( $pass2 ) ) {
			wc_add_notice( __( 'Please re-enter your password.', 'woocommerce' ), 'error' );
			$save_pass = false;
		} elseif ( ( ! empty( $pass1 ) || ! empty( $pass2 ) ) && $pass1 !== $pass2 ) {
			wc_add_notice( __( 'New passwords do not match.', 'woocommerce' ), 'error' );
			$save_pass = false;
		}

		if ( $pass1 && $save_pass ) {
			$user->user_pass = $pass1;
		}

		// Allow plugins to return their own errors.
		do_action_ref_array( 'woocommerce_save_account_details_errors', array( &$errors, &$user ) );

		if ( $errors->get_error_messages() ) {
			foreach ( $errors->get_error_messages() as $error ) {
				wc_add_notice( $error, 'error' );
			}
		}

		if ( wc_notice_count( 'error' ) === 0 ) {

			wp_update_user( $user );

			do_action( 'woocommerce_save_account_details', $user->ID );
			return $user;
		}
		return false;
	}


	/**
	 * Save and and update a billing or shipping address if the
	 * form was submitted through the user account page.
	 */
	public static function save_address() {
		global $wp;

		if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
			return false;
		}

		if ( empty( $_POST['action'] ) || 'edit_address' !== $_POST['action'] || empty( $_POST['wpnonce'] ) || ! wp_verify_nonce( $_POST['wpnonce'], 'woocommerce-edit_address' ) ) {
			return false;
		}

		$user_id = get_current_user_id();

		if ( $user_id <= 0 ) {
			return false;
		}
		$customer = new WC_Customer( $user_id );
		$load_address = isset( $wp->query_vars['edit-address'] ) ? wc_edit_address_i18n( sanitize_title( $wp->query_vars['edit-address'] ), true ) : 'billing';

		$address = WC()->countries->get_address_fields( esc_attr( $_POST[ $load_address . '_country' ] ), $load_address . '_' );

		foreach ( $address as $key => $field ) {

			if ( ! isset( $field['type'] ) ) {
				$field['type'] = 'text';
			}

			// Get Value.
			switch ( $field['type'] ) {
				case 'checkbox' :
					$_POST[ $key ] = isset( $_POST[ $key ] ) ? 1 : 0;
					break;
				default :
					$_POST[ $key ] = isset( $_POST[ $key ] ) ? wc_clean( $_POST[ $key ] ) : '';
					break;
			}

			// Hook to allow modification of value.
			$_POST[ $key ] = apply_filters( 'woocommerce_process_myaccount_field_' . $key, $_POST[ $key ] );

			// Validation: Required fields.
			if ( ! empty( $field['required'] ) && empty( $_POST[ $key ] ) ) {
				wc_add_notice( $field['label'] . ' ' . __( 'is a required field.', 'woocommerce' ), 'error' );
			}

			if ( ! empty( $_POST[ $key ] ) ) {

				// Validation rules
				if ( ! empty( $field['validate'] ) && is_array( $field['validate'] ) ) {
					foreach ( $field['validate'] as $rule ) {
						switch ( $rule ) {
							case 'postcode' :
								$_POST[ $key ] = strtoupper( str_replace( ' ', '', $_POST[ $key ] ) );

								if ( ! WC_Validation::is_postcode( $_POST[ $key ], $_POST[ $load_address . '_country' ] ) ) {
									wc_add_notice( __( 'Please enter a valid postcode/ZIP.', 'woocommerce' ), 'error' );
								} else {
									$_POST[ $key ] = wc_format_postcode( $_POST[ $key ], $_POST[ $load_address . '_country' ] );
								}
								break;
							case 'phone' :
								$_POST[ $key ] = wc_format_phone_number( $_POST[ $key ] );

								if ( ! WC_Validation::is_phone( $_POST[ $key ] ) ) {
									wc_add_notice( '<strong>' . $field['label'] . '</strong> ' . __( 'is not a valid phone number.', 'woocommerce' ), 'error' );
								}
								break;
							case 'email' :
								$_POST[ $key ] = strtolower( $_POST[ $key ] );

								if ( ! is_email( $_POST[ $key ] ) ) {
									wc_add_notice( '<strong>' . $field['label'] . '</strong> ' . __( 'is not a valid email address.', 'woocommerce' ), 'error' );
								}
								break;
						}
					}
				}
			}
		}		
		
		do_action( 'woocommerce_after_save_address_validation', $user_id, $load_address, $address, $customer );	
		$customer->save();
		
		if ( wc_notice_count( 'error' ) == 0 ) {
			foreach ( $address as $key => $field ) {
				update_user_meta( $user_id, $key, $_POST[ $key ] );
			}
			wc_add_notice( __( 'Address changed successfully.', 'woocommerce' ) );
			do_action( 'woocommerce_customer_save_address', $user_id, $load_address );
			return true;
		}
		return false;
	}

}
