<?php

namespace wpie\import\wc\order\address;

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'vj-wp-import-export'));
}
if (file_exists(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php')) {

    require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php');
}

class WPIE_Order_Address extends \wpie\import\base\WPIE_Import_Base {

    private $billing = array();
    private $fields = array(
        'first_name',
        'last_name',
        'company',
        'address_1',
        'address_2',
        'city',
        'postcode',
        'country',
        'state',
        'phone',
        'email'
    );

    public function __construct($wpie_import_option = array(), $wpie_import_record = array(), $item_id = 0, $is_new_item = true, $order = null) {

        $this->wpie_import_option = $wpie_import_option;

        $this->wpie_import_record = $wpie_import_record;

        $this->item_id = $item_id;

        $this->order = $order;

        $this->is_new_item = $is_new_item;

        if ($this->is_update_field("billing_details")) {
            $this->prepare_billing();
        }
        if ($this->is_update_field("shipping_details")) {
            $this->prepare_shipping();
        }
    }

    private function prepare_billing() {

        $source = wpie_sanitize_field($this->get_field_value('wpie_item_order_billing_source', true));

        $customer_id = 0;

        if (!empty($source)) {

            switch ($source) {

                case "existing":

                    $customer_id = $this->get_customer();

                    if (absint($customer_id) > 0) {

                        foreach ($this->fields as $field) {

                            $this->billing[$field] = get_user_meta($customer_id, "billing_" . $field, true);
                        }
                    } else {

                        $no_match_as_guest = absint(wpie_sanitize_field($this->get_field_value('wpie_item_order_billing_no_match_guest')));

                        if ($no_match_as_guest == 1) {

                            foreach ($this->fields as $field) {

                                $this->billing[$field] = wpie_sanitize_field($this->get_field_value('wpie_item_guest_billing_' . $field));
                            }
                        }
                        unset($no_match_as_guest);
                    }

                    break;

                default:

                    foreach ($this->fields as $field) {

                        $this->billing[$field] = wpie_sanitize_field($this->get_field_value('wpie_item_billing_' . $field));
                    }

                    break;
            }
        }

        $this->order->set_customer_id($customer_id);
        $this->order->set_billing_first_name(isset($this->billing["first_name"]) ? $this->billing["first_name"] : "");
        $this->order->set_billing_last_name(isset($this->billing["last_name"]) ? $this->billing["last_name"] : "");
        $this->order->set_billing_company(isset($this->billing["company"]) ? $this->billing["company"] : "");
        $this->order->set_billing_address_1(isset($this->billing["address_1"]) ? $this->billing["address_1"] : "");
        $this->order->set_billing_address_2(isset($this->billing["address_2"]) ? $this->billing["address_2"] : "");
        $this->order->set_billing_city(isset($this->billing["city"]) ? $this->billing["city"] : "");
        $this->order->set_billing_state(isset($this->billing["state"]) ? $this->billing["state"] : "");
        $this->order->set_billing_postcode(isset($this->billing["postcode"]) ? $this->billing["postcode"] : "");
        $this->order->set_billing_country(isset($this->billing["country"]) ? $this->billing["country"] : "");
        $this->order->set_billing_email(isset($this->billing["email"]) ? $this->billing["email"] : "");
        $this->order->set_billing_phone(isset($this->billing["phone"]) ? $this->billing["phone"] : "");


        unset($source, $customer_id);
    }

    private function prepare_shipping() {

        $source = wpie_sanitize_field($this->get_field_value('wpie_item_order_shipping_source', true));

        $shipping = array();

        if (!empty($source)) {

            switch ($source) {

                case "copy":
                    if (!empty($this->billing)) {
                        $shipping = $this->billing;
                    }
                    break;

                default:

                    $is_empty = true;

                    foreach ($this->fields as $field) {

                        $field_value = wpie_sanitize_field($this->get_field_value('wpie_item_shipping_' . $field));

                        if ($field_value != "") {
                            $is_empty = false;
                            break;
                        }

                        unset($field_value);
                    }

                    if ($is_empty) {

                        if (!empty($this->billing)) {

                            $shipping = $this->billing;
                        }
                    } else {

                        foreach ($this->fields as $field) {

                            $shipping[$field] = wpie_sanitize_field($this->get_field_value('wpie_item_shipping_' . $field));
                        }
                    }
                    unset($is_empty);

                    break;
            }
        }

        $this->order->set_shipping_first_name(isset($shipping["first_name"]) ? $shipping["first_name"] : "");
        $this->order->set_shipping_last_name(isset($shipping["last_name"]) ? $shipping["last_name"] : "");
        $this->order->set_shipping_company(isset($shipping["company"]) ? $shipping["company"] : "");
        $this->order->set_shipping_address_1(isset($shipping["address_1"]) ? $shipping["address_1"] : "");
        $this->order->set_shipping_address_2(isset($shipping["address_2"]) ? $shipping["address_2"] : "");
        $this->order->set_shipping_city(isset($shipping["city"]) ? $shipping["city"] : "");
        $this->order->set_shipping_state(isset($shipping["state"]) ? $shipping["state"] : "");
        $this->order->set_shipping_postcode(isset($shipping["postcode"]) ? $shipping["postcode"] : "");
        $this->order->set_shipping_country(isset($shipping["country"]) ? $shipping["country"] : "");

        unset($source);
    }

    private function get_customer() {

        global $wpdb;

        $user_id = 0;

        $indicator = wpie_sanitize_field($this->get_field_value('wpie_item_order_billing_match_by'));

        if ($indicator == "id") {

            $_id = absint(wpie_sanitize_field($this->get_field_value('wpie_item_order_billing_match_by_user_id')));

            if ($_id > 0) {
                $user = get_user_by('id', absint($_id));

                if ($user) {
                    $user_id = $_id;
                }
                unset($user);
            }

            unset($_id);
        } elseif ($indicator == "email") {

            $email = wpie_sanitize_field($this->get_field_value('wpie_item_order_billing_match_by_email'));

            if (!empty($email)) {
                $user = get_user_by('email', $email);

                if ($user) {
                    $user_id = $user->ID;
                }
                unset($user);
            }
            unset($email);
        } elseif ($indicator == "login") {

            $user_login = wpie_sanitize_field($this->get_field_value('wpie_item_order_billing_match_by_username'));

            if (!empty($user_login)) {
                $user = get_user_by('login', $user_login);

                if ($user) {
                    $user_id = $user->ID;
                }
                unset($user);
            }
            unset($user_login);
        } elseif ($indicator == "cf") {

            $meta_key = wpie_sanitize_field($this->get_field_value('wpie_item_order_billing_match_by_cf_name'));

            $meta_val = wpie_sanitize_field($this->get_field_value('wpie_item_order_billing_match_by_cf_value'));

            $user_query = array(
                'meta_query' => array(
                    0 => array(
                        'key' => $meta_key,
                        'value' => $meta_val,
                        'compare' => '='
                    )
                )
            );

            $user_data = new \WP_User_Query($user_query);

            unset($user_query);

            if (!empty($user_data->results)) {
                foreach ($user_data->results as $user) {
                    $this->existing_item_id = $user->ID;
                    break;
                }
            } else {
                $user_data_found = $wpdb->get_results($wpdb->prepare("SELECT SQL_CALC_FOUND_ROWS " . $wpdb->users . ".ID FROM " . $wpdb->users . " INNER JOIN " . $wpdb->usermeta . " ON (" . $wpdb->users . ".ID = " . $wpdb->usermeta . ".user_id) WHERE 1=1 AND ( (" . $wpdb->usermeta . ".meta_key = %s AND " . $wpdb->usermeta . ".meta_value = %s) ) GROUP BY " . $wpdb->users . ".ID ORDER BY " . $wpdb->users . ".ID ASC LIMIT 0, 1", $meta_key, $meta_val));

                if (!empty($user_data_found)) {
                    foreach ($user_data_found as $user) {
                        $user_id = $user->ID;
                        break;
                    }
                }
                unset($user_data_found);
            }
            unset($meta_key, $meta_val, $user_data);
        }

        unset($indicator);

        return $user_id;
    }

    public function __destruct() {

        parent::__destruct();

        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }

}
