<?php
if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}
if ( !function_exists( "wpie_import_order_mapping_fields" ) ) {

        function wpie_import_order_mapping_fields( $sections = array(), $wpie_import_type = "" ) {

                ob_start();

                ?>
                <div class="wpie_field_mapping_container_wrapper">
                        <div class="wpie_field_mapping_container_title wpie_active"><?php esc_html_e( 'Order Details', 'vj-wp-import-export' ); ?><div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div></div>
                        <div class="wpie_field_mapping_container_data wpie_show">
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Order Status', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper ">
                                                <select class="wpie_content_data_select wpie_item_status wpie_item_dropdown_as_specified" name="wpie_item_status" >
                                                    <?php
                                                    $statuses = wc_get_order_statuses();

                                                    if ( !empty( $statuses ) ) {
                                                            foreach ( $statuses as $status => $status_name ) {
                                                                    echo '<option value="' . esc_attr( $status ) . '" >' . esc_html( $status_name ) . '</option>';
                                                            }
                                                    }
                                                    unset( $statuses );

                                                    ?>
                                                        <option value="as_specified" ><?php esc_html_e( 'As Specified', 'vj-wp-import-export' ); ?></option>
                                                </select>    
                                                <div class="wpie_item_status_as_specified_wrapper wpie_item_as_specified_wrapper wpie_hide wpie_as_specified_wrapper">
                                                        <input type="text" class="wpie_content_data_input wpie_item_status_as_specified_data" name="wpie_item_status_as_specified_data" value=""/>
                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Order status can be matched by title or slug: wc-pending, wc-processing, wc-on-hold, wc-completed, wc-cancelled, wc-refunded, wc-failed. If order status is not found 'Pending Payment' will be applied to order.", "vj-wp-import-export" ); ?>"></i>
                                                </div>
                                        </div>
                                </div>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Date', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper wpie_as_specified_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_date" name="wpie_item_date" value=""/>
                                                <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Use any format supported by the PHP <b>strtotime</b> function. That means pretty much any human-readable date will work.", "vj-wp-import-export" ); ?>"></i>
                                        </div>
                                </div>
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Order Number', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper wpie_as_specified_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_order_number" name="wpie_item_order_number" value=""/>
                                                <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "For keep same order number of source site. leave empty for auto generated", "vj-wp-import-export" ); ?>"></i>
                                        </div>
                                </div>
                        </div>
                </div>
                <?php
                $item_details = ob_get_clean();

                ob_start();

                ?>
                <div class="wpie_field_mapping_container_wrapper">
                        <div class="wpie_field_mapping_container_title"><?php esc_html_e( 'Billing & Shipping Details', 'vj-wp-import-export' ); ?><div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div></div>
                        <div class="wpie_field_mapping_container_data">
                                <div class="wpie_product_data_section">
                                        <div class="wpie_product_menu_wrapper">
                                                <div class="wpie_order_item_list wpie_product_menu_general active_tab" display_block="wpie_order_billing_wrapper"><?php esc_html_e( 'Billing', 'vj-wp-import-export' ); ?></div>
                                                <div class="wpie_order_item_list wpie_product_menu_shipping" display_block="wpie_order_shipping_wrapper"><?php esc_html_e( 'Shipping', 'vj-wp-import-export' ); ?></div>
                                                <div class="wpie_order_item_list wpie_product_menu_payment" display_block="wpie_order_payment_wrapper" ><?php esc_html_e( 'Payment', 'vj-wp-import-export' ); ?></div>                        
                                        </div>
                                        <div class="wpie_product_content_wrapper">
                                                <div class="wpie_order_item_data_container wpie_order_billing_wrapper wpie_show">
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_item_order_billing_source" name="wpie_item_order_billing_source" id="wpie_item_order_billing_source_existing" value="existing" checked="checked"/>
                                                                        <label for="wpie_item_order_billing_source_existing" class="wpie_radio_label"><?php esc_html_e( 'Try to load data from existing customer', 'vj-wp-import-export' ); ?></label>
                                                                        <div class="wpie_radio_container">
                                                                                <div class="wpie_product_element_wrapper">
                                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Match by:', 'vj-wp-import-export' ); ?></div>
                                                                                        <div class="wpie_product_element_data">
                                                                                                <select class="wpie_content_data_select wpie_item_order_billing_match_by" name="wpie_item_order_billing_match_by">
                                                                                                        <option value="username" ><?php esc_html_e( 'Username', 'vj-wp-import-export' ); ?></option>
                                                                                                        <option value="email" ><?php esc_html_e( 'Email', 'vj-wp-import-export' ); ?></option>
                                                                                                        <option value="cf" ><?php esc_html_e( 'Custom Field', 'vj-wp-import-export' ); ?></option>
                                                                                                        <option value="id" ><?php esc_html_e( 'User Id', 'vj-wp-import-export' ); ?></option>
                                                                                                </select>
                                                                                                <div class="wpie_order_billing_match_data_wrapper">
                                                                                                        <input class="wpie_content_data_input wpie_item_order_billing_match_by_data wpie_item_order_billing_match_by_username wpie_show" type="text" name="wpie_item_order_billing_match_by_username" value="" placeholder="<?php esc_attr_e( 'Username', 'vj-wp-import-export' ); ?>">
                                                                                                        <input class="wpie_content_data_input wpie_item_order_billing_match_by_data wpie_item_order_billing_match_by_email" type="text" name="wpie_item_order_billing_match_by_email" value="" placeholder="<?php esc_attr_e( 'Email', 'vj-wp-import-export' ); ?>">
                                                                                                        <input class="wpie_content_data_input wpie_item_order_billing_match_by_data wpie_item_order_billing_match_by_cf_name" type="text" name="wpie_item_order_billing_match_by_cf_name" value="" placeholder="<?php esc_attr_e( 'Field Name', 'vj-wp-import-export' ); ?>">
                                                                                                        <input class="wpie_content_data_input wpie_item_order_billing_match_by_data wpie_item_order_billing_match_by_cf_value" type="text" name="wpie_item_order_billing_match_by_cf_value" value="" placeholder="<?php esc_attr_e( 'Field Value', 'vj-wp-import-export' ); ?>">
                                                                                                        <input class="wpie_content_data_input wpie_item_order_billing_match_by_data wpie_item_order_billing_match_by_user_id" type="text" name="wpie_item_order_billing_match_by_user_id" value="" placeholder="<?php esc_attr_e( 'User ID', 'vj-wp-import-export' ); ?>">
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="wpie_field_mapping_other_option_wrapper ">
                                                                                                <input type="checkbox" value="1" name="wpie_item_order_billing_no_match_guest" id="wpie_item_order_billing_no_match_guest" class="wpie_checkbox wpie_item_order_billing_no_match_guest">
                                                                                                <label class="wpie_checkbox_label" for="wpie_item_order_billing_no_match_guest"><?php esc_html_e( 'If no match found, import as guest customer', 'vj-wp-import-export' ); ?></label>
                                                                                                <div class="wpie_checkbox_container">
                                                                                                        <div class="wpie_order_user_billing_data">
                                                                                                                <div class="wpie_order_user_billing_data_outer">
                                                                                                                        <div class="wpie_order_user_billing_data_inner">
                                                                                                                                <div class="wpie_order_user_billing_data_label">
                                                                                                                                    <?php esc_html_e( 'First Name', 'vj-wp-import-export' ); ?>
                                                                                                                                </div>
                                                                                                                                <div class="wpie_order_user_billing_data_container">
                                                                                                                                        <input type="text" class="wpie_content_data_input wpie_item_guest_billing_first_name" name="wpie_item_guest_billing_first_name" value=""/>
                                                                                                                                </div>
                                                                                                                        </div>
                                                                                                                        <div class="wpie_order_user_billing_data_inner">
                                                                                                                                <div class="wpie_order_user_billing_data_label">
                                                                                                                                    <?php esc_html_e( 'Last Name', 'vj-wp-import-export' ); ?>
                                                                                                                                </div>
                                                                                                                                <div class="wpie_order_user_billing_data_container">
                                                                                                                                        <input type="text" class="wpie_content_data_input wpie_item_guest_billing_last_name" name="wpie_item_guest_billing_last_name" value=""/>
                                                                                                                                </div>
                                                                                                                        </div>
                                                                                                                </div>
                                                                                                                <div class="wpie_order_user_billing_data_outer">
                                                                                                                        <div class="wpie_order_user_billing_data_inner">
                                                                                                                                <div class="wpie_order_user_billing_data_label">
                                                                                                                                    <?php esc_html_e( 'Address 1', 'vj-wp-import-export' ); ?>
                                                                                                                                </div>
                                                                                                                                <div class="wpie_order_user_billing_data_container">
                                                                                                                                        <input type="text" class="wpie_content_data_input wpie_item_guest_billing_address_1" name="wpie_item_guest_billing_address_1" value=""/>
                                                                                                                                </div>
                                                                                                                        </div>
                                                                                                                        <div class="wpie_order_user_billing_data_inner">
                                                                                                                                <div class="wpie_order_user_billing_data_label">
                                                                                                                                    <?php esc_html_e( 'Address 2', 'vj-wp-import-export' ); ?>
                                                                                                                                </div>
                                                                                                                                <div class="wpie_order_user_billing_data_container">
                                                                                                                                        <input type="text" class="wpie_content_data_input wpie_item_guest_billing_address_2" name="wpie_item_guest_billing_address_2" value=""/>
                                                                                                                                </div>
                                                                                                                        </div>
                                                                                                                </div>
                                                                                                                <div class="wpie_order_user_billing_data_outer">
                                                                                                                        <div class="wpie_order_user_billing_data_inner">
                                                                                                                                <div class="wpie_order_user_billing_data_label">
                                                                                                                                    <?php esc_html_e( 'City', 'vj-wp-import-export' ); ?>
                                                                                                                                </div>
                                                                                                                                <div class="wpie_order_user_billing_data_container">
                                                                                                                                        <input type="text" class="wpie_content_data_input wpie_item_guest_billing_city" name="wpie_item_guest_billing_city" value=""/>
                                                                                                                                </div>
                                                                                                                        </div>
                                                                                                                        <div class="wpie_order_user_billing_data_inner">
                                                                                                                                <div class="wpie_order_user_billing_data_label">
                                                                                                                                    <?php esc_html_e( 'Postcode', 'vj-wp-import-export' ); ?>
                                                                                                                                </div>
                                                                                                                                <div class="wpie_order_user_billing_data_container">
                                                                                                                                        <input type="text" class="wpie_content_data_input wpie_item_guest_billing_postcode" name="wpie_item_guest_billing_postcode" value=""/>
                                                                                                                                </div>
                                                                                                                        </div>
                                                                                                                </div>
                                                                                                                <div class="wpie_order_user_billing_data_outer">
                                                                                                                        <div class="wpie_order_user_billing_data_inner">
                                                                                                                                <div class="wpie_order_user_billing_data_label">
                                                                                                                                    <?php esc_html_e( 'Country', 'vj-wp-import-export' ); ?>
                                                                                                                                </div>
                                                                                                                                <div class="wpie_order_user_billing_data_container">
                                                                                                                                        <input type="text" class="wpie_content_data_input wpie_item_guest_billing_country" name="wpie_item_guest_billing_country" value=""/>
                                                                                                                                </div>
                                                                                                                        </div>
                                                                                                                        <div class="wpie_order_user_billing_data_inner">
                                                                                                                                <div class="wpie_order_user_billing_data_label">
                                                                                                                                    <?php esc_html_e( 'State/Country', 'vj-wp-import-export' ); ?>
                                                                                                                                </div>
                                                                                                                                <div class="wpie_order_user_billing_data_container">
                                                                                                                                        <input type="text" class="wpie_content_data_input wpie_item_guest_billing_state" name="wpie_item_guest_billing_state" value=""/>
                                                                                                                                </div>
                                                                                                                        </div>
                                                                                                                </div>
                                                                                                                <div class="wpie_order_user_billing_data_outer">
                                                                                                                        <div class="wpie_order_user_billing_data_inner">
                                                                                                                                <div class="wpie_order_user_billing_data_label">
                                                                                                                                    <?php esc_html_e( 'Email', 'vj-wp-import-export' ); ?>
                                                                                                                                </div>
                                                                                                                                <div class="wpie_order_user_billing_data_container">
                                                                                                                                        <input type="text" class="wpie_content_data_input wpie_item_guest_billing_email" name="wpie_item_guest_billing_email" value=""/>
                                                                                                                                </div>
                                                                                                                        </div>
                                                                                                                        <div class="wpie_order_user_billing_data_inner">
                                                                                                                                <div class="wpie_order_user_billing_data_label">
                                                                                                                                    <?php esc_html_e( 'Phone', 'vj-wp-import-export' ); ?>
                                                                                                                                </div>
                                                                                                                                <div class="wpie_order_user_billing_data_container">
                                                                                                                                        <input type="text" class="wpie_content_data_input wpie_item_guest_billing_phone" name="wpie_item_guest_billing_phone" value=""/>
                                                                                                                                </div>
                                                                                                                        </div>
                                                                                                                </div>
                                                                                                                <div class="wpie_order_user_billing_data_outer">
                                                                                                                        <div class="wpie_order_user_billing_data_inner">
                                                                                                                                <div class="wpie_order_user_billing_data_label">
                                                                                                                                    <?php esc_html_e( 'Company', 'vj-wp-import-export' ); ?>
                                                                                                                                </div>
                                                                                                                                <div class="wpie_order_user_billing_data_container">
                                                                                                                                        <input type="text" class="wpie_content_data_input wpie_item_guest_billing_company" name="wpie_item_guest_billing_company" value=""/>
                                                                                                                                </div>
                                                                                                                        </div>
                                                                                                                </div>
                                                                                                        </div>
                                                                                                </div>
                                                                                        </div>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_product_element_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_item_order_billing_source" name="wpie_item_order_billing_source" id="wpie_item_order_billing_source_guest" value="guest"/>
                                                                        <label for="wpie_item_order_billing_source_guest" class="wpie_radio_label"><?php esc_html_e( 'Import as guest customer', 'vj-wp-import-export' ); ?></label>
                                                                        <div class="wpie_radio_container">
                                                                                <div class="wpie_order_user_billing_data">
                                                                                        <div class="wpie_order_user_billing_data_outer">
                                                                                                <div class="wpie_order_user_billing_data_inner">
                                                                                                        <div class="wpie_order_user_billing_data_label">
                                                                                                            <?php esc_html_e( 'First Name', 'vj-wp-import-export' ); ?>
                                                                                                        </div>
                                                                                                        <div class="wpie_order_user_billing_data_container">
                                                                                                                <input type="text" class="wpie_content_data_input wpie_item_billing_first_name" name="wpie_item_billing_first_name" value=""/>
                                                                                                        </div>
                                                                                                </div>
                                                                                                <div class="wpie_order_user_billing_data_inner">
                                                                                                        <div class="wpie_order_user_billing_data_label">
                                                                                                            <?php esc_html_e( 'Last Name', 'vj-wp-import-export' ); ?>
                                                                                                        </div>
                                                                                                        <div class="wpie_order_user_billing_data_container">
                                                                                                                <input type="text" class="wpie_content_data_input wpie_item_billing_last_name" name="wpie_item_billing_last_name" value=""/>
                                                                                                        </div>
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="wpie_order_user_billing_data_outer">
                                                                                                <div class="wpie_order_user_billing_data_inner">
                                                                                                        <div class="wpie_order_user_billing_data_label">
                                                                                                            <?php esc_html_e( 'Address 1', 'vj-wp-import-export' ); ?>
                                                                                                        </div>
                                                                                                        <div class="wpie_order_user_billing_data_container">
                                                                                                                <input type="text" class="wpie_content_data_input wpie_item_billing_address_1" name="wpie_item_billing_address_1" value=""/>
                                                                                                        </div>
                                                                                                </div>
                                                                                                <div class="wpie_order_user_billing_data_inner">
                                                                                                        <div class="wpie_order_user_billing_data_label">
                                                                                                            <?php esc_html_e( 'Address 2', 'vj-wp-import-export' ); ?>
                                                                                                        </div>
                                                                                                        <div class="wpie_order_user_billing_data_container">
                                                                                                                <input type="text" class="wpie_content_data_input wpie_item_billing_address_2" name="wpie_item_billing_address_2" value=""/>
                                                                                                        </div>
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="wpie_order_user_billing_data_outer">
                                                                                                <div class="wpie_order_user_billing_data_inner">
                                                                                                        <div class="wpie_order_user_billing_data_label">
                                                                                                            <?php esc_html_e( 'City', 'vj-wp-import-export' ); ?>
                                                                                                        </div>
                                                                                                        <div class="wpie_order_user_billing_data_container">
                                                                                                                <input type="text" class="wpie_content_data_input wpie_item_billing_city" name="wpie_item_billing_city" value=""/>
                                                                                                        </div>
                                                                                                </div>
                                                                                                <div class="wpie_order_user_billing_data_inner">
                                                                                                        <div class="wpie_order_user_billing_data_label">
                                                                                                            <?php esc_html_e( 'Postcode', 'vj-wp-import-export' ); ?>
                                                                                                        </div>
                                                                                                        <div class="wpie_order_user_billing_data_container">
                                                                                                                <input type="text" class="wpie_content_data_input wpie_item_billing_postcode" name="wpie_item_billing_postcode" value=""/>
                                                                                                        </div>
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="wpie_order_user_billing_data_outer">
                                                                                                <div class="wpie_order_user_billing_data_inner">
                                                                                                        <div class="wpie_order_user_billing_data_label">
                                                                                                            <?php esc_html_e( 'Country', 'vj-wp-import-export' ); ?>
                                                                                                        </div>
                                                                                                        <div class="wpie_order_user_billing_data_container">
                                                                                                                <input type="text" class="wpie_content_data_input wpie_item_billing_country" name="wpie_item_billing_country" value=""/>
                                                                                                        </div>
                                                                                                </div>
                                                                                                <div class="wpie_order_user_billing_data_inner">
                                                                                                        <div class="wpie_order_user_billing_data_label">
                                                                                                            <?php esc_html_e( 'State/Country', 'vj-wp-import-export' ); ?>
                                                                                                        </div>
                                                                                                        <div class="wpie_order_user_billing_data_container">
                                                                                                                <input type="text" class="wpie_content_data_input wpie_item_billing_state" name="wpie_item_billing_state" value=""/>
                                                                                                        </div>
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="wpie_order_user_billing_data_outer">
                                                                                                <div class="wpie_order_user_billing_data_inner">
                                                                                                        <div class="wpie_order_user_billing_data_label">
                                                                                                            <?php esc_html_e( 'Email', 'vj-wp-import-export' ); ?>
                                                                                                        </div>
                                                                                                        <div class="wpie_order_user_billing_data_container">
                                                                                                                <input type="text" class="wpie_content_data_input wpie_item_billing_email" name="wpie_item_billing_email" value=""/>
                                                                                                        </div>
                                                                                                </div>
                                                                                                <div class="wpie_order_user_billing_data_inner">
                                                                                                        <div class="wpie_order_user_billing_data_label">
                                                                                                            <?php esc_html_e( 'Phone', 'vj-wp-import-export' ); ?>
                                                                                                        </div>
                                                                                                        <div class="wpie_order_user_billing_data_container">
                                                                                                                <input type="text" class="wpie_content_data_input wpie_item_billing_phone" name="wpie_item_billing_phone" value=""/>
                                                                                                        </div>
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="wpie_order_user_billing_data_outer">
                                                                                                <div class="wpie_order_user_billing_data_inner">
                                                                                                        <div class="wpie_order_user_billing_data_label">
                                                                                                            <?php esc_html_e( 'Company', 'vj-wp-import-export' ); ?>
                                                                                                        </div>
                                                                                                        <div class="wpie_order_user_billing_data_container">
                                                                                                                <input type="text" class="wpie_content_data_input wpie_item_billing_company" name="wpie_item_billing_company" value=""/>
                                                                                                        </div>
                                                                                                </div>
                                                                                        </div>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                </div>
                                                <div class="wpie_order_item_data_container wpie_order_shipping_wrapper">
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_field_mapping_other_option_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_item_order_shipping_source" name="wpie_item_order_shipping_source" id="wpie_item_order_shipping_source_copy" value="copy" checked="checked"/>
                                                                        <label for="wpie_item_order_shipping_source_copy" class="wpie_radio_label"><?php esc_html_e( 'Copy from billing', 'vj-wp-import-export' ); ?></label>
                                                                </div>
                                                                <div class="wpie_product_element_wrapper">
                                                                        <input type="radio" class="wpie_radio wpie_item_order_shipping_source" name="wpie_item_order_shipping_source" id="wpie_item_order_shipping_source_guest" value="guest"/>
                                                                        <label for="wpie_item_order_shipping_source_guest" class="wpie_radio_label"><?php esc_html_e( 'Import shipping address', 'vj-wp-import-export' ); ?></label>
                                                                        <div class="wpie_radio_container">
                                                                                <div class="wpie_field_mapping_other_option_wrapper ">
                                                                                        <input type="checkbox" value="1" name="wpie_item_order_shipping_no_match_billing" id="wpie_item_order_shipping_no_match_billing" class="wpie_checkbox wpie_item_order_shipping_no_match_billing">
                                                                                        <label class="wpie_checkbox_label" for="wpie_item_order_shipping_no_match_billing"><?php esc_html_e( 'If order has no shipping info, copy from billing', 'vj-wp-import-export' ); ?></label>
                                                                                        <div class="wpie_checkbox_container">
                                                                                                <div class="wpie_order_user_billing_data">
                                                                                                        <div class="wpie_order_user_billing_data_outer">
                                                                                                                <div class="wpie_order_user_billing_data_inner">
                                                                                                                        <div class="wpie_order_user_billing_data_label">
                                                                                                                            <?php esc_html_e( 'First Name', 'vj-wp-import-export' ); ?>
                                                                                                                        </div>
                                                                                                                        <div class="wpie_order_user_billing_data_container">
                                                                                                                                <input type="text" class="wpie_content_data_input wpie_item_shipping_first_name" name="wpie_item_shipping_first_name" value=""/>
                                                                                                                        </div>
                                                                                                                </div>
                                                                                                                <div class="wpie_order_user_billing_data_inner">
                                                                                                                        <div class="wpie_order_user_billing_data_label">
                                                                                                                            <?php esc_html_e( 'Last Name', 'vj-wp-import-export' ); ?>
                                                                                                                        </div>
                                                                                                                        <div class="wpie_order_user_billing_data_container">
                                                                                                                                <input type="text" class="wpie_content_data_input wpie_item_shipping_last_name" name="wpie_item_shipping_last_name" value=""/>
                                                                                                                        </div>
                                                                                                                </div>
                                                                                                        </div>
                                                                                                        <div class="wpie_order_user_billing_data_outer">
                                                                                                                <div class="wpie_order_user_billing_data_inner">
                                                                                                                        <div class="wpie_order_user_billing_data_label">
                                                                                                                            <?php esc_html_e( 'Address 1', 'vj-wp-import-export' ); ?>
                                                                                                                        </div>
                                                                                                                        <div class="wpie_order_user_billing_data_container">
                                                                                                                                <input type="text" class="wpie_content_data_input wpie_item_shipping_address_1" name="wpie_item_shipping_address_1" value=""/>
                                                                                                                        </div>
                                                                                                                </div>
                                                                                                                <div class="wpie_order_user_billing_data_inner">
                                                                                                                        <div class="wpie_order_user_billing_data_label">
                                                                                                                            <?php esc_html_e( 'Address 2', 'vj-wp-import-export' ); ?>
                                                                                                                        </div>
                                                                                                                        <div class="wpie_order_user_billing_data_container">
                                                                                                                                <input type="text" class="wpie_content_data_input wpie_item_shipping_address_2" name="wpie_item_shipping_address_2" value=""/>
                                                                                                                        </div>
                                                                                                                </div>
                                                                                                        </div>
                                                                                                        <div class="wpie_order_user_billing_data_outer">
                                                                                                                <div class="wpie_order_user_billing_data_inner">
                                                                                                                        <div class="wpie_order_user_billing_data_label">
                                                                                                                            <?php esc_html_e( 'City', 'vj-wp-import-export' ); ?>
                                                                                                                        </div>
                                                                                                                        <div class="wpie_order_user_billing_data_container">
                                                                                                                                <input type="text" class="wpie_content_data_input wpie_item_shipping_city" name="wpie_item_shipping_city" value=""/>
                                                                                                                        </div>
                                                                                                                </div>
                                                                                                                <div class="wpie_order_user_billing_data_inner">
                                                                                                                        <div class="wpie_order_user_billing_data_label">
                                                                                                                            <?php esc_html_e( 'Postcode', 'vj-wp-import-export' ); ?>
                                                                                                                        </div>
                                                                                                                        <div class="wpie_order_user_billing_data_container">
                                                                                                                                <input type="text" class="wpie_content_data_input wpie_item_shipping_postcode" name="wpie_item_shipping_postcode" value=""/>
                                                                                                                        </div>
                                                                                                                </div>
                                                                                                        </div>
                                                                                                        <div class="wpie_order_user_billing_data_outer">
                                                                                                                <div class="wpie_order_user_billing_data_inner">
                                                                                                                        <div class="wpie_order_user_billing_data_label">
                                                                                                                            <?php esc_html_e( 'Country', 'vj-wp-import-export' ); ?>
                                                                                                                        </div>
                                                                                                                        <div class="wpie_order_user_billing_data_container">
                                                                                                                                <input type="text" class="wpie_content_data_input wpie_item_shipping_country" name="wpie_item_shipping_country" value=""/>
                                                                                                                        </div>
                                                                                                                </div>
                                                                                                                <div class="wpie_order_user_billing_data_inner">
                                                                                                                        <div class="wpie_order_user_billing_data_label">
                                                                                                                            <?php esc_html_e( 'State/Country', 'vj-wp-import-export' ); ?>
                                                                                                                        </div>
                                                                                                                        <div class="wpie_order_user_billing_data_container">
                                                                                                                                <input type="text" class="wpie_content_data_input wpie_item_shipping_state" name="wpie_item_shipping_state" value=""/>
                                                                                                                        </div>
                                                                                                                </div>
                                                                                                        </div>
                                                                                                        <div class="wpie_order_user_billing_data_outer">
                                                                                                                <div class="wpie_order_user_billing_data_inner">
                                                                                                                        <div class="wpie_order_user_billing_data_label">
                                                                                                                            <?php esc_html_e( 'Company', 'vj-wp-import-export' ); ?>
                                                                                                                        </div>
                                                                                                                        <div class="wpie_order_user_billing_data_container">
                                                                                                                                <input type="text" class="wpie_content_data_input wpie_item_shipping_company" name="wpie_item_shipping_company" value=""/>
                                                                                                                        </div>
                                                                                                                </div>
                                                                                                        </div>
                                                                                                </div>
                                                                                        </div>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Customer Provided Note', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_customer_provided_note" type="text" name="wpie_item_excerpt" value="">
                                                                        </div>
                                                                </div>
                                                        </div>
                                                </div>
                                                <div class="wpie_order_item_data_container wpie_order_payment_wrapper">
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Payment Method', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <select class="wpie_content_data_select wpie_item_dropdown_as_specified wpie_item_order_payment_method" name="wpie_item_order_payment_method">
                                                                                    <?php
                                                                                    $payment_gateways = WC_Payment_Gateways::instance()->payment_gateways();

                                                                                    if ( !empty( $payment_gateways ) ) {
                                                                                            foreach ( $payment_gateways as $id => $gateway ) {
                                                                                                    echo '<option value="' . esc_attr( $id ) . '" >' . esc_html( $gateway->title ) . '</option>';
                                                                                            }
                                                                                    }
                                                                                    unset( $payment_gateways );

                                                                                    ?>
                                                                                        <option value="as_specified" ><?php esc_html_e( 'As Specified', 'vj-wp-import-export' ); ?></option>
                                                                                </select>
                                                                                <div class="wpie_item_as_specified_wrapper"><input type="text" class="wpie_content_data_input wpie_item_order_payment_method_as_specified_data" name="wpie_item_order_payment_method_as_specified_data" value=""/></div>                                    
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Transaction ID', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_transaction_id" type="text" name="wpie_item_order_transaction_id" value="">
                                                                        </div>
                                                                </div>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                </div>
                <?php
                $item_billing = ob_get_clean();

                ob_start();

                ?>
                <div class="wpie_field_mapping_container_wrapper">
                        <div class="wpie_field_mapping_container_title"><?php esc_html_e( 'Order Items', 'vj-wp-import-export' ); ?><div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div></div>
                        <div class="wpie_field_mapping_container_data">
                                <div class="wpie_product_data_section">
                                        <div class="wpie_product_menu_wrapper">
                                                <div class="wpie_order_item_list wpie_product_menu_general active_tab" display_block="wpie_order_item_product_wrapper"><?php esc_html_e( 'Products', 'vj-wp-import-export' ); ?></div>
                                                <div class="wpie_order_item_list wpie_product_menu_inventory" display_block="wpie_order_item_fees_wrapper"><?php esc_html_e( 'Fees', 'vj-wp-import-export' ); ?></div>
                                                <div class="wpie_order_item_list wpie_product_menu_shipping" display_block="wpie_order_item_coupons_wrapper" ><?php esc_html_e( 'Coupons', 'vj-wp-import-export' ); ?></div>
                                                <div class="wpie_order_item_list wpie_product_menu_linked_products" display_block="wpie_order_item_shipping_wrapper"><?php esc_html_e( 'Shipping', 'vj-wp-import-export' ); ?></div>
                                                <div class="wpie_order_item_list wpie_product_menu_attributes" display_block="wpie_order_item_taxes_wrapper"><?php esc_html_e( 'Taxes', 'vj-wp-import-export' ); ?></div>
                                                <div class="wpie_order_item_list wpie_product_menu_variations" display_block="wpie_order_item_refunds_wrapper"><?php esc_html_e( 'Refunds', 'vj-wp-import-export' ); ?></div>
                                                <div class="wpie_order_item_list wpie_product_menu_advanced" display_block="wpie_order_item_total_wrapper"><?php esc_html_e( 'Total', 'vj-wp-import-export' ); ?></div>
                                        </div>
                                        <div class="wpie_product_content_wrapper">
                                                <div class="wpie_order_item_data_container wpie_order_item_product_wrapper">
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Product Name', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_product_name" type="text" name="wpie_item_order_item_product_name" value="">
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Price per Unit', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_product_price" type="text" name="wpie_item_order_item_product_price" value="">
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Quantity', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_product_quantity" type="text" name="wpie_item_order_item_product_quantity" value="">
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'SKU', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_product_sku" type="text" name="wpie_item_order_item_product_sku" value="">
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Is Variation', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_is_variation" type="text" name="wpie_item_order_item_is_variation" value="">
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Original Product Title', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_original_product_title" type="text" name="wpie_item_order_item_original_product_title" value="">
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Variation Attributes', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_variation_attributes" type="text" name="wpie_item_order_item_variation_attributes" value="">
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Product Metas', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_meta" type="text" name="wpie_item_order_item_meta" value="">
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_order_item_delim_label"><?php echo esc_html_e( 'Multiple products separated by', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_order_item_delim_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_product_delim" type="text" name="wpie_item_order_item_product_delim" value="|">
                                                                        </div>
                                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "For example, two products would be imported like this SKU1|SKU2, and their quantities like this 15|20", "vj-wp-import-export" ); ?>"></i>
                                                                </div>
                                                        </div>
                                                </div>
                                                <div class="wpie_order_item_data_container wpie_order_item_fees_wrapper">
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_element_half_wrapper">
                                                                                <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Fee Name', 'vj-wp-import-export' ); ?></div>
                                                                        </div>
                                                                        <div class="wpie_element_half_wrapper">
                                                                                <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Amount', 'vj-wp-import-export' ); ?></div>
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_element_half_wrapper">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_fee" type="text" name="wpie_item_order_item_fee" value="">
                                                                        </div>
                                                                        <div class="wpie_element_half_wrapper">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_fee_amount" type="text" name="wpie_item_order_item_fee_amount" value="">
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_order_item_delim_label"><?php echo esc_html_e( 'Multiple Fees separated by', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_order_item_delim_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_fees_delim" type="text" name="wpie_item_order_item_fees_delim" value="|">
                                                                        </div>
                                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "For example, two fees would be imported like this 'Fee 1|Fee 2' and the fee amounts like this 10|20", "vj-wp-import-export" ); ?>"></i>
                                                                </div>
                                                        </div>
                                                </div>
                                                <div class="wpie_order_item_data_container wpie_order_item_coupons_wrapper">
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Coupon Code', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_coupon" type="text" name="wpie_item_order_item_coupon" value="">
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Discount Amount', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_coupon_amount" type="text" name="wpie_item_order_item_coupon_amount" value="">
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_order_item_delim_label"><?php echo esc_html_e( 'Multiple Coupons separated by', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_order_item_delim_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_coupon_delim" type="text" name="wpie_item_order_item_coupon_delim" value="|">
                                                                        </div>
                                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "For example, two coupons would be imported like this coupon1|coupon2", "vj-wp-import-export" ); ?>"></i>
                                                                </div>
                                                        </div>
                                                </div>
                                                <div class="wpie_order_item_data_container wpie_order_item_shipping_wrapper">
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Shipping Name', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_shipping_name" type="text" name="wpie_item_order_item_shipping_name" value="">
                                                                        </div>
                                                                </div>                              
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Shipping Method', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_shipping_method" type="text" name="wpie_item_order_item_shipping_method" value="">
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Amount', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_shipping_amount" type="text" name="wpie_item_order_item_shipping_amount" value="">
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Shipping Meta', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_shipping_meta" type="text" name="wpie_item_order_item_shipping_meta" value="">
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_order_item_delim_label"><?php echo esc_html_e( 'Multiple Shipping costs separated by', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_order_item_delim_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_shipping_costs_delim" type="text" name="wpie_item_order_item_shipping_costs_delim" value="|">
                                                                        </div>
                                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "For example, two shipping names would be imported like this 'Shipping 1|Shipping 2' and the shipping amounts like this 10|20", "vj-wp-import-export" ); ?>"></i>
                                                                </div>
                                                        </div>
                                                </div>
                                                <div class="wpie_order_item_data_container wpie_order_item_taxes_wrapper">
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Tax Rate', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_tax_rate" type="text" name="wpie_item_order_item_tax_rate" value="">
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Tax Rate Amount', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_tax_rate_amount" type="text" name="wpie_item_order_item_tax_rate_amount" value="">
                                                                        </div>
                                                                </div>                                                                
                                                        </div>
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_order_item_delim_label"><?php echo esc_html_e( 'Multiple taxes separated by', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_order_item_delim_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_taxes_delim" type="text" name="wpie_item_order_item_taxes_delim" value="|">
                                                                        </div>
                                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "For example, two tax rate amounts would be imported like this '10|20'", "vj-wp-import-export" ); ?>"></i>
                                                                </div>
                                                        </div>
                                                </div>
                                                <div class="wpie_order_item_data_container wpie_order_item_refunds_wrapper">
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Refund Amount', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_refund_amount" type="text" name="wpie_item_order_item_refund_amount" value="">
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Reason', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_refund_reason" type="text" name="wpie_item_order_item_refund_reason" value="">
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Date', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_refund_date" type="text" name="wpie_item_order_item_refund_date" value="">
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Refund Name', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_product_element_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_refund_name" type="text" name="wpie_item_order_item_refund_name" value="">
                                                                        </div>
                                                                </div>
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Refund Issued By', 'vj-wp-import-export' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "If no user is matched, refund issuer will be left blank.", "vj-wp-import-export" ); ?>"></i></div>
                                                                        <div class="wpie_product_element_all_data_lable">
                                                                                <input type="radio" class="wpie_radio wpie_item_order_item_refund_issued_match_by_existing wpie_item_order_item_refund_issued_match_by" name="wpie_item_order_item_refund_issued_match_by" id="wpie_item_order_item_refund_issued_match_by_existing" value="existing" checked="checked"/>
                                                                                <label for="wpie_item_order_item_refund_issued_match_by_existing" class="wpie_radio_label"><?php esc_html_e( 'Load details from existing user', 'vj-wp-import-export' ); ?></label>
                                                                                <div class="wpie_radio_container">
                                                                                        <div class="wpie_product_element_data_lable"><?php echo esc_html_e( 'Match user by:', 'vj-wp-import-export' ); ?></div>
                                                                                        <div class="wpie_product_element_all_data_lable">
                                                                                                <input type="radio" class="wpie_radio wpie_item_order_item_refund_issued_by" name="wpie_item_order_item_refund_issued_by" id="wpie_item_order_item_refund_issued_by_username" value="login" checked="checked"/>
                                                                                                <label for="wpie_item_order_item_refund_issued_by_username" class="wpie_radio_label"><?php esc_html_e( 'Username', 'vj-wp-import-export' ); ?></label>
                                                                                                <div class="wpie_radio_container"><input type="text" class="wpie_content_data_input wpie_item_refund_customer_login" name="wpie_item_refund_customer_login" value=""/></div>
                                                                                        </div>
                                                                                        <div class="wpie_product_element_all_data_lable">
                                                                                                <input type="radio" class="wpie_radio wpie_item_order_item_refund_issued_by" name="wpie_item_order_item_refund_issued_by" id="wpie_item_order_item_refund_issued_by_email" value="email"/>
                                                                                                <label for="wpie_item_order_item_refund_issued_by_email" class="wpie_radio_label"><?php esc_html_e( 'Email', 'vj-wp-import-export' ); ?></label>
                                                                                                <div class="wpie_radio_container"><input type="text" class="wpie_content_data_input wpie_item_refund_customer_email" name="wpie_item_refund_customer_email" value=""/></div>
                                                                                        </div>
                                                                                        <div class="wpie_product_element_all_data_lable">
                                                                                                <input type="radio" class="wpie_radio wpie_item_order_item_refund_issued_by" name="wpie_item_order_item_refund_issued_by" id="wpie_item_order_item_refund_issued_by_cf" value="cf"/>
                                                                                                <label for="wpie_item_order_item_refund_issued_by_cf" class="wpie_radio_label"><?php esc_html_e( 'Custom Field', 'vj-wp-import-export' ); ?></label>
                                                                                                <div class="wpie_radio_container">
                                                                                                        <input type="text" class="wpie_content_data_input wpie_item_refund_customer_meta_key" name="wpie_item_refund_customer_meta_key" value=""/>
                                                                                                        <input type="text" class="wpie_content_data_input wpie_item_refund_customer_meta_val" name="wpie_item_refund_customer_meta_val" value=""/>
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="wpie_product_element_all_data_lable">
                                                                                                <input type="radio" class="wpie_radio wpie_item_order_item_refund_issued_by" name="wpie_item_order_item_refund_issued_by" id="wpie_item_order_item_refund_issued_by_id" value="id"/>
                                                                                                <label for="wpie_item_order_item_refund_issued_by_id" class="wpie_radio_label"><?php esc_html_e( 'User Id', 'vj-wp-import-export' ); ?></label>
                                                                                                <div class="wpie_radio_container"><input type="text" class="wpie_content_data_input wpie_item_refund_customer_id" name="wpie_item_refund_customer_id" value=""/></div>
                                                                                        </div>
                                                                                </div>
                                                                        </div>
                                                                        <div class="wpie_product_element_all_data_lable">
                                                                                <input type="radio" class="wpie_radio  wpie_item_order_item_refund_issued_match_by wpie_item_order_item_refund_issued_match_by_blank" name="wpie_item_order_item_refund_issued_match_by" id="wpie_item_order_item_refund_issued_match_by_blank" value="blank"/>
                                                                                <label for="wpie_item_order_item_refund_issued_match_by_blank" class="wpie_radio_label"><?php esc_html_e( 'Leave refund issuer blank', 'vj-wp-import-export' ); ?></label>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_wrapper">
                                                                        <div class="wpie_order_item_delim_label"><?php echo esc_html_e( 'Multiple Refunds separated by', 'vj-wp-import-export' ); ?></div>
                                                                        <div class="wpie_order_item_delim_data">
                                                                                <input class="wpie_content_data_input wpie_item_order_item_refund_delim" type="text" name="wpie_item_order_item_refund_delim" value="|">
                                                                        </div>
                                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "For example, two refunds amounts would be imported like this 'refund 1|refund 2'", "vj-wp-import-export" ); ?>"></i>
                                                                </div>
                                                        </div>
                                                </div>                        
                                                <div class="wpie_order_item_data_container wpie_order_item_total_wrapper">
                                                        <div class="wpie_product_element_data_container">
                                                                <div class="wpie_product_element_all_data_lable">
                                                                        <input type="radio" class="wpie_radio wpie_item_order_total" name="wpie_item_order_total" id="wpie_order_total_auto" value="auto" checked="checked"/>
                                                                        <label for="wpie_order_total_auto" class="wpie_radio_label"><?php esc_html_e( 'Calculate order total automatically', 'vj-wp-import-export' ); ?></label>
                                                                </div>
                                                                <div class="wpie_product_element_all_data_lable">
                                                                        <input type="radio" class="wpie_radio wpie_item_order_total" name="wpie_item_order_total" id="wpie_order_total_manually" value="manually"/>
                                                                        <label for="wpie_order_total_manually" class="wpie_radio_label"><?php esc_html_e( 'Calculate order total Manually', 'vj-wp-import-export' ); ?></label>
                                                                        <div class="wpie_radio_container"><input type="text" class="wpie_content_data_input wpie_item_order_total_as_specified" name="wpie_item_order_total_as_specified" value=""/></div>
                                                                </div>
                                                        </div>
                                                </div>
                                        </div>
                                </div>
                        </div>
                </div>
                <?php
                $order_item = ob_get_clean();

                ob_start();

                ?>
                <div class="wpie_field_mapping_container_wrapper">
                        <div class="wpie_field_mapping_container_title"><?php esc_html_e( 'Notes', 'vj-wp-import-export' ); ?><div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div></div>
                        <div class="wpie_field_mapping_container_data">
                                <div class="wpie_product_element_wrapper">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Content', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <textarea class="wpie_content_data_textarea wpie_item_import_order_note_content"  name="wpie_item_import_order_note_content"></textarea>
                                        </div>
                                </div>
                                <div class="wpie_product_element_wrapper">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Date', 'vj-wp-import-export' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Use any format supported by the PHP <b>strtotime</b> function. That means pretty much any human-readable date will work.", "vj-wp-import-export" ); ?>"></i></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_import_order_note_date" name="wpie_item_import_order_note_date" value=""/>
                                        </div>
                                </div>
                                <div class="wpie_product_element_wrapper">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Visibility', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="radio" class="wpie_radio wpie_item_import_order_note_visibility" name="wpie_item_import_order_note_visibility" id="wpie_import_order_note_visibility_private" value="private" checked="checked" />
                                                <label for="wpie_import_order_note_visibility_private" class="wpie_radio_label"><?php esc_html_e( 'Private note', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper ">
                                                <input type="radio" class="wpie_radio wpie_item_import_order_note_visibility" name="wpie_item_import_order_note_visibility" id="wpie_import_order_note_visibility_customer" value="customer"/>
                                                <label for="wpie_import_order_note_visibility_customer" class="wpie_radio_label"><?php esc_html_e( 'Note to customer', 'vj-wp-import-export' ); ?></label>
                                        </div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="radio" class="wpie_radio wpie_item_import_order_note_visibility wpie_item_import_order_note_visibility_as_specified" name="wpie_item_import_order_note_visibility" id="wpie_import_order_note_visibility_as_specified" value="as_specified"/>
                                                <label for="wpie_import_order_note_visibility_as_specified" class="wpie_radio_label"><?php esc_html_e( 'As specified', 'vj-wp-import-export' ); ?></label>
                                                <div class="wpie_radio_container wpie_as_specified_wrapper">
                                                        <input type="text" class="wpie_content_data_input wpie_item_import_order_note_visibility_as_specified_data" name="wpie_item_import_order_note_visibility_as_specified_data" value=""/>
                                                        <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Use 'private' or 'customer'.", "vj-wp-import-export" ); ?>"></i>
                                                </div>
                                        </div>
                                </div>
                                <div class="wpie_product_element_wrapper">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Username', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_import_order_note_username" name="wpie_item_import_order_note_username" value=""/>
                                        </div>
                                </div>
                                <div class="wpie_product_element_wrapper">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Email', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_import_order_note_email" name="wpie_item_import_order_note_email" value=""/>
                                        </div>
                                </div>
                                <div class="wpie_product_element_wrapper">
                                        <div class="wpie_field_mapping_inner_title"><?php esc_html_e( 'Multiple notes separated by', 'vj-wp-import-export' ); ?></div>
                                        <div class="wpie_field_mapping_other_option_wrapper wpie_as_specified_wrapper">
                                                <input type="text" class="wpie_content_data_input wpie_item_import_order_note_delim" name="wpie_item_import_order_note_delim" value="|"/>
                                                <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "For example, two notes would be imported like this 'Note 1|Note 2'", "vj-wp-import-export" ); ?>"></i>
                                        </div>
                                </div>
                        </div>
                </div>
                <?php
                $item_notes = ob_get_clean();

                ob_start();

                ?>
                <div class="wpie_field_mapping_container_wrapper wpie_<?php echo esc_attr( $wpie_import_type ); ?>_field_mapping_container">
                        <div class="wpie_field_mapping_container_title"><?php esc_html_e( "Email Notifications", 'vj-wp-import-export' ); ?><div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div></div>
                        <div class="wpie_field_mapping_container_data">
                                <div class="wpie_field_mapping_container_element">
                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                <input type="checkbox" id="wpie_item_send_email_notifications" name="wpie_item_send_email_notifications" value="1" class="wpie_checkbox wpie_item_send_email_notifications">
                                                <label class="wpie_checkbox_label" for="wpie_item_send_email_notifications"><?php esc_html_e( "Send Email Notifications to Customers", 'vj-wp-import-export' ); ?><i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "If disable, WP Import Export will prevent WordPress from sending notification emails to customers when their orders are imported or updated.", "vj-wp-import-export" ); ?>"></i></label>
                                        </div>
                                </div>

                        </div>
                </div>

                <?php
                $user_notifications = ob_get_clean();

                $order_fields = array(
                        '160' => $item_details,
                        '170' => $item_billing,
                        '180' => $order_item,
                        '190' => $item_notes,
                        '200' => $user_notifications
                );

                if ( isset( $sections[ "100" ] ) ) {
                        unset( $sections[ "100" ] );
                }
                if ( isset( $sections[ "200" ] ) ) {
                        unset( $sections[ "200" ] );
                }
                if ( isset( $sections[ "400" ] ) ) {
                        unset( $sections[ "400" ] );
                }
                if ( isset( $sections[ "500" ] ) ) {
                        unset( $sections[ "500" ] );
                }

                $sections = array_replace( $sections, $order_fields );

                unset( $item_details, $item_billing, $order_item, $item_notes, $order_fields );

                return apply_filters( "wpie_pre_order_field_mapping_section", $sections, $wpie_import_type );
        }

}
    