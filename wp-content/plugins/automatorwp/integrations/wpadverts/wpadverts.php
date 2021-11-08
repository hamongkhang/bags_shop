<?php
/**
 * Plugin Name:           AutomatorWP - WPAdverts integration
 * Plugin URI:            https://wordpress.org/plugins/automatorwp-wpadverts-integration/
 * Description:           Connect AutomatorWP with WPAdverts.
 * Version:               1.0.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-wpadverts-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\WPAdverts
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_WPAdverts {

    /**
     * @var         AutomatorWP_Integration_WPAdverts $instance The one true AutomatorWP_Integration_WPAdverts
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_WPAdverts self::$instance The one true AutomatorWP_Integration_WPAdverts
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_WPAdverts();

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
        define( 'AUTOMATORWP_WPADVERTS_VER', '1.0.0' );

        // Plugin file
        define( 'AUTOMATORWP_WPADVERTS_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_WPADVERTS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_WPADVERTS_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_WPADVERTS_DIR . 'includes/triggers/publish-advert.php';
            require_once AUTOMATORWP_WPADVERTS_DIR . 'includes/triggers/send-message.php';

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

        automatorwp_register_integration( 'wpadverts', array(
            'label' => 'WPAdverts',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/wpadverts.svg',
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

        if ( ! function_exists( 'adverts_init' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_WPAdverts' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_WPAdverts instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_WPAdverts The one true AutomatorWP_Integration_WPAdverts
 */
function AutomatorWP_Integration_WPAdverts() {
    return AutomatorWP_Integration_WPAdverts::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_WPAdverts' );
