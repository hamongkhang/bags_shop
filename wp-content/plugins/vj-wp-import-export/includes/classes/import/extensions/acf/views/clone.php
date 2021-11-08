<?php
$wpie_acf_fields = new \wpie\import\acf\WPIE_ACF();
?>
<div class="wpie_item_act_inner_wrapper" data-key="<?php echo esc_attr($field_key) ?>">
    <div class="wpie_item_act_label" ><?php echo esc_html($title); ?></div>
    <div class="wpie_field_mapping_radio_input_wrapper">
        <input type="radio" class="wpie_radio wpie_item_acf_choice_yes_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" checked="checked" name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key); ?>][value_option]" id="wpie_item_acf_choice_yes_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" value="direct"/>
        <label for="wpie_item_acf_choice_yes_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" class="wpie_radio_label"><?php esc_html_e('Select value for all records', 'vj-wp-import-export'); ?></label>
        <div class="wpie_radio_container">
            <div class="wpie_acf_layout_data_wrapper">       
                <div class="wpie_acf_layout_data_container " >
                    <?php
                    if (isset($field['sub_fields']) && !empty($field['sub_fields'])) {
                            foreach ($field['sub_fields'] as $field_data) {
                                    $wpie_acf_fields->get_acf_field_views($field_data, $field, true);
                            }
                    }
                    ?>
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
