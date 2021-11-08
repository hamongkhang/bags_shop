<?php

namespace wpie\import\wc\product\grouped;

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'vj-wp-import-export'));
}

if (file_exists(WPIE_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-wpie-product-base.php')) {

    require_once(WPIE_IMPORT_CLASSES_DIR . '/extensions/wc/product/class-wpie-product-base.php');
}

class WPIE_Grouped_Product extends \wpie\import\wc\product\base\WPIE_Product_Base {

    protected $product_type = 'grouped';

    public function import_data() {

        parent::import_data();
    }

    public function prepare_general_properties() {

        parent::prepare_general_properties();

        $this->product_properties['children'] = array();
    }
    public function __destruct() {
        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }

}
