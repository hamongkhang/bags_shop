<?php

/**
 * Class APPMAKER_WC_CONVERTER
 */
class APPMAKER_WC_Converter {
	static $key = 1;

	public static function convert_inAppPage_data( $data, $key ) {
		$app_value            = array();
		$app_value['id']      = $key;
		$app_value['title']   = $data->title;
		$app_value['widgets'] = array();
		foreach ( $data->widgets as $value ) {
			$widget = array(
				'type'  => $value->type,
				'title' => $value->title
			);

			foreach ( $value->data as $value_data ) {
				if ( $widget['type'] == 'product_scroller' ) {
					$data = APPMAKER_WC::$api->APPMAKER_WC_REST_Products_Controller->get_product_data( wc_get_product( $value_data->data->action_value->value ) );
				} else {
					$data = array(
						'image'  => self::getValue( $value_data->data->image ),
						'title'  => self::getValue( $value_data->data->title ),
						'action' => array(
							'type'   => $value_data->data->action->value,
							'params' => self::get_params( $value_data->data->action->value, $value_data->data->action_value->value ),
						)
					);
					if ( $widget['type'] == 'html' ) {
						$data['html'] = $value_data->data->html->value;
					}
				}
				$widget['data'][] = $data;
			}
			$app_value['widgets'][] = $widget;
		}
		$app_value['hash'] = md5( serialize( $app_value ) );

		return ( $app_value );
	}

	/**
	 * @param mixed $value
	 *
	 * @return string
	 */
	protected static function getValue( $value ) {
		if ( is_string( $value ) ) {
			return $value;
		} else {
			return $value->value;
		}
	}

	public static function get_params( $action, $action_value ) {
		switch ( $action ) {
			case 'OPEN_URL' :
			case 'OPEN_IN_WEB_VIEW' : {
				$params = array( 'url' => $action_value );
				break;
			}
			case 'LIST_POST' : {
				$params = array( 'categories' => $action_value );
				break;

			}
			case 'LIST_PRODUCT' : {
				$params = array( 'category' => $action_value );
				break;
			}
			case 'OPEN_IN_APP_PAGE' : {
				$params = array( 'id' => $action_value );
				break;

			}
			case 'OPEN_POST' : {
				$params = array( 'id' => $action_value );
				break;

			}
			case 'OPEN_PRODUCT' : {
				$params = array( 'id' => $action_value );
				break;
			}
			default : {
				$params = array();
				break;

			}
		}

		return $params;
	}

	public static function convert_navMenu_data( $data ) {
		$appValue         = array();
		$appValue['data'] = self::convert_navMenu_data_item( $data );
		$appValue['hash'] = md5( serialize( $appValue ) );

		return ( $appValue );
	}

	public static function convert_navMenu_data_item( $data ) {
		$return = array();
		foreach ( $data as $value ) {
			$menu = array(
				'id'     => self::$key ++,
				'title'  => $value->title,
				'icon'   => $value->icon,
				'type'   => $value->type->id,
				'action' => array(),
				'nodes'  => self::convert_navMenu_data_item( $value->nodes ),
			);
			if ( $value->type->id != 'title' ) {
				if ( is_string( $value->action_value ) ) {
					$val = $value->action_value;
				} else {
					$val = $value->action_value->id;
				}
				$menu['action'] = array(
					'type'   => $value->action->id,
					'params' => self::get_params( $value->action->id, $val )
				);
			}
			$return[] = $menu;
		}

		return $return;
	}
}
