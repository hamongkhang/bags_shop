<?php
if (!defined('ABSPATH')) {
        die(__("Can't load this file directly", 'vj-wp-import-export'));
}

if (!function_exists("wpie_import_order_search_existing_item")) {

        function wpie_import_order_search_existing_item($sections = "", $wpie_import_type = "") {

                ob_start();
                ?>
                <div class="wpie_field_mapping_container_element">
                    <div class="wpie_field_mapping_inner_title"><?php esc_html_e('Search Existing Item on your site based on...', 'vj-wp-import-export'); ?></div>
                    <div class="wpie_field_mapping_other_option_wrapper">
                        <input type="radio" class="wpie_radio wpie_existing_item_search_logic wpie_existing_item_search_logic_cf"  name="wpie_existing_item_search_logic" id="wpie_existing_item_search_logic_cf" value="cf"/>
                        <label for="wpie_existing_item_search_logic_cf" class="wpie_radio_label"><?php esc_html_e('Custom field', 'vj-wp-import-export'); ?></label>
                        <div class="wpie_radio_container">
                            <table class="wpie_search_based_on_cf_table">
                                <thead>
                                    <tr>
                                        <th><?php esc_html_e('Name', 'vj-wp-import-export'); ?></th>
                                        <th><?php esc_html_e('Value', 'vj-wp-import-export'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="text" class="wpie_content_data_input wpie_existing_item_search_logic_cf_key" name="wpie_existing_item_search_logic_cf_key" value=""/></td>
                                        <td><input type="text" class="wpie_content_data_input wpie_existing_item_search_logic_cf_value" name="wpie_existing_item_search_logic_cf_value" value=""/></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="wpie_field_mapping_other_option_wrapper">
                        <input type="radio" checked="checked" class="wpie_radio wpie_field_mapping_other_option_radio "  name="wpie_existing_item_search_logic" id="wpie_existing_item_search_logic_id" value="id"/>
                        <label for="wpie_existing_item_search_logic_id" class="wpie_radio_label"><?php esc_html_e('Order ID', 'vj-wp-import-export'); ?></label>
                        <div class="wpie_radio_container"><input type="text" class="wpie_content_data_input wpie_existing_item_search_logic_id" name="wpie_existing_item_search_logic_id" value=""/></div>
                    </div>
                </div>
                <?php
                return ob_get_clean();
        }

}