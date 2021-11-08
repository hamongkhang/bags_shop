<?php
if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}
if ( !function_exists( "wpie_import_get_polylang_tab" ) ) {

        function wpie_import_get_polylang_tab( $sections = array(), $wpie_import_type = "" ) {

                if ( (!function_exists( "PLL" ) ) || (!isset( PLL()->model )) ) {
                        return $sections;
                }
                $wpie_langs = PLL()->model->get_languages_list();

                $random = uniqid();

                ob_start();

                ?>
                <div class="wpie_field_mapping_container_wrapper">
                    <div class="wpie_field_mapping_container_title"><?php esc_html_e( 'Polylang', 'vj-wp-import-export' ); ?><div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div></div>
                    <div class="wpie_field_mapping_container_data">
                        <div class="wpie_field_mapping_container_element">
                            <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Content Language / Import Language', 'vj-wp-import-export' ); ?></div>
                            <?php if ( !empty( $wpie_langs ) ) { ?>
                                    <?php foreach ( $wpie_langs as $language ) { ?>
                                            <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="radio" class="wpie_radio wpie_polylang_lang_code wpie_polylang_lang_code_<?php echo esc_attr( $language->slug ); ?>" checked="checked" name="wpie_polylang_lang_code" id="<?php echo esc_attr( $random . '_polylang_lang_' . $language->slug ); ?>" value="<?php echo esc_attr( $language->slug ); ?>"/>
                                                <label for="<?php echo esc_attr( $random . '_polylang_lang_' . $language->slug ); ?>" class="wpie_radio_label"><img class="wpie_polylang_lang_flag_img" src="<?php echo esc_url( $language->flag_url ); ?>" /><?php echo esc_html( $language->name ); ?></label>
                                            </div>
                                    <?php } ?>
                            <?php } ?>
                            <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_polylang_lang_code wpie_polylang_lang_code_as_specified" name="wpie_polylang_lang_code" checked="checked" id="wpie_polylang_lang_code_as_specified" value="as_specified"/>
                                <label for="wpie_polylang_lang_code_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                <div class="wpie_radio_container"><input type="text" class="wpie_content_data_input wpie_item_polylang_lang" name="wpie_item_polylang_lang" value=""/></div>
                            </div>
                        </div>
                        <div class="wpie_field_mapping_container_element">
                            <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Search original language Translation Based On ( Connection for other Languages )', 'vj-wp-import-export' ); ?></div>
                            <?php if ( $wpie_import_type == "taxonomies" ) { ?>
                                    <div class="wpie_field_mapping_other_option_wrapper">
                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_polylang_default_item wpie_item_polylang_default_item_name" name="wpie_item_polylang_default_item"  id="wpie_item_polylang_default_item_name" value="name" checked="checked"/>
                                        <label for="wpie_item_polylang_default_item_name" class="wpie_radio_label"><?php esc_html_e( 'original language Name', 'vj-wp-import-export' ); ?></label>
                                        <div class="wpie_radio_container"><input type="text" class="wpie_content_data_input wpie_item_polylang_default_item_name_data" name="wpie_item_polylang_default_item_name_data" value=""/></div>
                                    </div>
                                    <div class="wpie_field_mapping_other_option_wrapper">
                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_polylang_default_item wpie_item_polylang_default_item_slug" name="wpie_item_polylang_default_item" checked="checked" id="wpie_item_polylang_default_item_slug" value="slug"/>
                                        <label for="wpie_item_polylang_default_item_slug" class="wpie_radio_label"><?php esc_html_e( 'original language Slug', 'vj-wp-import-export' ); ?></label>
                                        <div class="wpie_radio_container"><input type="text" class="wpie_content_data_input wpie_item_polylang_translation_slug_data" name="wpie_item_polylang_translation_slug_data" value=""/></div>
                                    </div>
                            <?php } else { ?>
                                    <div class="wpie_field_mapping_other_option_wrapper">
                                            <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_polylang_default_item wpie_item_polylang_default_item_title" name="wpie_item_polylang_default_item" id="wpie_item_polylang_default_item_title" value="title" checked="checked"/>
                                        <label for="wpie_item_polylang_default_item_title" class="wpie_radio_label"><?php esc_html_e( 'original language Title', 'vj-wp-import-export' ); ?></label>
                                        <div class="wpie_radio_container"><input type="text" class="wpie_content_data_input wpie_item_polylang_translation_title_data" name="wpie_item_polylang_translation_title_data" value=""/></div>
                                    </div>
                            <?php } ?>
                            <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_polylang_default_item wpie_item_polylang_default_item_id" name="wpie_item_polylang_default_item" id="wpie_item_polylang_default_item_id" value="id"/>
                                <label for="wpie_item_polylang_default_item_id" class="wpie_radio_label"><?php esc_html_e( 'original language ID', 'vj-wp-import-export' ); ?></label>
                                <div class="wpie_radio_container"><input type="text" class="wpie_content_data_input wpie_item_polylang_trid" name="wpie_item_polylang_trid" value=""/></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                $html = ob_get_clean();

                $polylang_section = [ '244' => $html ];

                $sections = array_replace( $sections, $polylang_section );

                unset( $wpie_langs, $random, $polylang_section, $html );

                return $sections;
        }

}