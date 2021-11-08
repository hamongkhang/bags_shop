<?php
$wpie_acf_fields = new \wpie\import\acf\WPIE_ACF();
?>
<div class="wpie_item_act_inner_wrapper" data-key="<?php echo esc_attr($field_key) ?>">
    <div class="wpie_item_act_label" ><?php echo esc_html($title); ?></div>

    <div class="wpie_field_mapping_radio_input_wrapper">
        <input type="radio" class="wpie_radio wpie_item_acf_choice_yes_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" checked="checked" name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key); ?>][value_option]" id="wpie_item_acf_choice_yes_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" value="direct"/>
        <label for="wpie_item_acf_choice_yes_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" class="wpie_radio_label"><?php esc_html_e('Select value for all records', 'vj-wp-import-export'); ?></label>
        <div class="wpie_radio_container wpie_fc_direct_container">
            <div class="wpie_acf_fc_layout_wrapper wpie_acf_layout_data_wrapper">
                <?php
                if (isset($field['layouts']) && !empty($field['layouts'])) {
                        foreach ($field['layouts'] as $i => $layout) {
                                $id = isset($layout['ID']) ? $layout['ID'] : $i;
                                ?>
                                <div class="wpie_acf_cf_layout_container wpie_acf_layout_data_container" data-container="wpie_acf_cf_layout_<?php echo esc_attr($id); ?>">
                                    <input type="hidden" class="wpie_item_acf_fc_<?php echo esc_attr($field_key) ?>" name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key) ?>][wpie_row_number][layout]" value="<?php echo esc_attr($id); ?>"/>
                                    <div class="wpie_acf_cf_layout_label"><?php echo isset($layout['name']) ? esc_html($layout['name']) : ""; ?></div>
                                    <?php
                                    if (isset($layout['sub_fields']) && !empty($layout['sub_fields'])) {
                                            foreach ($layout['sub_fields'] as $field_data) {
                                                    $wpie_acf_fields->get_acf_field_views($field_data, $field, true);
                                            }
                                    }
                                    ?>
                                </div>
                                <?php
                        }
                }
                ?>
            </div>
            <div class="wpie_acf_fc_layout_action_wrapper">
                <select class="wpie_content_data_select wpie_acf_cf_layout_data" >
                    <option selected="selected" value=""><?php esc_html_e('Select Layout', 'vj-wp-import-export'); ?></option>
                    <?php
                    foreach ($field['layouts'] as $key => $layout) {
                            $id = isset($layout['ID']) ? $layout['ID'] : $key;
                            ?>
                            <option value="wpie_acf_cf_layout_<?php echo esc_attr($id); ?>"><?php echo esc_html($layout['label']); ?></option>
                    <?php }
                    ?>
                </select>
                <div class="wpie_acf_fc_btn_wrapper">
                    <div class="wpie_btn wpie_btn_primary wpie_acf_fc_add_layout">
                        <i class="fas fa-plus wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e('Add Layout', 'vj-wp-import-export'); ?>
                    </div>
                    <div class="wpie_btn wpie_btn_primary wpie_acf_fc_remove_layout">
                        <i class="fas fa-times wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e('Delete Layout', 'vj-wp-import-export'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="wpie_field_mapping_radio_input_wrapper">
        <input type="radio" class="wpie_radio wpie_item_acf_choice_no_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?> " name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key); ?>][value_option]" id="wpie_item_acf_choice_no_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" value="custom"/>
        <label for="wpie_item_acf_choice_no_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" class="wpie_radio_label"><?php esc_html_e('As specified', 'vj-wp-import-export'); ?></label>
        <div class="wpie_radio_container wpie_as_specified_wrapper">
            <input type="text" class="wpie_content_data_input wpie_item_acf_choice_custom_data_<?php echo esc_attr($field_key); ?>_data" name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key); ?>][custom_value]" value=""/>
            <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e("All fields are combined together and json value", "vj-wp-import-export"); ?>"></i>
        </div>
    </div>
</div>
<?php
unset($wpie_acf_fields);
