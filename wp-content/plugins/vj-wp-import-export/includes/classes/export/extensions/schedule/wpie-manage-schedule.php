<?php
if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

global $wpdb;

$schedule_templates = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "wpie_template where `opration`='schedule_export_template'" );

$scheduleList = wp_get_schedules();

if ( file_exists( WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export.php' ) ) {
        require_once(WPIE_EXPORT_CLASSES_DIR . '/class-wpie-export.php');
}
$wpie_export = new \wpie\export\WPIE_Export();

$export_type = $wpie_export->get_export_type();

$wpie_taxonomies_list = $wpie_export->wpie_get_taxonomies();

?>
<div class="wpie_section_wrapper">
        <div class="wpie_content_data_header wpie_section_wrapper_selected">
                <div class="wpie_content_title"><?php esc_html_e( 'Manage Schedule Export', 'vj-wp-import-export' ); ?></div>
                <div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div>
        </div>
        <div class="wpie_section_content wpie_show wpie_schedule_export_section">
                <div class="wpie_table_action_wrapper">
                        <div class="wpie_table_action_container">
                                <select class="wpie_content_data_select wpie_log_bulk_action">
                                        <option value=""><?php esc_html_e( 'Bulk Actions', 'vj-wp-import-export' ); ?></option>   
                                        <option value="delete"><?php esc_html_e( 'Delete', 'vj-wp-import-export' ); ?></option>   
                                </select>
                        </div>
                        <div class="wpie_table_action_btn_container">
                                <div class="wpie_btn wpie_btn_secondary wpie_btn_radius wpie_log_action_btn">
                                        <i class="fas fa-check wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Apply', 'vj-wp-import-export' ); ?>
                                </div>
                        </div>
                </div>
                <table class="wpie_log_table table table-bordered">
                        <thead>
                                <tr>
                                        <td class="wpie_log_check_wrapper">
                                                <input type="checkbox" class="wpie_checkbox wpie_log_check_all" id="wpie_schedule_log_check_all" value="1"/>
                                                <label for="wpie_schedule_log_check_all" class="wpie_checkbox_label"></label>
                                        </td>
                                        <td class="wpie_log_lable"><?php esc_html_e( 'Scheduled ID', 'vj-wp-import-export' ); ?></td>
                                        <td class="wpie_log_lable"><?php esc_html_e( 'Scheduled Name', 'vj-wp-import-export' ); ?></td>
                                        <td class="wpie_log_lable"><?php esc_html_e( 'Export Type', 'vj-wp-import-export' ); ?></td>
                                        <td class="wpie_log_lable"><?php esc_html_e( 'Recurrence Time', 'vj-wp-import-export' ); ?></td>
                                        <td class="wpie_log_lable"><?php esc_html_e( 'Send E-mail', 'vj-wp-import-export' ); ?></td>
                                        <td class="wpie_log_lable"><?php esc_html_e( 'Recipients', 'vj-wp-import-export' ); ?></td>
                                        <td class="wpie_log_lable"><?php esc_html_e( 'Next event', 'vj-wp-import-export' ); ?></td>
                                        <td class="wpie_log_lable"><?php esc_html_e( 'Actions', 'vj-wp-import-export' ); ?></td>
                                </tr>
                        </thead>
                        <tbody>
                                <?php
                                $is_empty_template = "";
                                if ( !empty( $schedule_templates ) ) {
                                        $is_empty_template = "wpie_hidden";

                                        $date_format = get_option( 'date_format' );

                                        $time_format = get_option( 'time_format' );

                                        foreach ( $schedule_templates as $template ) {

                                                $id = isset( $template->id ) ? $template->id : 0;

                                                $opration_type = isset( $template->opration_type ) ? $template->opration_type : "";

                                                $options = isset( $template->options ) ? maybe_unserialize( $template->options ) : array();

                                                $interval = isset( $options[ 'wpie_export_interval' ] ) ? $options[ 'wpie_export_interval' ] : "";

                                                $s_name = isset( $options[ 'wpie_scheduled_name' ] ) && !empty( $options[ 'wpie_scheduled_name' ] ) ? $options[ 'wpie_scheduled_name' ] : "";

                                                if ( empty( $s_name ) ) {

                                                        $create_date = isset( $template->create_date ) ? $template->create_date : date( "Y-m-d h:i:s" );

                                                        $s_name = __( 'Scheduled', 'vj-wp-import-export' ) . " " . date( $date_format . " " . $time_format, strtotime( $create_date ) );
                                                }

                                                $send_email = isset( $options[ 'wpie_scheduled_send_email' ] ) && $options[ 'wpie_scheduled_send_email' ] == 1 ? __( 'Yes', 'vj-wp-import-export' ) : __( 'No', 'vj-wp-import-export' );

                                                $recipient = isset( $options[ 'wpie_scheduled_email_recipient' ] ) ? $options[ 'wpie_scheduled_email_recipient' ] : 0;

                                                $next_scheduled = wp_next_scheduled( 'wpie_cron_schedule_export', [ absint( $id ) ] );

                                                if ( !$next_scheduled ) {
                                                        continue;
                                                }

                                                $intervalLabel = isset( $scheduleList[ $interval ] ) && isset( $scheduleList[ $interval ][ 'display' ] ) ? $scheduleList[ $interval ][ 'display' ] : $interval;

                                                $opration_subtype = "";

                                                if ( $opration_type === "taxonomies" ) {
                                                        $typeLabel        = $opration_type;
                                                        $opration_subtype = isset( $options[ 'wpie_taxonomy_type' ] ) && !empty( $options[ 'wpie_taxonomy_type' ] ) ? $options[ 'wpie_taxonomy_type' ] : "";

                                                        $opration_subtype = !empty( $opration_subtype ) && isset( $wpie_taxonomies_list[ $opration_subtype ] ) ? $wpie_taxonomies_list[ $opration_subtype ] : $opration_subtype;
                                                } else {
                                                        $typeLabel = isset( $export_type[ $opration_type ] ) ? $export_type[ $opration_type ] : $opration_type;
                                                }

                                                $newType = $typeLabel . (empty( $opration_subtype ) ? "" : " > " . $opration_subtype);

                                                $next_scheduled = date( 'Y-m-d H:i:s', $next_scheduled );

                                                $next_scheduled = get_date_from_gmt( $next_scheduled, $date_format . " " . $time_format );

                                                ?>
                                                <tr class="wpie_log_wrapper wpie_log_wrapper_<?php echo esc_attr( $id ); ?>">
                                                        <td class="wpie_log_check_wrapper">
                                                                <input type="checkbox" class="wpie_checkbox wpie_log_check" id="wpie_sschedule_log_check_<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $id ); ?>"/>
                                                                <label for="wpie_sschedule_log_check_<?php echo esc_attr( $id ); ?>" class="wpie_checkbox_label"></label>
                                                        </td>
                                                        <td class="wpie_log_data"><?php echo esc_html( $id ); ?></td>
                                                        <td class="wpie_log_data"><?php echo esc_html( $s_name ); ?></td>
                                                        <td class="wpie_log_data"><?php echo esc_html( ucwords( $newType ) ); ?></td>
                                                        <td class="wpie_log_data"><?php echo esc_html( $intervalLabel ); ?></td>
                                                        <td class="wpie_log_data"><?php echo esc_html( $send_email ); ?></td>
                                                        <td class="wpie_log_data"><?php echo esc_html( $recipient ); ?></td>
                                                        <td class="wpie_log_data"><?php echo esc_html( $next_scheduled ); ?></td>
                                                        <td class="wpie_log_data">
                                                                <div class="wpie_log_action_btns wpie_delete_template_btn"><i class="fas fa-trash wpie_general_btn_icon wpie_data_tipso" data-tipso="<?php esc_attr_e( 'Delete', 'vj-wp-import-export' ); ?>" aria-hidden="true"></i></div>
                                                        </td>
                                                </tr>
                                                <?php
                                                unset( $id, $opration_type, $options, $interval, $send_email, $recipient, $next_scheduled );
                                        }

                                        ?>
                                <?php } ?>
                                <tr class="<?php echo esc_attr( $is_empty_template ); ?> wpie_log_empty">
                                        <td colspan="9">
                                                <div class="wpie_empty_records"><?php esc_html_e( 'No Records Found', 'vj-wp-import-export' ); ?></div>
                                        </td>
                                </tr>
                                <?php unset( $is_empty_template ); ?>
                        </tbody>
                </table>
        </div>
</div>