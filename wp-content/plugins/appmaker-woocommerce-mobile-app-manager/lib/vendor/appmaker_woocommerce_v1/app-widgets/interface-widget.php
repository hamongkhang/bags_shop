<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
require_once( "interface-widget.php" );

abstract class WOOAPP_Widget_Handler {
	var $values;
	var $type;
	var $id;

	public function getValues() {
		return $this->values;
	}

	public function getValueById( $id ) {
		return isset( $this->values[ $id ] ) ? $this->api_out( $this->values[ $id ] , $id) : false;
	}

	public function api_out( $value , $id ) {
		$return          = array();
		$return['type']  = $this->type;
		$return['title'] = $value['title'];
		$return['expanded'] = false;
		if ( 'product_scroller_widgets' === $this->type ) {
			$this->type = 'pre_build_product_scroller';
			$return['type'] = 'pre_build_product_scroller';
			if ( 'recent' === $id ) {
				$key = 'RECENT';
				$title = 'Recent Products';
			} elseif ( 'featured' === $id ) {
				$key = 'FEATURED';
				$title = 'Featured Products';
			} elseif ( 'sale' === $id ) {
				$key = 'SALE';
				$title = 'Sale Products';
			} elseif ( 'bestselling' === $id ) {
				$key = 'BEST_SELLING';
				$title = 'Best Selling Products';
			} elseif ( 'toprated' === $id ) {
				$key = 'TOP_RATED';
				$title = 'Top Rated Products';
			} else {
				$key = false;
				$this->type = 'product_scroller';
				$return['type'] = 'product_scroller';
				$title = '';
			}
			if ( false !== $key ) {
				$return['data'] = array(
					array(
						'image' => "",
						'data'  => array(
							'title' => array(
								'display' => true,
				                'label' => 'Title',
				                'value' => $return['title'],
				                'display_value' => $return['title'],
							),
							'action_value' => array(
								'display' => true,
				                'label' =>  'Select Product Scroller',
				                'value' => $key,
				                'display_value' => $title,
							)
						)
					)
				);
			}
		}
		$return['type'] = str_replace('_widgets','',$return['type']);
		if ( isset( $value['slides'] ) && is_array( $value['slides'] ) ) {
			foreach ( $value['slides'] as $item ) {
				$return['data'][] = $this->itemValue( $item );
			}
		} elseif ( $this->id == 5 && $this->type == "product_scroller" && isset( $value['products'] ) ) {
			$return['data'] = $this->fetchProductDetails( $value );
		} elseif ( $this->id == 6 && isset( $value['content'] ) ) {
			$return['data'][] = $this->html_Field( $value );
		}
		elseif($return['type'] != 'search') {
			$return['data'][] = $this->itemValue( $value );
		}
		return $return;
	}
	public function format_value($label,$value,$display_value,$display = true){
		return array(
			'display' => $display,
			'label' => $label,
			'value' => $value,
			'display_value' => $display_value,
		);
	}
	public function format_action($action){
		$return = array("action" => $action, "title"=> $action);
		switch ($action){
			case 'open_category':
				$return['action'] = 'LIST_PRODUCT';
				$return['title'] = 'Product Category';
				break;
			case 'open_product':
				$return['action'] = 'OPEN_PRODUCT';
				$return['title'] = 'Open Product';
				break;
			case 'open_page':
				$return['action'] = 'OPEN_IN_APP_PAGE';
				$return['title'] = 'Open In-App Page';
				break;
			case 'go_to_url':
				$return['action'] = 'OPEN_URL';
				$return['title'] = 'Open URL';
				break;
			case 'open_webview':
				$return['action'] = 'OPEN_IN_WEB_VIEW';
				$return['title'] = 'Open WebView';
				break;
		}
		return $return;
	}
	public function itemValue( $item ) {
		$title = isset( $item['title'] ) ? $item['title'] : "";
		$image = isset( $item['image'] ) ? $item['image'] : "";
		$action = isset( $item['click_action'] ) ? $item['click_action'] : "";
		if ( isset( $item['click_action_value'] ) && is_array( $item['click_action_value'] ) ) {
			$action_value = current( $item['click_action_value'] );
			$action_value_dsiplay = $action_value;
		}elseif ( isset( $item['click_action_value'] ) ) {
			$action_value = $item['click_action_value'];
			$action_value_dsiplay = $action_value;
		}else{
			$action_value = "";
			$action_value_dsiplay = $action_value;
		}
		if($action == "open_category"){
			$action_value_dsiplay = $this->fetchCatDetails( $item['click_action_value'] );
			$action_value_dsiplay = $action_value_dsiplay[1];
		}
		$action = $this->format_action($action);
		$return = array(
			"image" =>  $image,
			"data"  => array(
				"title" => $this->format_value("Title",$title,$title),
				"image" => $this->format_value("Image",$image,$image,false),
				"action" => $this->format_value('Action',$action['action'],$action['title']),
				"action_value" => $this->format_value('Select '.$action['title'],$action_value,$action_value_dsiplay),
			)
		);
		/*
		 "action" => array(
				"tagetType" => isset( $item['click_action'] ) ? $item['click_action'] : "",
			),
			"value"  => array(
				"title" => isset( $item['title'] ) ? $item['title'] : "",
				"url"   => isset( $item['image'] ) ? $item['image'] : ""
			)
		 */

		return $return;
	}

	public function fetchCatDetails( $cat ) {
		$catTerms = get_term( $cat, "product_cat" );
		$return   = array( (String) $catTerms->term_id, $catTerms->name );
		return $return;
	}

	public function fetchProductDetails( $value ) {
		$products = $value['products'];
		$return   = array();
		$i        = - 1;
		foreach ( $products as $product_id ) {
			$product = get_product( $product_id );
			if ( $product !== false ) {
				$return[ ++ $i ]['image'] = array();
				$return[ $i ]['data']['action_value']['display'] = true;
				$return[  $i ]['data']['action_value']['label']= 'Product';
				$return[  $i ]['data']['action_value']['value']= $product_id;
				$return[  $i ]['data']['action_value']['display_value']= $product->get_title();

			//	$return[ $i ]['value']      = getapi()->WOOAPP_API_Products->get_product_data( $product, true );
			}
		}

		return $return;
	}

	public function html_Field( $item ) {
		$title = isset( $item['title'] ) ? $item['title'] : "";

		$return = array(
			"image" => "",
			"data" => array(
				"title" => $this->format_value("Title",$title,$title),
				"html" => $this->format_value("Html",$item['content'],$item['content']),
			),
		);

		return $return;
	}

	public function count() {
		return is_array( $this->values ) ? count( $this->values ) : false;
	}
}