<?php
/**
 * Functions
 *
 * @package     AutomatorWP\FluentCRM\Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Gets the subscriber object from a user ID
 *
 * @param int $user_id
 *
 * @return FluentCrm\App\Models\Subscriber|null
 */
function automatorwp_fluentcrm_get_subscriber( $user_id ) {

    $subscriber = FluentCrm\App\Models\Subscriber::where( 'user_id', $user_id )->first();

    if( ! $subscriber ) {
        $user = get_userdata( $user_id );

        // Bail if user not exists
        if( ! $user ) {
            return;
        }

        $subscriber = FluentCrm\App\Models\Subscriber::where( 'email', $user->user_email )->first();

    }

    return $subscriber;

}

/**
 * Options callback for select2 fields assigned to tags
 *
 * @since 1.0.0
 *
 * @param stdClass $field
 *
 * @return array
 */
function automatorwp_fluentcrm_options_cb_tag( $field ) {

    // Setup vars
    $value = $field->escaped_value;
    $none_value = 'any';
    $none_label = __( 'any tag', 'automatorwp' );
    $options = automatorwp_options_cb_none_option( $field, $none_value, $none_label );

    if( ! empty( $value ) ) {
        if( ! is_array( $value ) ) {
            $value = array( $value );
        }

        foreach( $value as $tag_id ) {

            // Skip option none
            if( $tag_id === $none_value ) {
                continue;
            }

            $options[$tag_id] = automatorwp_fluentcrm_get_tag_title( $tag_id );
        }
    }

    return $options;

}

/**
 * Get the tag title
 *
 * @since 1.0.0
 *
 * @param int $tag_id
 *
 * @return string|null
 */
function automatorwp_fluentcrm_get_tag_title( $tag_id ) {

    // Empty title if no ID provided
    if( absint( $tag_id ) === 0 ) {
        return '';
    }

    global $wpdb;

    return $wpdb->get_var( $wpdb->prepare(
        "SELECT title FROM {$wpdb->prefix}fc_tags WHERE id = %s",
        $tag_id
    ) );

}