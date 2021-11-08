<?php

/**
 * Created by PhpStorm.
 * User: aneesv
 * Date: 7/10/16
 * Time: 12:40 AM
 */
class v1_nav_menu_convert {
	public $menuTypes = array( "title"=>0,"cat"=>1, "product"=>2, "wlink"=>3, "elink"=>4, "inapp"=>5, "typecount"=>6);
	private $navIds = array();
	private $lastId=0;
	public $level = 0;
	private function getUniqeId($slug){
		if(empty($slug) || $slug == "cat_0"){
			$id=0;
		}elseif(isset($this->navIds[$slug]))
			$id = $this->navIds[$slug];
		else
			$id=$this->navIds[$slug]=++$this->lastId;

		return $id;
	}
	public function get_action($type){
		$action              = new stdClass();

		switch ($type){
			case 'cat':
				$action->id          = 'LIST_PRODUCT';
				$action->label       = 'Open Product Category';
				break;
			case 'product':
				$action->id          = 'OPEN_PRODUCT';
				$action->label       = 'Open Product';
				break;
			case 'wlink':
				$action->id          = 'OPEN_IN_WEB_VIEW';
				$action->label       = 'Open WebView';
				break;
			case 'elink':
				$action->id          = 'OPEN_URL';
				$action->label       = 'Open URL';
				break;
			case 'inapp':
				$action->id          = 'OPEN_IN_APP_PAGE';
				$action->label       = 'Open In-App Page';
				break;
		}
		return $action;
	}
	public function get_action_value($type,$value,$label){
		$action              = new stdClass();
		switch ($type){
			case 'cat':
				$action->id          = $value;
				$action->label       = $label;
				break;
			case 'product':
				$action->id          = $value;
				$action->label       = $label;
				break;
			case 'wlink':
			return $value;
				break;
			case 'elink':
				return $value;
				break;
			case 'inapp':
				$action->id          = $value;
				$action->label       = $label;
				break;
		}
		return $action;
	}
	public function get_menu(){
		global $mobappNavigationSettings;
		$order=0;
		$return = array();
		if(!empty($mobappNavigationSettings['nav_menu']) && is_array($mobappNavigationSettings['nav_menu'])) {
			$menu_type = new stdClass();
			$menu_type->id            = 'menu_item';
			$menu_type->label         = 'Menu Item';

			$title_type = new stdClass();
			$title_type->id            = 'title';
			$title_type->label         = 'Title';


			foreach ($mobappNavigationSettings['nav_menu'] as $cat) {
				$id = $this->getUniqeId($cat['id']);
				$parent = $this->getUniqeId($cat['parent']);
				$type = $cat['type'];

				$menu                      = new stdClass();
				$menu->id                  = $id;
				$menu->title               = html_entity_decode($cat['label']);
				$menu->icon                = ( isset( $cat['media_url'] ) ) ? $cat['media_url'] : "";
				if($cat['type'] == 'title') {
					$menu->type                = $title_type ;
					$menu->nodes               = array();
				}else {
					$menu->type                = $menu_type;
					$menu->action              = $this->get_action( $cat['type'] );
					$menu->action_value        = $this->get_action_value( $cat['type'], $cat['value'], html_entity_decode( $cat['label'] ) );
					$menu->nodes               = array();

				}
				if ( 0 !== $parent ) {
					$this->insert_into_parent($return,$parent,$menu);
				} else {
					$return[ $id ] = $menu;
				}
			}
		}
		return $this->menu_values( $return );
	}
	public function menu_values( $return ){
		$menu = array_values( $return );
		foreach ( $menu as $id => $menu_item){
			if( isset( $menu_item->nodes ) && is_array($menu_item->nodes)){
				$menu[$id]->nodes = $this->menu_values( $menu_item->nodes );
			}else{
				$menu[$id]->nodes = array();
			}
		}
		return $menu;
	}
	public function insert_into_parent(&$return,$parent,$menu){
		if ( isset( $return[ $parent ] ) ) {
			$return[ $parent ]->nodes[$menu->id] = $menu;
		} else {
			foreach ( $return as $id => $menu_item){
				if( isset( $menu_item->nodes ) && is_array($menu_item->nodes)){
					$this->level++;
					$this->insert_into_parent($menu_item->nodes,$parent,$menu);
				}
			}
		}
	}
}