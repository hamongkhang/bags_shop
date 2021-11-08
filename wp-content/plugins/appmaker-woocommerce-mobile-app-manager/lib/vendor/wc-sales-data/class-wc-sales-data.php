<?php

class WC_Sales_data
{

    //$duration  = 'all_time','last_week','last_month'
    public static function total_sales($duration, $from_app = false)
    {
        global $wpdb;

        $order_totals = apply_filters('woocommerce_reports_sales_overview_order_totals',
            $wpdb->get_row("SELECT SUM(meta.meta_value) AS total_sales,
   COUNT(posts.ID) AS total_orders
   FROM {$wpdb->posts} AS posts
   LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
   WHERE meta.meta_key = '_order_total' AND posts.post_type = 'shop_order' AND posts.post_status IN ( '" . implode("','", array('wc-completed', 'wc-processing', 'wc-on-hold')) . "' )"));
        return absint($order_totals->total_sales);
    }



}