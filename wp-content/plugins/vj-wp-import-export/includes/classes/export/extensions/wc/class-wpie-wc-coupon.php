<?php

namespace wpie\export\wc\coupon;

use wpie\export\post;

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'vj-wp-import-export'));
}

class WPIE_WC_Coupon {

    public function __construct() {

        add_filter('wpie_pre_post_meta_fields', array($this, 'pre_post_meta_fields'), 10, 1);

        add_filter('wpie_export_fields', array($this, 'prepare_coupon_fields'), 10, 2);

        add_filter('wpie_post_standard_fields', array($this, 'prepare_coupon_standard_fields'), 10, 1);

        add_filter('wpie_post_other_fields', array($this, 'prepare_coupon_other_fields'), 10, 1);
    }

    public function pre_post_meta_fields($metas = array()) {

        return array_diff($metas, array("discount_type", "coupon_amount", "expiry_date", "free_shipping", "exclude_sale_items"));
    }

    public function prepare_coupon_standard_fields($fields = array()) {

        return array(
            'title' => __("Standerd", 'vj-wp-import-export'),
            "isDefault" => true,
            'data' => array(
                array(
                    'name' => 'Coupon ID',
                    'type' => 'id',
                    'isDefault' => true
                ),
                array(
                    'name' => 'Coupon Code',
                    'type' => 'title',
                    'isDefault' => true
                ),
                array(
                    'name' => 'Coupon Description',
                    'type' => 'excerpt',
                    'isDefault' => true
                ),
                array(
                    'name' => 'Discount Type',
                    'type' => 'wpie_cf',
                    'metaKey' => 'discount_type',
                    'isDefault' => true
                ),
                array(
                    'name' => 'Coupon Amount',
                    'type' => 'wpie_cf',
                    'metaKey' => 'coupon_amount',
                    'isDefault' => true
                ),
                array(
                    'name' => 'Expiry Date',
                    'type' => 'wpie_cf',
                    'metaKey' => 'expiry_date',
                    'isDate' => true,
                    'isDefault' => true
                ),
                array(
                    'name' => 'Free Shipping',
                    'type' => 'wpie_cf',
                    'metaKey' => 'free_shipping',
                    'isDefault' => true
                ),
                array(
                    'name' => 'Exclude Sale Items',
                    'type' => 'wpie_cf',
                    'metaKey' => 'exclude_sale_items',
                    'isDefault' => true
                )
            )
        );
    }

    public function prepare_coupon_other_fields($fields = array()) {

        return array(
            "title" => "Other",
            "data" => array(
                array(
                    'name' => 'Status',
                    'type' => 'status'
                ),
                array(
                    'name' => 'Slug',
                    'type' => 'slug'
                ),
                array(
                    'name' => 'Coupon ID',
                    'type' => 'date',
                    'isDate' => true
                ),
                array(
                    'name' => 'Post Type',
                    'type' => 'post_type'
                )
            )
        );
    }

    public function prepare_coupon_fields($coupon_fields = array(), $export_type = array()) {

        if (isset($coupon_fields['taxonomy'])) {
            unset($coupon_fields['taxonomy']);
        }
        if (isset($coupon_fields['image'])) {
            unset($coupon_fields['image']);
        }
        if (isset($coupon_fields['attachment'])) {
            unset($coupon_fields['attachment']);
        }

        return $coupon_fields;
    }

    public function process_addon_data(&$export_data = array(), $field_type = "", $field_name = "", $field_option = array(), $item = null, $site_date_format = "") {

        global $wp_taxonomies;

        if ($field_type == "wpie_cf") {

            $is_php = isset($field_option['isPhp']) ? wpie_sanitize_field($field_option['isPhp']) == 1 : false;

            $php_func = isset($field_option['phpFun']) ? wpie_sanitize_field($field_option['phpFun']) : "";

            $date_type = isset($field_option['dateType']) ? wpie_sanitize_field($field_option['dateType']) : "";

            $date_format = isset($field_option['dateFormat']) ? wpie_sanitize_field($field_option['dateFormat']) : "";

            $metaKey = isset($field_option['metaKey']) ? $field_option['metaKey'] : "";

            $value = isset($export_data[$field_name]) ? $export_data[$field_name] : "";

            if (!(empty($value) || empty($metaKey))) {

                switch ($metaKey) {

                    case 'exclude_product_categories':
                    case 'product_categories':

                        $value = maybe_unserialize($value);

                        if (empty($value)) {

                            $export_data[$field_name] = "";
                        } else {

                            $tax_value = array();

                            $post_data = new \wpie\export\post\WPIE_Post();

                            $tax_value = $post_data->get_hierarchy_by_taxonomy_id($value, "product_cat");

                            if ($tax_value !== false) {
                                $export_data[$field_name] = implode("||", $tax_value);
                            }
                            unset($tax_value, $post_data);
                        }

                        break;

                    case 'exclude_product_ids':
                    case 'product_ids':

                        $product_ids = explode(",", $value);

                        $title = array();

                        foreach ($product_ids as $_id) {
                            $title[] = get_the_title($_id);
                        }

                        $export_data[$field_name] = implode("||", $title);

                        unset($title, $product_ids);

                        break;

                    default:
                        break;
                }
            }
            unset($is_php, $php_func, $date_type, $date_format, $metaKey);
        }
    }

    public function __destruct() {
        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }

}
