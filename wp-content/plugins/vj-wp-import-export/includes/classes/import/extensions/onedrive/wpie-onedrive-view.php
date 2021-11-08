<?php
if ( !defined( 'ABSPATH' ) ) {
        die( __( "Can't load this file directly", 'vj-wp-import-export' ) );
}
$wpie_options = get_option( 'wpie_import_onedrive_file_upload' );
$_key         = "";
if ( !empty( $wpie_options ) ) {
        $wpie_options = maybe_unserialize( $wpie_options );
        $_key         = isset( $wpie_options[ 'wpie_onedrive_client_id' ] ) ? $wpie_options[ 'wpie_onedrive_client_id' ] : "";
}
unset( $wpie_options );

$is_valid = true;
if ( empty( $_key ) ) {
        $is_valid = false;
}

$odConfingUrl = add_query_arg( [ 'page' => 'wpie-extensions', 'wpie_ext' => 'wpie_import_onedrive_file_upload' ], admin_url( "admin.php" ) );
$odImportUrl  = add_query_arg( [ 'page' => 'wpie-new-import' ], admin_url( "admin.php" ) );

?>

<div class="wpie_upload_outer_container" >
        <input type="hidden" value="" class="wpie_upload_final_file" />
        <input type="hidden" value="<?php echo esc_attr( $_key ); ?>" class="wpie_onedrive_client_id" />
        <input type="hidden" value="<?php echo esc_attr( $odImportUrl ); ?>" class="wpie_onedrive_redirect_uri" />
        <div  class="wpie_file_upload_container">
                <div class="wpie_element_full_wrapper">
                        <div class="wpie_element_title"><?php esc_html_e( 'Click For Choose File', 'vj-wp-import-export' ); ?></div>         
                </div>
                <div class="wpie_download_btn_wrapper">
                        <?php if ( $is_valid ) { ?>
                                <div class="wpie_btn wpie_btn_primary wpie_onedrive_upload_btn">
                                        <i class="fas fa-cloud wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Choose From Onedrive ', 'vj-wp-import-export' ); ?>
                                </div>
                        <?php } ?>
                        <a class="wpie_btn wpie_btn_primary wpie_dropbox_config_btn" href="<?php echo esc_url( $dConfingUrl ); ?>">
                                <i class="fas fa-cog wpie_general_btn_icon " aria-hidden="true"></i><?php esc_html_e( 'Configure Onedrive ', 'vj-wp-import-export' ); ?>
                        </a>
                </div>
        </div>
        <div class="wpie_file_list_wrapper"></div>
        <div class="wpie_excel_sheets_wrapper"></div>
</div>