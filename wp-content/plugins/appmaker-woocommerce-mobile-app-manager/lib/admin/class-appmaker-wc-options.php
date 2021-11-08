<?php

class APPMAKER_WC_Options
{
	/**
	 * Holds the values to be used in the fields callbacks
	 *
	 * @var object
	 */
	private $options;


	/**
	 * Start up
	 */
	public function __construct()
	{
		add_action('admin_menu', array($this, 'add_plugin_page'));
		add_action('admin_init', array($this, 'page_init'));
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page()
	{
		// This page will be under "WooCommerce".
		add_submenu_page(
			'woocommerce',
			'Appmaker WooCommerce Mobile App Manager Settings',
			'Appmaker App Settings',
			'manage_options',
			'appmaker-wc-admin',
			array($this, 'create_admin_page')
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page()
	{
		$error = false;
		global $access_key;
		/** Moved saving from options.php to here to avoid logout issue of jetpack */
		if (isset($_POST['appmaker_wc_settings'])) {
			$options = $this->sanitize($_POST['appmaker_wc_settings']);
			if (is_array($options) && !empty($options['api_key']) && !empty($options['api_secret'])) {
				update_option('appmaker_wc_settings', $options, true);
				wp_redirect('admin.php?page=appmaker-wc-admin&tab=step3');
			} else {
				$error = true;
			}
		}
		$this->options = get_option('appmaker_wc_settings');
		$access_key = $this->get_access_key();
?>
		<div>
			<!--<h2>Appmaker Settings</h2>-->
			<?php require_once dirname(__FILE__) . '/class-appmaker-wc-admin-page.php'; ?>
		</div>
	<?php
	}

	/**
	 * Register and add settings
	 */
	public function page_init()
	{
		add_action('admin_head-woocommerce_page_appmaker-wc-admin', array($this, 'admin_hook_css'));
		if (isset($_GET['page']) && $_GET['page'] == 'appmaker-wc-admin') {
			add_action('admin_enqueue_scripts', array($this, 'intercom_script'));
			add_action('admin_enqueue_scripts', array($this, 'copy_to_clipboard_script'));
		}
		register_setting(
			'appmaker_wc_key_options',
			'appmaker_wc_settings',
			array($this, 'sanitize')
		);

		add_settings_section(
			'appmaker_wc_setting_section',
			__('API Credentials', 'appmaker-woocommerce-mobile-app-manager'),
			array($this, 'print_section_info'),
			'appmaker-wc-setting-admin'
		);

		if (isset($_GET['edit'])) {
			// add_settings_field(
			// 	'project_id',
			// 	__('Project ID', 'appmaker-woocommerce-mobile-app-manager'),
			// 	array($this, 'project_id_callback'),
			// 	'appmaker-wc-setting-admin',
			// 	'appmaker_wc_setting_section'
			// );

			add_settings_field(
				'api_key',
				__('API Key', 'appmaker-woocommerce-mobile-app-manager'),
				array($this, 'api_key_callback'),
				'appmaker-wc-setting-admin',
				'appmaker_wc_setting_section'
			);

			add_settings_field(
				'api_secret',
				__('API Secret', 'appmaker-woocommerce-mobile-app-manager'),
				array($this, 'api_secret_callback'),
				'appmaker-wc-setting-admin',
				'appmaker_wc_setting_section'
			);
		}
	}

	public function copy_to_clipboard_script()
	{
	?>
		<script>
			function appmakerFallbackCopyTextToClipboard(text) {
				var textArea = document.createElement("textarea");
				textArea.value = text;

				// Avoid scrolling to bottom
				textArea.style.top = "0";
				textArea.style.left = "0";
				textArea.style.position = "fixed";

				document.body.appendChild(textArea);
				textArea.focus();
				textArea.select();

				try {
					var successful = document.execCommand('copy');
					var msg = successful ? 'successful' : 'unsuccessful';
					console.log('Fallback: Copying text command was ' + msg);
				} catch (err) {
					console.error('Fallback: Oops, unable to copy', err);
				}

				document.body.removeChild(textArea);
			}

			function appmakerCopyTextToClipboard(text) {
				if (!navigator.clipboard) {
					appmakerFallbackCopyTextToClipboard(text);
					return;
				}
				navigator.clipboard.writeText(text).then(function() {
					console.log('Async: Copying to clipboard was successful!');
				}, function(err) {
					console.error('Async: Could not copy text: ', err);
				});
			}
		</script>
	<?php
	}

	public function intercom_script()
	{
	?>
		<script>
			window.intercomSettings = {
				app_id: "hkrwwq38"
			};
		</script>
		<script>
			// We pre-filled your app ID in the widget URL: 'https://widget.intercom.io/widget/hkrwwq38'
			(function() {
				var w = window;
				var ic = w.Intercom;
				if (typeof ic === "function") {
					ic('reattach_activator');
					ic('update', w.intercomSettings);
				} else {
					var d = document;
					var i = function() {
						i.c(arguments);
					};
					i.q = [];
					i.c = function(args) {
						i.q.push(args);
					};
					w.Intercom = i;
					var l = function() {
						var s = d.createElement('script');
						s.type = 'text/javascript';
						s.async = true;
						s.src = 'https://widget.intercom.io/widget/hkrwwq38';
						var x = d.getElementsByTagName('script')[0];
						x.parentNode.insertBefore(s, x);
					};
					if (w.attachEvent) {
						w.attachEvent('onload', l);
					} else {
						w.addEventListener('load', l, false);
					}
				}
			})();
		</script>
	<?php
	}

	public function admin_hook_css()
	{
	?>
		<link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet" />
<?php
	}
	/**
	 * Sanitize each setting field as needed.
	 *
	 * @param array $input Contains all settings fields as array keys.
	 *
	 * @return array
	 */
	public function sanitize($input)
	{
		$new_input = array();
		if (isset($input['project_id'])) {
			$new_input['project_id'] = sanitize_text_field($input['project_id']);
			if (!is_numeric($new_input['project_id'])) {
				$new_input['project_id'] = '';
			}
		}

		if (isset($input['api_key'])) {
			$new_input['api_key'] = sanitize_text_field($input['api_key']);
		}

		if (isset($input['api_secret'])) {
			$new_input['api_secret'] = sanitize_text_field($input['api_secret']);
		}

		if (isset($input['access_key'])) {
			$access_key  = base64_decode(sanitize_text_field($input['access_key']));
			$input_array = explode(':', $access_key);

			if (isset($input_array[0]) && isset($input_array[1]) && isset($input_array[1]) && isset($input_array[2])) {
				$new_input['project_id'] = $input_array[0];
				if (!is_numeric($new_input['project_id'])) {
					$new_input['project_id'] = '';
				}
				$new_input['api_key']    = $input_array[1];
				$new_input['api_secret'] = $input_array[2];
			}
			// if(isset($input_array[1])){
			//     $new_input['api_key'] = $input_array[1];
			// }

			// if(isset($input_array[2])){
			//     $new_input['api_secret'] = $input_array[2];
			// }

		}

		return $new_input;
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info()
	{
		printf('<p>Enter the access key given from <a target="_blank" href="https://manage.appmaker.xyz/?ref=wp-plugin-settings">Appmaker dashboard</a> to connect the store with app.</p>');
	}
	public function get_access_key()
	{
		// $project_id = isset( $this->options['project_id'] ) ? esc_attr( $this->options['project_id'] ) : '';
		$site_url   = get_site_url();
		if (
			!isset($this->options) || !is_array(($this->options))
			|| !(isset($this->options['api_key']) && !empty($this->options['api_key']))
			|| !(isset($this->options['api_secret']) && !empty($this->options['api_secret']))
		) {
			$args = array(
				'method'      => 'POST',
				'timeout'     => 45,
				'sslverify'   => false,
				'headers'     => array(
					'Content-Type'  => 'application/json',
				),
				'body'        => json_encode(array(
					"url"	=> get_site_url()
				)),
			);

			$request = wp_remote_post("https://app.appmaker.xyz/v2/generic-utils/generate-keys", $args);
			$response = wp_remote_retrieve_body($request);
			$responseObj = json_decode($response, true);
			$this->options =  array(
				"api_key" => $responseObj['body']['API_KEY'],
				"api_secret" => $responseObj['body']['API_SECRET']
			);
			update_option('appmaker_wc_settings', $this->options);
		}

		// if (!isset($this->options['api_key']) || empty($this->options['api_key'])) {
		// 	$this->options['api_key']    =  "ak_" . wc_rand_hash();
		// 	$updated = true;
		// }

		// if (!isset($this->options['api_secret']) || empty($this->options['api_secret'])) {
		// 	$this->options['api_secret']    =  "as_" . wc_rand_hash();
		// 	$updated = true;
		// }

		// if ($updated) {
		// 	update_option('appmaker_wc_settings', $this->options, true);
		// }

		// $access_key         = $site_url.':'.$project_id . ':' . $api_key . ':' . $api_secret;

		$api_details = [
			'url' 		=> $site_url,
			'key'		=> $this->options['api_key'],
			'secret'	=> $this->options['api_secret']
		];

		// $encoded_access_key = (json_encode($api_details));
		$encoded_access_key = base64_encode(json_encode($api_details));
		return $encoded_access_key;
	}



	/**
	 * Get the settings option array and print one of its values
	 */
	public function project_id_callback()
	{
		printf(
			'<input type="text" id="project_id" name="appmaker_wc_settings[project_id]" value="%s" />',
			isset($this->options['project_id']) ? esc_attr($this->options['project_id']) : ''
		);
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function api_key_callback()
	{
		printf(
			'<input type="text" id="api_key" name="appmaker_wc_settings[api_key]" value="%s" />',
			isset($this->options['api_key']) ? esc_attr($this->options['api_key']) : ''
		);
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function api_secret_callback()
	{
		printf(
			'<input type="text" id="api_secret" name="appmaker_wc_settings[api_secret]" value="%s" />',
			isset($this->options['api_secret']) ? esc_attr($this->options['api_secret']) : ''
		);
	}
}

new APPMAKER_WC_Options();
