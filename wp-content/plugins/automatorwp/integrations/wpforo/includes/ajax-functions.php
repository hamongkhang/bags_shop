<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\wpForo\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function for selecting forums
 *
 * @since 1.0.0
 */
function automatorwp_wpforo_ajax_get_forums() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

    $table = WPF()->tables->forums;

    // Get the forums
    $results = $wpdb->get_results( $wpdb->prepare(
        "SELECT f.forumid AS id, f.title AS text
        FROM {$table} AS f
        WHERE f.title LIKE %s",
        "%%{$search}%%"
    ) );

    // Prepend option none
    $results = automatorwp_ajax_parse_extra_options( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_wpforo_get_forums', 'automatorwp_wpforo_ajax_get_forums' );

/**
 * Ajax function for selecting topics
 *
 * @since 1.0.0
 */
function automatorwp_wpforo_ajax_get_topics() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

    $table = WPF()->tables->topics;

    // Get the topics
    $results = $wpdb->get_results( $wpdb->prepare(
        "SELECT f.topicid AS id, f.title AS text
        FROM {$table} AS f
        WHERE f.title LIKE %s",
        "%%{$search}%%"
    ) );

    // Prepend option none
    $results = automatorwp_ajax_parse_extra_options( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_wpforo_get_topics', 'automatorwp_wpforo_ajax_get_topics' );