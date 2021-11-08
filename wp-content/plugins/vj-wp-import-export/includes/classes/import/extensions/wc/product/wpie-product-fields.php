<?php
if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}
if ( !function_exists( "wpie_import_product_mapping_fields" ) ) {

        function wpie_import_product_mapping_fields( $sections = array(), $wpie_import_type = "" ) {

                $wpie_product_type = apply_filters( 'wpie_import_product_type', array(
                        'as_specified' => __( 'As specified', 'vj-wp-import-export' ),
                        'simple' => __( 'Simple product', 'vj-wp-import-export' ),
                        'grouped' => __( 'Grouped product', 'vj-wp-import-export' ),
                        'external' => __( 'External/Affiliate product', 'vj-wp-import-export' ),
                        'variable' => __( 'Variable product', 'vj-wp-import-export' )
                        ) );

                $product_group = array();

                $group_term = get_term_by( 'slug', 'grouped', 'product_type' );

                if ( $group_term ) {
                        $group_data = get_objects_in_term( $group_term->term_id, 'product_type' );

                        if ( !is_wp_error( $group_data ) ) {
                                $posts_in = array_unique( $group_data );

                                if ( sizeof( $posts_in ) > 0 ) {
                                        $posts_in = array_slice( $posts_in, 0, 100 );
                                        $args = array(
                                                'post_type' => 'product',
                                                'post_status' => 'any',
                                                'numberposts' => 100,
                                                'orderby' => 'title',
                                                'order' => 'asc',
                                                'post_parent' => 0,
                                                'include' => $posts_in,
                                        );
                                        $product_group = get_posts( $args );
                                        unset( $args );
                                }
                                unset( $posts_in );
                        }
                        unset( $group_data );
                }
                unset( $group_term );

                $wpie_product_tax_class = array_filter( array_map( 'trim', explode( "\n", get_option( 'woocommerce_tax_classes' ) ) ) );

                ob_start();

                ?>
                <div class="wpie_field_mapping_container_wrapper">
                        <div class="wpie_field_mapping_container_title"><?php esc_html_e( 'Product Data', 'vj-wp-import-export' ); ?><div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div></div>
                        <div class="wpie_field_mapping_container_data">
                                <div class="wpie_product_data_section">
                                        <div class="wpie_product_type_wrapper">
                                                <div class="wpie_product_type_label"><?php esc_html_e( 'Product Type', 'vj-wp-import-export' ); ?></div>
                                                <div class="wpie_product_type_list">
                                                        <select class="wpie_content_data_select wpie_item_product_type" name="wpie_item_product_type">
                                                            <?php if ( !empty( $wpie_product_type ) ) { ?>
                                                                    <?php
                                                                    foreach ( $wpie_product_type as $key => $value ) {
                                                                            if ( $key == "as_specified" ) {
                                                                                    $chk = ' checked="checked" ';
                                                                            } else {
                                                                                    $chk = "";
                                                                            }

                                                                            ?>
                                                                                <option value="<?php echo esc_attr( $key ); ?>" <?php echo $chk; ?>> <?php echo esc_html( $value ); ?></option>
                                                                                <?php
                                                                                unset( $chk );
                                                                        }

                                                                        ?>
                                                                <?php } ?>
                                                        </select>
                                                </div>
                                                <div class="wpie_product_type_as_specified_wrapper">
                                                        <input class="wpie_content_data_input wpie_item_product_type_as_specified_data" type="text" name="wpie_item_product_type_as_specified_data" value="">
                                                </div>
                                                <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "The value should be one of the following: ('simple', 'grouped', 'external', 'variable').", "vj-wp-import-export" ); ?>"></i>
                                        </div>
                                        <div class="wpie_product_menu_wrapper">
                                                <div class="wpie_product_menu_list wpie_product_menu_general active_tab" display_block="wpie_product_general_wrapper"><?php esc_html_e( 'General', 'vj-wp-import-export' ); ?></div>
                                                <div class="wpie_product_menu_list wpie_product_menu_inventory" display_block="wpie_product_inventory_wrapper"><?php esc_html_e( 'Inventory', 'vj-wp-import-export' ); ?></div>
                                                <div class="wpie_product_menu_list wpie_product_menu_shipping" display_block="wpie_product_shipping_wrapper" ><?php esc_html_e( 'Shipping', 'vj-wp-import-export' ); ?></div>
                                                <div class="wpie_product_menu_list wpie_product_menu_linked_products" display_block="wpie_product_linked_products_wrapper"><?php esc_html_e( 'Linked Products', 'vj-wp-import-export' ); ?></div>
                                                <div class="wpie_product_menu_list wpie_product_menu_attributes" display_block="wpie_product_attributes_wrapper"><?php esc_html_e( 'Attributes', 'vj-wp-import-export' ); ?></div>
                                                <div class="wpie_product_menu_list wpie_product_menu_variations" display_block="wpie_product_variations_wrapper"><?php esc_html_e( 'Variations', 'vj-wp-import-export' ); ?></div>
                                                <div class="wpie_product_menu_list wpie_product_menu_advanced" display_block="wpie_product_advanced_wrapper"><?php esc_html_e( 'Advanced', 'vj-wp-import-export' ); ?></div>
                                        </div>
                                        <div class="wpie_product_content_wrapper">
                                                <div class="wpie_product_data_container wpie_product_general_wrapper">
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php esc_html_e( 'SKU', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_meta_sku" type="text" name="wpie_item_meta_sku" value="">
                                                                                <div class="wpie_item_auto_generate_sku_wrapper ">
                                                                                        <input type="checkbox" value="1" name="wpie_item_auto_generate_sku" id="wpie_item_auto_generate_sku"  class="wpie_checkbox wpie_item_auto_generate_sku">
                                                                                        <label class="wpie_checkbox_label" for="wpie_item_auto_generate_sku"><?php esc_html_e( 'Auto generate sku if sku data is empty', 'vj-wp-import-export' ); ?></label>
                                                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Plugin will automatically generate the SKU for each product, if SKU data is empty.", "vj-wp-import-export" ); ?>"></i>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html( __( 'Regular Price', 'vj-wp-import-export' ) . " (" . get_woocommerce_currency_symbol() . ")" ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_meta_regular_price" type="text" name="wpie_item_meta_regular_price" value="">
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html( __( 'Sale Price', 'vj-wp-import-export' ) . " (" . get_woocommerce_currency_symbol() . ")" ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_meta_sale_price" type="text" name="wpie_item_meta_sale_price" value="">
                                                                        </div>
                                                                        <div class="wpie_schedule_price_link"><?php esc_html_e( 'Schedule', 'vj-wp-import-export' ); ?></div>
                                                                </div>
                                                                <div class="wpie_product_schedule_price_wrapper">
                                                                        <div class="wpie_product_element_wrapper">
                                                                                <div class="wpie_product_element_data_lable"><?php esc_html_e( 'Sale price dates', 'vj-wp-import-export' ); ?></div>
                                                                                <div class="wpie_product_element_data">
                                                                                        <input class="wpie_content_data_input wpie_item_meta_sale_price_dates_from" type="text" name="wpie_item_meta_sale_price_dates_from" value="" placeholder="<?php echo esc_attr_e( 'From', 'vj-wp-import-export' ); ?>">
                                                                                </div>
                                                                        </div>
                                                                        <div class="wpie_product_element_wrapper">
                                                                                <div class="wpie_product_element_data_lable"></div>
                                                                                <div class="wpie_product_element_data">
                                                                                        <input class="wpie_content_data_input wpie_item_meta_sale_price_dates_to" type="text" name="wpie_item_meta_sale_price_dates_to" value="" placeholder="<?php echo esc_attr_e( 'To', 'vj-wp-import-export' ); ?>">
                                                                                </div>
                                                                                <div class="wpie_schedule_price_cancel_link"><?php esc_html_e( 'Cancel', 'vj-wp-import-export' ); ?></div>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php esc_html_e( 'Product URL', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_meta_product_url" type="text" name="wpie_item_meta_product_url" value="">
                                                                        </div>
                                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "The external/affiliate link URL to the product.", "vj-wp-import-export" ); ?>"></i>
                                                                </div>
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php esc_html_e( 'Button text', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_meta_button_text" type="text" name="wpie_item_meta_button_text" value="">
                                                                        </div>
                                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "This text will be shown on the button linking to the external product.", "vj-wp-import-export" ); ?>"></i>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio  wpie_item_meta_virtual"  name="wpie_item_meta_virtual" id="wpie_item_meta_virtual_yes" value="yes"/>
                                                                                <label for="wpie_item_meta_virtual_yes" class="wpie_radio_label"><?php esc_html_e( 'Virtual', 'vj-wp-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio  wpie_item_meta_virtual" checked="checked" name="wpie_item_meta_virtual" id="wpie_item_meta_virtual_no" value="no"/>
                                                                                <label for="wpie_item_meta_virtual_no" class="wpie_radio_label"><?php esc_html_e( 'Not Virtual', 'vj-wp-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_virtual wpie_item_meta_virtual_as_specified" name="wpie_item_meta_virtual" id="wpie_item_meta_virtual_as_specified" value="as_specified"/>
                                                                                <label for="wpie_item_meta_virtual_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                                                <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                                                        <input type="text" class="wpie_content_data_input wpie_item_meta_virtual_as_specified_data" name="wpie_item_meta_virtual_as_specified_data" value=""/>
                                                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "The value should be one of the following: ('yes', 'no').", "vj-wp-import-export" ); ?>"></i>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_downloadable"  name="wpie_item_meta_downloadable" id="wpie_item_meta_downloadable_yes" value="yes"/>
                                                                                <label for="wpie_item_meta_downloadable_yes" class="wpie_radio_label"><?php esc_html_e( 'Downloadable', 'vj-wp-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_downloadable" checked="checked" name="wpie_item_meta_downloadable" id="wpie_item_meta_downloadable_no" value="no"/>
                                                                                <label for="wpie_item_meta_downloadable_no" class="wpie_radio_label"><?php esc_html_e( 'Not Downloadable', 'vj-wp-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_downloadable wpie_item_meta_downloadable_as_specified" name="wpie_item_meta_downloadable" id="wpie_item_meta_downloadable_as_specified" value="as_specified"/>
                                                                                <label for="wpie_item_meta_downloadable_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                                                <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                                                        <input type="text" class="wpie_content_data_input wpie_item_meta_downloadable_as_specified_data" name="wpie_item_meta_downloadable_as_specified_data" value=""/>
                                                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "The value should be one of the following: ('yes', 'no').", "vj-wp-import-export" ); ?>"></i>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_product_element_data_container wpie_product_downloadable_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_wrapper">
                                                                                <div class="wpie_product_element_data_lable"><?php esc_html_e( 'File URL', 'vj-wp-import-export' ); ?></div>
                                                                                <div class="wpie_product_element_data">
                                                                                        <input class="wpie_content_data_input wpie_item_meta_downloadable_files" type="text" name="wpie_item_meta_downloadable_files" value="">
                                                                                </div>
                                                                                <div class="wpie_product_element_option_data">
                                                                                        <input class="wpie_content_data_input wpie_item_downloadable_files_delim" type="text" name="wpie_item_downloadable_files_delim" value=",">
                                                                                </div>
                                                                                <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Multiple File paths/URLs are comma separated. i.e. <code>http://files.com/1.doc, http://files.com/2.doc</code>.", "vj-wp-import-export" ); ?>"></i>
                                                                        </div>
                                                                        <div class="wpie_product_element_wrapper">
                                                                                <div class="wpie_product_element_data_lable"><?php esc_html_e( 'File Name', 'vj-wp-import-export' ); ?></div>
                                                                                <div class="wpie_product_element_data">
                                                                                        <input class="wpie_content_data_input wpie_item_meta_downloadable_file_name" type="text" name="wpie_item_meta_downloadable_file_name" value="">
                                                                                </div>
                                                                                <div class="wpie_product_element_option_data">
                                                                                        <input class="wpie_content_data_input wpie_item_downloadable_file_name_delim" type="text" name="wpie_item_downloadable_file_name_delim" value=",">
                                                                                </div>
                                                                                <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Multiple File names are comma separated. i.e. <code>1.doc,2.doc</code>.", "vj-wp-import-export" ); ?>"></i>
                                                                        </div>
                                                                        <div class="wpie_product_element_wrapper">
                                                                                <div class="wpie_product_element_data_lable"><?php esc_html_e( 'Download Limit', 'vj-wp-import-export' ); ?></div>
                                                                                <div class="wpie_product_element_data">
                                                                                        <input class="wpie_content_data_input wpie_item_meta_download_limit" type="text" name="wpie_item_meta_download_limit" value="">
                                                                                </div>
                                                                                <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Leave blank for unlimited re-downloads.", "vj-wp-import-export" ); ?>"></i>
                                                                        </div>
                                                                        <div class="wpie_product_element_wrapper">
                                                                                <div class="wpie_product_element_data_lable"><?php esc_html_e( 'Download Expiry', 'vj-wp-import-export' ); ?></div>
                                                                                <div class="wpie_product_element_data">
                                                                                        <input class="wpie_content_data_input wpie_item_meta_download_expiry" type="text" name="wpie_item_meta_download_expiry" value="">
                                                                                </div>
                                                                                <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Enter the number of days before a download link expires, or leave blank.", "vj-wp-import-export" ); ?>"></i>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Tax Status', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_tax_status" checked="checked" name="wpie_item_meta_tax_status" id="wpie_item_meta_tax_status_none" value="none"/>
                                                                                <label for="wpie_item_meta_tax_status_none" class="wpie_radio_label"><?php esc_html_e( 'None', 'vj-wp-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_tax_status"  name="wpie_item_meta_tax_status" id="wpie_item_meta_tax_status_taxable" value="taxable"/>
                                                                                <label for="wpie_item_meta_tax_status_taxable" class="wpie_radio_label"><?php esc_html_e( 'Taxable', 'vj-wp-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_tax_status" name="wpie_item_meta_tax_status" id="wpie_item_meta_tax_status_shipping" value="shipping"/>
                                                                                <label for="wpie_item_meta_tax_status_shipping" class="wpie_radio_label"><?php esc_html_e( 'Shipping only', 'vj-wp-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_tax_status wpie_item_meta_tax_status_as_specified" name="wpie_item_meta_tax_status" id="wpie_item_meta_tax_status_as_specified" value="as_specified"/>
                                                                                <label for="wpie_item_meta_tax_status_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                                                <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                                                        <input type="text" class="wpie_content_data_input wpie_item_meta_tax_status_as_specified_data" name="wpie_item_meta_tax_status_as_specified_data" value=""/>
                                                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Value should be the slug for the tax status - 'taxable', 'shipping', and 'none' are the default slugs.", "vj-wp-import-export" ); ?>"></i>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Tax Class', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <select class="wpie_content_data_select wpie_item_dropdown_as_specified wpie_item_meta_tax_class" name="wpie_item_meta_tax_class">
                                                                                        <option value="" ><?php esc_html_e( 'Standard', 'vj-wp-import-export' ); ?></option>
                                                                                        <?php
                                                                                        if ( !empty( $wpie_product_tax_class ) ) {

                                                                                                foreach ( $wpie_product_tax_class as $class ) {

                                                                                                        ?>
                                                                                                        <option value="<?php echo esc_attr( sanitize_title( $class ) ); ?>" > <?php echo esc_html( $class ); ?></option>
                                                                                                <?php } ?>
                                                                                        <?php } ?>
                                                                                        <option value="as_specified" ><?php esc_html_e( 'As Specified', 'vj-wp-import-export' ); ?></option>
                                                                                </select>
                                                                                <div class="wpie_item_as_specified_wrapper wpie_as_specified_wrapper wpie_hide">
                                                                                        <input type="text" class="wpie_content_data_input wpie_item_meta_tax_class_as_specified_data" name="wpie_item_meta_tax_class_as_specified_data" value=""/>
                                                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Value should be the slug for the tax class - 'reduced-rate' and 'zero-rate', are the default slugs.", "vj-wp-import-export" ); ?>"></i>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                </div>
                                                <div class="wpie_product_data_container wpie_product_inventory_wrapper">
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Manage stock?', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_manage_stock" name="wpie_item_meta_manage_stock" id="wpie_item_meta_manage_stock_yes" value="yes"/>
                                                                                <label for="wpie_item_meta_manage_stock_yes" class="wpie_radio_label"><?php esc_html_e( 'Yes', 'vj-wp-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper ">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_manage_stock" name="wpie_item_meta_manage_stock"  checked="checked" id="wpie_item_meta_manage_stock_no" value="no"/>
                                                                                <label for="wpie_item_meta_manage_stock_no" class="wpie_radio_label"><?php esc_html_e( 'No', 'vj-wp-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_manage_stock wpie_item_meta_manage_stock_as_specified" name="wpie_item_meta_manage_stock" id="wpie_item_meta_manage_stock_as_specified" value="as_specified"/>
                                                                                <label for="wpie_item_meta_manage_stock_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                                                <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                                                        <input type="text" class="wpie_content_data_input wpie_item_meta_manage_stock_as_specified_data" name="wpie_item_meta_manage_stock_as_specified_data" value=""/>
                                                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "The value should be one of the following: ('yes', 'no').", "vj-wp-import-export" ); ?>"></i>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_product_element_data_container wpie_product_stock_qty_container ">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_wrapper">
                                                                                <div class="wpie_product_element_data_lable"><?php esc_html_e( 'Stock Qty', 'vj-wp-import-export' ); ?></div>
                                                                                <div class="wpie_product_element_data">
                                                                                        <input class="wpie_content_data_input wpie_item_meta_stock" type="text" name="wpie_item_meta_stock" value="">
                                                                                </div>
                                                                        </div>
                                                                        <div class="wpie_product_element_wrapper">
                                                                                <div class="wpie_product_element_data_lable"><?php esc_html_e( 'Low stock threshold', 'vj-wp-import-export' ); ?></div>
                                                                                <div class="wpie_product_element_data">
                                                                                        <input class="wpie_content_data_input wpie_item_meta_low_stock_amount" type="text" name="wpie_item_meta_low_stock_amount" value="">
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Stock status', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_stock_status" name="wpie_item_meta_stock_status" id="wpie_item_meta_stock_status_instock" value="instock"/>
                                                                                <label for="wpie_item_meta_stock_status_instock" class="wpie_radio_label"><?php esc_html_e( 'In stock', 'vj-wp-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_stock_status" name="wpie_item_meta_stock_status" id="wpie_item_meta_stock_status_outofstock" value="outofstock"/>
                                                                                <label for="wpie_item_meta_stock_status_outofstock" class="wpie_radio_label"><?php esc_html_e( 'Out of stock', 'vj-wp-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_stock_status" name="wpie_item_meta_stock_status"  checked="checked" id="wpie_item_meta_stock_status_auto" value="auto"/>
                                                                                <label for="wpie_item_meta_stock_status_auto" class="wpie_radio_label"><?php esc_html_e( 'Set automatically', 'vj-wp-import-export' ); ?></label>
                                                                                <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Set the stock status to In Stock for positive or blank Stock Qty values, and Out Of Stock if Stock Qty is 0.", "vj-wp-import-export" ); ?>"></i>
                                                                        </div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_stock_status wpie_item_meta_stock_status_as_specified" name="wpie_item_meta_stock_status" id="wpie_item_meta_stock_status_as_specified" value="as_specified"/>
                                                                                <label for="wpie_item_meta_stock_status_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                                                <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                                                        <input type="text" class="wpie_content_data_input wpie_item_meta_stock_status_as_specified_data" name="wpie_item_meta_stock_status_as_specified_data" value=""/>
                                                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "The value should be one of the following: ('instock', 'outofstock').", "vj-wp-import-export" ); ?>"></i>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Allow Backorders?', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_backorders" name="wpie_item_meta_backorders"  checked="checked" id="wpie_item_meta_backorders_no" value="no"/>
                                                                                <label for="wpie_item_meta_backorders_no" class="wpie_radio_label"><?php esc_html_e( 'Do not allow', 'vj-wp-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_backorders" name="wpie_item_meta_backorders" id="wpie_item_meta_backorders_notify" value="notify"/>
                                                                                <label for="wpie_item_meta_backorders_notify" class="wpie_radio_label"><?php esc_html_e( 'Allow, but notify customer', 'vj-wp-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_backorders" name="wpie_item_meta_backorders" id="wpie_item_meta_backorders_yes" value="yes"/>
                                                                                <label for="wpie_item_meta_backorders_yes" class="wpie_radio_label"><?php esc_html_e( 'Allow', 'vj-wp-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_backorders wpie_item_meta_backorders_as_specified" name="wpie_item_meta_backorders" id="wpie_item_meta_backorders_as_specified" value="as_specified"/>
                                                                                <label for="wpie_item_meta_backorders_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                                                <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                                                        <input type="text" class="wpie_content_data_input wpie_item_meta_backorders_as_specified_data" name="wpie_item_meta_backorders_as_specified_data" value=""/>
                                                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "The value of should be one of the following: ('no', 'notify', 'yes').", "vj-wp-import-export" ); ?>"></i>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Sold Individually?', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_sold_individually" name="wpie_item_meta_sold_individually" id="wpie_item_meta_sold_individually_yes" value="yes"/>
                                                                                <label for="wpie_item_meta_sold_individually_yes" class="wpie_radio_label"><?php esc_html_e( 'Yes', 'vj-wp-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_sold_individually" name="wpie_item_meta_sold_individually"  checked="checked" id="wpie_item_meta_sold_individually_no" value="no"/>
                                                                                <label for="wpie_item_meta_sold_individually_no" class="wpie_radio_label"><?php esc_html_e( 'No', 'vj-wp-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_sold_individually wpie_item_meta_sold_individually_as_specified" name="wpie_item_meta_sold_individually" id="wpie_item_meta_sold_individually_as_specified" value="as_specified"/>
                                                                                <label for="wpie_item_meta_sold_individually_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                                                <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                                                        <input type="text" class="wpie_content_data_input wpie_item_meta_sold_individually_as_specified_data" name="wpie_item_meta_sold_individually_as_specified_data" value=""/>
                                                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "The value should be one of the following: ('yes', 'no').", "vj-wp-import-export" ); ?>"></i>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                </div>
                                                <div class="wpie_product_data_container wpie_product_shipping_wrapper">
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html( __( 'Weight', 'vj-wp-import-export' ) . " (" . get_option( 'woocommerce_weight_unit' ) . ")" ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_meta_weight" type="text" name="wpie_item_meta_weight" value="">
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_product_element_wrapper wpie_product_dimensions">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html( __( 'Dimensions', 'vj-wp-import-export' ) . " (" . get_option( 'woocommerce_dimension_unit' ) . ")" ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_meta_length" type="text" name="wpie_item_meta_length"  placeholder="<?php esc_attr_e( 'Length', 'vj-wp-import-export' ); ?>" value="">
                                                                                <input class="wpie_content_data_input wpie_item_meta_width" type="text" name="wpie_item_meta_width"  placeholder="<?php esc_attr_e( 'Width', 'vj-wp-import-export' ); ?>" value="">
                                                                                <input class="wpie_content_data_input wpie_item_meta_height" type="text" name="wpie_item_meta_height"  placeholder="<?php esc_attr_e( 'Height', 'vj-wp-import-export' ); ?>" value="">
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_item_product_shipping_class_logic" name="wpie_item_product_shipping_class_logic" checked="checked" id="wpie_item_product_shipping_class_defined" value="defined"/>
                                                                        <label for="wpie_item_product_shipping_class_defined" class="wpie_radio_label"><?php esc_html_e( 'Shipping Class', 'vj-wp-import-export' ); ?></label>
                                                                        <div class="wpie_radio_container">
                                                                            <?php
                                                                            $args = array(
                                                                                    'taxonomy' => 'product_shipping_class',
                                                                                    'hide_empty' => 0,
                                                                                    'show_option_none' => __( 'No shipping class', 'vj-wp-import-export' ),
                                                                                    'name' => 'wpie_item_product_shipping_class',
                                                                                    'id' => 'wpie_item_product_shipping_class',
                                                                                    'selected' => "",
                                                                                    'class' => 'wpie_content_data_select wpie_item_product_shipping_class'
                                                                            );

                                                                            wp_dropdown_categories( $args );

                                                                            unset( $args );

                                                                            ?>
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_product_element_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_item_product_shipping_class_logic wpie_item_product_shipping_class_logic_as_specified" name="wpie_item_product_shipping_class_logic" id="wpie_item_product_shipping_class_logic_as_specified" value="as_specified"/>
                                                                        <label for="wpie_item_product_shipping_class_logic_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                                        <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                                                <input type="text" class="wpie_content_data_input wpie_item_product_shipping_class_as_specified_data" name="wpie_item_product_shipping_class_as_specified_data" value=""/>
                                                                                <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "The value should be one of the following: ('yes', 'no').", "vj-wp-import-export" ); ?>"></i>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                </div>
                                                <div class="wpie_product_data_container wpie_product_linked_products_wrapper">
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Up-Sells', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_meta_upsell_ids" type="text" name="wpie_item_meta_upsell_ids" value="">
                                                                        </div>
                                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Products can be matched by SKU, ID, or Title, and must be comma separated.", "vj-wp-import-export" ); ?>"></i>
                                                                </div>
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Cross-Sells', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_meta_crosssell_ids" type="text" name="wpie_item_meta_crosssell_ids" value="">
                                                                        </div>
                                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Products can be matched by SKU, ID, or Title, and must be comma separated.", "vj-wp-import-export" ); ?>"></i>
                                                                </div>

                                                        </div>
                                                </div>
                                                <div class="wpie_product_data_container wpie_product_attributes_wrapper">
                                                        <div class="wpie_product_element_data_container wpie_attr_data_wrapper">
                                                                <div class="wpie_attr_data_outer_container">
                                                                        <div class="wpie_attr_data_container">
                                                                                <div class="wpie_product_element_wrapper">
                                                                                        <div class="wpie_product_attr_data">
                                                                                                <div class="wpie_product_attr_data_label"><?php echo esc_html_e( 'Name', 'vj-wp-import-export' ); ?></div>
                                                                                                <input class="wpie_content_data_input wpie_product_attr_name" type="text" name="wpie_product_attr_name[0]" value="">
                                                                                        </div>
                                                                                        <div class="wpie_product_attr_data">
                                                                                                <div class="wpie_product_attr_data_label">
                                                                                                    <?php echo esc_html_e( 'Values', 'vj-wp-import-export' ); ?>
                                                                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Separate multiple values with a |", "vj-wp-import-export" ); ?>"></i>
                                                                                                </div>
                                                                                                <input class="wpie_content_data_input wpie_product_attr_value" type="text" name="wpie_product_attr_value[0]" value="">
                                                                                        </div>
                                                                                        <div class="wpie_delete_attr_wrapper"><i class="fas fa-trash wpie_trash_general_btn_icon wpie_delete_attr_data" aria-hidden="true"></i></div>
                                                                                </div>                                                                              
                                                                                <div class="wpie_product_element_wrapper">
                                                                                        <div class="wpie_product_attr_data">
                                                                                                <div class="wpie_product_attr_data_label"><?php echo esc_html_e( 'Slug', 'vj-wp-import-export' ); ?></div>
                                                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                                                        <input type="radio" class="wpie_radio  wpie_attr_slug" name="wpie_attr_slug[0]" checked="checked" id="wpie_attr_slug_auto_0" value="auto"/>
                                                                                                        <label for="wpie_attr_slug_auto_0" class="wpie_radio_label"><?php esc_html_e( 'Auto Create', 'vj-wp-import-export' ); ?></label>
                                                                                                </div>                                                                                                
                                                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                                                        <input type="radio" class="wpie_radio wpie_attr_slug wpie_attr_slug_as_specified" name="wpie_attr_slug[0]" id="wpie_attr_slug_as_specified_0" value="as_specified"/>
                                                                                                        <label for="wpie_attr_slug_as_specified_0" class="wpie_radio_label wpie_attr_slug_as_specified wpie_attr_slug_as_specified_as_specified"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                                                                        <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                                                                                <input type="text" class="wpie_content_data_input wpie_attr_slug_as_specified_data" name="wpie_attr_slug_as_specified_data[0]" value=""/>
                                                                                                        </div>
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="wpie_product_attr_data">
                                                                                                <div class="wpie_product_attr_data_label"><?php echo esc_html_e( 'Used for variations', 'vj-wp-import-export' ); ?></div>
                                                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                                                        <input type="radio" class="wpie_radio  wpie_attr_in_variations" name="wpie_attr_in_variations[0]" checked="checked" id="wpie_attr_in_variations_yes_0" value="yes"/>
                                                                                                        <label for="wpie_attr_in_variations_yes_0" class="wpie_radio_label wpie_attr_in_variations"><?php esc_html_e( 'Yes', 'vj-wp-import-export' ); ?></label>
                                                                                                </div>
                                                                                                <div class="wpie_field_mapping_other_option_wrapper ">
                                                                                                        <input type="radio" class="wpie_radio wpie_attr_in_variations" name="wpie_attr_in_variations[0]"  id="wpie_attr_in_variations_no_0" value="no"/>
                                                                                                        <label for="wpie_attr_in_variations_no_0" class="wpie_radio_label wpie_attr_in_variations"><?php esc_html_e( 'No', 'vj-wp-import-export' ); ?></label>
                                                                                                </div>
                                                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                                                        <input type="radio" class="wpie_radio wpie_attr_in_variations wpie_attr_in_variations_as_specified" name="wpie_attr_in_variations[0]" id="wpie_attr_in_variations_as_specified_0" value="as_specified"/>
                                                                                                        <label for="wpie_attr_in_variations_as_specified_0" class="wpie_radio_label wpie_attr_in_variations wpie_attr_in_variations_as_specified">
                                                                                                            <?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?>
                                                                                                                <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "The value should be one of the following: ('yes', 'no').", "vj-wp-import-export" ); ?>"></i>
                                                                                                        </label>
                                                                                                        <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                                                                                <input type="text" class="wpie_content_data_input wpie_attr_in_variations_as_specified_data" name="wpie_attr_in_variations_as_specified_data[0]" value=""/>                                                                                                                
                                                                                                        </div>
                                                                                                </div>
                                                                                        </div>

                                                                                </div>
                                                                                <div class="wpie_product_element_wrapper">
                                                                                        <div class="wpie_product_attr_data">
                                                                                                <div class="wpie_product_attr_data_label"><?php echo esc_html_e( 'Is Visible', 'vj-wp-import-export' ); ?></div>
                                                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                                                        <input type="radio" class="wpie_radio wpie_attr_is_visible" name="wpie_attr_is_visible[0]" checked="checked" id="wpie_attr_is_visible_yes_0" value="yes"/>
                                                                                                        <label for="wpie_attr_is_visible_yes_0" class="wpie_radio_label"><?php esc_html_e( 'Yes', 'vj-wp-import-export' ); ?></label>
                                                                                                </div>
                                                                                                <div class="wpie_field_mapping_other_option_wrapper ">
                                                                                                        <input type="radio" class="wpie_radio wpie_attr_is_visible" name="wpie_attr_is_visible[0]" id="wpie_attr_is_visible_no_0" value="no"/>
                                                                                                        <label for="wpie_attr_is_visible_no_0" class="wpie_radio_label"><?php esc_html_e( 'No', 'vj-wp-import-export' ); ?></label>
                                                                                                </div>
                                                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                                                        <input type="radio" class="wpie_radio  wpie_attr_is_visible wpie_attr_is_visible_as_specified" name="wpie_attr_is_visible[0]" id="wpie_attr_is_visible_as_specified_0" value="as_specified"/>
                                                                                                        <label for="wpie_attr_is_visible_as_specified_0" class="wpie_radio_label">
                                                                                                            <?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?>
                                                                                                                <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "The value should be one of the following: ('yes', 'no').", "vj-wp-import-export" ); ?>"></i>
                                                                                                        </label>
                                                                                                        <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                                                                                <input type="text" class="wpie_content_data_input wpie_attr_is_visible_as_specified_data" name="wpie_attr_is_visible_as_specified_data[0]" value=""/>                                                                                                                
                                                                                                        </div>
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="wpie_product_attr_data">
                                                                                                <div class="wpie_product_attr_data_label"><?php echo esc_html_e( 'Is Taxonomy', 'vj-wp-import-export' ); ?></div>
                                                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                                                        <input type="radio" class="wpie_radio wpie_attr_is_taxonomy" name="wpie_attr_is_taxonomy[0]" checked="checked" id="wpie_attr_is_taxonomy_yes_0" value="yes"/>
                                                                                                        <label for="wpie_attr_is_taxonomy_yes_0" class="wpie_radio_label"><?php esc_html_e( 'Yes', 'vj-wp-import-export' ); ?></label>
                                                                                                </div>
                                                                                                <div class="wpie_field_mapping_other_option_wrapper ">
                                                                                                        <input type="radio" class="wpie_radio wpie_attr_is_taxonomy" name="wpie_attr_is_taxonomy[0]" id="wpie_attr_is_taxonomy_no_0" value="no"/>
                                                                                                        <label for="wpie_attr_is_taxonomy_no_0" class="wpie_radio_label"><?php esc_html_e( 'No', 'vj-wp-import-export' ); ?></label>
                                                                                                </div>
                                                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                                                        <input type="radio" class="wpie_radio wpie_attr_is_taxonomy wpie_attr_is_taxonomy_as_specified" name="wpie_attr_is_taxonomy[0]" id="wpie_attr_is_taxonomy_as_specified_0" value="as_specified"/>
                                                                                                        <label for="wpie_attr_is_taxonomy_as_specified_0" class="wpie_radio_label">
                                                                                                            <?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?>
                                                                                                                <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "The value should be one of the following: ('yes', 'no').", "vj-wp-import-export" ); ?>"></i>
                                                                                                        </label>
                                                                                                        <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                                                                                <input type="text" class="wpie_content_data_input wpie_attr_is_taxonomy_as_specified_data" name="wpie_attr_is_taxonomy_as_specified_data[0]" value=""/>                                                                                                                
                                                                                                        </div>
                                                                                                </div>
                                                                                        </div>

                                                                                </div>
                                                                                <div class="wpie_product_element_wrapper">
                                                                                        <div class="wpie_product_attr_data">
                                                                                                <div class="wpie_product_attr_data_label"><?php echo esc_html_e( 'Auto-Create Terms', 'vj-wp-import-export' ); ?></div>
                                                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                                                        <input type="radio" class="wpie_radio  wpie_attr_is_auto_create_term" name="wpie_attr_is_auto_create_term[0]" checked="checked" id="wpie_attr_is_auto_create_term_yes_0" value="yes"/>
                                                                                                        <label for="wpie_attr_is_auto_create_term_yes_0" class="wpie_radio_label"><?php esc_html_e( 'Yes', 'vj-wp-import-export' ); ?></label>
                                                                                                </div>
                                                                                                <div class="wpie_field_mapping_other_option_wrapper ">
                                                                                                        <input type="radio" class="wpie_radio wpie_attr_is_auto_create_term" name="wpie_attr_is_auto_create_term[0]"  id="wpie_attr_is_auto_create_term_no_0" value="no"/>
                                                                                                        <label for="wpie_attr_is_auto_create_term_no_0" class="wpie_radio_label"><?php esc_html_e( 'No', 'vj-wp-import-export' ); ?></label>
                                                                                                </div>
                                                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                                                        <input type="radio" class="wpie_radio wpie_attr_is_auto_create_term wpie_attr_is_auto_create_term_as_specified" name="wpie_attr_is_auto_create_term[0]" id="wpie_attr_is_auto_create_term_as_specified_0" value="as_specified"/>
                                                                                                        <label for="wpie_attr_is_auto_create_term_as_specified_0" class="wpie_radio_label">
                                                                                                            <?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?>
                                                                                                                <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "The value should be one of the following: ('yes', 'no').", "vj-wp-import-export" ); ?>"></i>
                                                                                                        </label>
                                                                                                        <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                                                                                <input type="text" class="wpie_content_data_input wpie_attr_is_auto_create_term_as_specified_data" name="wpie_attr_is_auto_create_term_as_specified_data[0]" value=""/>

                                                                                                        </div>
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="wpie_product_attr_data">
                                                                                                <div class="wpie_product_attr_data_label"><?php echo esc_html_e( 'Position', 'vj-wp-import-export' ); ?></div>
                                                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                                                        <input type="radio" class="wpie_radio  wpie_attr_position wpie_attr_position_auto" name="wpie_attr_position[0]" checked="checked" id="wpie_attr_position_0" value="auto"/>
                                                                                                        <label for="wpie_attr_position_0" class="wpie_radio_label"><?php esc_html_e( 'Auto', 'vj-wp-import-export' ); ?></label>
                                                                                                </div>                                                                                                
                                                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                                                        <input type="radio" class="wpie_radio wpie_attr_position wpie_attr_position_as_specified" name="wpie_attr_position[0]" id="wpie_attr_position_as_specified_0" value="as_specified"/>
                                                                                                        <label for="wpie_attr_position_as_specified_0" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                                                                        <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                                                                                <input type="text" class="wpie_content_data_input wpie_attr_position_as_specified_data" name="wpie_attr_position_as_specified_data[0]" value=""/>
                                                                                                        </div>
                                                                                                </div>
                                                                                        </div>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_btn wpie_btn_primary wpie_import_attr_add_more_btn">
                                                                                <i class="fas fa-plus wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Add More', 'vj-wp-import-export' ); ?>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                </div>
                                                <div class="wpie_product_data_container wpie_product_variations_wrapper">
                                                        <div class="wpie_product_element_data_container wpie_variation_import_method_wrapper">
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_item_variation_import_method wpie_item_variation_import_method_attributes" name="wpie_item_variation_import_method" id="wpie_item_variation_import_method_attributes" value="attributes" checked="checked"/>
                                                                        <label for="wpie_item_variation_import_method_attributes" class="wpie_radio_label"><?php esc_html_e( "Create variations from all attributes", 'vj-wp-import-export' ); ?></label>
                                                                        <div class="wpie_radio_container">
                                                                                <div class="wpie_product_element_wrapper">
                                                                                        <div class="wpie_variation_example_wrapper">
                                                                                                <?php echo esc_html_e( 'Example Data For Use With This Option', 'vj-wp-import-export' ); ?> - 
                                                                                                <a class="wpie_variation_example_data" target="_blank" href="http://plugins.vjinfotech.com/wordpress-import-export/wp-content/uploads/2019/06/Create-variations-from-all-attributes.csv"><?php echo esc_html_e( 'Download', 'vj-wp-import-export' ); ?></a>
                                                                                        </div>
                                                                                        <div class="wpie_product_variation_image_wrapper">
                                                                                                <img class="wpie_product_variation_image" src="<?php echo esc_url( WPIE_IMPORT_ADDON_URL . '/wc/product/images/Create variations from all attributes.png' ); ?>"/>
                                                                                        </div>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_item_variation_import_method wpie_item_variation_import_method_match_unique_field" name="wpie_item_variation_import_method" id="wpie_item_variation_import_method_match_unique_field" value="match_unique_field"/>
                                                                        <label for="wpie_item_variation_import_method_match_unique_field" class="wpie_radio_label"><?php esc_html_e( "All my variable products have SKUs or some other unique identifier. Each variation is linked to its parent with its parent's SKU or other unique identifier.", 'vj-wp-import-export' ); ?></label>
                                                                        <div class="wpie_radio_container">
                                                                                <div class="wpie_product_element_wrapper">
                                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'SKU element for parent', 'vj-wp-import-export' ); ?></div>
                                                                                        <div class="wpie_product_element_data">
                                                                                                <input class="wpie_content_data_input wpie_item_product_variation_field_parent" type="text" name="wpie_item_product_variation_field_parent" value="">
                                                                                        </div>
                                                                                </div>
                                                                                <div class="wpie_product_element_wrapper">
                                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Parent SKU element for variation', 'vj-wp-import-export' ); ?></div>
                                                                                        <div class="wpie_product_element_data">
                                                                                                <input class="wpie_content_data_input wpie_item_product_variation_match_unique_field_parent" type="text" name="wpie_item_product_variation_match_unique_field_parent" value="">
                                                                                        </div>
                                                                                </div>
                                                                                <div class="wpie_product_element_wrapper">
                                                                                        <div class="wpie_variation_example_wrapper">
                                                                                                <?php echo esc_html_e( 'Example Data For Use With This Option', 'vj-wp-import-export' ); ?> - 
                                                                                                <a class="wpie_variation_example_data" target="_blank"  href="http://plugins.vjinfotech.com/wordpress-import-export/wp-content/uploads/2019/06/Create-Variations-To-Parent-Based-On-Parent-SKU.csv"><?php echo esc_html_e( 'Download', 'vj-wp-import-export' ); ?></a>
                                                                                        </div>
                                                                                        <div class="wpie_product_variation_image_wrapper">
                                                                                                <img class="wpie_product_variation_image" src="<?php echo esc_url( WPIE_IMPORT_ADDON_URL . '/wc/product/images/Create Variations To Parent Based On Parent SKU.png' ); ?>"/>
                                                                                        </div>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_item_variation_import_method wpie_item_variation_import_method_match_group_field" name="wpie_item_variation_import_method" id="wpie_item_variation_import_method_match_group_field" value="match_group_field"/>
                                                                        <label for="wpie_item_variation_import_method_match_group_field" class="wpie_radio_label"><?php esc_html_e( "All products with variations are grouped with a unique value that is the same for each variation and unique for each product.", 'vj-wp-import-export' ); ?></label>
                                                                        <div class="wpie_radio_container">
                                                                                <div class="wpie_product_element_wrapper">
                                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Unique Value', 'vj-wp-import-export' ); ?></div>
                                                                                        <div class="wpie_product_element_data">
                                                                                                <input class="wpie_content_data_input wpie_item_product_variation_match_group_field" type="text" name="wpie_item_product_variation_match_group_field" value="">
                                                                                        </div>
                                                                                </div>
                                                                                <div class="wpie_product_element_wrapper">
                                                                                        <div class="wpie_variation_example_wrapper">
                                                                                                <?php echo esc_html_e( 'Example Data For Use With This Option', 'vj-wp-import-export' ); ?> - 
                                                                                                <a class="wpie_variation_example_data" target="_blank" href="http://plugins.vjinfotech.com/wordpress-import-export/wp-content/uploads/2019/06/Product-Variations-Grouped-By-A-Unique-Identifier.csv"><?php echo esc_html_e( 'Download', 'vj-wp-import-export' ); ?></a>
                                                                                        </div>
                                                                                        <div class="wpie_product_variation_image_wrapper">
                                                                                                <img class="wpie_product_variation_image" src="<?php echo esc_url( WPIE_IMPORT_ADDON_URL . '/wc/product/images/Product Variations Grouped By A Unique Identifier.png' ); ?>"/>
                                                                                        </div>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_item_variation_import_method wpie_item_variation_import_method_match_title_field" name="wpie_item_variation_import_method" id="wpie_item_variation_import_method_match_title_field" value="match_title_field"/>
                                                                        <label for="wpie_item_variation_import_method_match_title_field" class="wpie_radio_label"><?php esc_html_e( "All variations for a particular product have the same title as the parent product.", 'vj-wp-import-export' ); ?></label>
                                                                        <div class="wpie_radio_container">
                                                                                <div class="wpie_product_element_wrapper">
                                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Product Title', 'vj-wp-import-export' ); ?></div>
                                                                                        <div class="wpie_product_element_data">
                                                                                                <input class="wpie_content_data_input wpie_item_variation_import_method_title_field" type="text" name="wpie_item_variation_import_method_title_field" value="">
                                                                                        </div>
                                                                                </div>
                                                                                <div class="wpie_product_element_wrapper">
                                                                                        <div class="wpie_variation_example_wrapper">
                                                                                                <?php echo esc_html_e( 'Example Data For Use With This Option', 'vj-wp-import-export' ); ?> - 
                                                                                                <a class="wpie_variation_example_data" target="_blank"  href="http://plugins.vjinfotech.com/wordpress-import-export/wp-content/uploads/2019/06/Variations-Grouped-By-Title.csv"><?php echo esc_html_e( 'Download', 'vj-wp-import-export' ); ?></a>
                                                                                        </div>
                                                                                        <div class="wpie_product_variation_image_wrapper">
                                                                                                <img class="wpie_product_variation_image" src="<?php echo esc_url( WPIE_IMPORT_ADDON_URL . '/wc/product/images/Variations Grouped By Title.png' ); ?>"/>
                                                                                        </div>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_item_variation_import_method wpie_item_variation_import_method_match_title_field_no_parent" name="wpie_item_variation_import_method" id="wpie_item_variation_import_method_match_title_field_no_parent" value="match_title_field_no_parent"/>
                                                                        <label for="wpie_item_variation_import_method_match_title_field_no_parent" class="wpie_radio_label"><?php esc_html_e( "All variations for a particular product have the same title. There are no parent products.", 'vj-wp-import-export' ); ?></label>
                                                                        <div class="wpie_radio_container">
                                                                                <div class="wpie_product_element_wrapper">
                                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Product Title', 'vj-wp-import-export' ); ?></div>
                                                                                        <div class="wpie_product_element_data">
                                                                                                <input class="wpie_content_data_input wpie_item_variation_import_method_title_field_no_parent" type="text" name="wpie_item_variation_import_method_title_field_no_parent" value="">
                                                                                        </div>
                                                                                </div>
                                                                                <div class="wpie_product_element_wrapper">
                                                                                        <div class="wpie_variation_example_wrapper">
                                                                                                <?php echo esc_html_e( 'Example Data For Use With This Option', 'vj-wp-import-export' ); ?> - 
                                                                                                <a class="wpie_variation_example_data" target="_blank"  href="http://plugins.vjinfotech.com/wordpress-import-export/wp-content/uploads/2019/06/Variations-Grouped-By-Title-No-Parent-Products.csv"><?php echo esc_html_e( 'Download', 'vj-wp-import-export' ); ?></a>
                                                                                        </div>
                                                                                        <div class="wpie_product_variation_image_wrapper">
                                                                                                <img class="wpie_product_variation_image" src="<?php echo esc_url( WPIE_IMPORT_ADDON_URL . '/wc/product/images/Variations Grouped By Title No Parent Products.png' ); ?>"/>
                                                                                        </div>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Variation Enabled', 'vj-wp-import-export' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "This option is the same as the Enabled checkbox when editing an individual variation in WooCommerce.", "vj-wp-import-export" ); ?>"></i></div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_variation_enable wpie_item_variation_enable_yes" name="wpie_item_variation_enable"  checked="checked" id="wpie_item_variation_enable_yes" value="yes"/>
                                                                                <label for="wpie_item_variation_enable_yes" class="wpie_radio_label"><?php esc_html_e( 'Yes', 'vj-wp-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_variation_enable wpie_item_variation_enable_no" name="wpie_item_variation_enable" id="wpie_item_variation_enable_no" value="no"/>
                                                                                <label for="wpie_item_variation_enable_no" class="wpie_radio_label"><?php esc_html_e( 'No', 'vj-wp-import-export' ); ?></label>
                                                                        </div>

                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_variation_enable wpie_item_variation_enable_as_specified" name="wpie_item_variation_enable" id="wpie_item_variation_enable_as_specified" value="as_specified"/>
                                                                                <label for="wpie_item_variation_enable_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                                                <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                                                        <input type="text" class="wpie_content_data_input wpie_item_variation_enable_as_specified_data" name="wpie_item_variation_enable_as_specified_data" value=""/>
                                                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "The value should be one of the following: ('yes', 'no').", "vj-wp-import-export" ); ?>"></i>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Variation Description', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_meta_variation_description" type="text" name="wpie_item_meta_variation_description" value="">
                                                                        </div>
                                                                </div>                                                                
                                                        </div>
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_field_mapping_other_option_wrapper ">
                                                                                <input type="checkbox" value="1" name="wpie_item_first_variation_as_default" id="wpie_item_first_variation_as_default" checked="checked" class="wpie_checkbox wpie_item_first_variation_as_default">
                                                                                <label class="wpie_checkbox_label" for="wpie_item_first_variation_as_default"><?php esc_html_e( 'Set first variation as the default selection.', 'vj-wp-import-export' ); ?></label>
                                                                                <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "The attributes for the first variation will be automatically selected on the frontend.", "vj-wp-import-export" ); ?>"></i>
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_field_mapping_other_option_wrapper ">
                                                                                <input type="checkbox" value="1" name="wpie_item_set_image_parent_gallery" id="wpie_item_set_image_parent_gallery" checked="checked" class="wpie_checkbox wpie_item_set_image_parent_gallery">
                                                                                <label class="wpie_checkbox_label" for="wpie_item_set_image_parent_gallery"><?php esc_html_e( 'Save variations Extra image to Product the gallery.', 'vj-wp-import-export' ); ?></label>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                </div>
                                                <div class="wpie_product_data_container wpie_product_advanced_wrapper">
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Purchase Note', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_meta_purchase_note" type="text" name="wpie_item_meta_purchase_note" value="">
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Featured', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_featured" name="wpie_item_meta_featured" id="is_product_featured_yes" value="yes"/>
                                                                                <label for="is_product_featured_yes" class="wpie_radio_label"><?php esc_html_e( 'Yes', 'vj-wp-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper ">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_featured" name="wpie_item_meta_featured"  checked="checked" id="is_product_featured_no" value="no"/>
                                                                                <label for="is_product_featured_no" class="wpie_radio_label"><?php esc_html_e( 'No', 'vj-wp-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_featured wpie_item_meta_featured_as_specified" name="wpie_item_meta_featured" id="is_product_featured_as_specified" value="as_specified"/>
                                                                                <label for="is_product_featured_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                                                <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                                                        <input type="text" class="wpie_content_data_input wpie_item_meta_featured_as_specified_data" name="wpie_item_meta_featured_as_specified_data" value=""/>
                                                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "The value should be one of the following: ('yes', 'no').", "vj-wp-import-export" ); ?>"></i>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Catalog visibility', 'vj-wp-import-export' ); ?></div>
                                                                        <?php
                                                                        if ( function_exists( 'wc_get_product_visibility_options' ) ) {

                                                                                $visibility_options = wc_get_product_visibility_options();

                                                                                if ( !empty( $visibility_options ) ) {
                                                                                        $check = 'checked="checked"';
                                                                                        foreach ( $visibility_options as $visibility_option_key => $visibility_option_name ) {

                                                                                                ?>
                                                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                                                        <input type="radio" class="wpie_radio wpie_item_meta_visibility" name="wpie_item_meta_visibility" <?php echo $check; ?> id="wpie_item_meta_visibility_<?php echo esc_attr( $visibility_option_key ) ?>" value="<?php echo esc_attr( $visibility_option_key ) ?>"/>
                                                                                                        <label for="wpie_item_meta_visibility_<?php echo esc_attr( $visibility_option_key ) ?>" class="wpie_radio_label"><?php echo esc_html( $visibility_option_name ); ?></label>
                                                                                                </div>
                                                                                                <?php
                                                                                                $check = "";
                                                                                        }
                                                                                        unset( $check );
                                                                                }
                                                                                unset( $visibility_options );
                                                                        } else {

                                                                                ?>
                                                                                <div class="wpie_field_mapping_other_option_wrapper ">
                                                                                        <input type="radio" class="wpie_radio wpie_item_meta_visibility" name="wpie_item_meta_visibility" checked="checked" id="wpie_item_meta_visibility_visible" value="visible"/>
                                                                                        <label for="wpie_item_meta_visibility_visible" class="wpie_radio_label"><?php esc_html_e( 'Shop and search results', 'vj-wp-import-export' ); ?></label>
                                                                                </div>
                                                                                <div class="wpie_field_mapping_other_option_wrapper ">
                                                                                        <input type="radio" class="wpie_radio wpie_item_meta_visibility" name="wpie_item_meta_visibility" id="wpie_item_meta_visibility_catalog" value="catalog"/>
                                                                                        <label for="wpie_item_meta_visibility_catalog" class="wpie_radio_label"><?php esc_html_e( 'Shop only', 'vj-wp-import-export' ); ?></label>
                                                                                </div>
                                                                                <div class="wpie_field_mapping_other_option_wrapper ">
                                                                                        <input type="radio" class="wpie_radio wpie_item_meta_visibility" name="wpie_item_meta_visibility" id="wpie_item_meta_visibility_search" value="search"/>
                                                                                        <label for="wpie_item_meta_visibility_search" class="wpie_radio_label"><?php esc_html_e( 'Search results only', 'vj-wp-import-export' ); ?></label>
                                                                                </div>
                                                                                <div class="wpie_field_mapping_other_option_wrapper ">
                                                                                        <input type="radio" class="wpie_radio wpie_item_meta_visibility" name="wpie_item_meta_visibility" id="wpie_item_meta_visibility_hidden" value="hidden"/>
                                                                                        <label for="wpie_item_meta_visibility_hidden" class="wpie_radio_label"><?php esc_html_e( 'Hidden', 'vj-wp-import-export' ); ?></label>
                                                                                </div>
                                                                        <?php } ?>

                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_meta_visibility wpie_item_meta_visibility_as_specified" name="wpie_item_meta_visibility" id="wpie_item_meta_visibility_as_specified" value="as_specified"/>
                                                                                <label for="wpie_item_meta_visibility_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                                                <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                                                        <input type="text" class="wpie_content_data_input wpie_item_meta_visibility_as_specified_data" name="wpie_item_meta_visibility_as_specified_data" value=""/>
                                                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "The value should be one of the following: ('visible', 'catalog', 'search', 'hidden').", "vj-wp-import-export" ); ?>"></i>
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
                $product_section = ob_get_clean();

                $field_mapping_sections = array(
                        '150' => $product_section,
                );

                unset( $wpie_product_type );
                unset( $wpie_product_tax_class );
                unset( $product_group );

                return apply_filters( "wpie_pre_product_field_mapping_section", array_replace( $sections, $field_mapping_sections ), $wpie_import_type );
        }

}