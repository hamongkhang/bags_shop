<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
class WOOAPP_API_InAppPages {
	var $widgets;
	function __construct() {
		require_once( 'class-wooapp-api-widgets.php' );
		$this->widgets = new WOOAPP_API_Widgets();
	}

	function get_page_by_name( $id ) {
		global $mobappSettings;
		$mobappSettings = get_option("mobappSettings");
		$pages  = $this->get_pages();
		$return = array('data'=>array());
		if ( isset( $pages[ $id ] ) ) {
			$return['data']['id']          = $id;
			$return['data']['title']          = $pages[ $id ][ 'name' ];
			$return['data']['widgets'] = $this->widgets->getWidgetsOfPage( $id );
		} else {
			return false;
		}

		return $return;
	}

	public static function get_pages() {
		$option_name          = 'inApp_pages';
		$return_pages["home"] = array( "id" => "home", "name" => "Home" );
		$pages                = get_option( $option_name );
		if ( $pages !== false ) {
			$pages = json_decode( $pages, true );
			if ( is_array( $pages ) ) {
				foreach ( $pages as $id => $name ) {
					$return_pages[ $id ] = array( "id" => $id, "name" => $name );
				}
			}
		}

		return $return_pages;
	}
}
