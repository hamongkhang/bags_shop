<?php
/**
 * Created by IntelliJ IDEA.
 * User: shifa
 * Date: 11/14/18
 * Time: 5:36 PM
 */

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly


class APPMAKER_WC_Out_Of_Stock_Order{

    public function __construct()
    {
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) && !empty($_GET['rest_route']) && false != strpos($_SERVER['REQUEST_URI'], 'products')) {

            add_filter('posts_clauses', array($this, 'order_by_stock_status'), 2000);
        }
    }
    public function order_by_stock_status($posts_clauses) {
        global $wpdb;

        // only change query on WooCommerce loops

        $posts_clauses['join'] .= " INNER JOIN $wpdb->postmeta istockstatus ON ($wpdb->posts.ID = istockstatus.post_id) ";
        $posts_clauses['orderby'] = " istockstatus.meta_value ASC, " . $posts_clauses['orderby'];
        $posts_clauses['where'] = " AND istockstatus.meta_key = '_stock_status' AND istockstatus.meta_value <> '' " . $posts_clauses['where'];

        return $posts_clauses;
    }


}
new APPMAKER_WC_Out_Of_Stock_Order();