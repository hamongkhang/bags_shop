<?php
/**
 * Tag Added
 *
 * @package     AutomatorWP\Integrations\FluentCRM\Triggers\Tag_Added
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_FluentCRM_Tag_Added extends AutomatorWP_Integration_Trigger {

    public $integration = 'fluentcrm';
    public $trigger = 'fluentcrm_tag_added';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'Tag added to user', 'automatorwp' ),
            'select_option'     => __( '<strong>Tag</strong> added to user', 'automatorwp' ),
            /* translators: %1$s: Tag. %2$s: Number of times. */
            'edit_label'        => sprintf( __( '%1$s added to user %2$s time(s)', 'automatorwp' ), '{tag}', '{times}' ),
            /* translators: %1$s: Tag. */
            'log_label'         => sprintf( __( '%1$s added to user', 'automatorwp' ), '{tag}' ),
            'action'            => 'fluentcrm_contact_added_to_tags',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'tag' => automatorwp_utilities_ajax_selector_option( array(
                    'field'             => 'tag',
                    'name'              => __( 'Tag:', 'automatorwp' ),
                    'option_none_value' => 'any',
                    'option_none_label' => __( 'any tag', 'automatorwp' ),
                    'action_cb'         => 'automatorwp_fluentcrm_get_tags',
                    'options_cb'        => 'automatorwp_fluentcrm_options_cb_tag',
                    'default'           => 'any'
                ) ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param array $tags_ids
     * @param \FluentCrm\App\Models\Subscriber $subscriber
     */
    public function listener( $tags_ids, $subscriber ) {

        $user_id = 0;

        // Get the user ID
        if( absint( $subscriber->user_id ) !== 0 ) {
            $user_id = $subscriber->user_id;
        } else if( ! empty( $subscriber->email ) ) {
            $user = get_user_by_email( $subscriber->email );

            $user_id = $user->ID;
        }

        // Make sure subscriber has a user ID assigned
        if ( $user_id === 0 ) {
            return;
        }

        foreach( $tags_ids as $tag_id ) {
            // Trigger the tag added
            automatorwp_trigger_event( array(
                'trigger'           => $this->trigger,
                'user_id'           => $user_id,
                'tag_id'            => $tag_id,
            ) );
        }

    }

    /**
     * User deserves check
     *
     * @since 1.0.0
     *
     * @param bool      $deserves_trigger   True if user deserves trigger, false otherwise
     * @param stdClass  $trigger            The trigger object
     * @param int       $user_id            The user ID
     * @param array     $event              Event information
     * @param array     $trigger_options    The trigger's stored options
     * @param stdClass  $automation         The trigger's automation object
     *
     * @return bool                          True if user deserves trigger, false otherwise
     */
    public function user_deserves_trigger( $deserves_trigger, $trigger, $user_id, $event, $trigger_options, $automation ) {

        // Don't deserve if post is not received
        if( ! isset( $event['tag_id'] ) ) {
            return false;
        }

        // Don't deserve if post doesn't match with the trigger option
        if( $trigger_options['tag'] !== 'any' && absint( $trigger_options['tag'] ) !== absint( $event['tag_id'] ) ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_FluentCRM_Tag_Added();