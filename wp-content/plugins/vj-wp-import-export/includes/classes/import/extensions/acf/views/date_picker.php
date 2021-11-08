<div class="wpie_acf_item_wrapper wpie_as_specified_wrapper" >
    <div class="wpie_item_act_label" ><?php echo esc_html($title); ?></div>
    <input type="text" class="wpie_content_data_input wpie_item_acf_text" name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key) ?>][value]" placeholder=""/>
    <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e("Use any format supported by the PHP strtotime function.", "vj-wp-import-export"); ?>"></i>
</div>