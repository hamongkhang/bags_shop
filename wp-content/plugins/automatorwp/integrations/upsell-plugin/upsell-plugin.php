<?php
/**
 * Plugin Name:           AutomatorWP - Upsell Plugin integration
 * Plugin URI:            https://wordpress.org/plugins/automatorwp-upsell-plugin-integration/
 * Description:           Connect AutomatorWP with Upsell Plugin.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-upsell-plugin-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\Upsell_Plugin
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_Upsell_Plugin {

    /**
     * @var         AutomatorWP_Integration_Upsell_Plugin $instance The one true AutomatorWP_Integration_Upsell_Plugin
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_Upsell_Plugin self::$instance The one true AutomatorWP_Integration_Upsell_Plugin
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_Upsell_Plugin();
            
            if( ! self::$instance->pro_installed() ) {

                self::$instance->constants();
                self::$instance->includes();
                
            }

            self::$instance->hooks();
        }

        return self::$instance;
    }

    /**
     * Setup plugin constants
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function constants() {
        // Plugin version
        define( 'AUTOMATORWP_UPSELL_PLUGIN_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_UPSELL_PLUGIN_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_UPSELL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_UPSELL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Include plugin files
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function includes() {

        if( $this->meets_requirements() ) {

            // Triggers
            require_once AUTOMATORWP_UPSELL_PLUGIN_DIR . 'includes/triggers/purchase-product.php';

        }
    }

    /**
     * Setup plugin hooks
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function hooks() {

        add_action( 'automatorwp_init', array( $this, 'register_integration' ) );
        
    }

    /**
     * Registers this integration
     *
     * @since 1.0.0
     */
    function register_integration() {

        automatorwp_register_integration( 'upsell_plugin', array(
            'label' => 'Upsell Plugin',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/upsell-plugin.svg',
        ) );

    }

    /**
     * Check if there are all plugin requirements
     *
     * @since  1.0.0
     *
     * @return bool True if installation meets all requirements
     */
    private function meets_requirements() {

        if ( ! class_exists( 'AutomatorWP' ) ) {
            return false;
        }

        if ( ! function_exists( 'upsell' ) ) {
            return false;
        }

        return true;

    }

    /**
     * Check if the pro version of this integration is installed
     *
     * @since  1.0.0
     *
     * @return bool True if pro version installed
     */
    private function pro_installed() {

        if ( ! class_exists( 'AutomatorWP_Upsell_Plugin' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_Upsell_Plugin instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_Upsell_Plugin The one true AutomatorWP_Integration_Upsell_Plugin
 */
function AutomatorWP_Integration_Upsell_Plugin() {
    return AutomatorWP_Integration_Upsell_Plugin::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_Upsell_Plugin' );
