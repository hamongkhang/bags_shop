<?php

class APPMAKER_WC_Options {
	/**
	 * Holds the values to be used in the fields callbacks
	 *
	 * @var object
	 */
	private $options;


	/**
	 * Start up
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page() {
		// This page will be under "WooCommerce".
		add_submenu_page(
			'woocommerce',
			'Appmaker WooCommerce Mobile App Manager Settings',
			'Appmaker App Settings',
			'manage_options',
			'appmaker-wc-admin',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page() {
		/** Moved saving from options.php to here to avoid logout issue of jetpack */
		if ( isset( $_POST['appmaker_wc_settings'] ) ) {
			$options = $this->sanitize( $_POST['appmaker_wc_settings'] );
			update_option( 'appmaker_wc_settings', $options, false );
		}
		$this->options = get_option( 'appmaker_wc_settings' );
		?>
		<div class="wrap">
			<h2>Appmaker Settings</h2>
             <?php require_once dirname( __FILE__ ) . '/class-appmaker-wc-admin-page.php'; ?>
		</div>
		<?php
	}

	/**
	 * Register and add settings
	 */
	public function page_init() {
        add_action('admin_head-woocommerce_page_appmaker-wc-admin',array($this,'admin_hook_css'));
		register_setting(
			'appmaker_wc_key_options',
			'appmaker_wc_settings',
			array( $this, 'sanitize' )
		);

		add_settings_section(
			'appmaker_wc_setting_section',
			__( 'API Credentials', 'appmaker-woocommerce-mobile-app-manager' ),
			array( $this, 'print_section_info' ),
			'appmaker-wc-setting-admin'
		);

		add_settings_field(
			'project_id',
			__( 'Project ID', 'appmaker-woocommerce-mobile-app-manager' ),
			array( $this, 'project_id_callback' ),
			'appmaker-wc-setting-admin',
			'appmaker_wc_setting_section'
		);

		add_settings_field(
			'api_key',
			__( 'API Key', 'appmaker-woocommerce-mobile-app-manager' ),
			array( $this, 'api_key_callback' ),
			'appmaker-wc-setting-admin',
			'appmaker_wc_setting_section'
		);

		add_settings_field(
			'api_secret',
			__( 'API Secret', 'appmaker-woocommerce-mobile-app-manager' ),
			array( $this, 'api_secret_callback' ),
			'appmaker-wc-setting-admin',
			'appmaker_wc_setting_section'
		);

	}

    public function admin_hook_css() {
        ?>
        <style>
            html{
                margin: 0;
                padding: 0;
                border: 0;
                box-sizing:border-box;
            }
            ol, ul {
                list-style: none;
                padding-inline-start: 0;
            }
            body {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 14px;
                color: rgb(0, 0, 0);
                min-width: 320px;
                background-color: #f5f5f5;
            }
            input,textarea,select{
                font-family: Arial, Helvetica, sans-serif;
            }
            a{
                color: rgb(14, 14, 14);
                text-decoration: none;
            }
            a:hover,.submit:hover{
                filter: alpha(opacity = 85);
                -moz-opacity: 0.85;
                -khtml-opacity: 0.85;
                opacity: 0.85;
            }
            p{
                line-height: 1.5rem;
            }
            label{
                margin-bottom: 5px;
            }
            *,*:before,*:after{box-sizing:inherit;}
            .row {
                display: -webkit-flex;
                display: flex;
            }
            .column.main{
                flex: 3;
            }
            .column.side{
                flex: 1;
                margin-left: 1.6rem;
            }
            /* custom css */
            .logo{
                margin: 20px 0;
            }

            .navbar, .main-box, .testimonials{
                background-color: #fff;
                border: #E2E2E2 1px solid;
            }
            .main-box{
                margin-top: 1rem;
            }
            .box-header{
                border-bottom: #E2E2E2 1px solid;
                padding: 0 20px;
            }
            .box-body{
                padding: 0 20px;
            }
            .casestudy .column{
                padding: 20px 20px 20px 0;
            }
            .navbar ul li{
                display: inline-block;
                font-size: 1.1rem;
                padding: 0 20px;
            }
            .current{
                border-bottom: 2px solid #000;
                padding: 13px 0;
            }
            form{
                padding: 2rem 0;
            }
            form > *{
                display: block;
            }
            form input{
                margin-bottom: 20px;
                padding: 5px 3px;
                font-size: 0.9rem;
            }
            .support a{
                color: #0277BD;
                display: block;
                margin-bottom: 10px;
            }
            a.button-custom{
                text-align: center;
                width: 100%;
                background-color: #0277BD;
                padding: 12px 50px;
                display: block;
                color: #fff;
                margin-top: 1rem;
                font-size: 1.05rem;
            }
            input[type="submit"]{
                background-color: #0277BD;
                padding: 8px 50px;
                border: none;
                color: #fff;
                font-size: 1.05rem;
                cursor: pointer;
            }
            .testimonials{
                margin-top: 1rem;
            }
            .testimonials .thumb{
                width: 200px;
                height: 200px;

            }
            .testimonials .testimonial-content{
                margin: auto 12px;
            }
            .testimonials .testimonial-content h2{
                margin-bottom: 0;
            }
            .social-media li{
                display: inline-block;
                margin-right: 10px;
            }
            .infograph-container{
                position: relative;
            }
            .infograph-container::before{
                position: absolute;
                content: '';
                width: 67%;
                height: 2px;
                background-color: #b5b5b5;
                top: 34px;
                left: 140px;
            }
            .infograph{
                padding: 90px 25px 15px 25px;
                text-align: center;
                position: relative;
            }
            .infograph h5{
                color: #fff;
                background-color: #0277BD;
                padding: 10px 15px;
                display: block;
                border-radius: 50%;
                position: absolute;
                top: 0;
                right: 45%;
            }
            @media (max-width: 600px) {
                .row {
                    -webkit-flex-direction: column;
                    flex-direction: column;
                }
            }
        </style>
        <?php
    }
	/**
	 * Sanitize each setting field as needed.
	 *
	 * @param array $input Contains all settings fields as array keys.
	 *
	 * @return array
	 */
	public function sanitize( $input ) {
		$new_input = array();
		if ( isset( $input['project_id'] ) ) {
			$new_input['project_id'] = sanitize_text_field( $input['project_id'] );
			if ( ! is_numeric( $new_input['project_id'] ) ) {
				$new_input['project_id'] = '';
			}
		}

		if ( isset( $input['api_key'] ) ) {
			$new_input['api_key'] = sanitize_text_field( $input['api_key'] );
		}

		if ( isset( $input['api_secret'] ) ) {
			$new_input['api_secret'] = sanitize_text_field( $input['api_secret'] );
		}

		return $new_input;
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info() {
		print 'Enter your Appmaker API settings below:';
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function project_id_callback() {
		printf(
			'<input type="text" id="project_id" name="appmaker_wc_settings[project_id]" value="%s" />',
			isset( $this->options['project_id'] ) ? esc_attr( $this->options['project_id'] ) : ''
		);
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function api_key_callback() {
		printf(
			'<input type="text" id="api_key" name="appmaker_wc_settings[api_key]" value="%s" />',
			isset( $this->options['api_key'] ) ? esc_attr( $this->options['api_key'] ) : ''
		);
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function api_secret_callback() {
		printf(
			'<input type="text" id="api_secret" name="appmaker_wc_settings[api_secret]" value="%s" />',
			isset( $this->options['api_secret'] ) ? esc_attr( $this->options['api_secret'] ) : ''
		);
	}
}

new APPMAKER_WC_Options();
