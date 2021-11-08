<?php
if (!defined('ABSPATH')) {
    die(__("Can't load this file directly", 'vj-wp-import-export'));
}

$wpml = new SitePress();

$wpie_langs = $wpml->get_active_languages();

$random = uniqid();
?>
<div class="wpie_section_wrapper wpie_hide_if_comment wpie_hide_if_shop_coupon wpie_hide_if_users">
    <div class="wpie_content_data_header">
        <div class="wpie_content_title"><?php esc_html_e('WPML', 'vj-wp-import-export'); ?></div>
        <div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div>
    </div>
    <div class="wpie_section_content">
        <div class="wpie_content_data_wrapper">
            <div class="wpie_options_data_title"><?php esc_html_e('Language', 'vj-wp-import-export'); ?></div>
            <div class="wpie_options_data_content">
                <div class="wpie_wpml_lang_wrapper">
                    <input type="radio" class="wpie_radio wpie_wpml_lang wpie_wpml_lang_all" checked="checked" id="<?php echo esc_attr($random); ?>_wpml_lang_all" name="wpie_wpml_lang" value="all" />
                    <label for="<?php echo esc_attr($random); ?>_wpml_lang_all" class="wpie_radio_label wpie_wpml_lang_lbl"><?php esc_html_e('All', 'vj-wp-import-export'); ?></label>
                </div>
                <?php if (!empty($wpie_langs)) { ?>
                    <?php foreach ($wpie_langs as $code => $langInfo) { ?>
                        <div class="wpie_wpml_lang_wrapper">
                            <input type="radio" class="wpie_radio wpie_wpml_lang wpie_wpml_lang_<?php echo esc_attr($code); ?>" id="<?php echo esc_attr($random . '_wpml_lang_' . $code); ?>" name="wpie_wpml_lang" value="<?php echo esc_attr($code); ?>" />
                            <label for="<?php echo esc_attr($random); ?>_wpml_lang_<?php echo esc_attr($code); ?>" class="wpie_radio_label wpie_wpml_lang_lbl"><img class="wpie_wpml_lang_flag_img" src="<?php echo esc_url($wpml->get_flag_url($code)); ?>" /><?php echo esc_html($langInfo['display_name']); ?></label>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>