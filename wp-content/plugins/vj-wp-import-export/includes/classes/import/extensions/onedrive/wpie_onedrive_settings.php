<?php
if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'vj-wp-import-export'));
}
$wpie_options = get_option('wpie_import_onedrive_file_upload');

$_key = "";
if (!empty($wpie_options)) {
    $wpie_options = maybe_unserialize($wpie_options);
    $_key = isset($wpie_options['wpie_onedrive_client_id']) ? $wpie_options['wpie_onedrive_client_id'] : "";
}
?>
<div class="wpie_element_full_wrapper">
    <div class="wpie_element_title">
        <?php esc_html_e('Onedrive Client Id', 'vj-wp-import-export'); ?>
         <div class="wpie_import_title_hint">
            <a class="wpie_import_title_hint_link" target="_blank" href="https://docs.microsoft.com/en-us/onedrive/developer/controls/file-pickers/js-v72/?view=odsp-graph-online"><i class="wpie_support_icon far fa-question-circle"></i><?php esc_html_e('File Picker Doc and Client Id', 'vj-wp-import-export'); ?></a>
        </div>
    </div>
    <div class="wpie_element_data">
        <input type="text" class="wpie_content_data_input wpie_content_data_rule_value" name="wpie_onedrive_client_id" value="<?php echo esc_attr($_key); ?>"/>
    </div>
</div>
