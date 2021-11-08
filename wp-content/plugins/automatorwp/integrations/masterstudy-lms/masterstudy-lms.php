<?php
/**
 * Plugin Name:           AutomatorWP - MasterStudy LMS integration
 * Plugin URI:            https://wordpress.org/plugins/automatorwp-masterstudy-lms-integration/
 * Description:           Connect AutomatorWP with MasterStudy LMS.
 * Version:               1.0.1
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-masterstudy-lms-integration
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          5.7
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\MasterStudy_LMS
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_Integration_MasterStudy_LMS {

    /**
     * @var         AutomatorWP_Integration_MasterStudy_LMS $instance The one true AutomatorWP_Integration_MasterStudy_LMS
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_Integration_MasterStudy_LMS self::$instance The one true AutomatorWP_Integration_MasterStudy_LMS
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_Integration_MasterStudy_LMS();
            
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
        define( 'AUTOMATORWP_MASTERSTUDY_LMS_VER', '1.0.1' );

        // Plugin file
        define( 'AUTOMATORWP_MASTERSTUDY_LMS_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_MASTERSTUDY_LMS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_MASTERSTUDY_LMS_URL', plugin_dir_url( __FILE__ ) );
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
            require_once AUTOMATORWP_MASTERSTUDY_LMS_DIR . 'includes/triggers/complete-quiz.php';
            require_once AUTOMATORWP_MASTERSTUDY_LMS_DIR . 'includes/triggers/complete-lesson.php';
            require_once AUTOMATORWP_MASTERSTUDY_LMS_DIR . 'includes/triggers/complete-course.php';

            // Actions
            require_once AUTOMATORWP_MASTERSTUDY_LMS_DIR . 'includes/actions/user-course.php';

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

        automatorwp_register_integration( 'masterstudy_lms', array(
            'label' => 'MasterStudy LMS',
            'icon'  => plugin_dir_url( __FILE__ ) . 'assets/masterstudy-lms.svg',
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

        if ( ! defined( 'STM_LMS_FILE' ) ) {
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

        if ( ! class_exists( 'AutomatorWP_MasterStudy_LMS' ) ) {
            return false;
        }

        return true;

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_Integration_MasterStudy_LMS instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_Integration_MasterStudy_LMS The one true AutomatorWP_Integration_MasterStudy_LMS
 */
function AutomatorWP_Integration_MasterStudy_LMS() {
    return AutomatorWP_Integration_MasterStudy_LMS::instance();
}
add_action( 'automatorwp_pre_init', 'AutomatorWP_Integration_MasterStudy_LMS' );
