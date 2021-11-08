<?php
if ( ! defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}
$get_schedules_list = \wpie\WPIE_Schedule::get_schedules();

$schedules_start_int_time = array ( '00:00', '00:30', '01:00', '01:30', '02:00', '02:30', '03:00', '03:30', '04:00', '04:30', '05:00', '05:30',
        '06:00', '06:30', '07:00', '07:30', '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
        '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30',
        '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00', '21:30', '22:00', '22:30', '23:00', '23:30'
);

ob_start();
?>
<div class="wpie_section_wrapper">
    <div class="wpie_content_data_header wpie_schedule_export">
        <div class="wpie_content_title"><?php esc_html_e( 'Automatic Scheduling with Background Export', 'vj-wp-import-export' ); ?></div>
        <div class="wpie_layout_header_icon_wrapper"><i class="fas fa-chevron-up wpie_layout_header_icon wpie_layout_header_icon_collapsed" aria-hidden="true"></i><i class="fas fa-chevron-down wpie_layout_header_icon wpie_layout_header_icon_expand" aria-hidden="true"></i></div>
    </div>
    <div class="wpie_section_content ">
        <div class="wpie_content_data_wrapper">
            <table class="wpie_content_data_tbl table table-bordered">
                <tr>
                    <td>
                        <div class="wpie_options_data">
                            <div class="wpie_options_data_title"><?php esc_html_e( 'Schedule Friendly Name', 'vj-wp-import-export' ); ?></div>
                            <div class="wpie_options_data_content">
                                <input type="text" class="wpie_content_data_input" value="" name="wpie_scheduled_name"/>
                                <div class="wpie_export_default_hint"><?php esc_html_e( 'Give any name for schedule', 'vj-wp-import-export' ); ?></div>
                            </div>
                        </div>
                    </td>
                    <td >
                        <div class="wpie_options_data">
                            <div class="wpie_options_data_title"><?php esc_html_e( 'Export Interval', 'vj-wp-import-export' ); ?></div>
                            <div class="wpie_options_data_content">
                                <select class="wpie_content_data_select wpie_sceduled_export_interval" data-placeholder="<?php esc_attr_e( 'Select Interval', 'vj-wp-import-export' ); ?>" name="wpie_export_interval">
                                    <?php
                                    if ( ! empty( $get_schedules_list ) ) {
                                            foreach ( $get_schedules_list as $key => $value ) {
                                                    ?>
                                                    <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value[ 'display' ] ); ?></option>
                                                    <?php
                                            }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </td>

                </tr>
                <tr>
                    <td>
                        <div class="wpie_options_data">
                            <div class="wpie_options_data_title"><?php esc_html_e( 'Export Interval Start Time', 'vj-wp-import-export' ); ?></div>
                            <div class="wpie_options_data_content">
                                <select class="wpie_content_data_select wpie_sceduled_export_interval_start_time" data-placeholder="' . esc_attr__('Select Interval TIme', 'vj-wp-import-export') . '" name="wpie_interval_start_time">
                                    <option value=""><?php esc_html_e( 'Current time', 'vj-wp-import-export' ); ?></option>
                                    <?php foreach ( $schedules_start_int_time as $int_time ) { ?>
                                            <option value="<?php echo esc_attr( $int_time ); ?>"><?php echo esc_html( $int_time ); ?></option>
                                    <?php } ?>
                                </select>
                                <div class="wpie_export_default_hint"><?php esc_html_e( 'Default : Current time, Value : 00:00 to 23:30', 'vj-wp-import-export' ); ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="wpie_options_data">
                            <div class="wpie_options_data_content">
                                <input type="checkbox" class="wpie_checkbox wpie_migrate_package" id="is_migrate_package" name="is_migrate_package" value="1"/>
                                <label for="is_migrate_package" class="wpie_checkbox_label"><?php esc_html_e( 'Migrate Package', 'vj-wp-import-export' ); ?></label>
                                <i class="far fa-question-circle wpie_data_tipso" data-tipso="<?php esc_attr_e( "Export with migrate package and configuration for later import any data", "vj-wp-import-export" ); ?>"></i>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="wpie_options_data">
                            <div class="wpie_options_data_content">
                                <input type="checkbox" class="wpie_scheduled_send_email_chk wpie_scheduled_send_email wpie_checkbox" id="wpie_scheduled_send_email" name="wpie_scheduled_send_email" value="1"/>
                                <label for="wpie_scheduled_send_email" class="wpie_options_data_title_email wpie_checkbox_label"><?php esc_html_e( 'Send E-mail with attachment', 'vj-wp-import-export' ); ?></label>
                            </div>
                        </div>
                        <div class="wpie_options_data_send_mail">
                            <div class="wpie_schedule_mail_wrapper">
                                <div class="wpie_options_data">
                                    <div class="wpie_options_data_title"><?php esc_html_e( 'Enter Email Recipient(s)', 'vj-wp-import-export' ); ?></div>
                                    <div class="wpie_options_data_content">
                                        <input type="text" class="wpie_content_data_input" value="" name="wpie_scheduled_email_recipient"/>
                                        <div class="wpie_export_default_hint"><?php esc_html_e( 'Ex. example@gmail.com, demo@yahoo.com', 'vj-wp-import-export' ); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="wpie_schedule_mail_wrapper">
                                <div class="wpie_options_data ">
                                    <div class="wpie_options_data_title"><?php esc_html_e( 'Enter Email Subject', 'vj-wp-import-export' ); ?></div>
                                    <div class="wpie_options_data_content">
                                        <input type="text" class="wpie_content_data_input" value="" name="wpie_scheduled_email_subject"/>
                                    </div>
                                </div>
                            </div>
                            <div class="wpie_schedule_mail_wrapper">
                                <div class="wpie_options_data">
                                    <div class="wpie_options_data_title"><?php esc_html_e( 'Enter Email message', 'vj-wp-import-export' ); ?></div>
                                    <div class="wpie_options_data_content">
                                        <textarea class="wpie_sceduled_export_msg" name="wpie_scheduled_email_msg"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="wpie_save_scheduled_btn_wrapper"> 
                <div class="wpie_btn wpie_btn_secondary wpie_btn_radius wpie_save_scheduled_btn">
                    <i class="fas fa-check wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Save Scheduled', 'vj-wp-import-export' ); ?>
                </div>
            </div>
        </div>
    </div>
</div>
