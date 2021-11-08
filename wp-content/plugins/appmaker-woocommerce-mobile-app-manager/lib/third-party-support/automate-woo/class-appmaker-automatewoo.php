<?php


defined( 'ABSPATH' ) || exit;


/**
 */
class Appmaker_AutomateWoo {


	/**
	 * Constructor
	 */
	public function __construct() {
		include_once $this->path() . '/class-appmaker-action-send-push.php';
		add_action( 'automatewoo/actions', [ $this, 'add_actions' ], 20 );
	}

	/**
	 *
	 * @return array
	 */
	function add_actions( $actions ) {
		$actions = array_merge(
			$actions,
			[
				'appmaker_send_push' => 'Appmaker_Action_Send_Push',
			]
		);
		return $actions;
	}

	/**
	 * Get the path to the plugin dir.
	 *
	 * @param string $end End of the path.
	 *
	 * @return string
	 */
	function path( $end = '' ) {
		return untrailingslashit( dirname( __FILE__ ) ) . $end;
	}

}

if ( class_exists( 'AutomateWoo_Loader' ) ) {

	$settings = get_option( 'appmaker_wc_custom_settings', array() );
	$key      = 'appmaker_automate_woo';

	if ( isset( $settings[ $key ] ) && ( $settings[ $key ] === 1 || $settings[ $key ] === "1" ) ) {		
		new Appmaker_AutomateWoo();
	} 	
}
