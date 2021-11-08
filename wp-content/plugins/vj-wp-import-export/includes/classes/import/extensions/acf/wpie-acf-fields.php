<?php
if (!defined('ABSPATH')) {
        die(__("Can't load this file directly", 'vj-wp-import-export'));
}
if (!function_exists("wpie_get_acf_fields")) {

        function wpie_get_acf_fields($sections = array(), $wpie_import_type = "") {

                global $acf;

                if ($acf && isset($acf->settings) && isset($acf->settings['version']) && version_compare($acf->settings['version'], '5.0.0') >= 0) {

                        $fileName = WPIE_IMPORT_CLASSES_DIR . '/extensions/acf/class-wpie-acf.php';

                        $class = '\wpie\import\acf\WPIE_ACF';
                } else {
                        return $sections;
                }

                require_once($fileName);

                $wpie_acf = new $class();

                $acf_groups = $wpie_acf->get_acf_groups();

                unset($fileName, $class);

                ob_start();
                ?>
                <div class="wpie_field_mapping_container_wrapper wpie_acf_field_container">
                    <div class="wpie_field_mapping_container_title"><?php esc_html_e('Advanced Custom Fields', 'vj-wp-import-export'); ?><div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div></div>
                    <div class="wpie_field_mapping_container_data">                       
                        <div class="wpie_field_mapping_container_element wpie_acf_field_outer_wrapper">
                            <div class="wpie_field_mapping_radio_input_wrapper wpie_cf_notice_wrapper">
                                <input type="checkbox" id="acf_skip_empty" name="skip_empty" checked="checked" value="1" class="wpie_checkbox acf_skip_empty">
                                <label class="wpie_checkbox_label" for="acf_skip_empty"><?php esc_html_e("Don't add Empty value fields in database.", 'vj-wp-import-export'); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e("it's highly recommended. If custom field value is empty then it skip perticular field and not add to database. it's save memory and increase import speed", "vj-wp-import-export"); ?>"></i></label>
                            </div>
                            <div class="wpie_field_mapping_inner_title wpie_acf_group_header"><?php esc_html_e('Please choose your Field Groups.', 'vj-wp-import-export'); ?></div>
                            <?php
                            if (!empty($acf_groups)) {
                                    foreach ($acf_groups as $group_key => $group) {

                                            $group_id = isset($group->ID) ? $group->ID : "";

                                            $title = isset($group->post_title) ? $group->post_title : "";
                                            ?>
                                            <div class="wpie_field_mapping_other_option_wrapper wpie_item_add_on_demand_wrapper">
                                                <input id="wpie_item_acf_group_<?php echo esc_attr($group_key); ?>" type="checkbox" class="wpie_checkbox wpie_item_add_on_demand wpie_item_acf_group wpie_item_acf_group_<?php echo esc_attr($group_key); ?>" name="wpie_item_acf_group[<?php echo esc_attr($group_key); ?>]" data-container="wpie_acf_group_data_<?php echo esc_attr($group_key); ?>" value="1">
                                                <label for="wpie_item_acf_group_<?php echo esc_attr($group_key); ?>" class="wpie_checkbox_label"><?php echo esc_html($title); ?></label>
                                                <?php
                                                $acf_fields = $wpie_acf->get_acf_field_by_group($group_id);

                                                if (!empty($acf_fields)) {
                                                        ?>
                                                        <div class="wpie_checkbox_container wpie_acf_field_wrapper wpie_acf_group_data_<?php echo esc_attr($group_key); ?>">
                                                            <?php
                                                            $wpie_acf->get_acf_fields_view($acf_fields);
                                                            ?>
                                                        </div>
                                                        <?php
                                                }
                                                unset($acf_fields);
                                                ?>
                                            </div>
                                            <?php
                                    }
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
                $acf_section = ob_get_clean();

                $fields = array(
                        '340' => $acf_section,
                );

                $sections = array_replace($sections, $fields);

                unset($acf_section, $fields, $acf_groups, $wpie_acf);

                return $sections;
        }

}