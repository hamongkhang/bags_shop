<?php
/**
 * Displays the content of the dialog box when the user clicks on the "Deactivate" link on the plugin settings page
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Displays a confirmation and feedback dialog box when the user clicks on the "Deactivate" link on the plugins
 * page.
 */
if ( ! function_exists( 'appmaker_add_deactivation_feedback_dialog_box' ) ) {
	function appmaker_add_deactivation_feedback_dialog_box() {
		global $appmaker_wc_active_plugin;
		if ( empty( $appmaker_wc_active_plugin ) ) {
			return;
		}

		$contact_support_template = __( 'Need help? We are ready to answer your questions.', 'appmaker' ) . ' <a href="https://appmaker.freshdesk.com/support/tickets/new" target="_blank">' . __( 'Contact Support', 'appmaker' ) . '</a>';

		$reasons = array(
			array(
				'id'                => 'NOT_WORKING',
				'text'              => __( 'The plugin is not working', 'appmaker' ),
				'input_type'        => 'textarea',
				'input_placeholder' => esc_attr__( "Kindly share what didn't work so we can fix it in future updates.", 'appmaker' ),
			),
			array(
				'id'                => 'SUDDENLY_STOPPED_WORKING',
				'text'              => __( 'The plugin suddenly stopped working', 'appmaker' ),
				'input_type'        => '',
				'input_placeholder' => '',
				'internal_message'  => $contact_support_template,
			),
			array(
				'id'                => 'BROKE_MY_SITE',
				'text'              => __( 'The plugin broke my site', 'appmaker' ),
				'input_type'        => '',
				'input_placeholder' => '',
				'internal_message'  => $contact_support_template,
			),
			array(
				'id'                => 'COULDNT_MAKE_IT_WORK',
				'text'              => __( "I couldn't understand how to get it work", 'appmaker' ),
				'input_type'        => '',
				'input_placeholder' => '',
				'internal_message'  => $contact_support_template,
			),
			array(
				'id'                => 'FOUND_A_BETTER_PLUGIN',
				'text'              => __( 'I found a better plugin', 'appmaker' ),
				'input_type'        => '',
				'input_placeholder' => '',
			),
			array(
				'id'                => 'GREAT_BUT_NEED_SPECIFIC_FEATURE',
				'text'              => __( "The plugin is great, but I need specific feature that you don't support", 'appmaker' ),
				'input_type'        => 'textarea',
				'input_placeholder' => esc_attr__( 'Can you share more details on the missing feature?', 'appmaker' ),
			),
			array(
				'id'                => 'TEMPORARY_DEACTIVATION',
				'text'              => __( "It's a temporary deactivation, I'm just debugging an issue", 'appmaker' ),
				'input_type'        => '',
				'input_placeholder' => '',
			),
			array(
				'id'                => 'OTHER',
				'text'              => __( 'Other', 'appmaker' ),
				'input_type'        => 'textarea',
				'input_placeholder' => '',
			),
		);

		$modal_html = '<div class="appmaker_wc-modal appmaker_wc-modal-deactivation-feedback">
	    	<div class="appmaker_wc-modal-dialog">
	    		<div class="appmaker_wc-modal-body">
	    			<h2>' . __( 'Quick Feedback', 'appmaker' ) . '</h2>
	    			<div class="appmaker_wc-modal-panel active">
	    				<p>' . __( 'If you have a moment, please let us know why you are deactivating', 'appmaker' ) . ':</p><ul>';

		foreach ( $reasons as $reason ) {
			$list_item_classes = 'appmaker_wc-modal-reason' . ( ! empty( $reason['input_type'] ) ? ' has-input' : '' );

			if ( ! empty( $reason['internal_message'] ) ) {
				$list_item_classes      .= ' has-internal-message';
				$reason_internal_message = $reason['internal_message'];
			} else {
				$reason_internal_message = '';
			}

			$modal_html .= '<li class="' . $list_item_classes . '" data-input-type="' . $reason['input_type'] . '" data-input-placeholder="' . $reason['input_placeholder'] . '">
				<label>
					<span>
						<input type="radio" name="selected-reason" value="' . $reason['id'] . '"/>
					</span>
					<span>' . $reason['text'] . '</span>
				</label>
				<div class="appmaker_wc-modal-internal-message">' . $reason_internal_message . '</div>
			</li>';
		}
		$modal_html .= '</ul>
		    				<label class="appmaker_wc-modal-anonymous-label">
			    				<input type="checkbox" checked/>' .
								__( 'Send website data and allow to contact me back', 'appmaker' ) .
							'</label>
						</div>
					</div>
					<div class="appmaker_wc-modal-footer">
						<a href="#" class="button button-primary appmaker_wc-modal-button-deactivate"></a>
						<div class="clear"></div>
					</div>
				</div>
			</div>';

		$script = '';

		foreach ( $appmaker_wc_active_plugin as $basename => $plugin_data ) {

			$slug      = dirname( $basename );
			$plugin_id = sanitize_title( $plugin_data['Name'] );

			$script .= '(function($) {
					var modalHtml = ' . json_encode( $modal_html ) . ",
					    \$modal                = $( modalHtml ),
					    \$deactivateLink       = $( '#the-list .active[data-plugin=\"" . $basename . "\"] .deactivate a' ),
						\$anonymousFeedback    = \$modal.find( '.appmaker_wc-modal-anonymous-label' ),
						selectedReasonID      = false;

					/* WP added data-plugin attr after 4.5 version/ In prev version was id attr */
					if ( 0 == \$deactivateLink.length )
						\$deactivateLink = $( '#the-list .active#" . $plugin_id . " .deactivate a' );

					\$modal.appendTo( $( 'body' ) );

					appmaker_wcModalRegisterEventHandlers();
					
					function appmaker_wcModalRegisterEventHandlers() {
						\$deactivateLink.click( function( evt ) {
							evt.preventDefault();

							/* Display the dialog box.*/
							appmaker_wcModalReset();
							\$modal.addClass( 'active' );
							$( 'body' ).addClass( 'has-appmaker_wc-modal' );
						});

						\$modal.on( 'input propertychange', '.appmaker_wc-modal-reason-input input', function() {
							if ( ! appmaker_wcModalIsReasonSelected( 'OTHER' ) ) {
								return;
							}

							var reason = $( this ).val().trim();

							/* If reason is not empty, remove the error-message class of the message container to change the message color back to default. */
							if ( reason.length > 0 ) {
								\$modal.find( '.message' ).removeClass( 'error-message' );
								appmaker_wcModalEnableDeactivateButton();
							}
						});

						\$modal.on( 'blur', '.appmaker_wc-modal-reason-input input', function() {
							var \$userReason = $( this );

							setTimeout( function() {
								if ( ! appmaker_wcModalIsReasonSelected( 'OTHER' ) ) {
									return;
								}
							}, 150 );
						});

						\$modal.on( 'click', '.appmaker_wc-modal-footer .button', function( evt ) {
							evt.preventDefault();

							if ( $( this ).hasClass( 'disabled' ) ) {
								return;
							}

							var _parent = $( this ).parents( '.appmaker_wc-modal:first' ),
								_this =  $( this );

							if ( _this.hasClass( 'allow-deactivate' ) ) {
								var \$radio = \$modal.find( 'input[type=\"radio\"]:checked' );

								if ( 0 === \$radio.length ) {
									/* If no selected reason, just deactivate the plugin. */
									window.location.href = \$deactivateLink.attr( 'href' );
									return;
								}

								var \$selected_reason = \$radio.parents( 'li:first' ),
								    \$input = \$selected_reason.find( 'textarea, input[type=\"text\"]' ),
								    userReason = ( 0 !== \$input.length ) ? \$input.val().trim() : '';

								var is_anonymous = ( \$anonymousFeedback.find( 'input' ).is( ':checked' ) ) ? 0 : 1;

								$.ajax({
									url       : ajaxurl,
									method    : 'POST',
									data      : {
										'action'			: 'appmaker_submit_uninstall_reason_action',
										'plugin'			: '" . $basename . "',
										'reason_id'			: \$radio.val(),
										'reason_info'		: userReason,
										'is_anonymous'		: is_anonymous,
										'appmaker_wc_ajax_nonce'	: '" . wp_create_nonce( 'appmaker_wc_ajax_nonce' ) . "'
									},
									beforeSend: function() {
										_parent.find( '.appmaker_wc-modal-footer .button' ).addClass( 'disabled' );
										_parent.find( '.appmaker_wc-modal-footer .button-secondary' ).text( '" . __( 'Processing', 'appmaker' ) . "' + '...' );
									},
									complete  : function( message ) {
										/* Do not show the dialog box, deactivate the plugin. */
										window.location.href = \$deactivateLink.attr( 'href' );
									}
								});
							} else if ( _this.hasClass( 'appmaker_wc-modal-button-deactivate' ) ) {
								/* Change the Deactivate button's text and show the reasons panel. */
								_parent.find( '.appmaker_wc-modal-button-deactivate' ).addClass( 'allow-deactivate' );
								appmaker_wcModalShowPanel();
							}
						});

						\$modal.on( 'click', 'input[type=\"radio\"]', function() {
							var \$selectedReasonOption = $( this );

							/* If the selection has not changed, do not proceed. */
							if ( selectedReasonID === \$selectedReasonOption.val() )
								return;

							selectedReasonID = \$selectedReasonOption.val();

							\$anonymousFeedback.show();

							var _parent = $( this ).parents( 'li:first' );

							\$modal.find( '.appmaker_wc-modal-reason-input' ).remove();
							\$modal.find( '.appmaker_wc-modal-internal-message' ).hide();
							\$modal.find( '.appmaker_wc-modal-button-deactivate' ).text( '" . __( 'Submit and Deactivate', 'appmaker' ) . "' );

							appmaker_wcModalEnableDeactivateButton();

							if ( _parent.hasClass( 'has-internal-message' ) ) {
								_parent.find( '.appmaker_wc-modal-internal-message' ).show();
							}

							if (_parent.hasClass('has-input')) {
								var reasonInputHtml = '<div class=\"appmaker_wc-modal-reason-input\"><span class=\"message\"></span>' + ( ( 'textfield' === _parent.data( 'input-type' ) ) ? '<input type=\"text\" />' : '<textarea rows=\"5\" maxlength=\"200\"></textarea>' ) + '</div>';

								_parent.append( $( reasonInputHtml ) );
								_parent.find( 'input, textarea' ).attr( 'placeholder', _parent.data( 'input-placeholder' ) ).focus();

								if ( appmaker_wcModalIsReasonSelected( 'OTHER' ) ) {
									\$modal.find( '.message' ).text( '" . __( 'Please tell us the reason so we can improve it.', 'appmaker' ) . "' ).show();
								}
							}
						});

						/* If the user has clicked outside the window, cancel it. */
						\$modal.on( 'click', function( evt ) {
							var \$target = $( evt.target );

							/* If the user has clicked anywhere in the modal dialog, just return. */
							if ( \$target.hasClass( 'appmaker_wc-modal-body' ) || \$target.hasClass( 'appmaker_wc-modal-footer' ) ) {
								return;
							}

							/* If the user has not clicked the close button and the clicked element is inside the modal dialog, just return. */
							if ( ! \$target.hasClass( 'appmaker_wc-modal-button-close' ) && ( \$target.parents( '.appmaker_wc-modal-body' ).length > 0 || \$target.parents( '.appmaker_wc-modal-footer' ).length > 0 ) ) {
								return;
							}

							/* Close the modal dialog */
							\$modal.removeClass( 'active' );
							$( 'body' ).removeClass( 'has-appmaker_wc-modal' );

							return false;
						});
					}

					function appmaker_wcModalIsReasonSelected( reasonID ) {
						/* Get the selected radio input element.*/
						return ( reasonID == \$modal.find('input[type=\"radio\"]:checked').val() );
					}

					function appmaker_wcModalReset() {
						selectedReasonID = false;

						appmaker_wcModalEnableDeactivateButton();

						/* Uncheck all radio buttons.*/
						\$modal.find( 'input[type=\"radio\"]' ).prop( 'checked', false );

						/* Remove all input fields ( textfield, textarea ).*/
						\$modal.find( '.appmaker_wc-modal-reason-input' ).remove();

						\$modal.find( '.message' ).hide();

						/* Hide, since by default there is no selected reason.*/
						\$anonymousFeedback.hide();

						var \$deactivateButton = \$modal.find( '.appmaker_wc-modal-button-deactivate' );

						\$deactivateButton.addClass( 'allow-deactivate' );
						appmaker_wcModalShowPanel();
					}

					function appmaker_wcModalEnableDeactivateButton() {
						\$modal.find( '.appmaker_wc-modal-button-deactivate' ).removeClass( 'disabled' );
					}

					function appmaker_wcModalDisableDeactivateButton() {
						\$modal.find( '.appmaker_wc-modal-button-deactivate' ).addClass( 'disabled' );
					}

					function appmaker_wcModalShowPanel() {
						\$modal.find( '.appmaker_wc-modal-panel' ).addClass( 'active' );
						/* Update the deactivate button's text */
						\$modal.find( '.appmaker_wc-modal-button-deactivate' ).text( '" . __( 'Skip and Deactivate', 'appmaker' ) . "' );
					}
				})(jQuery);";
		}

		/* add script in FOOTER */
		wp_register_script( 'appmaker-deactivation-feedback-dialog-boxes', '', array( 'jquery' ), false, true );
		wp_enqueue_script( 'appmaker-deactivation-feedback-dialog-boxes' );
		wp_add_inline_script( 'appmaker-deactivation-feedback-dialog-boxes', sprintf( $script ) );
	}
}

