<?php
if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

?>
<td class="wpie_order_item_container wpie_advance_options_items">
        <div class="wpie_options_data wpie_order_item_row_option_wrapper">
                <div class="wpie_options_data_title"><?php esc_html_e( 'WC Order Product Items', 'vj-wp-import-export' ); ?></div>
                <div class="wpie_options_data_content">
                        <input type="checkbox" class="wpie_checkbox wpie_order_item_sigle_row" id="wpie_order_item_sigle_row"  name="wpie_order_item_sigle_row" value="1"/>
                        <label for="wpie_order_item_sigle_row" class="wpie_checkbox_label"><?php esc_html_e( 'Display each product in its own row', 'vj-wp-import-export' ); ?></label>
                </div>
                <div class="wpie_order_item_fill_empty_wrapper wpie_hide">
                        <input type="checkbox" class="wpie_checkbox wpie_order_item_fill_empty" checked="checked" id="wpie_order_item_fill_empty" name="wpie_order_item_fill_empty" value="1"/>
                        <label for="wpie_order_item_fill_empty" class="wpie_checkbox_label"><?php esc_html_e( 'Fill in empty columns', 'vj-wp-import-export' ); ?></label>
                </div>
        </div>
</td>
<td class="wpie_product_variations_container wpie_advance_options_items">
        <div class="wpie_options_data wpie_product_option_wrapper">
                <div class="wpie_options_data_title"><?php esc_html_e( 'Product Variations', 'vj-wp-import-export' ); ?></div>
                <div class="wpie_options_data_content">
                        <input type="radio" class="wpie_radio wpie_product_variation_options wpie_product_variation_options_all" id="wpie_product_variation_options_all" checked="checked"  name="wpie_product_variation_options" value="all"/>
                        <label for="wpie_product_variation_options_all" class="wpie_radio_label"><?php esc_html_e( 'Export product variations and their parent products', 'vj-wp-import-export' ); ?></label>
                </div>                
                <div class="wpie_options_data_content">
                        <input type="radio" class="wpie_radio wpie_product_variation_options wpie_product_variation_options_child" id="wpie_product_variation_options_child"  name="wpie_product_variation_options" value="child"/>
                        <label for="wpie_product_variation_options_child" class="wpie_radio_label"><?php esc_html_e( 'Only export product variations', 'vj-wp-import-export' ); ?></label>
                </div>
                <div class="wpie_options_data_content">
                        <input type="radio" class="wpie_radio wpie_product_variation_options wpie_product_variation_options_parent" id="wpie_product_variation_options_parent"  name="wpie_product_variation_options" value="parent"/>
                        <label for="wpie_product_variation_options_parent" class="wpie_radio_label"><?php esc_html_e( 'Only export parent products', 'vj-wp-import-export' ); ?></label>
                </div>
                <div class="wpie_export_default_warning wpie_export_product_variation_warning"><?php esc_html_e( 'Note : Export With Settings For Import option not available for only child or only parent product', 'vj-wp-import-export' ); ?></div>
        </div>
</td>
<td class="wpie_wc_customer_container wpie_advance_options_items">
        <div class="wpie_options_data wpie_product_option_wrapper">
                <div class="wpie_options_data_title"><?php esc_html_e( 'Customer Options', 'vj-wp-import-export' ); ?></div>
                <div class="wpie_options_data_content">
                        <input type="checkbox" class="wpie_export_include_bom_chk wpie_checkbox wpie_export_only_customer_with_purchase" id="wpie_export_only_customer_with_purchase" name="wpie_export_only_customer_with_purchase" value="1"/>
                        <label for="wpie_export_only_customer_with_purchase" class="wpie_export_only_customer_with_purchase wpie_checkbox_label"><?php esc_html_e( 'Only export customers who have made a purchase ?', 'vj-wp-import-export' ); ?></label>
                </div>
        </div>
</td>