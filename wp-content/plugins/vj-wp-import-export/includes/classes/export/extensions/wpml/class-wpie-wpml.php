<?php

namespace wpie\export\wpml;

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'vj-wp-import-export'));
}

if (file_exists(WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export-base.php')) {

    require_once(WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export-base.php');
}

class WPIE_WPML_Export extends \wpie\export\base\WPIE_Export_Base {

    public function __construct() {
        
    }

    public function pre_process_fields(&$export_fields = array(), $export_type = array()) {

        if (in_array("shop_coupon", $export_type)) {
            return;
        }

        if (!isset($export_fields['standard'])) {
            $export_fields['standard'] = array();
        }
        if (!isset($export_fields['standard']['data'])) {
            $export_fields['standard']['data'] = array();
        }
        $export_fields['standard']['data'][] = array(
            'name' => 'WPML Translation ID',
            'type' => 'wpml_trid',
        );
        if (in_array("taxonomies", $export_type)) {
            $export_fields['standard']['data'][] = array(
                'name' => 'WPML Translation Slug',
                'type' => 'wpml_translation_slug',
            );
        } else {
            $export_fields['standard']['data'][] = array(
                'name' => 'WPML Translation Title',
                'type' => 'wpml_translation_title',
            );
        }
        $export_fields['standard']['data'][] = array(
            'name' => 'WPML Language Code',
            'type' => 'wpml_lang',
        );
    }

    public function init_process($template_options = array()) {
        if (isset($template_options['wpie_wpml_lang']) && !empty($template_options['wpie_wpml_lang'])) {
            do_action('wpml_switch_language', wpie_sanitize_field($template_options['wpie_wpml_lang']));
        }
    }

    public function process_addon_data(&$export_data = array(), $field_type = "", $field_name = "", $field_option = array(), $item = null, $site_date_format = "") {

        global $wp_taxonomies;

        if ($field_type && in_array($field_type, array("wpml_trid", "wpml_lang", "wpml_translation_title","wpml_translation_slug"))) {

            $is_php = isset($field_option['isPhp']) ? wpie_sanitize_field($field_option['isPhp']) == 1 : false;

            $php_func = isset($field_option['phpFun']) ? wpie_sanitize_field($field_option['phpFun']) : "";

            $item_id = 0;

            $item_type = "";

            $element_type = "";

            if (isset($item->term_taxonomy_id)) {
                $item_id = $item->term_taxonomy_id;
                $item_type = "taxonomy";
                $element_type = isset($item->taxonomy) ? $item->taxonomy : "category";
            } elseif (isset($item->ID)) {
                $item_id = $item->ID;
                $item_type = "post";
                $element_type = isset($item->post_type) ? $item->post_type : "post";
            }


            switch ($field_type) {

                case 'wpml_trid':

                    $element_type = apply_filters('wpml_element_type', $element_type);

                    $wpml_original_element_id = apply_filters('wpml_original_element_id', null, $item_id, $element_type);

                    $export_data[$field_name] = apply_filters('wpie_export_wpml_trid_field', $this->apply_user_function($wpml_original_element_id, $is_php, $php_func), $item);

                    break;

                case 'wpml_lang':

                    $element_type = apply_filters('wpml_element_type', $element_type);

                    $post_language_details = apply_filters('wpml_element_language_details', null, array(
                        'element_id' => $item_id,
                        'element_type' => $element_type
                            )
                    );

                    $language_code = (isset($post_language_details->language_code) && !empty($post_language_details->language_code)) ? $post_language_details->language_code : "";

                    $export_data[$field_name] = apply_filters('wpie_export_wpml_language_code', $this->apply_user_function($language_code, $is_php, $php_func), $item);

                    unset($post_language_details, $language_code);

                    break;
                case 'wpml_translation_title':

                    $element_type = apply_filters('wpml_element_type', $element_type);

                    $original_post_id = apply_filters('wpml_original_element_id', null,
                            $item_id,
                            $element_type
                    );

                    $title = "";

                    if ($original_post_id != $item_id) {

                        if ($item_type == "post") {

                            $post = get_post($original_post_id);

                            $title = isset($post->post_title) ? $post->post_title : "";

                            unset($post);
                        } elseif ($item_type == "taxonomy") {

                            $taxonomy = get_term_by("term_taxonomy_id", $original_post_id, $item->taxonomy);

                            $title = isset($taxonomy->name) ? $taxonomy->name : "";

                            unset($taxonomy);
                        }
                    }

                    $export_data[$field_name] = apply_filters('wpie_export_wpml_language_title', $this->apply_user_function($title, $is_php, $php_func), $item);

                    unset($post_language_details, $original_post_id, $title);

                    break;
                case 'wpml_translation_slug':

                    $element_type = apply_filters('wpml_element_type', $element_type);

                    $original_post_id = apply_filters('wpml_original_element_id', null,
                            $item_id,
                            $element_type
                    );

                    $title = "";

                    if ($original_post_id != $item_id) {

                       if ($item_type == "taxonomy") {

                            $taxonomy = get_term_by("term_taxonomy_id", $original_post_id, $item->taxonomy);

                            $title = isset($taxonomy->slug) ? $taxonomy->slug : "";

                            unset($taxonomy);
                        }
                    }

                    $export_data[$field_name] = apply_filters('wpie_export_wpml_language_slug', $this->apply_user_function($title, $is_php, $php_func), $item);

                    unset($post_language_details, $original_post_id, $title);

                    break;
            }

            unset($is_php, $php_func);
        }
    }

    public function __destruct() {
        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }

}
