<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_Third_Polylang {
	public static function init() {
		global $polylang;
		if ( ! empty( $_REQUEST['language'] ) && ! empty( $polylang ) ) {
			$polylang->curlang         = $polylang->model->get_language( $_REQUEST['language'] );
			$GLOBALS['text_direction'] = $polylang->curlang ? 'rtl' : 'ltr';
		}
	}
}

APPMAKER_WC_Third_Polylang::init();