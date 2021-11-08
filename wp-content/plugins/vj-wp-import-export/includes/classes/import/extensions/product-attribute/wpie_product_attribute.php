<?php

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'vj-wp-import-export'));
}

class WPIE_Product_Attribute_Import_Extension {

    public function __construct() {

        add_filter('wpie_import_engine_init', array($this, "wpie_import_engine_init"), 10, 3);

        add_filter('wpie_import_mapping_fields_file', array($this, "wpie_import_mapping_fields_file"), 10, 2);
    }

    public function wpie_import_mapping_fields_file($fileName = "", $import_type = "") {

        if ($import_type === "product_attributes" ) {

            $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/product-attribute/wpie-product-attribute-fields.php';
        }

        return $fileName;
    }

    public function wpie_import_engine_init($import_engine = "", $wpie_import_type = "", $template_data = "") {

        if ($wpie_import_type == "product_attributes") {

            $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/product-attribute/class-wpie-product-attribute.php';

            if (file_exists($fileName)) {

                require_once($fileName);
            }
            unset($fileName);

            $import_engine = '\wpie\import\WPIE_Product_Attribute_Import';
        }

        return $import_engine;
    }

}

new WPIE_Product_Attribute_Import_Extension();
