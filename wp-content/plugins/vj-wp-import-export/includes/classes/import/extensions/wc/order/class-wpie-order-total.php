<?php

namespace wpie\import\wc\order\total;

use WC_Payment_Gateways;

if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'vj-wp-import-export'));
}
if (file_exists(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php')) {

    require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php');
}

class WPIE_Order_Total extends \wpie\import\base\WPIE_Import_Base {

    /**
     * @var \WC_Order
     */
    private $order;

    public function __construct($wpie_import_option = array(), $wpie_import_record = array(), $item_id = 0, $is_new_item = true, &$addon_error = false, &$addon_log = array(), $order) {

        $this->wpie_import_option = $wpie_import_option;

        $this->wpie_import_record = $wpie_import_record;

        $this->item_id = $item_id;

        $this->is_new_item = $is_new_item;

        $this->order = $order;

        $this->addon_error = &$addon_error;

        $this->addon_log = &$addon_log;

        $this->prepare_total();
    }

    private function prepare_total() {

        $order_total_logic = wpie_sanitize_field($this->get_field_value('wpie_item_order_total'));

        if ($order_total_logic == "manually") {

            $total = wpie_sanitize_field($this->get_field_value('wpie_item_order_total_as_specified'));

            $this->order->set_total($total);

            unset($total);
        } else {
            $this->order->calculate_totals(false);
        }

        unset($order_total_logic);
    }

    public function __destruct() {

        parent::__destruct();

        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }

}
