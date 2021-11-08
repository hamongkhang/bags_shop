<?php
if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

add_filter( 'wpie_import_mapping_fields', "wpie_import_post_mapping_fields", 10, 2 );

if ( !function_exists( "wpie_import_post_mapping_fields" ) ) {

        function wpie_import_post_mapping_fields( $sections = array(), $wpie_import_type = "" ) {

                global $wp_version;

                $wpie_import_type_title = ucfirst( $wpie_import_type );

                $import = new \wpie\import\WPIE_Import();

                $wpie_post_taxonomies = $import->wpie_get_all_taxonomies( array( 'post_format' ), array( $wpie_import_type ), 'all' );

                $uniqid = uniqid();

                unset( $import );

                ob_start();

                ?>
                <div class="wpie_field_mapping_container_wrapper">
                        <div class="wpie_field_mapping_container_title wpie_active"><?php esc_html_e( 'Title & Content', 'vj-wp-import-export' ); ?><div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div></div>
                        <div class="wpie_field_mapping_container_data wpie_show">
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Post Title', 'vj-wp-import-export' ); ?> *</div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_title" name="wpie_item_title" placeholder="<?php esc_attr_e( 'Title', 'vj-wp-import-export' ); ?>"/>
                                                <div class="wpie_required_field_notice"><?php esc_html_e( 'Note : Title is required field for new items. Optional for update items', 'vj-wp-import-export' ); ?></div>
                                        </div>
                                </div>
                                <div class="wpie_field_mapping_container_element wpie_import_content_editor_wrapper wpie_hide_if_shop_coupon">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Post Content', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <textarea class="wpie_content_data_textarea wpie_item_content" placeholder="<?php esc_attr_e( 'Content', 'vj-wp-import-export' ); ?>" name="wpie_item_content"></textarea>
                                                <div class="wpie_content_options">
                                                        <div class="wpie_field_mapping_radio_input_wrapper">
                                                                <input type="checkbox" id="wpie_item_import_img_tags" name="wpie_item_import_img_tags" checked="checked" value="1" class="wpie_checkbox wpie_item_import_img_tags">
                                                                <label class="wpie_checkbox_label" for="wpie_item_import_img_tags"><?php esc_html_e( 'Search image through content and import images wrapped in <img> tags ', 'vj-wp-import-export' ); ?></label>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Post Excerpt', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_excerpt" name="wpie_item_excerpt" placeholder="<?php esc_attr_e( 'Excerpt', 'vj-wp-import-export' ); ?>"/>
                                        </div>
                                </div>
                        </div>
                </div>

                <?php
                $title_n_content = ob_get_clean();

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
                        <div class="wpie_field_mapping_container_title"><?php esc_html_e( 'Custom Fields', 'vj-wp-import-export' ); ?><div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div></div>
                        <div class="wpie_field_mapping_container_data">
                                <div class="wpie_cf_wrapper">
                                        <div class="wpie_field_mapping_radio_input_wrapper wpie_cf_notice_wrapper">
                                                <input type="checkbox" id="wpie_item_not_add_empty" name="wpie_item_not_add_empty" checked="checked" value="1" class="wpie_checkbox wpie_item_not_add_empty">
                                                <label class="wpie_checkbox_label" for="wpie_item_not_add_empty"><?php esc_html_e( "Don't add Empty value fields in database.", 'vj-wp-import-export' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "it's highly recommended. If custom field value is empty then it skip perticular field and not add to database. it's save memory and increase import speed", "vj-wp-import-export" ); ?>"></i></label>
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
                $cf_section = ob_get_clean();

                ob_start();

                if ( !empty( $wpie_post_taxonomies ) ) {

                        ?>
                        <div class="wpie_field_mapping_container_wrapper">
                                <div class="wpie_field_mapping_container_title"><?php esc_html_e( 'Taxonomies, Categories, Tags', 'vj-wp-import-export' ); ?><div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div></div>
                                <div class="wpie_field_mapping_container_data">

                                        <?php
                                        foreach ( $wpie_post_taxonomies as $slug => $tax ) {

                                                if ( !empty( $tax->labels->name ) && strpos( $tax->labels->name, "_" ) === false ) {
                                                        $name = $tax->labels->name;
                                                } else {
                                                        $name = empty( $tax->labels->singular_name ) ? $tax->name : $tax->labels->singular_name;
                                                }

                                                ?>
                                                <div class="wpie_field_mapping_container_element">
                                                        <input id="wpie_item_set_taxonomy_<?php echo esc_attr( $slug ); ?>" type="checkbox" class="wpie_checkbox wpie_field_mapping_tax_wrapper wpie_item_set_taxonomy wpie_item_set_taxonomy_<?php echo esc_attr( $slug ); ?>" name="wpie_item_set_taxonomy[<?php echo esc_attr( $slug ); ?>]" value="1">
                                                        <label for="wpie_item_set_taxonomy_<?php echo esc_attr( $slug ); ?>" class="wpie_checkbox_label"><?php echo esc_html( $name ); ?></label>
                                                        <div class="wpie_checkbox_container wpie_field_mapping_tax_data">
                                                                <div class="wpie_field_mapping_radio_input_wrapper wpie_field_mapping_cat_inner_wrapper">
                                                                        <div class="wpie_cat_inner_data_wrapper">
                                                                                <div class="wpie_half_container">
                                                                                        <input type="text" class="wpie_content_data_input wpie_item_taxonomy wpie_item_taxonomy_<?php echo esc_attr( $slug ); ?>" name="wpie_item_taxonomy[<?php echo esc_attr( $slug ); ?>]" value=""/>
                                                                                </div>
                                                                                <div class="wpie_half_container wpie_taxonomy_delim_wrapper">
                                                                                        <div class="wpie_field_mapping_image_separator"><?php esc_html_e( 'Separate by', 'vj-wp-import-export' ); ?></div>
                                                                                        <input type="text" class="wpie_content_data_input wpie_field_mapping_input_separator wpie_item_taxonomy_delim wpie_item_taxonomy_delim_<?php echo esc_attr( $slug ); ?>" name="wpie_item_taxonomy_delim[<?php echo esc_attr( $slug ); ?>]" placeholder="," value=","/>
                                                                                </div>
                                                                        </div>
                                                                        <?php if ( $tax->hierarchical ) { ?>
                                                                                <div class="wpie_cat_inner_data_wrapper wpie_cat_group_sep_wrapper wpie_cat_sep_wrapper">
                                                                                        <div class="wpie_field_mapping_image_separator"><?php echo esc_html( sprintf( __( 'Separate %s hierarchy (parent/child) via symbol (i.e. Clothing > Men > TShirts)', 'vj-wp-import-export' ), $name ) ); ?></div>
                                                                                        <input type="text" class="wpie_content_data_input wpie_field_mapping_input_separator wpie_item_taxonomy_hierarchical_delim wpie_item_taxonomy_hierarchical_delim_<?php echo esc_attr( $slug ); ?>" name="wpie_item_taxonomy_hierarchical_delim[<?php echo esc_attr( $slug ); ?>]" placeholder=">" value=">"/>
                                                                                </div>
                                                                                <div class="wpie_cat_inner_data_wrapper">
                                                                                        <input type="checkbox" class="wpie_checkbox wpie_item_taxonomy_child_only_<?php echo esc_attr( $slug ); ?>"  name="wpie_item_taxonomy_child_only[<?php echo esc_attr( $slug ); ?>]" id="wpie_item_taxonomy_child_only_<?php echo esc_attr( $slug ); ?>" value="1"/>
                                                                                        <label for="wpie_item_taxonomy_child_only_<?php echo esc_attr( $slug ); ?>" class="wpie_checkbox_label"><?php echo esc_html( sprintf( __( 'Only assign %s to the bottom level term in the hierarchy', 'vj-wp-import-export' ), $name ) ); ?></label>                                            
                                                                                </div>
                                                                        <?php } ?>
                                                                </div>
                                                        </div>
                                                </div>
                                                <?php
                                                unset( $name );
                                        }

                                        ?>
                                </div>
                        </div>

                        <?php
                }
                $taxonomy_section = ob_get_clean();

                ob_start();

                $wpie_is_support_post_format = ( current_theme_supports( 'post-formats' ) && post_type_supports( $wpie_import_type, 'post-formats' ) ) ? true : false;

                ?>
                <div class="wpie_field_mapping_container_wrapper wpie_other_item_option_wrapper">
                        <div class="wpie_field_mapping_container_title"><?php echo esc_html( __( 'Other', 'vj-wp-import-export' ) . ' ' . $wpie_import_type_title . ' ' . __( 'Options', 'vj-wp-import-export' ) ); ?> <div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div></div>
                        <div class="wpie_field_mapping_container_data  wpie_field_mapping_other_option_outer_wrapper">
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Post Status', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_status wpie_item_status_publish" checked="checked" name="wpie_item_status" id="wpie_field_mapping_post_type_published" value="publish"/>
                                                <label for="wpie_field_mapping_post_type_published" class="wpie_radio_label"><?php esc_html_e( 'Published', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_status wpie_item_status_draft"  name="wpie_item_status" id="wpie_field_mapping_post_type_draft" value="draft"/>
                                                <label for="wpie_field_mapping_post_type_draft" class="wpie_radio_label"><?php esc_html_e( 'Draft', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_status wpie_item_status_as_specified" name="wpie_item_status" id="wpie_field_mapping_post_type_as_specified" value="as_specified"/>
                                                <label for="wpie_field_mapping_post_type_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                        <input type="text" class="wpie_content_data_input wpie_item_status_as_specified_data" name="wpie_item_status_as_specified_data" value=""/>
                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "The value should be one of the following: ('publish', 'draft', 'trash', 'private').", "vj-wp-import-export" ); ?>"></i>
                                                </div>
                                        </div>
                                </div>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Post Dates', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_date wpie_item_date_now"  checked="checked"  name="wpie_item_date" id="wpie_field_mapping_post_date_now" value="now"/>
                                                <label for="wpie_field_mapping_post_date_now" class="wpie_radio_label"><?php esc_html_e( 'Now', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_date wpie_item_date_as_specified" name="wpie_item_date" id="wpie_field_mapping_post_date_as_specified" value="as_specified"/>
                                                <label for="wpie_field_mapping_post_date_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                        <input type="text" class="wpie_content_data_input wpie_item_date_as_specified_data" name="wpie_item_date_as_specified_data" value=""/>
                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Use any format supported by the PHP strtotime function. That means pretty much any human-readable date will work.", "vj-wp-import-export" ); ?>"></i>
                                                </div>
                                        </div>
                                </div>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Comments', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_comment_status wpie_item_comment_status_open" checked="checked" name="wpie_item_comment_status" id="wpie_field_mapping_comment_open" value="open"/>
                                                <label for="wpie_field_mapping_comment_open" class="wpie_radio_label"><?php esc_html_e( 'Open', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_comment_status wpie_item_comment_status_closed"  name="wpie_item_comment_status" id="wpie_field_mapping_comment_closed" value="closed"/>
                                                <label for="wpie_field_mapping_comment_closed" class="wpie_radio_label"><?php esc_html_e( 'Closed', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_comment_status wpie_item_comment_status_as_specified"  name="wpie_item_comment_status" id="wpie_field_mapping_comment_as_specified" value="as_specified"/>
                                                <label for="wpie_field_mapping_comment_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                        <input type="text" class="wpie_content_data_input wpie_item_comment_status_as_specified_data" name="wpie_item_comment_status_as_specified_data" value=""/>
                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "The value should be one of the following: ('open', 'closed').", "vj-wp-import-export" ); ?>"></i>
                                                </div>
                                        </div>
                                </div>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Trackbacks and Pingbacks', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_ping_status wpie_item_ping_status_open" checked="checked" name="wpie_item_ping_status" id="wpie_item_ping_status_open" value="open"/>
                                                <label for="wpie_item_ping_status_open" class="wpie_radio_label"><?php esc_html_e( 'Open', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_ping_status wpie_item_ping_status_closed"  name="wpie_item_ping_status" id="wpie_import_ping_status_closed" value="closed"/>
                                                <label for="wpie_import_ping_status_closed" class="wpie_radio_label"><?php esc_html_e( 'Closed', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_ping_status wpie_item_ping_status_as_specified"  name="wpie_item_ping_status" id="wpie_import_ping_status_data" value="as_specified"/>
                                                <label for="wpie_import_ping_status_data" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                        <input type="text" class="wpie_content_data_input wpie_item_ping_status_as_specified_data" name="wpie_item_ping_status_as_specified_data" value=""/>
                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "The value should be one of the following: ('open', 'closed').", "vj-wp-import-export" ); ?>"></i>
                                                </div>
                                        </div>
                                </div>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Post Slug', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_slug" name="wpie_item_slug" value=""/>
                                        </div>
                                </div>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Post Password', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_post_password" name="wpie_item_post_password" value=""/>
                                        </div>
                                </div>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Post Author', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper wpie_as_specified_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_author" name="wpie_item_author" value=""/>
                                                <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Assign the post to an existing user account by specifying the user ID, username, or e-mail address.", "vj-wp-import-export" ); ?>"></i>
                                        </div>
                                </div>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Download & Import Attachments', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_attachments" name="wpie_item_attachments" value=""/>
                                                <div class="wpie_field_mapping_post_attach_wrapper">
                                                        <div class="wpie_field_mapping_post_attach_sep_label"><?php esc_html_e( 'Separated by', 'vj-wp-import-export' ); ?></div>
                                                        <div class="wpie_field_mapping_post_attach_sep_input"><input type="text" class="wpie_content_data_input wpie_item_attachments_delim" name="wpie_item_attachments_delim" value="|"/></div>
                                                </div>
                                                <input type="checkbox" class="wpie_checkbox wpie_item_attachement_search_for_existing"  name="wpie_item_attachement_search_for_existing" id="wpie_item_attachement_search_for_existing" value="1"/>
                                                <label for="wpie_item_attachement_search_for_existing" class="wpie_checkbox_label"><?php esc_html_e( 'Search for existing attachments to prevent duplicates in media library', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                </div>

                                <?php if ( $wpie_is_support_post_format ) { ?>
                                        <div class="wpie_field_mapping_container_element">
                                                <div class="wpie_field_mapping_inner_title"><?php echo esc_html( $wpie_import_type_title . " " . __( 'Format', 'vj-wp-import-export' ) ); ?></div>
                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_post_format wpie_item_post_format_standard" checked="checked" name="wpie_item_post_format" id="wpie_item_post_format_standard" value="standard"/>
                                                        <label for="wpie_item_post_format_standard" class="wpie_radio_label"><?php esc_html_e( 'Standard', 'vj-wp-import-export' ); ?></label>
                                                </div>
                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_post_format wpie_item_post_format_aside"  name="wpie_item_post_format" id="wpie_item_post_format_aside" value="aside"/>
                                                        <label for="wpie_item_post_format_aside" class="wpie_radio_label"><?php esc_html_e( 'Aside', 'vj-wp-import-export' ); ?></label>
                                                </div>
                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_post_format wpie_item_post_format_image" name="wpie_item_post_format" id="wpie_item_post_format_image" value="image"/>
                                                        <label for="wpie_item_post_format_image" class="wpie_radio_label"><?php esc_html_e( 'Image', 'vj-wp-import-export' ); ?></label>
                                                </div>
                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_post_format wpie_item_post_format_video" name="wpie_item_post_format" id="wpie_item_post_format_video" value="video"/>
                                                        <label for="wpie_item_post_format_video" class="wpie_radio_label"><?php esc_html_e( 'Video', 'vj-wp-import-export' ); ?></label>
                                                </div>
                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_post_format wpie_item_post_format_quote" name="wpie_item_post_format" id="wpie_item_post_format_quote" value="quote"/>
                                                        <label for="wpie_item_post_format_quote" class="wpie_radio_label"><?php esc_html_e( 'Quote', 'vj-wp-import-export' ); ?></label>
                                                </div>
                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_post_format wpie_item_post_format_link"  name="wpie_item_post_format" id="wpie_item_post_format_link" value="link"/>
                                                        <label for="wpie_item_post_format_link" class="wpie_radio_label"><?php esc_html_e( 'Link', 'vj-wp-import-export' ); ?></label>
                                                </div>
                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_post_format wpie_item_post_format_gallery"  name="wpie_item_post_format" id="wpie_item_post_format_gallery" value="gallery"/>
                                                        <label for="wpie_item_post_format_gallery" class="wpie_radio_label"><?php esc_html_e( 'Gallery', 'vj-wp-import-export' ); ?></label>
                                                </div>
                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_post_format wpie_item_post_format_audio" name="wpie_item_post_format" id="wpie_item_post_format_audio" value="audio"/>
                                                        <label for="wpie_item_post_format_audio" class="wpie_radio_label"><?php esc_html_e( 'Audio', 'vj-wp-import-export' ); ?></label>
                                                </div>
                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_post_format wpie_item_post_format_as_specified" name="wpie_item_post_format" id="wpie_item_post_format_as_specified" value="as_specified"/>
                                                        <label for="wpie_item_post_format_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                        <div class="wpie_radio_container wpie_as_specified_wrapper"><input type="text" class="wpie_content_data_input wpie_item_post_format_as_specified_data" name="wpie_item_post_format_as_specified_data" value=""/></div>
                                                </div>
                                        </div>
                                <?php } ?>

                                <?php if ( 'page' == $wpie_import_type || version_compare( $wp_version, '4.7.0', '>=' ) ) { ?>
                                        <div class="wpie_field_mapping_container_element">
                                                <div class="wpie_field_mapping_inner_title"><?php echo esc_html( $wpie_import_type_title . " " . __( 'Template', 'vj-wp-import-export' ) ); ?></div>
                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_template wpie_item_template_as_specified" checked="checked"  name="wpie_item_template" id="wpie_item_template_as_specified" value="as_specified"/>
                                                        <label for="wpie_item_template_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                        <div class="wpie_radio_container" style="display: block;"><input type="text" class="wpie_content_data_input wpie_item_template_as_specified_data" name="wpie_item_template_as_specified_data" value=""/></div>
                                                </div>
                                        </div>
                                <?php } ?>
                                <?php if ( 'page' == $wpie_import_type ) { ?>
                                        <div class="wpie_field_mapping_container_element  wpie_field_mapping_data_option">
                                                <div class="wpie_field_mapping_inner_title"><?php echo esc_html( $wpie_import_type_title . " " . __( 'Parent', 'vj-wp-import-export' ) ); ?></div>
                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_parent wpie_item_parent_manually" checked="checked" name="wpie_item_parent" id="wpie_item_parent" value="manually"/>
                                                        <label for="wpie_item_parent" class="wpie_radio_label"><?php esc_html_e( 'Select page parent', 'vj-wp-import-export' ); ?></label>
                                                        <div class="wpie_field_mapping_option_wrapper" style="display: block;">
                                                                <?php wp_dropdown_pages( array( 'post_type' => 'page', 'selected' => '', 'class' => 'wpie_content_data_select', 'name' => 'wpie_item_parent_data', 'show_option_none' => __( '(no parent)', 'vj-wp-import-export' ), 'sort_column' => 'menu_order, post_title', 'number' => 500 ) ); ?>
                                                        </div>
                                                </div>
                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_parent wpie_item_parent_as_specified" name="wpie_item_parent" id="wpie_item_parent_as_specified" value="as_specified"/>
                                                        <label for="wpie_item_parent_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                        <div class="wpie_radio_container wpie_as_specified_wrapper"><input type="text" class="wpie_content_data_input wpie_item_parent_as_specified_data" name="wpie_item_parent_as_specified_data" value=""/></div>
                                                </div>
                                        </div>
                                <?php } ?>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Menu Order', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_order" name="wpie_item_order" value=""/>
                                        </div>
                                </div>
                        </div>
                </div>

                <?php
                $other_section = ob_get_clean();

                $sections = array_replace( $sections, array(
                        '100' => $title_n_content,
                        '200' => $image_section,
                        '300' => $cf_section,
                        '400' => $taxonomy_section,
                        '500' => $other_section,
                        )
                );

                unset( $wpie_import_type_title, $title_n_content, $image_section, $cf_section, $taxonomy_section, $wpie_is_support_post_format, $wpie_post_taxonomies );

                return apply_filters( "wpie_pre_post_field_mapping_section", $sections, $wpie_import_type );
        }

}

add_filter( 'wpie_import_search_existing_item', "wpie_import_post_search_existing_item", 10, 2 );

if ( !function_exists( "wpie_import_post_search_existing_item" ) ) {

        function wpie_import_post_search_existing_item( $sections = "", $wpie_import_type = "" ) {

                ob_start();

                ?>
                <div class="wpie_field_mapping_container_element">
                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Search Existing Item on your site based on...', 'vj-wp-import-export' ); ?></div>
                        <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_existing_item_search_logic wpie_existing_item_search_logic_title" checked="checked" name="wpie_existing_item_search_logic" id="wpie_existing_item_search_logic_title" value="title"/>
                                <label for="wpie_existing_item_search_logic_title" class="wpie_radio_label"><?php esc_html_e( 'Title', 'vj-wp-import-export' ); ?></label>
                        </div>
                        <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_existing_item_search_logic wpie_existing_item_search_logic_content"  name="wpie_existing_item_search_logic" id="wpie_existing_item_search_logic_content" value="content"/>
                                <label for="wpie_existing_item_search_logic_content" class="wpie_radio_label"><?php esc_html_e( 'Content', 'vj-wp-import-export' ); ?></label>
                        </div>
                        <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_existing_item_search_logic wpie_existing_item_search_logic_cf"  name="wpie_existing_item_search_logic" id="wpie_existing_item_search_logic_cf" value="cf"/>
                                <label for="wpie_existing_item_search_logic_cf" class="wpie_radio_label"><?php esc_html_e( 'Custom Field', 'vj-wp-import-export' ); ?></label>
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
                        <?php if ( $wpie_import_type == "product" ) { ?>
                                <div class="wpie_field_mapping_other_option_wrapper">
                                        <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_existing_item_search_logic"  name="wpie_existing_item_search_logic" id="wpie_existing_item_search_logic_sku" value="sku"/>
                                        <label for="wpie_existing_item_search_logic_sku" class="wpie_radio_label"><?php esc_html_e( 'SKU', 'vj-wp-import-export' ); ?></label>
                                        <div class="wpie_radio_container"><input type="text" class="wpie_content_data_input wpie_existing_item_search_logic_sku" name="wpie_existing_item_search_logic_sku" value=""/></div>
                                </div>
                        <?php } ?>
                        <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_existing_item_search_logic"  name="wpie_existing_item_search_logic" id="wpie_existing_item_search_logic_id" value="id"/>
                                <label for="wpie_existing_item_search_logic_id" class="wpie_radio_label"><?php esc_html_e( 'Post ID', 'vj-wp-import-export' ); ?></label>
                                <div class="wpie_radio_container"><input type="text" class="wpie_content_data_input wpie_existing_item_search_logic_id" name="wpie_existing_item_search_logic_id" value=""/></div>
                        </div>
                </div>
                <?php
                return apply_filters( "wpie_import_post_search_existing_item", $sections . ob_get_clean(), $wpie_import_type );
        }

}

add_filter( 'wpie_import_update_existing_item_fields', "wpie_import_post_update_existing_item_fields", 10, 2 );

if ( !function_exists( "wpie_import_post_update_existing_item_fields" ) ) {

        function wpie_import_post_update_existing_item_fields( $sections = "", $wpie_import_type = "" ) {

                $is_yoast_added = apply_filters( 'wpie_import_yoast_addon', false );

                ob_start();

                ?>
                <div class="wpie_field_mapping_container_element">
                        <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_item_update wpie_item_update_all" name="wpie_item_update" id="wpie_item_update_all" value="all"/>
                                <label for="wpie_item_update_all" class="wpie_radio_label"><?php esc_html_e( 'Update all data', 'vj-wp-import-export' ); ?></label>
                        </div>
                        <?php if ( $wpie_import_type == "product" ) { ?>
                                <div class="wpie_field_mapping_other_option_wrapper">
                                        <input type="radio" class="wpie_radio wpie_item_update wpie_item_update_product_price_stock" name="wpie_item_update" id="wpie_item_update_product_price_stock" value="price_stock"/>
                                        <label for="wpie_item_update_product_price_stock" class="wpie_radio_label"><?php esc_html_e( 'Update Price / Stock Only', 'vj-wp-import-export' ); ?></label>
                                        <div class="wpie_radio_container">
                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                        <input type="checkbox" class="wpie_checkbox is_update_item_product_price_data wpie_item_update_price_stock_item" name="is_update_item_product_price_data" id="is_update_item_product_price_data" value="1"/>
                                                        <label for="is_update_item_product_price_data" class="wpie_checkbox_label"><?php esc_html_e( 'Price', 'vj-wp-import-export' ); ?></label>
                                                        <div class="wpie_checkbox_container">
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="checkbox" class="wpie_checkbox is_update_item_product_price_regular_price wpie_item_update_price_stock_item" name="is_update_item_product_price_regular_price" id="is_update_item_product_price_regular_price" value="1" checked="checked"/>
                                                                        <label for="is_update_item_product_price_regular_price" class="wpie_checkbox_label"><?php esc_html_e( 'Regular Price', 'vj-wp-import-export' ); ?></label>
                                                                </div>
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="checkbox" class="wpie_checkbox is_update_item_product_price_sale_price wpie_item_update_price_stock_item" name="is_update_item_product_price_sale_price" id="is_update_item_product_price_sale_price" value="1"/>
                                                                        <label for="is_update_item_product_price_sale_price" class="wpie_checkbox_label"><?php esc_html_e( 'Sale Price', 'vj-wp-import-export' ); ?></label>
                                                                </div>

                                                        </div>
                                                </div>                                             
                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                        <input type="checkbox" class="wpie_checkbox is_update_item_product_stock_data wpie_item_update_price_stock_item" name="is_update_item_product_stock_data" id="is_update_item_product_stock_data" value="1"/>
                                                        <label for="is_update_item_product_stock_data" class="wpie_checkbox_label"><?php esc_html_e( 'Stock', 'vj-wp-import-export' ); ?></label>
                                                        <div class="wpie_checkbox_container">
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="checkbox" class="wpie_checkbox is_update_item_product_stock_stock wpie_item_update_price_stock_item" name="is_update_item_product_stock_stock" id="is_update_item_product_stock_stock" value="1" checked="checked"/>
                                                                        <label for="is_update_item_product_stock_stock" class="wpie_checkbox_label"><?php esc_html_e( 'Stock Qty', 'vj-wp-import-export' ); ?></label>
                                                                </div>
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="checkbox" class="wpie_checkbox is_update_item_product_stock_manage_stock wpie_item_update_price_stock_item" name="is_update_item_product_stock_manage_stock" id="is_update_item_product_stock_manage_stock" value="1"/>
                                                                        <label for="is_update_item_product_stock_manage_stock" class="wpie_checkbox_label"><?php esc_html_e( 'Manage stock?', 'vj-wp-import-export' ); ?></label>
                                                                </div>
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="checkbox" class="wpie_checkbox is_update_item_product_stock_stock_status wpie_item_update_price_stock_item" name="is_update_item_product_stock_stock_status" id="is_update_item_product_stock_stock_status" value="1"/>
                                                                        <label for="is_update_item_product_stock_stock_status" class="wpie_checkbox_label"><?php esc_html_e( 'Stock status', 'vj-wp-import-export' ); ?></label>
                                                                </div>
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="checkbox" class="wpie_checkbox is_update_item_product_stock_low_stock_amount wpie_item_update_price_stock_item" name="is_update_item_product_stock_low_stock_amount" id="is_update_item_product_stock_low_stock_amount" value="1"/>
                                                                        <label for="is_update_item_product_stock_low_stock_amount" class="wpie_checkbox_label"><?php esc_html_e( 'Low stock threshold', 'vj-wp-import-export' ); ?></label>
                                                                </div>
                                                        </div>
                                                </div>                                             
                                        </div>
                                </div>
                        <?php } ?>
                        <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_item_update wpie_item_update_specific" name="wpie_item_update" id="wpie_item_update_specific" value="specific" checked="checked"/>
                                <label for="wpie_item_update_specific" class="wpie_radio_label"><?php esc_html_e( 'Choose which data to update', 'vj-wp-import-export' ); ?></label>
                                <div class="wpie_radio_container">
                                        <div class="wpie_update_item_all_action"><?php esc_html_e( 'Check/Uncheck All', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_post_status" name="is_update_item_post_status" id="is_update_item_post_status" value="1"/>
                                                <label for="is_update_item_post_status" class="wpie_checkbox_label"><?php esc_html_e( 'Post status', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_title" name="is_update_item_title" id="is_update_item_title" value="1"/>
                                                <label for="is_update_item_title" class="wpie_checkbox_label"><?php esc_html_e( 'Title', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_author" name="is_update_item_author" id="is_update_item_author" value="1"/>
                                                <label for="is_update_item_author" class="wpie_checkbox_label"><?php esc_html_e( 'Author', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_slug" name="is_update_item_slug" id="is_update_item_slug" value="1"/>
                                                <label for="is_update_item_slug" class="wpie_checkbox_label"><?php esc_html_e( 'Slug', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_content" name="is_update_item_content" id="is_update_item_content" value="1"/>
                                                <label for="is_update_item_content" class="wpie_checkbox_label"><?php esc_html_e( 'Content / Description', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_excerpt" name="is_update_item_excerpt" id="is_update_item_excerpt" value="1"/>
                                                <label for="is_update_item_excerpt" class="wpie_checkbox_label"><?php esc_html_e( 'Excerpt / Short Description', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_dates" name="is_update_item_dates" id="is_update_item_dates" value="1"/>
                                                <label for="is_update_item_dates" class="wpie_checkbox_label"><?php esc_html_e( 'Dates', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_order" name="is_update_item_order" id="is_update_item_order" value="1"/>
                                                <label for="is_update_item_order" class="wpie_checkbox_label"><?php esc_html_e( 'Menu order', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_parent" name="is_update_item_parent" id="is_update_item_parent" value="1"/>
                                                <label for="is_update_item_parent" class="wpie_checkbox_label"><?php esc_html_e( 'Parent post', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_post_type" name="is_update_item_post_type" id="is_update_item_post_type" value="1"/>
                                                <label for="is_update_item_post_type" class="wpie_checkbox_label"><?php esc_html_e( 'Post type', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <?php
                                        if ( $wpie_import_type == "product" ) {
                                                $commnet_status = __( 'Enable review setting', 'vj-wp-import-export' );
                                        } else {
                                                $commnet_status = __( 'Comment status', 'vj-wp-import-export' );
                                        }

                                        ?>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_comment_status" name="is_update_item_comment_status" id="is_update_item_comment_status" value="1"/>
                                                <label for="is_update_item_comment_status" class="wpie_checkbox_label"><?php echo esc_html( $commnet_status ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_ping_status" name="is_update_item_ping_status" id="is_update_item_ping_status" value="1"/>
                                                <label for="is_update_item_ping_status" class="wpie_checkbox_label"><?php esc_html_e( 'Ping Status', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_post_password" name="is_update_item_post_password" id="is_update_item_post_password" value="1"/>
                                                <label for="is_update_item_post_password" class="wpie_checkbox_label"><?php esc_html_e( 'Post Password', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_attachments" name="is_update_item_attachments" id="is_update_item_attachments" value="1"/>
                                                <label for="is_update_item_attachments" class="wpie_checkbox_label"><?php esc_html_e( 'Attachments', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <?php if ( $wpie_import_type == "product" ) { ?>
                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                        <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_product_type" name="is_update_item_product_type" id="is_update_item_product_type" value="1"/>
                                                        <label for="is_update_item_product_type" class="wpie_checkbox_label"><?php esc_html_e( 'Product Type', 'vj-wp-import-export' ); ?></label>
                                                </div>
                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                        <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_catalog_visibility" name="is_update_item_catalog_visibility" id="is_update_item_catalog_visibility" value="1"/>
                                                        <label for="is_update_item_catalog_visibility" class="wpie_checkbox_label"><?php esc_html_e( 'Catalog Visibility', 'vj-wp-import-export' ); ?></label>
                                                </div>
                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                        <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_featured_status" name="is_update_item_featured_status" id="is_update_item_featured_status" value="1"/>
                                                        <label for="is_update_item_featured_status" class="wpie_checkbox_label"><?php esc_html_e( 'Featured Status', 'vj-wp-import-export' ); ?></label>
                                                </div>

                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                        <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_attributes" name="is_update_item_attributes" id="is_update_item_attributes" value="1"/>
                                                        <label for="is_update_item_attributes" class="wpie_checkbox_label"><?php esc_html_e( 'Attributes', 'vj-wp-import-export' ); ?></label>
                                                        <div class="wpie_checkbox_container">
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_item_update_attributes wpie_item_update_attributes_all" checked="checked" name="wpie_item_update_attributes" id="wpie_item_update_attributes_all" value="all"/>
                                                                        <label for="wpie_item_update_attributes_all" class="wpie_radio_label"><?php esc_html_e( 'Update all Attributes and Remove Attributes if not found in file', 'vj-wp-import-export' ); ?></label>
                                                                </div>
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_item_update_attributes wpie_item_update_attributes_append" name="wpie_item_update_attributes" id="wpie_item_update_attributes_append" value="append"/>
                                                                        <label for="wpie_item_update_attributes_append" class="wpie_radio_label"><?php esc_html_e( 'Update all Attributes and keep Attributes if not found in file', 'vj-wp-import-export' ); ?></label>
                                                                </div>
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_item_update_attributes wpie_item_update_attributes_includes" name="wpie_item_update_attributes" id="wpie_item_update_attributes_includes" value="includes"/>
                                                                        <label for="wpie_item_update_attributes_includes" class="wpie_radio_label"><?php esc_html_e( "Update only these Attributes, leave the rest alone", 'vj-wp-import-export' ); ?></label>
                                                                        <div class="wpie_radio_container">
                                                                                <input type="text" class="wpie_content_data_input wpie_item_update_attributes_includes_data" name="wpie_item_update_attributes_includes_data" value=""/>
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="radio" class="wpie_radio " name="wpie_item_update_attributes" id="wpie_item_update_attributes_excludes" value="excludes"/>
                                                                        <label for="wpie_item_update_attributes_excludes" class="wpie_radio_label"><?php esc_html_e( "Leave these attributes alone, update all other Attributes", 'vj-wp-import-export' ); ?></label>
                                                                        <div class="wpie_radio_container">
                                                                                <input type="text" class="wpie_content_data_input wpie_item_update_attributes_excludes_data" name="wpie_item_update_attributes_excludes_data" value=""/>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                </div>
                                        <?php } ?>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_images" name="is_update_item_images" id="is_update_item_images" value="1"/>
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
                                                <label for="is_update_item_cf" class="wpie_checkbox_label"><?php esc_html_e( 'Custom Fields', 'vj-wp-import-export' ); ?></label>
                                                <div class="wpie_checkbox_container">
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="wpie_radio wpie_item_update_cf wpie_item_update_cf_append" checked="checked" name="wpie_item_update_cf" id="wpie_item_update_cf_append" value="append"/>
                                                                <label for="wpie_item_update_cf_append" class="wpie_radio_label"><?php esc_html_e( 'Update all Custom Fields and keep fields if not found in file', 'vj-wp-import-export' ); ?></label>
                                                        </div>
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="wpie_radio wpie_item_update_cf wpie_item_update_cf_all" name="wpie_item_update_cf" id="wpie_item_update_cf_all" value="all"/>
                                                                <label for="wpie_item_update_cf_all" class="wpie_radio_label"><?php esc_html_e( 'Update all Custom Fields and Remove fields if not found in file', 'vj-wp-import-export' ); ?></label>
                                                        </div>
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="wpie_radio wpie_item_update_cf wpie_item_update_cf_includes" name="wpie_item_update_cf" id="wpie_item_update_cf_includes" value="includes"/>
                                                                <label for="wpie_item_update_cf_includes" class="wpie_radio_label"><?php esc_html_e( "Update only these Custom Fields, leave the rest alone", 'vj-wp-import-export' ); ?></label>
                                                                <div class="wpie_radio_container">
                                                                        <input type="text" class="wpie_content_data_input wpie_item_update_cf_includes_data" name="wpie_item_update_cf_includes_data" value=""/>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="wpie_radio wpie_item_update_cf wpie_item_update_cf_excludes" name="wpie_item_update_cf" id="wpie_item_update_cf_excludes" value="excludes"/>
                                                                <label for="wpie_item_update_cf_excludes" class="wpie_radio_label"><?php esc_html_e( "Leave these fields alone, update all other Custom Fields", 'vj-wp-import-export' ); ?></label>
                                                                <div class="wpie_radio_container">
                                                                        <input type="text" class="wpie_content_data_input wpie_item_update_cf_excludes_data" name="wpie_item_update_cf_excludes_data" value=""/>
                                                                </div>
                                                        </div>
                                                </div>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_taxonomies" name="is_update_item_taxonomies" id="is_update_item_taxonomies" value="1"/>
                                                <label for="is_update_item_taxonomies" class="wpie_checkbox_label"><?php esc_html_e( 'Taxonomies (incl. Categories and Tags)', 'vj-wp-import-export' ); ?></label>
                                                <div class="wpie_checkbox_container">
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="wpie_radio wpie_item_update_taxonomies wpie_item_update_taxonomies_includes" name="wpie_item_update_taxonomies" id="wpie_item_update_taxonomies_includes" value="includes"/>
                                                                <label for="wpie_item_update_taxonomies_includes" class="wpie_radio_label"><?php esc_html_e( "Update only these taxonomies, leave the rest alone", 'vj-wp-import-export' ); ?></label>
                                                                <div class="wpie_radio_container">
                                                                        <input type="text" class="wpie_content_data_input wpie_item_update_taxonomies_includes_data" name="wpie_item_update_taxonomies_includes_data" value=""/>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="wpie_radio wpie_item_update_taxonomies wpie_item_update_taxonomies_excludes" name="wpie_item_update_taxonomies" id="wpie_item_update_taxonomies_excludes" value="excludes"/>
                                                                <label for="wpie_item_update_taxonomies_excludes" class="wpie_radio_label"><?php esc_html_e( "Leave these taxonomies alone, update all others", 'vj-wp-import-export' ); ?></label>
                                                                <div class="wpie_radio_container">
                                                                        <input type="text" class="wpie_content_data_input wpie_item_update_taxonomies_excludes_data" name="wpie_item_update_taxonomies_excludes_data" value=""/>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="wpie_radio wpie_item_update_taxonomies wpie_item_update_taxonomies_all" checked="checked" name="wpie_item_update_taxonomies" id="wpie_item_update_taxonomies_all" value="all"/>
                                                                <label for="wpie_item_update_taxonomies_all" class="wpie_radio_label"><?php esc_html_e( 'Remove existing taxonomies, add new taxonomies', 'vj-wp-import-export' ); ?></label>
                                                        </div>
                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                <input type="radio" class="wpie_radio wpie_item_update_taxonomies wpie_item_update_taxonomies_append"  name="wpie_item_update_taxonomies" id="wpie_item_update_taxonomies_append" value="append"/>
                                                                <label for="wpie_item_update_taxonomies_append" class="wpie_radio_label"><?php esc_html_e( 'Only add new', 'vj-wp-import-export' ); ?></label>
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
                $sections .= ob_get_clean();

                return apply_filters( "wpie_import_post_update_item_fields", $sections, $wpie_import_type );
        }

}