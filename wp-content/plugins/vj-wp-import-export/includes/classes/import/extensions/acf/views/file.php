<div class="wpie_acf_item_wrapper" >
    <div class="wpie_item_act_label" ><?php echo esc_html($title); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e("Specify the URL to the image or file.", "vj-wp-import-export"); ?>"></i></div>
    <input type="text" class="wpie_content_data_input wpie_item_acf_text" name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key) ?>][value]" placeholder=""/>
    <div class="wpie_field_mapping_radio_input_wrapper">
        <input type="checkbox" class="wpie_update_data_inner_option wpie_checkbox wpie_item_acf_content_search_through_media_<?php echo esc_attr($field_key) ?>" name="acf[<?php echo esc_attr($field_key) ?>][search_through_media]" id="wpie_item_acf_content_search_through_media_<?php echo esc_attr($field_key) ?>" value="1"/>
        <label for="wpie_item_acf_content_search_through_media_<?php echo esc_attr($field_key) ?>" class="wpie_checkbox_label"><?php esc_html_e(' Search through the Media Library for existing images before importing new images', 'vj-wp-import-export'); ?></label>    
        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e("If an image with the same file name is found in the Media Library then that image will be attached to this record instead of importing a new image. Disable this setting if your import has different images with the same file name.", "vj-wp-import-export"); ?>"></i>
    </div>
    <div class="wpie_field_mapping_radio_input_wrapper">
        <input type="checkbox" class="wpie_update_data_inner_option wpie_checkbox wpie_item_acf_content_search_through_upload_dir_<?php echo esc_attr($field_key) ?>" name="acf[<?php echo esc_attr($field_key) ?>][use_upload_dir]" id="wpie_item_acf_content_search_through_upload_dir_<?php echo esc_attr($field_key) ?>" value="1"/>
        <label for="wpie_item_acf_content_search_through_upload_dir_<?php echo esc_attr($field_key) ?>" class="wpie_checkbox_label"><?php echo esc_html(__('Use images currently uploaded in', 'vj-wp-import-export') . " " . WPIE_UPLOAD_TEMP_DIR); ?></label>
    </div>
</div>