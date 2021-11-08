<?php

class AppmakerCheckout
{
    public static function init()
    {                
        add_filter('woocommerce_locate_template',  array('AppmakerCheckout', 'test_woocommerce_locate_template'), 99, 3);
        add_action('wp_enqueue_scripts', array('AppmakerCheckout', 'appmaker_wp_enqueue_scripts'));
        add_filter('page_template', array('AppmakerCheckout', 'single_page_template'));
        self::wc_hooks();
    }

    public static function test_woocommerce_locate_template($template, $template_name, $template_path)
    {
        if ('checkout/form-checkout.php' !== $template_name) {
            return $template;
        }
        $template = plugin_dir_path(__FILE__) . 'includes/form-checkout.php';
        return $template;
    }

    public static function wc_hooks()
    {
        // Remove login messages.
        remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10);
        remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);

        // Split the `Order` and the `Payment` tabs.
        remove_action('woocommerce_checkout_order_review', 'woocommerce_order_review', 10);
        remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);
        add_action('appmakercheckout-woocommerce_order_review', 'woocommerce_order_review', 20);
        add_action('appmakercheckout-woocommerce_checkout_payment', 'woocommerce_checkout_payment', 10);

        // Split the `woocommerce_before_checkout_form`.
        remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10);
        remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
        add_action('appmakercheckout-woocommerce_checkout_login_form', 'woocommerce_checkout_login_form', 10);
        add_action('appmakercheckout-woocommerce_checkout_coupon_form', 'woocommerce_checkout_coupon_form', 10);

        // Add the content functions to the steps.
        add_action('appmakercheckout_step_content_login', 'appmakercheckout_step_content_login', 10);
        add_action('appmakercheckout_step_content_shipping', 'appmakercheckout_step_content_shipping', 10);
        add_action('appmakercheckout_step_content_billing', 'appmakercheckout_step_content_billing', 10);
    }

    public static  function appmaker_wp_enqueue_scripts()
    {

        if (!is_checkout()) {
            return;
        }

        $options      = get_option('appmakercheckout_options');
        $keyboard_nav = (isset($options['keyboard_nav']) && $options['keyboard_nav']) ? true : false;
        $color        = (isset($options['main_color'])) ? wp_strip_all_tags($options['main_color']) : '#1e85be';
        $url          = plugins_url('/', __FILE__) . 'assets/';
        $prefix       = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '';
        $version = 2;
        // // Load scripts.
        wp_register_script('appmakercheckout', $url . 'js/script' . $prefix . '.js', array('jquery'), $version, false);
        wp_localize_script('appmakercheckout', 'appmakercheckout', apply_filters('appmakercheckout_js_variables', array('keyboard_nav' => $keyboard_nav)));
        wp_register_style('appmakercheckout', $url . 'css/style-progress' . $prefix . '.css', array(), $version);
        wp_register_style('appmaker-checkout', $url . 'css/appmaker-checkout' . $prefix . '.css', array(), $version);

        wp_enqueue_script('appmakercheckout');
        wp_enqueue_style('appmakercheckout');
        wp_enqueue_style('appmaker-checkout');

        // // Load the inline styles.
        $style  = '.appmakercheckout-tabs-wrapper .appmakercheckout-tab-item.current::before { border-bottom-color:' . $color . '; }';
        $style .= '.appmakercheckout-tabs-wrapper .appmakercheckout-tab-item.current .appmakercheckout-tab-number { border-color: ' . $color . '; }';
        if (is_rtl()) {
            $style .= '.appmakercheckout-tabs-list .appmakercheckout-tab-item { float: right; }';
        }
        wp_add_inline_style('appmakercheckout', $style);
    }

    public static  function single_page_template($single_template)
    {
        global $post;
        if (!is_checkout()) {
            return $single_template;
        }
        $single_template =  plugin_dir_path(__FILE__) . 'includes/page-template.php';
        return $single_template;
    }
}

if ( isset( $_REQUEST['appmaker_multi_step_checkout'] ) ) {
    AppmakerCheckout::init();
    // add_action('plugins_loaded', array('AppmakerCheckout', 'init'));
    include_once 'includes/class-appmakercheckout-compatibilities.php';
}
