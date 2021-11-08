<?php
if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

if ( !function_exists( "\pll_languages_list" ) ) {
        return $sections;
}
$wpie_langs = \pll_languages_list( [ 'fields' => '' ] );

$random = uniqid();

?>
<div class="wpie_section_wrapper wpie_polylang_section">
    <div class="wpie_content_data_header">
        <div class="wpie_content_title"><?php esc_html_e( 'Polylang', 'vj-wp-import-export' ); ?></div>
        <div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div>
    </div>
    <div class="wpie_section_content">
        <div class="wpie_content_data_wrapper">
            <div class="wpie_options_data_title"><?php esc_html_e( 'Language', 'vj-wp-import-export' ); ?></div>
            <div class="wpie_options_data_content">
                <div class="wpie_polylang_lang_wrapper">
                    <input type="radio" class="wpie_radio wpie_polylang_lang wpie_polylang_lang_all" checked="checked" id="<?php echo esc_attr( $random ); ?>_polylang_lang_all" name="wpie_polylang_lang" value="all" />
                    <label for="<?php echo esc_attr( $random ); ?>_polylang_lang_all" class="wpie_radio_label wpie_polylang_lang_lbl"><?php esc_html_e( 'All', 'vj-wp-import-export' ); ?></label>
                </div>
                <?php if ( !empty( $wpie_langs ) ) { ?>
                        <?php foreach ( $wpie_langs as $language ) { ?>
                                <div class="wpie_polylang_lang_wrapper">
                                    <input type="radio" class="wpie_radio wpie_polylang_lang wpie_polylang_lang_<?php echo esc_attr( $language->slug ); ?>" id="<?php echo esc_attr( $random . '_polylang_lang_' . $language->slug ); ?>" name="wpie_polylang_lang" value="<?php echo esc_attr( $language->slug ); ?>" />
                                    <label for="<?php echo esc_attr( $random ); ?>_polylang_lang_<?php echo esc_attr( $language->slug ); ?>" class="wpie_radio_label wpie_polylang_lang_lbl"><img class="wpie_polylang_lang_flag_img" src="<?php echo esc_url( $language->flag_url ); ?>" /><?php echo esc_html( $language->name ); ?></label>
                                </div>
                        <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>