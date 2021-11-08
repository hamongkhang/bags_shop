<?php
if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'vj-wp-import-export'));
}
if (!function_exists("wpie_import_get_wpml_tab")) {

    function wpie_import_get_wpml_tab($sections = array(), $wpie_import_type = "") {

        $wpml = new SitePress();

        $wpie_langs = $wpml->get_active_languages();

        $random = uniqid();

        ob_start();
        ?>
        <div class="wpie_field_mapping_container_wrapper">
            <div class="wpie_field_mapping_container_title"><?php esc_html_e('WPML', 'vj-wp-import-export'); ?><div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div></div>
            <div class="wpie_field_mapping_container_data">
                <div class="wpie_field_mapping_container_element">
                    <div class="wpie_field_mapping_inner_title"><?php esc_html_e('Content Language', 'vj-wp-import-export'); ?></div>
                    <?php if (!empty($wpie_langs)) { ?>
                        <?php foreach ($wpie_langs as $code => $langInfo) { ?>
                            <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_wpml_lang_code wpie_wpml_lang_code_<?php echo esc_attr($code); ?>" checked="checked" name="wpie_wpml_lang_code" id="<?php echo esc_attr($random . '_wpml_lang_' . $code); ?>" value="<?php echo esc_attr($code); ?>"/>
                                <label for="<?php echo esc_attr($random . '_wpml_lang_' . $code); ?>" class="wpie_radio_label"><img class="wpie_wpml_lang_flag_img" src="<?php echo esc_url($wpml->get_flag_url($code)); ?>" /><?php echo esc_html($langInfo['display_name']); ?></label>
                            </div>
                        <?php } ?>
                    <?php } ?>
                    <div class="wpie_field_mapping_other_option_wrapper">
                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_wpml_lang_code wpie_wpml_lang_code_as_specified" name="wpie_wpml_lang_code" checked="checked" id="wpie_wpml_lang_code_as_specified" value="as_specified"/>
                        <label for="wpie_wpml_lang_code_as_specified" class="wpie_radio_label"><?php esc_html_e('As specified', 'vj-wp-import-export'); ?></label>
                        <div class="wpie_radio_container"><input type="text" class="wpie_content_data_input wpie_item_wpml_lang" name="wpie_item_wpml_lang" value=""/></div>
                    </div>
                </div>
                <div class="wpie_field_mapping_container_element">
                    <div class="wpie_field_mapping_inner_title"><?php esc_html_e('Search original language Translation Based On', 'vj-wp-import-export'); ?></div>
                    <?php if ($wpie_import_type == "taxonomies") { ?>
                        <div class="wpie_field_mapping_other_option_wrapper">
                            <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_wpml_default_item wpie_item_wpml_default_item_name" name="wpie_item_wpml_default_item" checked="checked" id="wpie_item_wpml_default_item_name" value="name"/>
                            <label for="wpie_item_wpml_default_item_name" class="wpie_radio_label"><?php esc_html_e('original language Name', 'vj-wp-import-export'); ?></label>
                            <div class="wpie_radio_container"><input type="text" class="wpie_content_data_input wpie_item_wpml_default_item_name" name="wpie_item_wpml_default_item_name" value=""/></div>
                        </div>
                        <div class="wpie_field_mapping_other_option_wrapper">
                            <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_wpml_default_item wpie_item_wpml_default_item_slug" name="wpie_item_wpml_default_item" checked="checked" id="wpie_item_wpml_default_item_slug" value="slug"/>
                            <label for="wpie_item_wpml_default_item_slug" class="wpie_radio_label"><?php esc_html_e('original language Slug', 'vj-wp-import-export'); ?></label>
                            <div class="wpie_radio_container"><input type="text" class="wpie_content_data_input wpie_item_wpml_translation_slug" name="wpie_item_wpml_translation_slug" value=""/></div>
                        </div>
                    <?php } else { ?>
                        <div class="wpie_field_mapping_other_option_wrapper">
                            <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_wpml_default_item wpie_item_wpml_default_item_title" name="wpie_item_wpml_default_item" checked="checked" id="wpie_item_wpml_default_item_title" value="title"/>
                            <label for="wpie_item_wpml_default_item_title" class="wpie_radio_label"><?php esc_html_e('original language Title', 'vj-wp-import-export'); ?></label>
                            <div class="wpie_radio_container"><input type="text" class="wpie_content_data_input wpie_item_wpml_translation_title" name="wpie_item_wpml_translation_title" value=""/></div>
                        </div>
                    <?php } ?>
                    <div class="wpie_field_mapping_other_option_wrapper">
                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_wpml_default_item wpie_item_wpml_default_item_id" name="wpie_item_wpml_default_item" id="wpie_item_wpml_default_item_id" value="id"/>
                        <label for="wpie_item_wpml_default_item_id" class="wpie_radio_label"><?php esc_html_e('original language ID', 'vj-wp-import-export'); ?></label>
                        <div class="wpie_radio_container"><input type="text" class="wpie_content_data_input wpie_item_wpml_trid" name="wpie_item_wpml_trid" value=""/></div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $wpml_html = ob_get_clean();

        $wpml_section = array(
            '241' => $wpml_html,
        );

        $sections = array_replace($sections, $wpml_section);

        unset($wpml, $wpie_langs, $random, $wpml_section, $wpml_html);

        return $sections;
    }

}