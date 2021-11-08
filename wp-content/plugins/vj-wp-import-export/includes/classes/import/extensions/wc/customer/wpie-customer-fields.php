<?php
if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}
if ( !function_exists( "wpie_import_wc_customer_mapping_fields" ) ) {

        function wpie_import_wc_customer_mapping_fields( $sections = [], $wpie_import_type = "" ) {


                ob_start();

                ?>
                <div class="wpie_field_mapping_container_wrapper">
                        <div class="wpie_field_mapping_container_title"><?php esc_html_e( 'Billing & Shipping Details', 'vj-wp-import-export' ); ?><div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div></div>
                        <div class="wpie_field_mapping_container_data">
                                <div class="wpie_product_data_section">
                                        <div class="wpie_customer_menu_wrapper">
                                                <div class="wpie_customer_item_list wpie_customer_item_billing active_tab" display_block="wpie_customer_billing_wrapper"><?php esc_html_e( 'Billing', 'vj-wp-import-export' ); ?></div>
                                                <div class="wpie_customer_item_list wpie_customer_item_shipping" display_block="wpie_customer_shipping_wrapper"><?php esc_html_e( 'Shipping', 'vj-wp-import-export' ); ?></div>
                                        </div>
                                        <div class="wpie_product_content_wrapper">
                                                <div class="wpie_customer_item_data_container wpie_customer_billing_wrapper wpie_show">
                                                        <div class="wpie_customer_billing_field_container">
                                                                <div class="wpie_customer_billing_data">
                                                                        <div class="wpie_customer_billing_data_outer">
                                                                                <div class="wpie_customer_billing_data_inner">
                                                                                        <div class="wpie_customer_billing_data_label">
                                                                                            <?php esc_html_e( 'First Name', 'vj-wp-import-export' ); ?>
                                                                                        </div>
                                                                                        <div class="wpie_customer_billing_data_container">
                                                                                                <input type="text" class="wpie_content_data_input wpie_item_billing_first_name" name="wpie_item_billing_first_name" value=""/>
                                                                                        </div>
                                                                                </div>
                                                                                <div class="wpie_customer_billing_data_inner">
                                                                                        <div class="wpie_customer_billing_data_label">
                                                                                            <?php esc_html_e( 'Last Name', 'vj-wp-import-export' ); ?>
                                                                                        </div>
                                                                                        <div class="wpie_customer_billing_data_container">
                                                                                                <input type="text" class="wpie_content_data_input wpie_item_billing_last_name" name="wpie_item_billing_last_name" value=""/>
                                                                                        </div>
                                                                                </div>
                                                                        </div>
                                                                        <div class="wpie_customer_billing_data_outer">
                                                                                <div class="wpie_customer_billing_data_inner">
                                                                                        <div class="wpie_customer_billing_data_label">
                                                                                            <?php esc_html_e( 'Address 1', 'vj-wp-import-export' ); ?>
                                                                                        </div>
                                                                                        <div class="wpie_customer_billing_data_container">
                                                                                                <input type="text" class="wpie_content_data_input wpie_item_billing_address_1" name="wpie_item_billing_address_1" value=""/>
                                                                                        </div>
                                                                                </div>
                                                                                <div class="wpie_customer_billing_data_inner">
                                                                                        <div class="wpie_customer_billing_data_label">
                                                                                            <?php esc_html_e( 'Address 2', 'vj-wp-import-export' ); ?>
                                                                                        </div>
                                                                                        <div class="wpie_customer_billing_data_container">
                                                                                                <input type="text" class="wpie_content_data_input wpie_item_billing_address_2" name="wpie_item_billing_address_2" value=""/>
                                                                                        </div>
                                                                                </div>
                                                                        </div>
                                                                        <div class="wpie_customer_billing_data_outer">
                                                                                <div class="wpie_customer_billing_data_inner">
                                                                                        <div class="wpie_customer_billing_data_label">
                                                                                            <?php esc_html_e( 'City', 'vj-wp-import-export' ); ?>
                                                                                        </div>
                                                                                        <div class="wpie_customer_billing_data_container">
                                                                                                <input type="text" class="wpie_content_data_input wpie_item_billing_city" name="wpie_item_billing_city" value=""/>
                                                                                        </div>
                                                                                </div>
                                                                                <div class="wpie_customer_billing_data_inner">
                                                                                        <div class="wpie_customer_billing_data_label">
                                                                                            <?php esc_html_e( 'Postcode', 'vj-wp-import-export' ); ?>
                                                                                        </div>
                                                                                        <div class="wpie_customer_billing_data_container">
                                                                                                <input type="text" class="wpie_content_data_input wpie_item_billing_postcode" name="wpie_item_billing_postcode" value=""/>
                                                                                        </div>
                                                                                </div>
                                                                        </div>
                                                                        <div class="wpie_customer_billing_data_outer">
                                                                                <div class="wpie_customer_billing_data_inner">
                                                                                        <div class="wpie_customer_billing_data_label">
                                                                                            <?php esc_html_e( 'Country', 'vj-wp-import-export' ); ?>
                                                                                        </div>
                                                                                        <div class="wpie_customer_billing_data_container">
                                                                                                <input type="text" class="wpie_content_data_input wpie_item_billing_country" name="wpie_item_billing_country" value=""/>
                                                                                        </div>
                                                                                </div>
                                                                                <div class="wpie_customer_billing_data_inner">
                                                                                        <div class="wpie_customer_billing_data_label">
                                                                                            <?php esc_html_e( 'State/Country', 'vj-wp-import-export' ); ?>
                                                                                        </div>
                                                                                        <div class="wpie_customer_billing_data_container">
                                                                                                <input type="text" class="wpie_content_data_input wpie_item_billing_state" name="wpie_item_billing_state" value=""/>
                                                                                        </div>
                                                                                </div>
                                                                        </div>
                                                                        <div class="wpie_customer_billing_data_outer">
                                                                                <div class="wpie_customer_billing_data_inner">
                                                                                        <div class="wpie_customer_billing_data_label">
                                                                                            <?php esc_html_e( 'Email', 'vj-wp-import-export' ); ?>
                                                                                        </div>
                                                                                        <div class="wpie_customer_billing_data_container">
                                                                                                <input type="text" class="wpie_content_data_input wpie_item_billing_email" name="wpie_item_billing_email" value=""/>
                                                                                        </div>
                                                                                </div>
                                                                                <div class="wpie_customer_billing_data_inner">
                                                                                        <div class="wpie_customer_billing_data_label">
                                                                                            <?php esc_html_e( 'Phone', 'vj-wp-import-export' ); ?>
                                                                                        </div>
                                                                                        <div class="wpie_customer_billing_data_container">
                                                                                                <input type="text" class="wpie_content_data_input wpie_item_billing_phone" name="wpie_item_billing_phone" value=""/>
                                                                                        </div>
                                                                                </div>
                                                                        </div>
                                                                        <div class="wpie_customer_billing_data_outer">
                                                                                <div class="wpie_customer_billing_data_inner">
                                                                                        <div class="wpie_customer_billing_data_label">
                                                                                            <?php esc_html_e( 'Company', 'vj-wp-import-export' ); ?>
                                                                                        </div>
                                                                                        <div class="wpie_customer_billing_data_container">
                                                                                                <input type="text" class="wpie_content_data_input wpie_item_billing_company" name="wpie_item_billing_company" value=""/>
                                                                                        </div>
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                        </div>
                                                </div>
                                                <div class="wpie_customer_item_data_container wpie_customer_shipping_wrapper">
                                                        <div class="wpie_customer_shipping_field_container">
                                                                <div class="wpie_product_element_data_container">
                                                                        <div class="wpie_field_mapping_other_option_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_customer_shipping_source wpie_item_customer_shipping_source_copy" name="wpie_item_customer_shipping_source" id="wpie_item_customer_shipping_source_copy" value="copy" checked="checked"/>
                                                                                <label for="wpie_item_customer_shipping_source_copy" class="wpie_radio_label"><?php esc_html_e( 'Copy from billing', 'vj-wp-import-export' ); ?></label>
                                                                        </div>
                                                                        <div class="wpie_product_element_wrapper">
                                                                                <input type="radio" class="wpie_radio wpie_item_customer_shipping_source wpie_item_customer_shipping_source_import" name="wpie_item_customer_shipping_source" id="wpie_item_customer_shipping_source_import" value="import"/>
                                                                                <label for="wpie_item_customer_shipping_source_import" class="wpie_radio_label"><?php esc_html_e( 'Import shipping address', 'vj-wp-import-export' ); ?></label>
                                                                                <div class="wpie_radio_container">
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
                                        </div>
                                </div>
                        </div>
                </div>
                <?php
                $item_billing = ob_get_clean();

                $field_mapping_sections = array(
                        '160' => $item_billing,
                );
                ;

                return apply_filters( "wpie_pre_wc_customer_field_mapping_section", array_replace( $sections, $field_mapping_sections ), $wpie_import_type );
        }

}