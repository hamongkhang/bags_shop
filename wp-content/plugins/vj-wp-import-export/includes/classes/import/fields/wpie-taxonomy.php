<?php
if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

add_filter( 'wpie_import_mapping_fields', "wpie_import_taxonomy_mapping_fields", 10, 2 );

if ( !function_exists( "wpie_import_taxonomy_mapping_fields" ) ) {

        function wpie_import_taxonomy_mapping_fields( $sections = array(), $wpie_import_type = "" ) {

                global $wp_version;

                $uniqid = uniqid();

                $wpie_import_type_title = ucfirst( $wpie_import_type );

                ob_start();

                ?>
                <div class="wpie_field_mapping_container_wrapper">
                        <div class="wpie_field_mapping_container_title wpie_active"><?php esc_html_e( 'Name & Description', 'vj-wp-import-export' ); ?><div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div></div>
                        <div class="wpie_field_mapping_container_data wpie_show">                               
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Name', 'vj-wp-import-export' ); ?> *</div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_term_name" name="wpie_item_term_name" placeholder="<?php esc_html_e( 'Name', 'vj-wp-import-export' ); ?>"/>
                                                <div class="wpie_required_field_notice"><?php esc_html_e( 'Note : Name is required field for new items. Optional for update items', 'vj-wp-import-export' ); ?></div>
                                        </div>
                                </div>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Description', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <textarea class="wpie_content_data_textarea wpie_item_term_description" name="wpie_item_term_description" placeholder="<?php esc_html_e( 'Description', 'vj-wp-import-export' ); ?>"></textarea>
                                        </div>
                                </div>
                        </div>
                </div>
                <?php
                $name_and_desc = ob_get_clean();

                ob_start();

                ?>
                <div class="wpie_field_mapping_container_wrapper">
                        <div class="wpie_field_mapping_container_title"><?php esc_html_e( 'Images', 'vj-wp-import-export' ); ?><div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div></div>
                        <div class="wpie_field_mapping_container_data">
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_image_download_options">
                                                <div class="wpie_field_mapping_radio_input_wrapper wpie_radio_wrapper">
                                                        <input type="radio" class="wpie_radio wpie_item_image_option" checked="checked" name="wpie_item_image_option" id="wpie_download_images" value="download_images"/>
                                                        <label for="wpie_download_images" class="wpie_radio_label"><?php esc_html_e( 'Download images hosted elsewhere', 'vj-wp-import-export' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Image URL with http:// or https://", "vj-wp-import-export" ); ?>"></i></label>
                                                        <div class="wpie_radio_container">
                                                                <div class="wpie_field_mapping_image_separator_wrapper">
                                                                        <div class="wpie_field_mapping_image_separator"><?php esc_html_e( 'Enter image filenames one per line, or separate them with a', 'vj-wp-import-export' ); ?></div>
                                                                        <input type="text" class="wpie_content_data_input wpie_item_delim wpie_item_image_url_delim" name="wpie_item_image_url_delim"  value="||"/>
                                                                </div>
                                                                <textarea class="wpie_content_data_textarea wpie_item_image_url" name="wpie_item_image_url" placeholder="<?php esc_attr_e( 'URL', 'vj-wp-import-export' ); ?>"></textarea>
                                                        </div>
                                                </div>
                                                <div class="wpie_field_mapping_radio_input_wrapper">
                                                        <input type="radio" class="wpie_radio wpie_item_image_option " name="wpie_item_image_option" id="wpie_media_library" value="media_library"/>
                                                        <label for="wpie_media_library" class="wpie_radio_label"><?php esc_html_e( 'Use images currently in Media Library', 'vj-wp-import-export' ); ?></label>
                                                        <div class="wpie_radio_container">
                                                                <div class="wpie_field_mapping_image_separator_wrapper">
                                                                        <div class="wpie_field_mapping_image_separator"><?php esc_html_e( 'Enter image filenames one per line, or separate them with a', 'vj-wp-import-export' ); ?></div>
                                                                        <input type="text" class="wpie_content_data_input wpie_item_image_media_library_delim wpie_item_delim" name="wpie_item_image_media_library_delim" value="|"/>
                                                                </div>
                                                                <textarea class="wpie_content_data_textarea wpie_item_image_media_library" name="wpie_item_image_media_library" placeholder="<?php esc_attr_e( 'Images.jpg', 'vj-wp-import-export' ); ?>"></textarea>
                                                        </div>
                                                </div>
                                                <div class="wpie_field_mapping_radio_input_wrapper">
                                                        <input type="radio" class="wpie_radio wpie_item_image_option " name="wpie_item_image_option" id="wpie_local_images" value="local_images"/>
                                                        <label for="wpie_local_images" class="wpie_radio_label"><?php echo esc_html( __( 'Use images currently uploaded in', 'vj-wp-import-export' ) . " " . WPIE_UPLOAD_TEMP_DIR ); ?> </label>
                                                        <div class="wpie_radio_container">
                                                                <div class="wpie_field_mapping_image_separator_wrapper">
                                                                        <div class="wpie_field_mapping_image_separator"><?php esc_html_e( 'Enter image filenames one per line, or separate them with a', 'vj-wp-import-export' ); ?></div>
                                                                        <input type="text" class="wpie_content_data_input wpie_item_image_local_delim wpie_item_delim" name="wpie_item_image_local_delim"  value="|"/>
                                                                </div>
                                                                <textarea class="wpie_content_data_textarea wpie_item_image_local" name="wpie_item_image_local" placeholder="<?php esc_attr_e( 'Images.jpg', 'vj-wp-import-export' ); ?>"></textarea>
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Image Options', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_image_option_wrapper">
                                                <div class="wpie_field_mapping_radio_input_wrapper wpie_image_media_option_data">
                                                        <input type="checkbox" id="wpie_item_search_existing_images" name="wpie_item_search_existing_images" checked="checked" value="1" class="wpie_checkbox wpie_search_existing_images wpie_item_search_existing_images">
                                                        <label class="wpie_checkbox_label" for="wpie_item_search_existing_images"><?php esc_html_e( 'Search through the Media Library for existing images before importing new images', 'vj-wp-import-export' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "If an image with the same file name or remote URL is found in the Media Library then that image will be attached to this record instead of importing a new image. Disable this setting if you always want to download a new image.", "vj-wp-import-export" ); ?>"></i></label>
                                                </div>
                                                <div class="wpie_field_mapping_radio_input_wrapper wpie_image_media_option_data">
                                                        <input type="checkbox" id="wpie_item_keep_images" name="wpie_item_keep_images" checked="checked" value="1" class="wpie_checkbox wpie_item_keep_images">
                                                        <label class="wpie_checkbox_label" for="wpie_item_keep_images"><?php esc_html_e( 'Keep images currently in Media Library', 'vj-wp-import-export' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "If disabled, images attached to imported posts will be deleted and then all images will be imported.", "vj-wp-import-export" ); ?>"></i></label>
                                                </div> 
                                                <div class="wpie_field_mapping_radio_input_wrapper">
                                                        <input type="checkbox"  id="wpie_item_first_imaege_is_featured" name="wpie_item_first_imaege_is_featured" checked="checked" value="1" class="wpie_checkbox wpie_item_first_imaege_is_featured">
                                                        <label class="wpie_checkbox_label" for="wpie_item_first_imaege_is_featured"><?php esc_html_e( 'Set the first image to the Featured Image (_thumbnail_id)', 'vj-wp-import-export' ); ?></label>
                                                </div>
                                                <div class="wpie_field_mapping_radio_input_wrapper">
                                                        <input type="checkbox" id="wpie_item_unsuccess_set_draft" value="1" name="wpie_item_unsuccess_set_draft" class="wpie_checkbox wpie_item_unsuccess_set_draft">
                                                        <label class="wpie_checkbox_label" for="wpie_item_unsuccess_set_draft"><?php esc_html_e( 'If no images are downloaded successfully, create entry as Draft.', 'vj-wp-import-export' ); ?></label>
                                                </div>
                                        </div>
                                        <div class="wpie_field_advanced_option_wrapper">
                                                <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Media SEO & Advanced Options', 'vj-wp-import-export' ); ?></div>
                                                <div class="wpie_field_advanced_option_container">
                                                        <div class="wpie_field_advanced_option_data_lbl"><?php esc_html_e( 'Meta Data', 'vj-wp-import-export' ); ?></div>
                                                        <div class="wpie_field_advanced_option_data_container">
                                                                <div class="wpie_field_mapping_radio_input_wrapper">
                                                                        <input type="checkbox" class="wpie_update_data_inner_option wpie_checkbox wpie_item_set_image_title" name="wpie_item_set_image_title" id="wpie_item_set_image_title" value="1"/>
                                                                        <label for="wpie_item_set_image_title" class="wpie_checkbox_label"><?php esc_html_e( 'Set Title(s)', 'vj-wp-import-export' ); ?></label>
                                                                        <div class="wpie_checkbox_container">
                                                                                <div class="wpie_field_mapping_container_element" >
                                                                                        <div class="wpie_field_mapping_image_separator"><?php esc_html_e( 'Enter one per line, or separate them with a', 'vj-wp-import-export' ); ?></div>
                                                                                        <input type="text" class="wpie_content_data_input wpie_item_set_image_title_delim wpie_item_delim" name="wpie_item_set_image_title_delim" value="||"/>
                                                                                </div>
                                                                                <div class="wpie_import_image_seo_hint"><?php esc_html_e( 'The first title will be linked to the first image, the second title will be linked to the second image, ...', 'vj-wp-import-export' ); ?></div>
                                                                                <div class="wpie_field_mapping_container_element">
                                                                                        <textarea class="wpie_content_data_textarea wpie_item_image_title" name="wpie_item_image_title"></textarea>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_field_mapping_radio_input_wrapper">
                                                                        <input type="checkbox" class="wpie_update_data_inner_option wpie_checkbox wpie_item_set_image_caption" name="wpie_item_set_image_caption" id="wpie_item_set_image_caption" value="1"/>
                                                                        <label for="wpie_item_set_image_caption" class="wpie_checkbox_label"><?php esc_html_e( 'Set Caption(s)', 'vj-wp-import-export' ); ?></label>
                                                                        <div class="wpie_checkbox_container">
                                                                                <div class="wpie_field_mapping_container_element" >
                                                                                        <div class="wpie_field_mapping_image_separator"><?php esc_html_e( 'Enter one per line, or separate them with a', 'vj-wp-import-export' ); ?></div>
                                                                                        <input type="text" class="wpie_content_data_input wpie_item_set_image_caption_delim wpie_item_delim" name="wpie_item_set_image_caption_delim" value="||"/>
                                                                                </div>
                                                                                <div class="wpie_import_image_seo_hint"><?php esc_html_e( 'The first caption will be linked to the first image, the second caption will be linked to the second image, ...', 'vj-wp-import-export' ); ?></div>
                                                                                <div class="wpie_field_mapping_container_element">
                                                                                        <textarea class="wpie_content_data_textarea  wpie_item_image_caption" name="wpie_item_image_caption" ></textarea>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_field_mapping_radio_input_wrapper">
                                                                        <input type="checkbox" class="wpie_update_data_inner_option wpie_checkbox wpie_item_set_image_alt" name="wpie_item_set_image_alt" id="wpie_item_set_image_alt" value="1"/>
                                                                        <label for="wpie_item_set_image_alt" class="wpie_checkbox_label"><?php esc_html_e( 'Set Alt Text(s)', 'vj-wp-import-export' ); ?></label>
                                                                        <div class="wpie_checkbox_container">
                                                                                <div class="wpie_field_mapping_container_element" >
                                                                                        <div class="wpie_field_mapping_image_separator"><?php esc_html_e( 'Enter one per line, or separate them with a', 'vj-wp-import-export' ); ?></div>
                                                                                        <input type="text" class="wpie_content_data_input wpie_item_set_image_alt_delim wpie_item_delim" name="wpie_item_set_image_alt_delim"  value="||"/>
                                                                                </div>
                                                                                <div class="wpie_import_image_seo_hint"><?php esc_html_e( 'The first alt text will be linked to the first image, the second alt text will be linked to the second image, ...', 'vj-wp-import-export' ); ?></div>
                                                                                <div class="wpie_field_mapping_container_element">
                                                                                        <textarea class="wpie_content_data_textarea wpie_item_image_alt" name="wpie_item_image_alt"></textarea>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_field_mapping_radio_input_wrapper">
                                                                        <input type="checkbox" class="wpie_update_data_inner_option wpie_checkbox wpie_item_set_image_description" name="wpie_item_set_image_description" id="wpie_item_set_image_description" value="1"/>
                                                                        <label for="wpie_item_set_image_description" class="wpie_checkbox_label"><?php esc_html_e( 'Set Description(s)', 'vj-wp-import-export' ); ?></label>
                                                                        <div class="wpie_checkbox_container">
                                                                                <div class="wpie_field_mapping_container_element" >
                                                                                        <div class="wpie_field_mapping_image_separator"><?php esc_html_e( 'Enter one per line, or separate them with a', 'vj-wp-import-export' ); ?></div>
                                                                                        <input type="text" class="wpie_content_data_input wpie_item_set_image_description_delim wpie_item_delim" name="wpie_item_set_image_description_delim" value="||"/>
                                                                                </div>
                                                                                <div class="wpie_import_image_seo_hint"><?php esc_html_e( 'The first description will be linked to the first image, the second description will be linked to the second image, ...', 'vj-wp-import-export' ); ?></div>
                                                                                <div class="wpie_field_mapping_container_element">
                                                                                        <textarea class="wpie_content_data_textarea  wpie_item_image_description" name="wpie_item_image_description" ></textarea>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                </div>
                                                <div class="wpie_field_advanced_option_container">
                                                        <div class="wpie_field_advanced_option_data_lbl"><?php esc_html_e( 'Files', 'vj-wp-import-export' ); ?></div>
                                                        <div class="wpie_field_advanced_option_data_container">
                                                                <div class="wpie_field_mapping_radio_input_wrapper">
                                                                        <input type="checkbox" class="wpie_update_data_inner_option wpie_checkbox wpie_item_image_rename" name="wpie_item_image_rename" id="wpie_item_image_rename" value="1"/>
                                                                        <label for="wpie_item_image_rename" class="wpie_checkbox_label"><?php esc_html_e( 'Change image file names to', 'vj-wp-import-export' ); ?></label>
                                                                        <div class="wpie_checkbox_container">
                                                                                <div class="wpie_field_mapping_container_element" >
                                                                                        <div class="wpie_field_mapping_image_separator"><?php esc_html_e( 'Enter one per line, or separate them with a', 'vj-wp-import-export' ); ?></div>
                                                                                        <input type="text" class="wpie_content_data_input wpie_item_set_image_rename_delim wpie_item_delim" name="wpie_item_set_image_rename_delim" value="||"/>
                                                                                </div>
                                                                                <div class="wpie_field_mapping_container_element">
                                                                                        <textarea class="wpie_content_data_textarea  wpie_item_image_new_name" name="wpie_item_image_new_name" ></textarea>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_field_mapping_radio_input_wrapper">
                                                                        <input type="checkbox" class="wpie_update_data_inner_option wpie_checkbox wpie_item_change_ext" name="wpie_item_change_ext" id="wpie_item_change_ext" value="1"/>
                                                                        <label for="wpie_item_change_ext" class="wpie_checkbox_label"><?php esc_html_e( 'Change image file extensions', 'vj-wp-import-export' ); ?></label>
                                                                        <div class="wpie_checkbox_container">
                                                                                <div class="wpie_field_mapping_container_element" >
                                                                                        <div class="wpie_field_mapping_image_separator"><?php esc_html_e( 'Enter one per line, or separate them with a', 'vj-wp-import-export' ); ?></div>
                                                                                        <input type="text" class="wpie_content_data_input wpie_item_set_image_new_ext_delim wpie_item_delim" name="wpie_item_set_image_new_ext_delim" value="||"/>
                                                                                </div>
                                                                                <div class="wpie_field_mapping_container_element">
                                                                                        <textarea class="wpie_content_data_textarea wpie_item_new_ext" name="wpie_item_new_ext" ></textarea>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                </div>
                <?php
                $image_section = ob_get_clean();

                ob_start();

                ?>
                <div class="wpie_field_mapping_container_wrapper">
                        <div class="wpie_field_mapping_container_title"><?php esc_html_e( 'Term Meta', 'vj-wp-import-export' ); ?><div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div></div>
                        <div class="wpie_field_mapping_container_data">
                                <div class="wpie_cf_wrapper">
                                        <div class="wpie_field_mapping_radio_input_wrapper wpie_cf_notice_wrapper">
                                                <input type="checkbox" id="wpie_item_not_add_empty" name="wpie_item_not_add_empty" checked="checked" value="1" class="wpie_checkbox wpie_item_not_add_empty">
                                                <label class="wpie_checkbox_label" for="wpie_item_not_add_empty"><?php esc_html_e( 'Do not add empty value fields in database', 'vj-wp-import-export' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "If custom field value is empty then it skip perticular field and not add to database", "vj-wp-import-export" ); ?>"></i></label>
                                        </div>
                                        <table class="wpie_cf_table">
                                                <thead>
                                                        <tr>
                                                                <th><?php esc_html_e( 'Name', 'vj-wp-import-export' ); ?></th>
                                                                <th><?php esc_html_e( 'Value', 'vj-wp-import-export' ); ?></th>
                                                                <th><?php esc_html_e( 'Options', 'vj-wp-import-export' ); ?></th>
                                                                <th></th>
                                                        </tr>
                                                </thead>
                                                <tbody class="wpie_cf_option_outer_wrapper">
                                                        <tr class="wpie_cf_option_wrapper wpie_data_row" wpie_row_id="<?php echo esc_attr( $uniqid ); ?>">
                                                                <td class="wpie_item_cf_name_wrapper">
                                                                        <input type="text" class="wpie_content_data_input wpie_item_cf_name" value="" name="wpie_item_cf[<?php echo esc_attr( $uniqid ); ?>][name]"/>
                                                                </td>
                                                                <td class="wpie_item_cf_value_wrapper">
                                                                        <div class="wpie_cf_normal_data">
                                                                                <input type="text" class="wpie_content_data_input wpie_item_cf_value" value="" name="wpie_item_cf[<?php echo esc_attr( $uniqid ); ?>][value]"/>
                                                                        </div>
                                                                        <div class="wpie_btn wpie_btn_primary wpie_cf_serialized_data_btn">
                                                                                <i class="fas fa-hand-point-up wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Click to specify', 'vj-wp-import-export' ); ?>
                                                                        </div>
                                                                        <div class="wpie_cf_child_data"></div>
                                                                </td>
                                                                <td class="wpie_item_cf_option_wrapper">
                                                                        <select class="wpie_content_data_select wpie_item_cf_option" name="wpie_item_cf[<?php echo esc_attr( $uniqid ); ?>][option]" >
                                                                                <option value="normal"><?php esc_html_e( 'Normal Data', 'vj-wp-import-export' ); ?></option>
                                                                                <option value="serialized"><?php esc_html_e( 'Serialized Data', 'vj-wp-import-export' ); ?></option>
                                                                        </select>
                                                                </td>
                                                                <td>
                                                                        <div class="wpie_remove_cf_btn"><i class="fas fa-trash wpie_trash_general_btn_icon " aria-hidden="true"></i></div>
                                                                </td>
                                                        </tr>
                                                </tbody>
                                                <tfoot>
                                                        <tr>
                                                                <th colspan="4">
                                                                        <div class="wpie_btn wpie_btn_primary wpie_cf_add_btn">
                                                                                <i class="fas fa-plus wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Add New', 'vj-wp-import-export' ); ?>
                                                                        </div> 
                                                                        <div class="wpie_btn wpie_btn_primary wpie_cf_close_btn">
                                                                                <i class="fas fa-times wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Close', 'vj-wp-import-export' ); ?>
                                                                        </div> 
                                                                </th>
                                                </tfoot>
                                        </table>
                                </div>
                        </div>
                </div>

                <?php
                $term_meta = ob_get_clean();

                ob_start();

                ?>
                <div class="wpie_field_mapping_container_wrapper">
                        <div class="wpie_field_mapping_container_title"><?php esc_html_e( 'Other Category Options', 'vj-wp-import-export' ); ?><div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div></div>
                        <div class="wpie_field_mapping_container_data  wpie_field_mapping_other_option_outer_wrapper">

                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Parent Term', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper wpie_as_specified_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_term_parent" name="wpie_item_term_parent" value=""/>
                                                <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "If your taxonomies have parent/child relationships, use this field to set the parent for the imported taxonomy term. Terms can be matched by slug, name, or ID.", "vj-wp-import-export" ); ?>"></i>
                                        </div>
                                </div>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Category Slug', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="radio" class="wpie_radio wpie_item_term_slug wpie_item_term_slug_auto" checked="checked" name="wpie_item_term_slug" id="wpie_item_slug_auto" value="auto"/>
                                                <label for="wpie_item_slug_auto" class="wpie_radio_label"><?php esc_html_e( 'Set slug automatically', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper ">
                                                <input type="radio" class="wpie_radio wpie_item_term_slug wpie_item_term_slug_as_specified" name="wpie_item_term_slug" id="wpie_item_slug_as_specified" value="as_specified"/>
                                                <label for="wpie_item_slug_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                        <input type="text" class="wpie_content_data_input wpie_item_term_slug_as_specified_data" name="wpie_item_term_slug_as_specified_data" value=""/>
                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "The term slug must be unique.", "vj-wp-import-export" ); ?>"></i>
                                                </div>
                                        </div>
                                </div>
                        </div>
                </div>
                <?php
                $other_section = ob_get_clean();

                $sections = array_replace( $sections, array(
                        '100' => $name_and_desc,
                        '200' => $image_section,
                        '300' => $term_meta,
                        '400' => $other_section
                        )
                );

                unset( $wpie_import_type_title, $name_and_desc, $image_section, $term_meta, $other_section );

                return apply_filters( "wpie_pre_term_field_mapping_section", $sections, $wpie_import_type );
        }

}

