<?php

namespace wpie\import\wc\order\payment;

use WC_Payment_Gateways;

if (!defined('ABSPATH')) {
        die(__("Can't load this file directly", 'vj-wp-import-export'));
}
if (file_exists(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php')) {

        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import-base.php');
}

class WPIE_Order_Payment extends \wpie\import\base\WPIE_Import_Base {

        public function __construct($wpie_import_option = array(), $wpie_import_record = array(), $item_id = 0, $is_new_item = true, $order = null) {

                $this->wpie_import_option = $wpie_import_option;

                $this->wpie_import_record = $wpie_import_record;

                $this->item_id = $item_id;

                $this->order = $order;

                $this->is_new_item = $is_new_item;

                if ($this->is_update_field("payment")) {
                        $this->prepare_payment();
                }
        }

        private function prepare_payment() {

                $payment_method = wpie_sanitize_field($this->get_field_value('wpie_item_order_payment_method', false, true));

                $payment = \WC_Payment_Gateways::instance();

                $payment_gateways = $payment->payment_gateways();

                $_payment_method = "";

                $_payement_method_title = "";

                if (!empty($payment_method)) {

                        if (isset($payment_gateways[$payment_method])) {

                                $_payment_method        = $payment_gateways[$payment_method]->id;
                                $_payement_method_title = $payment_gateways[$payment_method]->get_title();
                        } else {

                                if (!empty($payment_gateways)) {
                                        foreach ($payment_gateways as $slug => $gateway) {
                                                if (strtolower($gateway->method_title) == strtolower(trim($payment_method))) {
                                                        $_payment_method        = $gateway->id;
                                                        $_payement_method_title = $gateway->get_title();
                                                        break;
                                                }
                                        }
                                }
                        }
                } else {
                        $_payment_method = 'N/A';
                }

                $_transaction_id = wpie_sanitize_field($this->get_field_value('wpie_item_order_transaction_id'));

                $this->order->set_transaction_id($_transaction_id);

                $this->order->set_payment_method($_payment_method);

                $this->order->set_payment_method_title($_payement_method_title);

                unset($payment_method, $payment, $payment_gateways, $_payment_method, $_transaction_id);
        }

        public function __destruct() {

                parent::__destruct();

                foreach ($this as $key => $value) {
                        unset($this->$key);
                }
        }

}
