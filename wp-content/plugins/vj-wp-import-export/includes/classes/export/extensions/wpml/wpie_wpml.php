<?php

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'vj-wp-import-export'));
}

class WPIE_WPML_Export_Extension {

    public function __construct() {

        if (class_exists('SitePress')) {

            add_filter('wpie_add_export_extension_files', array($this, 'get_wpml_tab_view'), 10, 1);

            add_filter('wpie_prepare_post_fields', array($this, 'prepare_wpml_addon'), 10, 2);

            add_filter('wpie_prepare_taxonomy_fields', array($this, 'prepare_wpml_addon'), 10, 2);

            add_filter('wpie_prepare_export_addons', array($this, 'prepare_wpml_addon'), 10, 2);
        }
    }

    public function prepare_wpml_addon($addons = array(), $export_type = "post") {

        if (!in_array($export_type, array("users", "comments"))) {

            $fileName = WPIE_EXPORT_CLASSES_DIR . '/extensions/wpml/class-wpie-wpml.php';

            $class = '\wpie\export\wpml\WPIE_WPML_Export';

            if (file_exists($fileName)) {

                require_once($fileName);
            }

            if ($class != "" && !in_array($class, $addons)) {
                $addons[] = $class;
            }

            unset($class, $fileName);
        }
        return $addons;
    }

    public function get_wpml_tab_view($files = array()) {

        $fileName = WPIE_EXPORT_CLASSES_DIR . '/extensions/wpml/wpie_wpml_tab.php';

        if (!in_array($fileName, $files)) {

            $files[] = $fileName;
        }

        return $files;
    }

}

new WPIE_WPML_Export_Extension();
