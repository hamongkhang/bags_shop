<?php
/**
 * Add Level
 *
 * @package     AutomatorWP\Integrations\WishList_Member\Triggers\Add_Level
 * @author      AutomatorWP <contact@automatorwp.com>, Ruben Garcia <rubengcdev@gmail.com>
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

class AutomatorWP_WishList_Member_Add_Level extends AutomatorWP_Integration_Trigger {

    public $integration = 'wishlist_member';
    public $trigger = 'wishlist_member_add_level';

    /**
     * Register the trigger
     *
     * @since 1.0.0
     */
    public function register() {

        automatorwp_register_trigger( $this->trigger, array(
            'integration'       => $this->integration,
            'label'             => __( 'User gets added to a level', 'automatorwp' ),
            'select_option'     => __( 'User gets <strong>added</strong> to a level', 'automatorwp' ),
            /* translators: %1$s: Level. %2$s: Number of times. */
            'edit_label'        => sprintf( __( 'User gets added to %1$s %2$s time(s)', 'automatorwp' ), '{level}', '{times}' ),
            /* translators: %1$s: Level. */
            'log_label'         => sprintf( __( 'User gets added to %1$s', 'automatorwp' ), '{level}' ),
            'action'            => 'wishlistmember_add_user_levels',
            'function'          => array( $this, 'listener' ),
            'priority'          => 10,
            'accepted_args'     => 2,
            'options'           => array(
                'level' => array(
                    'from' => 'level',
                    'fields' => array(
                        'level' => array(
                            'name' => __( 'Level:', 'automatorwp' ),
                            'type' => 'select',
                            'options_cb' => array( $this, 'options_cb_levels' ),
                            'default' => 'any'
                        )
                    )
                ),
                'times' => automatorwp_utilities_times_option(),
            ),
            'tags' => array_merge(
                automatorwp_utilities_times_tag()
            )
        ) );

    }

    /**
     * Options callback for levels options
     *
     * @since 1.0.0
     *
     * @param stdClass $field
     *
     * @return array
     */
    public function options_cb_levels( $field ) {

        global $WishListMemberInstance;

        $options = array(
            'any' => __( 'any level', 'automatorwp' ),
        );

        $levels = $WishListMemberInstance->GetOption( 'wpm_levels' );

        if( is_array( $levels ) ) {
            foreach( $levels as $level ) {
                $options[$level['id']] = $level['name'];
            }
        }

        return $options;

    }

    /**
     * Trigger listener
     *
     * @since 1.0.0
     *
     * @param int   $user_id    The user ID
     * @param array $levels_ids Levels added to the user
     */
    public function listener( $user_id, $levels_ids ) {

        // Trigger the level added
        automatorwp_trigger_event( array(
            'trigger'       => $this->trigger,
            'user_id'       => $user_id,
            'levels_ids'    => $levels_ids,
        ) );

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

        // Don't deserve if levels IDs are not received
        if( ! isset( $event['levels_ids'] ) ) {
            return false;
        }

        // Don't deserve if level doesn't match with the trigger option
        if( $trigger_options['level'] !== 'any' && ! in_array( $trigger_options['level'], $event['levels_ids'] ) ) {
            return false;
        }

        return $deserves_trigger;

    }

}

new AutomatorWP_WishList_Member_Add_Level();