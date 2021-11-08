<?php
/**
 * Cresta Social Messenger Metabox
 */
 
/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
 
function cresta_facebook_messenger_add_meta_box() {
	$cfm_options = get_option( 'crestafacebookmessenger_settings' );
	$facebook_show_floating_box = $cfm_options['cresta_facebook_messenger_show_floating_box'];
	if ($facebook_show_floating_box == 1) {
		$thePostType = $cfm_options['cresta_facebook_messenger_selected_page'];
		$screens = explode(",",$thePostType);
		foreach ( $screens as $screen ) {
			add_meta_box(
				'cresta_facebook_messenger_sectionid',
				esc_html__( 'Cresta Social Messenger', 'cresta-facebook-messenger' ),
				'cresta_facebook_messenger_metabox_callback',
				$screen,
				'side',
				'low'
			);
		}
	}
}
add_action( 'add_meta_boxes', 'cresta_facebook_messenger_add_meta_box' );

function cresta_facebook_messenger_metabox_callback( $post ) {
	wp_nonce_field( 'cresta_facebook_messenger_meta_box', 'cresta_facebook_messenger_nonce' );
	$crestaValue = get_post_meta( $post->ID, '_get_cresta_facebook_messenger_plugin', true );
	?>
	<label for="cresta_facebook_messenger_new_field">
        <input type="checkbox" name="cresta_facebook_messenger_new_field" id="cresta_facebook_messenger_new_field" value="1" <?php checked( $crestaValue, '1' ); ?> /><?php esc_html_e( 'Hide Cresta Social Messenger in this page?', 'cresta-facebook-messenger' )?>
    </label>
	<?php
}

function cresta_facebook_messenger_save_meta_box_data( $post_id ) {
	if ( ! isset( $_POST['cresta_facebook_messenger_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['cresta_facebook_messenger_nonce'], 'cresta_facebook_messenger_meta_box' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	if ( isset( $_POST['cresta_facebook_messenger_new_field'] ) ) {
		update_post_meta( $post_id, '_get_cresta_facebook_messenger_plugin', sanitize_text_field(wp_unslash($_POST['cresta_facebook_messenger_new_field'])) );
	} else {
		delete_post_meta( $post_id, '_get_cresta_facebook_messenger_plugin' );
	}
	
}
add_action( 'save_post', 'cresta_facebook_messenger_save_meta_box_data' );