add_filter( 'wpie_import_search_existing_item', "wpie_import_taxonomy_search_existing_item", 10, 2 );

if ( !function_exists( "wpie_import_taxonomy_search_existing_item" ) ) {

        function wpie_import_taxonomy_search_existing_item( $sections = "", $wpie_import_type = "" ) {

                ob_start();

                ?>
                <div class="wpie_field_mapping_container_element">
                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Search Existing Item on your site based on...', 'vj-wp-import-export' ); ?></div>
                        <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_existing_item_search_logic wpie_existing_item_search_logic_name" checked="checked" name="wpie_existing_item_search_logic" id="wpie_existing_item_search_logic_name" value="name"/>
                                <label for="wpie_existing_item_search_logic_name" class="wpie_radio_label"><?php esc_html_e( 'Name', 'vj-wp-import-export' ); ?></label>
                        </div>
                        <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_existing_item_search_logic wpie_existing_item_search_logic_slug"  name="wpie_existing_item_search_logic" id="wpie_existing_item_search_logic_slug" value="slug"/>
                                <label for="wpie_existing_item_search_logic_slug" class="wpie_radio_label"><?php esc_html_e( 'Slug', 'vj-wp-import-export' ); ?></label>
                        </div>
                        <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_existing_item_search_logic wpie_existing_item_search_logic_cf"  name="wpie_existing_item_search_logic" id="wpie_existing_item_search_logic_cf" value="cf"/>
                                <label for="wpie_existing_item_search_logic_cf" class="wpie_radio_label"><?php esc_html_e( 'Custom field', 'vj-wp-import-export' ); ?></label>
                                <div class="wpie_radio_container">
                                        <table class="wpie_search_based_on_cf_table">
                                                <thead>
                                                        <tr>
                                                                <th><?php esc_html_e( 'Name', 'vj-wp-import-export' ); ?></th>
                                                                <th><?php esc_html_e( 'Value', 'vj-wp-import-export' ); ?></th>
                                                        </tr>
                                                </thead>
                                                <tbody>
                                                        <tr>
                                                                <td><input type="text" class="wpie_content_data_input wpie_existing_item_search_logic_cf_key" name="wpie_existing_item_search_logic_cf_key" value=""/></td>
                                                                <td><input type="text" class="wpie_content_data_input wpie_existing_item_search_logic_cf_value" name="wpie_existing_item_search_logic_cf_value" value=""/></td>
                                                        </tr>
                                                </tbody>
                                        </table>
                                </div>
                        </div>
                        <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_existing_item_search_logic"  name="wpie_existing_item_search_logic" id="wpie_existing_item_search_logic_id" value="id"/>
                                <label for="wpie_existing_item_search_logic_id" class="wpie_radio_label"><?php esc_html_e( 'Term ID', 'vj-wp-import-export' ); ?></label>
                                <div class="wpie_radio_container"><input type="text" class="wpie_content_data_input wpie_existing_item_search_logic_id" name="wpie_existing_item_search_logic_id" value=""/></div>
                        </div>
                </div>
                <?php
                return ob_get_clean();
        }

}

