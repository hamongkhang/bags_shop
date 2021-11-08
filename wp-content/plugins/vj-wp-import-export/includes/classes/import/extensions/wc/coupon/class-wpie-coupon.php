<?php

namespace wpie\import\wc\coupon;

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'vj-wp-import-export'));
}

class WPIE_Coupon_Import {

    public function __construct($wpie_import_option = array(), $import_type = "") {
        
    }

    public function update_meta_field_value($meta_key = "", $meta_val = "") {

        if (empty($meta_val)) {
            return $meta_val;
        } elseif ($meta_key == "product_categories" || $meta_key == "exclude_product_categories") {

            $temp_val = maybe_unserialize($meta_val);

            if (!is_array($temp_val)) {

                global $wpdb;

                $product_terms = array();

                $terms = explode("||", $meta_val);
                
                foreach ($terms as $term_name) {
                    $term = get_term_by('name', $term_name, "product_cat");
                    if (!empty($term) && !is_wp_error($term)) {
                        $product_terms[] = $term->term_id;
                    }
                    unset($term);
                }

                if (!empty($product_terms)) {
                    $meta_val = $product_terms;
                }
                unset($product_terms, $terms);
            }
            unset($temp_val);
        } elseif ($meta_key == "product_ids" || $meta_key == "exclude_product_ids") {

            if (strpos($meta_val, '||') !== false || (strpos($meta_val, ',') === false && !is_numeric($meta_val))) {

                global $wpdb;

                $_post = $wpdb->get_col(
                        "SELECT ID FROM " . $wpdb->posts . "
                                WHERE
                                    post_type = 'product'
                                    AND ID != 0
                                    AND post_title IN ('" . implode("','", explode("||", $meta_val)) . "')"
                );

                if ($_post) {
                    $meta_val = implode(",", $_post);
                }

                unset($_post);
            }
        }

        return $meta_val;
    }

    public function __destruct() {

        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }

}
