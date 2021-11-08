<?php

//$this->options = get_option( 'appmaker_option' );
class APPMAKER_WC_REST_BACKEND_NAV_Controller extends APPMAKER_WP_WC_REST_BACKEND_NAV_Controller
{
    public $plugin = 'appmaker_wc';
    /**
     * Endpoint namespace.
     *
     * @var string
     */
    protected $namespace = 'appmaker-wc/v1';

    /**
     *  Return default menu
     *
     * @return array
     */
    public function get_default_menu()
    {
        $args = array(
        'taxonomy'     => 'product_cat',
        'orderby'      => 'id',
        'show_count'   => true,
        'pad_counts'   => true,
        'hierarchical' => true,
        'title_li'     => '',
        'hide_empty'   => true,
        );

        $cat_terms = get_categories($args);
        $all_categories = $this->buildmenuTree( $cat_terms );
        $return    = array();
        $menu_type = new stdClass();
        $menu_type->id            = 'menu_item';
        $menu_type->label         = 'Menu Item';

        $menu_action_in_app_page = new stdClass();
        $menu_action_in_app_page->id            = 'OPEN_IN_APP_PAGE';
        $menu_action_in_app_page->label         = 'Open In-App Page';

        $home_action_value = new stdClass();
        $home_action_value->id            = 'home';
        $home_action_value->label         = 'Home';
        $home_action_value->key         = 'home';

        $return[0] = new stdClass();
        $return[0]->id           = 0;
        $return[0]->title        = __('Home');
        $return[0]->icon         = '';
        $return[0]->type         = $menu_type;
        $return[0]->action       = $menu_action_in_app_page;
        $return[0]->action_value = $home_action_value;
        $return[0]->children = array();
        $return = array_merge($return,$all_categories);
        return array_values($return);
    }

    function buildmenuTree( array $elements, $options = array(
		'parent_id_column_name' => 'parent',
		'children_key_name'     => 'children',
		'id_column_name'        => 'term_id',
	), $parentId = 0 ) {
        $branch = array();
        $menu_type = new stdClass();
		foreach ( $elements as $element ) {
			if ( $element->{$options['parent_id_column_name']} == $parentId ) {
				$children = $this->buildmenuTree( $elements, $options, $element->{$options['id_column_name']} );
                $menu                      = new stdClass();
                $menu->id                  = $element->term_id;
                $menu->title               = $element->name;
                $menu->icon                = '';
                $menu->type                = $menu_type;
                $menu->type->id            = 'menu_item';
			    $menu->type->label         = 'Menu Item';
                $menu->action              = new stdClass();
                $menu->action->id          = 'LIST_PRODUCT';
                $menu->action->label       = 'Open Product Category';
                $menu->action_value        = new stdClass();
                $menu->action_value->id    = $element->term_id;
                $menu->action_value->label = $element->name;
                $menu->children               = array();
                if ( $children ) {
                  $menu->children = $children;
				} else {
					$menu->children = array();
                }
				$branch[] = $menu;
			}
		}
		return $branch;
	}
    
}
