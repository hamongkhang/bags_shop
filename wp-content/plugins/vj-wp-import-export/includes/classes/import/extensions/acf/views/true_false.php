<div class="wpie_acf_item_wrapper" >
    <div class="wpie_item_act_label" ><?php echo esc_html($title); ?></div>
    <div class="wpie_field_mapping_radio_input_wrapper">
        <input type="radio" class="wpie_radio wpie_item_acf_choice_yes_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" checked="checked" name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key); ?>][value_option]" id="wpie_item_acf_choice_yes_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" value="direct"/>
        <label for="wpie_item_acf_choice_yes_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" class="wpie_radio_label"><?php esc_html_e('Select value for all records', 'vj-wp-import-export'); ?></label>
        <div class="wpie_radio_container">
            <?php
            $field['class'] = "wpie_acf_true_false_wrapper";

            $field['prefix'] = "acf" . $parent_field_key . "[" . $field_key . "]";

            echo acf_render_field($field);
            ?>
        </div>
    </div>
    <div class="wpie_field_mapping_radio_input_wrapper">
        <input type="radio" class="wpie_radio wpie_item_acf_choice_no_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?> " name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key); ?>][value_option]" id="wpie_item_acf_choice_no_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" value="custom"/>
        <label for="wpie_item_acf_choice_no_<?php echo esc_attr($parent_field_key); ?><?php echo esc_attr($field_key); ?>" class="wpie_radio_label"><?php esc_html_e('As specified', 'vj-wp-import-export'); ?></label>
        <div class="wpie_radio_container wpie_as_specified_wrapper">
            <input type="text" class="wpie_content_data_input wpie_item_acf_choice_custom_data_<?php echo esc_attr($field_key); ?>_data" name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key); ?>][custom_value]" value=""/>
            <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e("Specify the value. For multiple values, separate with commas. If the choices are of the format option : Option, option-2 : Option 2, use option and option-2 for values.", "vj-wp-import-export"); ?>"></i>
        </div>
    </div>
</div>