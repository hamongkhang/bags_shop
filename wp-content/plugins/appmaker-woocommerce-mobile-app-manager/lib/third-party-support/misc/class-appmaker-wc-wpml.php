<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class APPMAKER_WC_Third_WPML {
	public static function init() {
	    add_filter('appmaker_pre_build_product_scroller',array('APPMAKER_WC_Third_WPML','product_by_language'),2,2);
		add_filter('wcml_client_currency',array('APPMAKER_WC_Third_WPML','wcml_client_currency'),2,1);

		global $sitepress, $sitepress_settings, $woocommerce;
		if ( ! empty( $_REQUEST['language'] ) && $_REQUEST['language'] != 'default' ) {
			$language = $_REQUEST['language'];
		} elseif ( isset( $_GET['from_app'] ) || ( !empty($_GET['rest_route']) && false != strpos($_SERVER['REQUEST_URI'], 'appmaker-wc') ) ){
			$language = APPMAKER_WC::$api->get_settings( 'default_language', 'default' );
		} else {
			$language = false;
		}
		$wpml_post_types = new WPML_Post_Types( $sitepress );
		/*$custom_posts = $wpml_post_types->get_translatable_and_readonly();
		if ( $custom_posts ) {
			$translation_mode = WPML_CONTENT_TYPE_DONT_TRANSLATE;
			foreach ( $custom_posts as $k => $custom_post ){					 
					 if($k == 'product'){
			            if ( isset( $sitepress_settings['custom_posts_sync_option'][ $k ] ) ) {
							$translation_mode = (int) $sitepress_settings['custom_posts_sync_option'][ $k ];							
			            }
			            $unlocked = false;
			            if ( isset( $sitepress_settings['custom_posts_unlocked_option'][ $k ] ) ) {
				            $unlocked = (int) $sitepress_settings['custom_posts_unlocked_option'][ $k ];
						}
					}				    
			}
		}*/
				
		if ( ! empty( $language ) && $language != 'default' && ! empty( $sitepress ) ) {
			if ( preg_match( '/-/i',$language ) ) {
				$language = explode( '-',$language );
				$language = $language[0];
			}
			$language = apply_filters('appmaker_wpml_language_code', $language);
			if($language == 'zh'){
				$language = 'zh-hans';
			}
						
			if ( $sitepress->get_current_language() != $language) {
				
				$lang_switch_enable = apply_filters( 'appmaker_switch_language', true );

				if ( $lang_switch_enable ) {
					$sitepress->switch_lang( $language, true );
				}				
				
			}
			
		}
	}
	
	// Added support for  wpml currency swither
	public static function wcml_client_currency($currency){
		if( isset ( $_REQUEST['currency'] )  && ! empty ( $_REQUEST['currency'] )  &&  $_REQUEST['currency'] != 'null' ) {
			$currency = $_REQUEST['currency'];
        } 		
		return $currency;
	}

	public static function product_by_language($products,$language){
        if( !empty($products) && $language != false) {
            foreach ($products as $id => $product_id) {

                global $wpml_post_translations, $sitepress;
                if ($wpml_post_translations->get_element_lang_code($product_id) != $sitepress->get_current_language()) {
                    unset($products[$id]);
                }
            }
        }
        return $products;
    }
}

APPMAKER_WC_Third_WPML::init();
