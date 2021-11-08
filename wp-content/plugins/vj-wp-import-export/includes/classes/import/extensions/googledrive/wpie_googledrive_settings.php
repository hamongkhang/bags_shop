<?php
if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'vj-wp-import-export'));
}
$wpie_options = get_option('wpie_import_googledrive_file_upload');

$developer_key = "";
$client_id = "";

if (!empty($wpie_options)) {

    $wpie_options = maybe_unserialize($wpie_options);

    $developer_key = isset($wpie_options['wpie_gd_developer_key']) ? $wpie_options['wpie_gd_developer_key'] : "";

    $client_id = isset($wpie_options['wpie_gd_client_id']) ? $wpie_options['wpie_gd_client_id'] : "";
}
?>
<div class="wpie_element_full_wrapper">
    <div class="wpie_element_title">
        <?php esc_html_e('API Key', 'vj-wp-import-export'); ?>
        <div class="wpie_import_title_hint">
            <a class="wpie_import_title_hint_link" target="_blank" href="https://developers.google.com/picker/docs/"><i class="wpie_support_icon far fa-question-circle"></i><?php esc_html_e('Picker Doc and API key', 'vj-wp-import-export'); ?></a>
        </div>
    </div>
    <div class="wpie_element_data">
        <input type="text" class="wpie_content_data_input wpie_content_data_rule_value" name="wpie_gd_developer_key" value="<?php echo esc_attr($developer_key); ?>"/>
    </div>
</div>
<div class="wpie_element_full_wrapper">
    <div class="wpie_element_title"><?php esc_html_e('Client Id', 'vj-wp-import-export'); ?></div>
    <div class="wpie_element_data">
        <input type="text" class="wpie_content_data_input wpie_content_data_rule_value" name="wpie_gd_client_id" value="<?php echo esc_attr($client_id); ?>"/>
    </div>
</div>
