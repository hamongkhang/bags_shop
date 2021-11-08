<div class="wpie_acf_item_wrapper wpie_acf_item_post_object_wrapper" >
    <div class="wpie_item_act_label" ><?php echo esc_html($title); ?></div>
    <input type="text" class="wpie_content_data_input wpie_item_acf_text wpie_content_data_input_medium" name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key) ?>][value]" placeholder=""/>
    <input type="text" class="wpie_content_data_input wpie_field_mapping_input_separator wpie_item_acf_post_object_delim wpie_item_acf_post_object_delim_<?php echo esc_attr($field_key) ?>" name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key) ?>][delim]" placeholder="," value=","/>
    <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e("Enter the ID or Title. Separate multiple entries with commas.", "vj-wp-import-export"); ?>"></i>
</div>