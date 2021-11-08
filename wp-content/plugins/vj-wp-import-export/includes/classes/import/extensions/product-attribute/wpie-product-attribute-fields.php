<?php
if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

add_filter( 'wpie_import_mapping_fields', "wpie_import_attribute_mapping_fields", 20, 2 );

if ( !function_exists( "wpie_import_attribute_mapping_fields" ) ) {

        function wpie_import_attribute_mapping_fields( $sections = array(), $wpie_import_type = "" ) {

                $attributes = null;

                if ( class_exists( "WooCommerce" ) && file_exists( WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import.php' ) ) {

                        require_once(WPIE_IMPORT_CLASSES_DIR . '/class-wpie-import.php');

                        $wpie_import = new \wpie\import\WPIE_Import();

                        $attributes = $wpie_import->get_attribute_list();

                        unset( $wpie_import );
                }
                $uniqid = uniqid();

                $wpie_import_type_title = ucfirst( $wpie_import_type );

                ob_start();

                ?>
                <div class="wpie_attribute_fields_wrapper wpie_field_mapping_container_wrapper wpie_<?php echo esc_attr( $wpie_import_type ); ?>_field_mapping_container">
                        <div class="wpie_field_mapping_container_title wpie_active"><?php esc_html_e( "Attribute Data", 'vj-wp-import-export' ); ?><div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div></div>
                        <div class="wpie_field_mapping_container_data" style="display: block;">
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Select Attribute', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_attribute_source wpie_item_attribute_source_single" checked="checked" name="wpie_item_attribute_source" id="wpie_item_attribute_source_single" value="single"/>
                                                <label for="wpie_item_attribute_source_single" class="wpie_radio_label"><?php esc_html_e( 'Single from list', 'vj-wp-import-export' ); ?></label>
                                                <div class="wpie_radio_container">
                                                        <select class="wpie_content_data_select wpie_item_attribute_list wpie_item_dropdown_as_specified" name="wpie_item_attribute_list" >
                                                            <?php
                                                            if ( !empty( $attributes ) ) {

                                                                    foreach ( $attributes as $attr ) {

                                                                            ?>
                                                                                <option value="<?php echo isset( $attr->attribute_name ) ? esc_attr( $attr->attribute_name ) : ''; ?>" ><?php echo isset( $attr->attribute_label ) ? esc_html( $attr->attribute_label ) : ''; ?></option>
                                                                                <?php
                                                                        }
                                                                }

                                                                ?>
                                                        </select>
                                                </div>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_attribute_name wpie_item_attribute_name_as_specified wpie_item_attribute_source wpie_item_attribute_source_as_specified" name="wpie_item_attribute_source" id="wpie_item_attribute_source_as_specified" value="as_specified"/>
                                                <label for="wpie_item_attribute_source_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                        <div class="wpie_atrtibute_properties">
                                                                <div class="wpie_field_mapping_container_element">
                                                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Name / Label', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="text" class="wpie_content_data_input wpie_item_name wpie_item_attribute_label" name="wpie_item_name" value=""/>
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_field_mapping_container_element">
                                                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Slug', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_attribute_name wpie_item_attribute_name_auto wpie_item_slug wpie_item_slug_auto" checked="checked" name="wpie_item_slug" id="wpie_item_slug_auto" value="auto"/>
                                                                                <label for="wpie_item_slug_auto" class="wpie_radio_label"><?php esc_html_e( 'Auto', 'vj-wp-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_attribute_name wpie_item_attribute_name_as_specified wpie_item_slug wpie_item_slug_as_specified" name="wpie_item_slug" id="wpie_item_slug_as_specified" value="as_specified"/>
                                                                                <label for="wpie_item_slug_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                                                <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                                                        <input type="text" class="wpie_content_data_input wpie_item_attribute_name_as_specified_data wpie_item_slug_as_specified_data" name="wpie_item_slug_as_specified_data" value=""/>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_field_mapping_container_element">
                                                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Enable Archives?', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_attribute_public wpie_item_attribute_public_no" checked="checked" name="wpie_item_attribute_public" id="wpie_item_attribute_public_no" value="0"/>
                                                                                <label for="wpie_item_attribute_public_no" class="wpie_radio_label"><?php esc_html_e( 'No', 'vj-wp-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_attribute_public wpie_item_attribute_public_yes" name="wpie_item_attribute_public" id="wpie_item_attribute_public_yes" value="1"/>
                                                                                <label for="wpie_item_attribute_public_yes" class="wpie_radio_label"><?php esc_html_e( 'Yes', 'vj-wp-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_attribute_public wpie_item_attribute_public_as_specified" name="wpie_item_attribute_public" id="wpie_item_attribute_public_as_specified" value="as_specified"/>
                                                                                <label for="wpie_item_attribute_public_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                                                <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                                                        <input type="text" class="wpie_content_data_input wpie_item_attribute_public_as_specified_data" name="wpie_item_attribute_public_as_specified_data" value=""/>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_field_mapping_container_element">
                                                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Default sort order', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper ">
                                                                                <select class="wpie_content_data_select wpie_item_attribute_orderby wpie_item_dropdown_as_specified" name="wpie_item_attribute_orderby" >
                                                                                        <option value="id" ><?php esc_html_e( 'Term ID', 'vj-wp-import-export' ); ?></option>
                                                                                        <option value="menu_order" selected="selected" ><?php esc_html_e( 'Custom ordering', 'vj-wp-import-export' ); ?></option>
                                                                                        <option value="name" ><?php esc_html_e( 'Name', 'vj-wp-import-export' ); ?></option>
                                                                                        <option value="name_num" ><?php esc_html_e( 'Name (numeric)', 'vj-wp-import-export' ); ?></option>
                                                                                        <option value="as_specified" ><?php esc_html_e( 'As Specified', 'vj-wp-import-export' ); ?></option>
                                                                                </select>    
                                                                                <div class="wpie_item_attribute_orderby_as_specified_wrapper wpie_item_as_specified_wrapper wpie_hide wpie_as_specified_wrapper">
                                                                                        <input type="text" class="wpie_content_data_input wpie_item_attribute_orderby_as_specified_data" name="wpie_item_attribute_orderby_as_specified_data" value=""/>
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
                $attr_data = ob_get_clean();

                ob_start();

                ?>
                <div class="wpie_attribute_fields_wrapper wpie_field_mapping_container_wrapper wpie_<?php echo esc_attr( $wpie_import_type ); ?>_field_mapping_container">
                        <div class="wpie_field_mapping_container_title wpie_active"><?php esc_html_e( "Term / Value Data", 'vj-wp-import-export' ); ?><div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div></div>
                        <div class="wpie_field_mapping_container_data" style="display: block;">
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Name / Label', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_term_name" name="wpie_item_term_name" value=""/>
                                                <div class="wpie_required_field_notice"><?php esc_html_e( 'Note : Name / Label is required field for new items. Optional for update items', 'vj-wp-import-export' ); ?></div>
                                        </div>
                                </div>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Slug', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_term_slug wpie_item_term_slug_auto" checked="checked" name="wpie_item_term_slug" id="wpie_item_term_slug_auto" value="auto"/>
                                                <label for="wpie_item_term_slug_auto" class="wpie_radio_label"><?php esc_html_e( 'Auto', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_item_term_slug wpie_item_term_slug_as_specified" name="wpie_item_term_slug" id="wpie_item_term_slug_as_specified" value="as_specified"/>
                                                <label for="wpie_item_term_slug_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                        <input type="text" class="wpie_content_data_input wpie_item_term_slug_as_specified_data" name="wpie_item_term_slug_as_specified_data" value=""/>
                                                </div>
                                        </div>
                                </div>                                
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Description', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_term_description" name="wpie_item_term_description" value=""/>
                                        </div>
                                </div>                                                             
                        </div>
                </div>
                <?php
                $term_data = ob_get_clean();

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

                $field_mapping_sections = [
                        '100' => $attr_data,
                        '200' => $term_data,
                        '300' => $term_meta
                ];

                unset( $attr_data, $term_data, $term_meta );

                return apply_filters( "wpie_pre_attribute_field_mapping_section", array_replace( $sections, $field_mapping_sections ), $wpie_import_type );
        }

}

add_filter( 'wpie_import_search_existing_item', "wpie_import_attribute_search_existing_item", 20, 2 );

if ( !function_exists( "wpie_import_attribute_search_existing_item" ) ) {

        function wpie_import_attribute_search_existing_item( $sections = "", $wpie_import_type = "" ) {

                ob_start();

                ?>
                <div class="wpie_field_mapping_container_element wpie_attribute_search_existing_data">
                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Search Existing Attribute on your site based on...', 'vj-wp-import-export' ); ?></div>                  
                        <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_existing_item_search_logic" checked="checked"  name="wpie_existing_item_search_logic" id="wpie_existing_item_search_logic_slug" value="slug"/>
                                <label for="wpie_existing_item_search_logic_slug" class="wpie_radio_label"><?php esc_html_e( 'Attribute Slug', 'vj-wp-import-export' ); ?></label>
                        </div>
                        <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_existing_item_search_logic"  name="wpie_existing_item_search_logic" id="wpie_existing_item_search_logic_name" value="name"/>
                                <label for="wpie_existing_item_search_logic_name" class="wpie_radio_label"><?php esc_html_e( 'Attribute Name', 'vj-wp-import-export' ); ?></label>
                        </div>
                </div>
                <div class="wpie_field_mapping_container_element">
                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Search Existing Term on your site based on...', 'vj-wp-import-export' ); ?></div>
                        <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_existing_item_term_search_logic wpie_existing_item_term_search_logic_name"name="wpie_existing_item_term_search_logic" id="wpie_existing_item_term_search_logic_name" value="name"/>
                                <label for="wpie_existing_item_term_search_logic_name" class="wpie_radio_label"><?php esc_html_e( 'Term Name', 'vj-wp-import-export' ); ?></label>
                        </div>
                        <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_existing_item_term_search_logic wpie_existing_item_term_search_logic_slug"  checked="checked"  name="wpie_existing_item_term_search_logic" id="wpie_existing_item_term_search_logic_slug" value="slug"/>
                                <label for="wpie_existing_item_term_search_logic_slug" class="wpie_radio_label"><?php esc_html_e( 'Term Slug', 'vj-wp-import-export' ); ?></label>
                        </div>
                        <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_existing_item_term_search_logic wpie_existing_item_term_search_logic_cf"  name="wpie_existing_item_term_search_logic" id="wpie_existing_item_term_search_logic_cf" value="cf"/>
                                <label for="wpie_existing_item_term_search_logic_cf" class="wpie_radio_label"><?php esc_html_e( 'Term Custom field', 'vj-wp-import-export' ); ?></label>
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
                                                                <td><input type="text" class="wpie_content_data_input wpie_existing_item_term_search_logic_cf_key" name="wpie_existing_item_term_search_logic_cf_key" value=""/></td>
                                                                <td><input type="text" class="wpie_content_data_input wpie_existing_item_term_search_logic_cf_value" name="wpie_existing_item_term_search_logic_cf_value" value=""/></td>
                                                        </tr>
                                                </tbody>
                                        </table>
                                </div>
                        </div>
                        <div class="wpie_field_mapping_other_option_wrapper">
                                <input type="radio" class="wpie_radio wpie_field_mapping_other_option_radio wpie_existing_item_term_search_logic"  name="wpie_existing_item_term_search_logic" id="wpie_existing_item_term_search_logic_id" value="id"/>
                                <label for="wpie_existing_item_term_search_logic_id" class="wpie_radio_label"><?php esc_html_e( 'Term ID', 'vj-wp-import-export' ); ?></label>
                                <div class="wpie_radio_container"><input type="text" class="wpie_content_data_input wpie_existing_item_term_search_logic_id" name="wpie_existing_item_term_search_logic_id" value=""/></div>
                        </div>
                </div>
                <?php
                $handle_section = ob_get_clean();
                return $handle_section;
        }

}

add_filter( 'wpie_import_update_existing_item_fields', "wpie_import_attribute_update_existing_item_fields", 20, 2 );

if ( !function_exists( "wpie_import_attribute_update_existing_item_fields" ) ) {

        function wpie_import_attribute_update_existing_item_fields( $sections = "", $wpie_import_type = "" ) {

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
                                                <label for="is_update_item_name" class="wpie_checkbox_label"><?php esc_html_e( 'Attribute Name', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_slug" name="is_update_item_slug" id="is_update_item_slug" value="1"/>
                                                <label for="is_update_item_slug" class="wpie_checkbox_label"><?php esc_html_e( 'Attribute Slug', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_public" name="is_update_item_public" id="is_update_item_public" value="1"/>
                                                <label for="is_update_item_public" class="wpie_checkbox_label"><?php esc_html_e( 'Enable Archives?', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_orderby" name="is_update_item_orderby" id="is_update_item_orderby" value="1"/>
                                                <label for="is_update_item_orderby" class="wpie_checkbox_label"><?php esc_html_e( 'Default sort order', 'vj-wp-import-export' ); ?></label>
                                        </div>         
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_term_name" name="is_update_item_term_name" id="is_update_item_term_name" value="1"/>
                                                <label for="is_update_item_term_name" class="wpie_checkbox_label"><?php esc_html_e( 'Term Name', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_term_slug" name="is_update_item_term_slug" id="is_update_item_term_slug" value="1"/>
                                                <label for="is_update_item_term_slug" class="wpie_checkbox_label"><?php esc_html_e( 'Term Slug', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_item_update_field is_update_item_term_description" name="is_update_item_term_description" id="is_update_item_term_description" value="1"/>
                                                <label for="is_update_item_term_description" class="wpie_checkbox_label"><?php esc_html_e( 'Term Description', 'vj-wp-import-export' ); ?></label>
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
                                </div>
                        </div>
                </div>

                <?php
                $existing_item = ob_get_clean();

                return $existing_item;
        }

}