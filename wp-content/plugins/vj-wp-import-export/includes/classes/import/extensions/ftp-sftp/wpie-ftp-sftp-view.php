<?php
if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}

?>
<div class="wpie_upload_outer_container" >
        <input type="hidden" name="wpie_ftp_details" class="wpie_ftp_details" value="" >
        <div  class="wpie_file_upload_container wpie_ftp_upload_container">
                <div class="wpie_element_half_wrapper">
                        <div class="wpie_element_title"><?php esc_html_e( 'Host', 'vj-wp-import-export' ); ?></div>
                        <div class="wpie_element_data">
                                <input class="wpie_content_data_input wpie_ftp_hostname" type="text" name="wpie_ftp_hostname" value="">
                        </div>
                        <div class="wpie_element_hint">example.com</div>
                </div>
                <div class="wpie_element_half_wrapper">
                        <div class="wpie_element_title"><?php esc_html_e( 'Port', 'vj-wp-import-export' ); ?></div>
                        <div class="wpie_element_data">
                                <input class="wpie_content_data_input wpie_ftp_host_port" type="text" name="wpie_ftp_host_port" value="">
                        </div>
                        <div class="wpie_element_hint"><?php esc_html_e( 'Default Port : 21', 'vj-wp-import-export' ); ?></div>
                </div>
                <div class="wpie_element_half_wrapper">
                        <div class="wpie_element_title"><?php esc_html_e( 'Username', 'vj-wp-import-export' ); ?></div>
                        <div class="wpie_element_data">
                                <input class="wpie_content_data_input wpie_ftp_host_username" type="text" name="wpie_ftp_host_username" value="">
                        </div>
                </div>
                <div class="wpie_element_half_wrapper">
                        <div class="wpie_element_title"><?php esc_html_e( 'Password', 'vj-wp-import-export' ); ?></div>
                        <div class="wpie_element_data">
                                <input class="wpie_content_data_input wpie_ftp_host_password" type="password" name="wpie_ftp_host_password" value="" >
                        </div>
                </div>
                <div class="wpie_element_full_wrapper">
                        <div class="wpie_element_title"><?php esc_html_e( 'Path', 'vj-wp-import-export' ); ?></div>
                        <div class="wpie_element_data">
                                <input class="wpie_content_data_input wpie_ftp_host_path" type="text" name="wpie_ftp_host_path" value="">
                        </div>
                        <div class="wpie_element_hint">/home/example/sample.csv</div>
                </div>
                <div class="wpie_element_full_wrapper">
                        <div class="wpie_element_title"><?php esc_html_e( 'Connection Type', 'vj-wp-import-export' ); ?></div>
                        <div class="wpie_element_data wpie_connection_type_wrapper">
                                <div class="wpie_radio_wrapper">
                                        <input type="radio" class="wpie_radio wpie_connection_type" checked="checked" name="wpie_connection_type" id="wpie_connection_type_ftp" value="ftp"/>
                                        <label for="wpie_connection_type_ftp" class="wpie_radio_label"><?php esc_html_e( 'FTP', 'vj-wp-import-export' ); ?></label>                                                       
                                </div>
                                <div class="wpie_radio_wrapper">
                                        <input type="radio" class="wpie_radio wpie_connection_type" name="wpie_connection_type" id="wpie_connection_type_ftps" value="ftps"/>
                                        <label for="wpie_connection_type_ftps" class="wpie_radio_label"><?php esc_html_e( 'FTPS (SSL)', 'vj-wp-import-export' ); ?></label>                                                       
                                </div>
                                <div class="wpie_radio_wrapper">
                                        <input type="radio" class="wpie_radio wpie_connection_type" name="wpie_connection_type" id="wpie_connection_type_sftp" value="sftp"/>
                                        <label for="wpie_connection_type_sftp" class="wpie_radio_label"><?php esc_html_e( 'SSH2 / SFTP', 'vj-wp-import-export' ); ?></label>                                                       
                                </div>
                        </div>
                </div>
                <div class="wpie_download_btn_wrapper">
                        <div class="wpie_btn wpie_btn_primary wpie_ftp_upload_btn">
                                <i class="fas fa-download wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Download', 'vj-wp-import-export' ); ?>
                        </div>
                </div>
        </div>
        <div class="wpie_file_list_wrapper"></div>
        <div class="wpie_excel_sheets_wrapper"></div>
</div>