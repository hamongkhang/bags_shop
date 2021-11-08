<div class="wpie_acf_item_wrapper" >
    <div class="wpie_item_act_label" ><?php echo esc_html($title); ?></div>
    <div class="wpie_item_act_label wpie_item_act_label_inner" ><?php esc_html_e('Title', 'vj-wp-import-export'); ?></div>
    <input type="text" class="wpie_content_data_input wpie_item_acf_link_title" name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key) ?>][value][title]" placeholder=""/>
    <div class="wpie_item_act_label wpie_item_act_label_inner" ><?php esc_html_e('URL', 'vj-wp-import-export'); ?></div>
    <input type="text" class="wpie_content_data_input wpie_item_acf_link_url" name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key) ?>][value][url]" placeholder=""/>
    <div class="wpie_item_act_label wpie_item_act_label_inner" ><?php esc_html_e('Target', 'vj-wp-import-export'); ?></div>
    <input type="text" class="wpie_content_data_input wpie_item_acf_link_target" name="acf<?php echo esc_attr($parent_field_key); ?>[<?php echo esc_attr($field_key) ?>][value][target]" placeholder=""/>
</div>