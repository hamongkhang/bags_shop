<?php
/**
 * Created by IntelliJ IDEA.
 * User: shifa
 * Date: 8/18/18
 * Time: 12:44 PM
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class APPMAKER_WC_instant_search{

    public function __construct()
    {

        remove_filter( 'posts_search', array( WCISPlugin::get_instance(), 'posts_search_handler' ) );
        remove_action( 'pre_get_posts', array( WCISPlugin::get_instance(), 'pre_get_posts_handler' ) );
        remove_filter( 'post_limits', array(  WCISPlugin::get_instance(), 'post_limits_handler' ) );
        remove_filter( 'the_posts', array(  WCISPlugin::get_instance(), 'the_posts_handler' ) );


    }

}
new APPMAKER_WC_instant_search();