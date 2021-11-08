<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Action to add push notification.
 *
 * @class Appmaker_Action_Send_Push
 */
class Appmaker_Action_Send_Push extends \AutomateWoo\Action {

	/**
	 * Explain to store admin what this action does via a unique title and description.
	 */
	function load_admin_details() {
		parent::load_admin_details();
		$this->title       = __( 'Appmaker Send Mobile Push Notification', 'appmaker-woocommerce-mobile-app-manager' );
		$this->description = __( 'Appmaker addon Send Push notifications', 'appmaker-woocommerce-mobile-app-manager' );
		$this->group       = __( 'Appmaker', 'appmaker-woocommerce-mobile-app-manager' );
	}

	function load_fields() {
		$title = new \AutomateWoo\Fields\Text_Area();
		$title->set_name( 'push_title' );
		$title->set_title( __( 'Push Title', 'appmaker-woocommerce-mobile-app-manager' ) );
		$title->set_variable_validation();
		$title->set_required();

		$note = new \AutomateWoo\Fields\Text_Area();
		$note->set_name( 'push_message' );
		$note->set_title( __( 'Push Message', 'appmaker-woocommerce-mobile-app-manager' ) );
		$note->set_variable_validation();
		$note->set_required();

		$field_action = new \AutomateWoo\Fields\Select();
		$field_action->set_name( 'push_action' );
		$field_action->set_title( __( 'Push notification action', 'appmaker-woocommerce-mobile-app-manager' ) );
		$field_action->set_default( 'OPEN_CART' );
		$field_action->set_options(
			[
				'OPEN_CART'        => 'Open cart',
				'OPEN_IN_APP_PAGE' => 'Open In-App Page',
				'OPEN_PRODUCT'     => 'Open Product',
			]
		);
		$field_action->set_required();

		$field_action_value = new \AutomateWoo\Fields\Text();
		$field_action_value->set_name( 'push_action_value' );
		$field_action_value->set_title( __( 'Push notification action value (Not required for open cart)', 'appmaker-woocommerce-mobile-app-manager' ) );
		$this->add_field( $title );
		$this->add_field( $note );
		$this->add_field( $field_action );
		$this->add_field( $field_action_value );
	}

	public function run() {
		if ( ! $customer = $this->workflow->data_layer()->get_customer() ) {
			return;
		}
		$title        = $this->get_option( 'push_title', true );
		$message      = $this->get_option( 'push_message', true );
		$action       = $this->get_option( 'push_action', true );
		$action_value = $this->get_option( 'push_action_value', true );

		if ( empty( $action_value ) ) {
			$action_value = '';
		}

		$fcm_key = \APPMAKER_WC::$api->get_settings( 'fcm_server_key' );
		$fcm     = new \Appmaker_WC_FCM_Helper( $fcm_key );
		$user_id = $customer->get_user_id();
		$fcm->setTopic( "user-$user_id" )
		->setMessage(
			$title,
			$message
		)
		->setAction(
			array(
				'type'   => $action,
				'params' => array(
					'id' => $action_value,
				),
			)
		)
		->send();
	}
}