/**
 * Called after the user has submitted his reason for deactivating the plugin.
 *
 * @since  2.1.3
 */
if ( ! function_exists( 'appmaker_submit_uninstall_reason_action' ) ) {
	function appmaker_submit_uninstall_reason_action() {
		global $appmaker_wc_options, $wp_version, $appmaker_wc_active_plugin, $current_user;

		wp_verify_nonce( $_REQUEST['appmaker_wc_ajax_nonce'], 'appmaker_wc_ajax_nonce' );

		$reason_id = isset( $_REQUEST['reason_id'] ) ? stripcslashes( sanitize_text_field( $_REQUEST['reason_id'] ) ) : '';
		$basename  = isset( $_REQUEST['plugin'] ) ? stripcslashes( sanitize_text_field( $_REQUEST['plugin'] ) ) : '';

		if ( empty( $reason_id ) || empty( $basename ) ) {
			exit;
		}

		$reason_info = isset( $_REQUEST['reason_info'] ) ? stripcslashes( sanitize_textarea_field( $_REQUEST['reason_info'] ) ) : '';
		if ( ! empty( $reason_info ) ) {
			$reason_info = substr( $reason_info, 0, 255 );
		}
		$is_anonymous = isset( $_REQUEST['is_anonymous'] ) && 1 == $_REQUEST['is_anonymous'];

		$options = array(
			'product'     => $basename,
			'reason_id'   => $reason_id,
			'reason_info' => $reason_info,
		);

		if ( ! $is_anonymous ) {
			if ( ! isset( $appmaker_wc_settings ) ) {
				$appmaker_wc_settings = ( is_multisite() ) ? get_site_option( 'appmaker_wc_settings' ) : get_option( 'appmaker_wc_settings' );
			}

			$options['project_id'] = ! empty( $appmaker_wc_settings['project_id'] ) ? $appmaker_wc_settings['project_id'] : false;
			$options['url']                  = get_bloginfo( 'url' );
			$options['wp_version']           = $wp_version;
			$options['is_active']            = false;
			$options['version']              = $appmaker_wc_active_plugin[ $basename ]['Version'];

			$options['email'] = $current_user->data->user_email;
		}

		/* send data */
		$raw_response = wp_remote_post(
			'https://us-central1-appmaker-core.cloudfunctions.net/pluginFeedback',
			array(
				'method'  => 'POST',
				'body'    => $options,
				'timeout' => 15,
			)
		);

		if ( ! is_wp_error( $raw_response ) && 200 == wp_remote_retrieve_response_code( $raw_response ) ) {
			if ( ! $is_anonymous ) {
				$response = maybe_unserialize( wp_remote_retrieve_body( $raw_response ) );
			}
			echo 'done';
		} else {
			echo $response->get_error_code() . ': ' . $response->get_error_message();
		}
		exit;
	}
}

add_action( 'wp_ajax_appmaker_submit_uninstall_reason_action', 'appmaker_submit_uninstall_reason_action' );