add_filter( 'wpie_import_update_existing_item_fields', "wpie_import_taxonomy_update_existing_item_fields", 10, 2 );

if ( !function_exists( "wpie_import_taxonomy_update_existing_item_fields" ) ) {

        function wpie_import_taxonomy_update_existing_item_fields( $sections = "", $wpie_import_type = "" ) {

                $is_yoast_added = apply_filters( 'wpie_import_yoast_addon', false );

                ob_start();

                ?>
                <div class="wpie_field_mapping_container_element">
                        <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_item_update wpie_item_update_all" name="wpie_item_update" id="wpie_item_update_all" value="all"/>
                                <label for="wpie_item_update_all" class="wpie_radio_label"><?php esc_html_e( 'Update all data', 'vj-wp-import-export' ); ?></label>
                        </div>
                        <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_item_update wpie_item_update_specific" name="wpie_item_update" id="wpie_item_update_specific" value="specific"  checked="checked"/>
                                <label for="wpie_item_update_specific" class="wpie_radio_label"><?php esc_html_e( 'Choose which data to update', 'vj-wp-import-export' ); ?></label>
                                <div class="wpie_radio_container">
                                        <div class="wpie_update_item_all_action"><?php esc_html_e( 'Check/Uncheck All', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_name" name="is_update_item_name" id="is_update_item_name" value="1"/>
                                                <label for="is_update_item_name" class="wpie_checkbox_label"><?php esc_html_e( 'Name', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_description" name="is_update_item_description" id="is_update_item_description" value="1"/>
                                                <label for="is_update_item_description" class="wpie_checkbox_label"><?php esc_html_e( 'Description', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_slug" name="is_update_item_slug" id="is_update_item_slug" value="1"/>
                                                <label for="is_update_item_slug" class="wpie_checkbox_label"><?php esc_html_e( 'Slug', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_parent" name="is_update_item_parent" id="is_update_item_parent" value="1"/>
                                                <label for="is_update_item_parent" class="wpie_checkbox_label"><?php esc_html_e( 'Parent term', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_images "  name="is_update_item_images" id="is_update_item_images" value="1"/>
                                                <label for="is_update_item_images" class="wpie_checkbox_label"><?php esc_html_e( 'Images', 'vj-wp-import-export' ); ?></label>
                                                <div class="wpie_checkbox_container">
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="wpie_radio wpie_item_update_images  wpie_item_update_images_all" checked="checked" name="wpie_item_update_images" id="wpie_item_update_images_all" value="all"/>
                                                                <label for="wpie_item_update_images_all" class="wpie_radio_label"><?php esc_html_e( 'Update all images', 'vj-wp-import-export' ); ?></label>
                                                        </div>
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="wpie_radio wpie_item_update_images wpie_item_update_images_append" name="wpie_item_update_images" id="wpie_item_update_images_append" value="append"/>
                                                                <label for="wpie_item_update_images_append" class="wpie_radio_label"><?php esc_html_e( "Don't touch existing images, append new images", 'vj-wp-import-export' ); ?></label>
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_cf" name="is_update_item_cf" id="is_update_item_cf" value="1"/>
                                                <label for="is_update_item_cf" class="wpie_checkbox_label"><?php esc_html_e( 'Term Meta', 'vj-wp-import-export' ); ?></label>
                                                <div class="wpie_checkbox_container">
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="wpie_radio wpie_item_update_cf wpie_item_update_cf_append" checked="checked" name="wpie_item_update_cf" id="wpie_item_update_cf_append" value="append"/>
                                                                <label for="wpie_item_update_cf_append" class="wpie_radio_label"><?php esc_html_e( 'Update all Term Meta and keep meta if not found in file', 'vj-wp-import-export' ); ?></label>
                                                        </div>
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="wpie_radio wpie_item_update_cf wpie_item_update_cf_all" name="wpie_item_update_cf" id="wpie_item_update_cf_all" value="all"/>
                                                                <label for="wpie_item_update_cf_all" class="wpie_radio_label"><?php esc_html_e( 'Update all Term Meta and Remove meta if not found in file', 'vj-wp-import-export' ); ?></label>
                                                        </div>
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="wpie_radio wpie_item_update_cf wpie_item_update_cf_includes" name="wpie_item_update_cf" id="wpie_item_update_cf_includes" value="includes"/>
                                                                <label for="wpie_item_update_cf_includes" class="wpie_radio_label"><?php esc_html_e( "Update only these Term Meta, leave the rest alone", 'vj-wp-import-export' ); ?></label>
                                                                <div class="wpie_radio_container">
                                                                        <input type="text" class="wpie_content_data_input wpie_item_update_cf_includes_data" name="wpie_item_update_cf_includes_data" value=""/>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="wpie_radio wpie_item_update_cf wpie_item_update_cf_excludes" name="wpie_item_update_cf" id="wpie_item_update_cf_excludes" value="excludes"/>
                                                                <label for="wpie_item_update_cf_excludes" class="wpie_radio_label"><?php esc_html_e( "Leave these fields alone, update all other Term Meta", 'vj-wp-import-export' ); ?></label>
                                                                <div class="wpie_radio_container">
                                                                        <input type="text" class="wpie_content_data_input wpie_item_update_cf_excludes_data" name="wpie_item_update_cf_excludes_data" value=""/>
                                                                </div>
                                                        </div>
                                                </div>
                                        </div>
                                        <?php if ( $is_yoast_added ) { ?>
                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                        <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_yoast_seo" name="is_update_item_yoast_seo" id="is_update_item_yoast_seo" value="1"/>
                                                        <label for="is_update_item_yoast_seo" class="wpie_checkbox_label"><?php esc_html_e( 'Yoast SEO Data', 'vj-wp-import-export' ); ?></label>
                                                </div>                                                
                                        <?php } ?>
                                </div>
                        </div>
                </div>

                <?php
                return ob_get_clean();
        }

}
