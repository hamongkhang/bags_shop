<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
require_once( "interface-widget.php" );

class Search_Widgets extends WOOAPP_Widget_Handler {
	public function __construct() {
		global $mobappSettings;
		$this->id     = 1;
		$this->type   = "search_widgets";
		$this->values = array();
		if ( isset( $mobappSettings[ $this->type ]['slides'] ) ) {
			$this->values = $mobappSettings[ $this->type ]['slides'];
			if ( isset( $this->values['_blank'] ) ) {
				unset( $this->values['_blank'] );
			}
		}
	}
}