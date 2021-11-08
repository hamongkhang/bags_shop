<?php
/**
 * Plugin Name: Cresta Social Messenger
 * Plugin URI: https://crestaproject.com/downloads/cresta-facebook-messenger/
 * Description: <strong>*** <a href="https://crestaproject.com/downloads/cresta-facebook-messenger/" target="_blank">Get Cresta Social Messenger PRO</a> ***</strong> Allow your users and customers to contact you via Facebook Messenger with a single click.
 * Version: 1.2.4
 * Author: CrestaProject - Rizzo Andrea
 * Author URI: https://crestaproject.com
 * Domain Path: /languages
 * Text Domain: cresta-facebook-messenger
 * License: GPL2
 */
 
/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
 
define( 'CRESTA_FACEBOOK_MESSENGER_PLUGIN_VERSION', '1.2.4' );
add_action('admin_menu', 'cresta_facebook_messenger_menu');
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'cresta_facebook_messenger_setting_link' );
add_filter('plugin_row_meta', 'cresta_facebook_messenger_meta_links', 10 , 2 );
add_action('plugins_loaded', 'crestafacebookplugin_textdomain' );
add_action('admin_init', 'register_cresta_facebook_messenger_settings' );
add_action('wp_enqueue_scripts', 'crestafacebookplugin_front_enqueue_scripts');
add_action('admin_enqueue_scripts', 'crestafacebookplugin_admin_enqueue_scripts');

require_once( dirname( __FILE__ ) . '/cresta-facebook-messenger-metabox.php' );

function crestafacebookplugin_textdomain() {
	load_plugin_textdomain( 'cresta-facebook-messenger', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

function cresta_facebook_messenger_menu() {
	global $cresta_facebook_options_page;
	$cresta_facebook_options_page = add_options_page(
		esc_html__( 'Cresta Social Messenger Settings', 'cresta-facebook-messenger'),
		esc_html__( 'Cresta Social Messenger', 'cresta-facebook-messenger'),
		'manage_options',
		'cresta-facebook-messenger.php',
		'cresta_facebook_messenger_option',
		81
	);
}

function cresta_facebook_messenger_setting_link($links) { 
	$settings_link = array(
		'<a href="' . admin_url('options-general.php?page=cresta-facebook-messenger.php') . '">' . esc_html__( 'Settings','cresta-facebook-messenger') . '</a>',
	);
	return array_merge( $links, $settings_link );
}

function cresta_facebook_messenger_meta_links( $links, $file ) {
	if ( strpos( $file, 'cresta-facebook-messenger.php' ) !== false ) {
		$new_links = array(
			'<a style="color:#39b54a;font-weight:bold;" href="https://crestaproject.com/downloads/cresta-facebook-messenger/" target="_blank" rel="external" ><span class="dashicons dashicons-megaphone"></span> ' . esc_html__( 'Upgrade to PRO', 'cresta-facebook-messenger' ) . '</a>', 
		);
		$links = array_merge( $links, $new_links );
	}
	return $links;
}

/* Plugin enqueue style and script */
function crestafacebookplugin_front_enqueue_scripts() {
	wp_enqueue_style( 'cresta-facebook-messenger-front-style', plugins_url('css/cresta-social-messenger-front-css.min.css',__FILE__), array(), CRESTA_FACEBOOK_MESSENGER_PLUGIN_VERSION);
	wp_enqueue_script( 'jquery' );
}

/* Plugin enqueue admin style and script */
function crestafacebookplugin_admin_enqueue_scripts( $hook ) {
	global $cresta_facebook_options_page;
	if ( $hook == $cresta_facebook_options_page ) {
		wp_enqueue_style( 'cresta-facebook-messenger-admin-style', plugins_url('css/cresta-social-messenger-admin-css.css',__FILE__), array(), CRESTA_FACEBOOK_MESSENGER_PLUGIN_VERSION);
	}
}

/* Register Settings */
function register_cresta_facebook_messenger_settings() {
	register_setting( 'cfmplugin', 'crestafacebookmessenger_settings','crestafacebookmessenger_options_validate' );
	$cfm_options_arr = array(
		'cresta_facebook_messenger_page_url' => '',
		'cresta_facebook_messenger_box_text' => 'Cresta Social Messenger',
		'cresta_facebook_messenger_hide_cover' => false,
		'cresta_facebook_messenger_small_header' => false,	
		'cresta_facebook_messenger_show_facepile' => false,
		'cresta_facebook_messenger_tabs' => 'messages',
		'cresta_facebook_messenger_box_language' => 'en_US',
		'cresta_facebook_messenger_zindex' => '999',
		'cresta_facebook_messenger_show_floating_box' => true,	
		'cresta_facebook_messenger_selected_page' => 'homepage,blogpage,post,page',
		'cresta_facebook_messenger_mobile_option' => 'onApp',
		'cresta_facebook_messenger_show_option' => 'onBoth',
		'cresta_facebook_messenger_click_to_close' => false,
		'cresta_facebook_messenger_what_icon' => 'messenger'
	);
	add_option( 'crestafacebookmessenger_settings', $cfm_options_arr );
}

/* CSS Code filter to header */ 
function cresta_facebook_messenger_css_top() {
	$cfm_options = get_option( 'crestafacebookmessenger_settings' );
	$facebook_zindex = $cfm_options['cresta_facebook_messenger_zindex'];
	echo "<style id='cresta-social-messenger-inline-css'>";
	echo ".cresta-facebook-messenger-box, .cresta-facebook-messenger-button {z-index:". intval($facebook_zindex + 1) ."}";
	echo ".cresta-facebook-messenger-container, .cresta-facebook-messenger-container-button {z-index:". intval($facebook_zindex) ."}";
	echo ".cresta-facebook-messenger-overlay {z-index:". intval($facebook_zindex - 1) ."}";
	echo "</style>";
}
add_action('wp_head', 'cresta_facebook_messenger_css_top');

/* Cresta Facebook Messenger shortcode */
function cresta_facebook_messenger_shortcode( $atts ) {
    extract(shortcode_atts( array(
        'text' => 'Need Help?',
		'icon' => 'yes',
		'position' => 'top',
		'fbpage' => ''
    ), $atts ));
	$cfm_options = get_option( 'crestafacebookmessenger_settings' );
	$facebook_page_url = $cfm_options['cresta_facebook_messenger_page_url'];
	$facebook_box_text = $cfm_options['cresta_facebook_messenger_box_text'];
	$facebook_hide_cover = $cfm_options['cresta_facebook_messenger_hide_cover'] ? 'true' : 'false';
	$facebook_small_header = $cfm_options['cresta_facebook_messenger_small_header'] ? 'true' : 'false';
	$facebook_show_facepile = $cfm_options['cresta_facebook_messenger_show_facepile'] ? 'true' : 'false';
	$facebook_tabs_to_show = $cfm_options['cresta_facebook_messenger_tabs'];
	$facebook_language = $cfm_options['cresta_facebook_messenger_box_language'];
	$facebook_mobile = $cfm_options['cresta_facebook_messenger_mobile_option'];
	$what_icon = $cfm_options['cresta_facebook_messenger_what_icon'];
	if ($facebook_box_text) {
		$top_text = '<div class="cresta-facebook-messenger-top-header"><span>'. esc_html($facebook_box_text) .'</span></div>';
	} else {
		$top_text = '';
	}
	if ($icon == 'yes') {
		if ($what_icon == 'facebook') {
			$svg_icon = '<svg id="fb-msng-icon-button" data-name="messenger icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50"><path d="M40,0H10C4.486,0,0,4.486,0,10v30c0,5.514,4.486,10,10,10h30c5.514,0,10-4.486,10-10V10C50,4.486,45.514,0,40,0z M39,17h-3 c-2.145,0-3,0.504-3,2v3h6l-1,6h-5v20h-7V28h-3v-6h3v-3c0-4.677,1.581-8,7-8c2.902,0,6,1,6,1V17z" style="fill:#ffffff"></path></svg>';
		} else {
			$svg_icon = '<svg id="fb-msng-icon-button" data-name="messenger icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30.47 30.66"><path d="M29.56,14.34c-8.41,0-15.23,6.35-15.23,14.19A13.83,13.83,0,0,0,20,39.59V45l5.19-2.86a16.27,16.27,0,0,0,4.37.59c8.41,0,15.23-6.35,15.23-14.19S38,14.34,29.56,14.34Zm1.51,19.11-3.88-4.16-7.57,4.16,8.33-8.89,4,4.16,7.48-4.16Z" transform="translate(-14.32 -14.34)" style="fill:#ffffff"/></svg>';
		}
	} else {
		$svg_icon = '';
	}
	if ($fbpage == '') {
		$thepage = $facebook_page_url;
	} else {
		$thepage = $fbpage;
	}
	$random_number = rand(1,1000);
	return '<div id="fb-root"></div>
			<script async defer crossorigin="anonymous" src="https://connect.facebook.net/'.esc_attr(trim($facebook_language)).'/sdk.js#xfbml=1&version=v11.0&autoLogAppEvents=1"></script>
			<script>
			(function($) {
			"use strict";
				$(document).ready(function() {
					var mobileDetect = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
					if (mobileDetect && $(".cresta-facebook-messenger-button").hasClass("onApp")) {
						$(".cresta-facebook-messenger-container-button").css("display","none");
						$(".cresta-facebook-messenger-button.click-'.esc_attr($random_number).'").on("click", function(){
							window.location = "https://m.me/'. esc_attr(trim($thepage)).'";
						});
					} else {
						$(".cresta-facebook-messenger-button.click-'.esc_attr($random_number).'").on("click", function(){
							$(this).find(".cresta-facebook-messenger-container-button").toggleClass("open");
						});
					}
				});
			})(jQuery);
			</script>
			<div class="cresta-facebook-messenger-button '.esc_attr($facebook_mobile).' click-'.esc_attr($random_number).'">
				'.$svg_icon.'
				<span>'.esc_html($text).'</span>
				<div class="cresta-facebook-messenger-container-button '.esc_attr($position).'">
					'.$top_text.'
					<div class="fb-page" data-href="https://facebook.com/'. esc_attr($thepage).'" data-tabs="'. esc_attr($facebook_tabs_to_show) .'" data-width="300" data-height="350" data-small-header="'.esc_attr($facebook_small_header).'" data-adapt-container-width="true" data-hide-cover="'.esc_attr($facebook_hide_cover).'" data-show-facepile="'.esc_attr($facebook_show_facepile).'"></div>
				</div>
			</div>';
}
add_shortcode( 'cresta-facebook-messenger', 'cresta_facebook_messenger_shortcode' );

/* Where to show the floating box */
function cresta_facebook_messenger_show_floating() {
	$cfm_options = get_option( 'crestafacebookmessenger_settings' );
	$cresta_facebook_current_post_type = get_post_type();	
	$facebook_selected_pages = explode (',',$cfm_options['cresta_facebook_messenger_selected_page'] );
	if ( is_singular() && in_array( $cresta_facebook_current_post_type, $facebook_selected_pages ) ) {
		$checkCrestaFacebookMetaBox = get_post_meta(get_the_ID(), '_get_cresta_facebook_messenger_plugin', true);
		if ( $checkCrestaFacebookMetaBox == '1' ) {
			return false;
		} else {
			return true;
		}
	}
	if (in_array( 'website', $facebook_selected_pages ) ) {
		return true;
	} else {
		if (is_home() && in_array( 'blogpage', $facebook_selected_pages ) ) {
			return true;
		}
		if (is_front_page() && in_array( 'homepage', $facebook_selected_pages ) ) {
			return true;
		}
		if (is_category() && in_array( 'catpage', $facebook_selected_pages ) ) {
			return true;
		}
		if (is_tag() && in_array( 'tagpage', $facebook_selected_pages ) ) {
			return true;
		}
		if (is_author() && in_array( 'authorpage', $facebook_selected_pages ) ) {
			return true;
		}
		if (is_date() && in_array( 'datepage', $facebook_selected_pages ) ) {
			return true;
		}
		if (is_search() && in_array( 'searchpage', $facebook_selected_pages ) ) {
			return true;
		}
		if (function_exists( 'is_woocommerce' ) ) {
			if (is_shop() && in_array( 'shoppage', $facebook_selected_pages ) ) {
				return true;
			}
			if (is_product_category() && in_array( 'woocatpage', $facebook_selected_pages ) ) {
				return true;
			}
			if (is_product_tag() && in_array( 'wootagpage', $facebook_selected_pages ) ) {
				return true;
			}
			return false;
		}
	}
	return false;
}

/* This is the float button output */
function add_cresta_facebook_messenger_box() {
	$cfm_options = get_option( 'crestafacebookmessenger_settings' );
	$facebook_show_floating_box = $cfm_options['cresta_facebook_messenger_show_floating_box'];
	if ($facebook_show_floating_box == 1 && cresta_facebook_messenger_show_floating()) {
		$facebook_page_url = $cfm_options['cresta_facebook_messenger_page_url'];
		$facebook_box_text = $cfm_options['cresta_facebook_messenger_box_text'];
		$facebook_hide_cover = $cfm_options['cresta_facebook_messenger_hide_cover'] ? 'true' : 'false';
		$facebook_small_header = $cfm_options['cresta_facebook_messenger_small_header'] ? 'true' : 'false';
		$facebook_show_facepile = $cfm_options['cresta_facebook_messenger_show_facepile'] ? 'true' : 'false';
		$facebook_tabs_to_show = $cfm_options['cresta_facebook_messenger_tabs'];
		$facebook_language = $cfm_options['cresta_facebook_messenger_box_language'];
		$facebook_mobile = $cfm_options['cresta_facebook_messenger_mobile_option'];
		$facebook_show = $cfm_options['cresta_facebook_messenger_show_option'];
		$what_icon = $cfm_options['cresta_facebook_messenger_what_icon'];
		?>
		<?php if($facebook_page_url): ?>
		<div id="cfmexist"></div>
		<div id="fb-root"></div>
		<script async defer crossorigin="anonymous" src="https://connect.facebook.net/<?php echo esc_attr(trim($facebook_language)); ?>/sdk.js#xfbml=1&version=v11.0&autoLogAppEvents=1"></script>
		<script>
		(function($) {
		"use strict";
			$(document).ready(function() {
				var mobileDetect = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
				if (mobileDetect && $(".cresta-facebook-messenger-box").hasClass("onApp")) {
					$('.cresta-facebook-messenger-container').css('display','none');
					$('.cresta-facebook-messenger-box').on('click', function(){
						window.location = 'https://m.me/<?php echo esc_attr(trim($facebook_page_url)); ?>';
					});
				} else {
					$('.cresta-facebook-messenger-box, .cresta-facebook-messenger-overlay').on('click', function(){
						$('.cresta-facebook-messenger-box, .cresta-facebook-messenger-container, .cresta-facebook-messenger-overlay').toggleClass('open');
					});
				}
			});
		})(jQuery);
		</script>
		<?php
			$facebook_click_to_close = $cfm_options['cresta_facebook_messenger_click_to_close'];
			if ($facebook_click_to_close == 1) {
				echo '<div class="cresta-facebook-messenger-overlay"></div>';
			}
		?>
		<div class="cresta-facebook-messenger-box <?php echo esc_attr($facebook_mobile); ?> <?php echo esc_attr($facebook_show); ?>">
			<?php if ($what_icon == 'facebook'): ?>
				<svg id="fb-msng-icon" data-name="messenger icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50"><path d="M40,0H10C4.486,0,0,4.486,0,10v30c0,5.514,4.486,10,10,10h30c5.514,0,10-4.486,10-10V10C50,4.486,45.514,0,40,0z M39,17h-3 c-2.145,0-3,0.504-3,2v3h6l-1,6h-5v20h-7V28h-3v-6h3v-3c0-4.677,1.581-8,7-8c2.902,0,6,1,6,1V17z" style="fill:#ffffff"></path></svg>
			<?php else: ?>
				<svg id="fb-msng-icon" data-name="messenger icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30.47 30.66"><path d="M29.56,14.34c-8.41,0-15.23,6.35-15.23,14.19A13.83,13.83,0,0,0,20,39.59V45l5.19-2.86a16.27,16.27,0,0,0,4.37.59c8.41,0,15.23-6.35,15.23-14.19S38,14.34,29.56,14.34Zm1.51,19.11-3.88-4.16-7.57,4.16,8.33-8.89,4,4.16,7.48-4.16Z" transform="translate(-14.32 -14.34)" style="fill:#ffffff"/></svg>
			<?php endif; ?>
			<svg id="close-icon" data-name="close icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 39.98 39.99"><path d="M48.88,11.14a3.87,3.87,0,0,0-5.44,0L30,24.58,16.58,11.14a3.84,3.84,0,1,0-5.44,5.44L24.58,30,11.14,43.45a3.87,3.87,0,0,0,0,5.44,3.84,3.84,0,0,0,5.44,0L30,35.45,43.45,48.88a3.84,3.84,0,0,0,5.44,0,3.87,3.87,0,0,0,0-5.44L35.45,30,48.88,16.58A3.87,3.87,0,0,0,48.88,11.14Z" transform="translate(-10.02 -10.02)" style="fill:#ffffff"/></svg>
		</div>
		<div class="cresta-facebook-messenger-container">
			<?php if ($facebook_box_text) : ?>
				<div class="cresta-facebook-messenger-top-header"><span><?php echo esc_html($facebook_box_text); ?></span></div>
			<?php endif; ?>
			<div class="fb-page" data-href="https://facebook.com/<?php echo esc_attr(trim($facebook_page_url)); ?>" data-tabs="<?php echo esc_attr($facebook_tabs_to_show); ?>" data-width="300" data-height="350" data-small-header="<?php echo esc_attr($facebook_small_header); ?>" data-adapt-container-width="true" data-hide-cover="<?php echo esc_attr($facebook_hide_cover); ?>" data-show-facepile="<?php echo esc_attr($facebook_show_facepile); ?>"></div>
		</div>
		<?php endif; ?>
		<?php
	}
}
add_action('wp_footer', 'add_cresta_facebook_messenger_box');

function cresta_facebook_messenger_option() {
	ob_start();
	?>
	<script type="text/javascript">
		jQuery(document).ready(function(){
			if ( jQuery('input.crestaisPossbile').hasClass('active') ) {
				jQuery('.chkifpossible input').attr('disabled', false);
				jQuery('.chkifpossible').removeClass('crestaOpa');
			} else {
				jQuery('.chkifpossible input').attr('disabled', true);
				jQuery('.chkifpossible').addClass('crestaOpa');
			}
			if ( jQuery('input.ifwebsite').hasClass('active') ) {
				jQuery('.yesiswebsite input').attr('disabled', true);
				jQuery('.yesiswebsite').addClass('crestaOpa');
			} else {
				jQuery('.yesiswebsite input').attr('disabled', false);
				jQuery('.yesiswebsite').removeClass('crestaOpa');
			}
			jQuery('input.crestaisPossbile').on('click', function(){
				if ( jQuery(this).is(':checked') ) {
					jQuery('.chkifpossible input').attr('disabled', false);
					jQuery('.chkifpossible').removeClass('crestaOpa');
				} else {
					jQuery('.chkifpossible input').attr('disabled', true);
					jQuery('.chkifpossible').addClass('crestaOpa');
				}
			});
			jQuery('input.ifwebsite').on('click', function(){
				if ( jQuery(this).is(':checked') ) {
					jQuery('.yesiswebsite input').attr('disabled', true);
					jQuery('.yesiswebsite').addClass('crestaOpa');
				} else {
					jQuery('.yesiswebsite input').attr('disabled', false);
					jQuery('.yesiswebsite').removeClass('crestaOpa');
				}
			});
		});
	</script>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"></div>
		<a class="crestaButtonUpgrade" href="https://crestaproject.com/downloads/cresta-facebook-messenger/" target="_blank" title="<?php esc_attr_e('See Details: Cresta Social Messenger PRO', 'cresta-facebook-messenger'); ?>"><span class="dashicons dashicons-megaphone"></span><?php esc_html_e('Upgrade to PRO version!', 'cresta-facebook-messenger'); ?></a>
		<h2><?php esc_html_e('Cresta Social Messenger FREE', 'cresta-facebook-messenger'); ?></h2>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
			<!-- main content -->
			<div id="post-body-content">
			<div class="meta-box-sortables">
			<div class="postbox">
			<div class="inside">
			<form method="post" action="options.php">
				<?php
				settings_fields( 'cfmplugin' ); 
				$cfm_options = get_option( 'crestafacebookmessenger_settings' );
				?>
				<h3><div class="dashicons dashicons-admin-tools space"></div><?php esc_html_e( 'Main settings', 'cresta-facebook-messenger' ); ?></h3>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'How to use the plugin:', 'cresta-facebook-messenger' ); ?></th>
							<td>	
								<ul>
									<li>
										<label><input class="whatsMode" type="radio" value='classic' checked><?php _e('Classic mode (To allow your users to send you a message via Facebook Messenger)', 'cresta-facebook-messenger'); ?></label>
									</li>
									<li>
										<label><input class="whatsMode" type="radio" value='livechat' disabled><?php _e('Live chat mode * (To chat live with your users via Facebook Messenger)', 'cresta-facebook-messenger'); ?> - <span class="getPRO"><?php esc_html_e('PRO Version', 'cresta-facebook-messenger'); ?></span></label>
									</li>
								</ul>
								<span class="description attributes"><?php esc_html_e( '* Live chat mode is in beta version, customization options are limited by Facebook itself. Live Chat available only as a floating box, shortcodes available only with the classic mode.', 'cresta-facebook-messenger' ); ?></span>
							</td>
						</tr>
					</tbody>
				</table>
				<h3><div class="dashicons dashicons-admin-generic space"></div><?php esc_html_e( 'General Box Settings', 'cresta-facebook-messenger' ); ?></h3>
				<table class="form-table">
					<span class="description attributes notice"><?php echo wp_kses_post('To enable messaging on your Facebook page go to your <strong>Page Settings</strong>. In the row <strong>Messages</strong> check <strong>Allow people to contact my Page privately by showing the Message button</strong>.<br/>Direct link:', 'cresta-facebook-messenger'); ?> <i>https://www.facebook.com/<span style="color:red;">your-page-name</span>/settings/?tab=settings&#038;section=messages&#038;view</i></span>
					<tbody>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Your Facebook Page ID or Page name:', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<input class="regular-text" type='text' name='crestafacebookmessenger_settings[cresta_facebook_messenger_page_url]' value='<?php echo esc_attr($cfm_options['cresta_facebook_messenger_page_url']); ?>' placeholder='Example: 893215094058066'>
								<span class="description attributes">
								<?php
								/* translators: 1: start option panel link, 2: end option panel link */
								printf( esc_html__( 'The system works only with Facebook pages (no profiles or groups). Use %1$s this website %2$s to find the ID of your Facebook page.', 'cresta-facebook-messenger' ), '<a href="https://lookup-id.com/" rel="noopener noreferrer" target="_blank">', '</a>' );
								?>
								</span>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Box Text:', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<input class="regular-text" type='text' name='crestafacebookmessenger_settings[cresta_facebook_messenger_box_text]' value='<?php echo esc_attr($cfm_options['cresta_facebook_messenger_box_text']); ?>'>
								<span class="description attributes"><?php esc_html_e('Leave it blank if you do not want to use the box text.', 'cresta-facebook-messenger'); ?></span>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Title color:', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<span class="description getPRO"><?php esc_html_e('PRO Version', 'cresta-facebook-messenger'); ?></span>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Title background:', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<span class="description getPRO"><?php esc_html_e('PRO Version', 'cresta-facebook-messenger'); ?></span>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Hide page cover:', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<input type='checkbox' name='crestafacebookmessenger_settings[cresta_facebook_messenger_hide_cover]' value="1" <?php checked( $cfm_options['cresta_facebook_messenger_hide_cover'], '1' ); ?>>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Use small header:', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<input type='checkbox' name='crestafacebookmessenger_settings[cresta_facebook_messenger_small_header]' value="1" <?php checked( $cfm_options['cresta_facebook_messenger_small_header'], '1' ); ?>>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Show facepile:', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<input type='checkbox' name='crestafacebookmessenger_settings[cresta_facebook_messenger_show_facepile]' value="1" <?php checked( $cfm_options['cresta_facebook_messenger_show_facepile'], '1' ); ?>>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Tabs to show:', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<ul>
									<?php $tabs_to_show = explode (',',$cfm_options['cresta_facebook_messenger_tabs'] ); ?>
									<li>
										<input type="checkbox" <?php if(in_array( 'messages' ,$tabs_to_show)) { echo 'checked="checked"'; }?> name="crestafacebookmessenger_settings[cresta_facebook_messenger_tabs][]" value="messages"/><?php esc_html_e( 'Messages', 'cresta-facebook-messenger' ); ?>
									</li>
									<li>
										<input type="checkbox" <?php if(in_array( 'timeline' ,$tabs_to_show)) { echo 'checked="checked"'; }?> name="crestafacebookmessenger_settings[cresta_facebook_messenger_tabs][]" value="timeline"/><?php esc_html_e( 'Timeline', 'cresta-facebook-messenger' ); ?>
									</li>
									<li>
										<input type="checkbox" <?php if(in_array( 'events' ,$tabs_to_show)) { echo 'checked="checked"'; }?> name="crestafacebookmessenger_settings[cresta_facebook_messenger_tabs][]" value="events"/><?php esc_html_e( 'Events', 'cresta-facebook-messenger' ); ?>
									</li>
								</ul>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Choose the icon to show:', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<select name="crestafacebookmessenger_settings[cresta_facebook_messenger_what_icon]" id="crestafacebookmessenger_settings[cresta_facebook_messenger_what_icon]">
									<option value="messenger" <?php selected( $cfm_options['cresta_facebook_messenger_what_icon'], 'messenger' ); ?>><?php esc_html_e( 'Messenger Icon', 'cresta-facebook-messenger' ); ?></option>
									<option value="facebook" <?php selected( $cfm_options['cresta_facebook_messenger_what_icon'], 'facebook' ); ?>><?php esc_html_e( 'Facebook Icon', 'cresta-facebook-messenger' ); ?></option>
								</select>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Box Language:', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<select name="crestafacebookmessenger_settings[cresta_facebook_messenger_box_language]" id="crestafacebookmessenger_settings[cresta_facebook_messenger_box_language]">
									<option value="af_ZA" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'af_ZA' ); ?>><?php esc_html_e( 'Afrikaans', 'cresta-facebook-messenger' ); ?></option>
									<option value="sq_AL" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'sq_AL' ); ?>><?php esc_html_e( 'Albanian', 'cresta-facebook-messenger' ); ?></option>
									<option value="ar_AR" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'ar_AR' ); ?>><?php esc_html_e( 'Arabic', 'cresta-facebook-messenger' ); ?></option>
									<option value="hy_AM" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'hy_AM' ); ?>><?php esc_html_e( 'Armenian', 'cresta-facebook-messenger' ); ?></option>
									<option value="as_IN" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'as_IN' ); ?>><?php esc_html_e( 'Assamese', 'cresta-facebook-messenger' ); ?></option>
									<option value="az_AZ" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'az_AZ' ); ?>><?php esc_html_e( 'Azerbaijani', 'cresta-facebook-messenger' ); ?></option>
									<option value="eu_ES" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'eu_ES' ); ?>><?php esc_html_e( 'Basque', 'cresta-facebook-messenger' ); ?></option>
									<option value="be_BY" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'be_BY' ); ?>><?php esc_html_e( 'Belarusian', 'cresta-facebook-messenger' ); ?></option>
									<option value="bn_IN" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'bn_IN' ); ?>><?php esc_html_e( 'Bengali', 'cresta-facebook-messenger' ); ?></option>
									<option value="bs_BA" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'bs_BA' ); ?>><?php esc_html_e( 'Bosnian', 'cresta-facebook-messenger' ); ?></option>
									<option value="br_FR" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'br_FR' ); ?>><?php esc_html_e( 'Breton', 'cresta-facebook-messenger' ); ?></option>
									<option value="bg_BG" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'bg_BG' ); ?>><?php esc_html_e( 'Bulgarian', 'cresta-facebook-messenger' ); ?></option>
									<option value="my_MM" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'my_MM' ); ?>><?php esc_html_e( 'Burmese', 'cresta-facebook-messenger' ); ?></option>
									<option value="qz_MM" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'qz_MM' ); ?>><?php esc_html_e( 'Burmese (Zawgyi)', 'cresta-facebook-messenger' ); ?></option>
									<option value="ca_ES" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'ca_ES' ); ?>><?php esc_html_e( 'Catalan', 'cresta-facebook-messenger' ); ?></option>
									<option value="cx_PH" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'cx_PH' ); ?>><?php esc_html_e( 'Cebuano', 'cresta-facebook-messenger' ); ?></option>
									<option value="co_FR" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'co_FR' ); ?>><?php esc_html_e( 'Corsican', 'cresta-facebook-messenger' ); ?></option>
									<option value="hr_HR" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'hr_HR' ); ?>><?php esc_html_e( 'Croatian', 'cresta-facebook-messenger' ); ?></option>
									<option value="cs_CZ" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'cs_CZ' ); ?>><?php esc_html_e( 'Czech', 'cresta-facebook-messenger' ); ?></option>
									<option value="da_DK" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'da_DK' ); ?>><?php esc_html_e( 'Danish', 'cresta-facebook-messenger' ); ?></option>
									<option value="nl_NL" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'nl_NL' ); ?>><?php esc_html_e( 'Dutch', 'cresta-facebook-messenger' ); ?></option>
									<option value="nl_BE" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'nl_BE' ); ?>><?php esc_html_e( 'Dutch (Belgie)', 'cresta-facebook-messenger' ); ?></option>
									<option value="en_PI" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'en_PI' ); ?>><?php esc_html_e( 'English (Pirate)', 'cresta-facebook-messenger' ); ?></option>
									<option value="en_GB" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'en_GB' ); ?>><?php esc_html_e( 'English (UK)', 'cresta-facebook-messenger' ); ?></option>
									<option value="en_US" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'en_US' ); ?>><?php esc_html_e( 'English (US)', 'cresta-facebook-messenger' ); ?></option>
									<option value="en_UD" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'en_UD' ); ?>><?php esc_html_e( 'English (Upside Down)', 'cresta-facebook-messenger' ); ?></option>
									<option value="eo_EO" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'eo_EO' ); ?>><?php esc_html_e( 'Esperanto', 'cresta-facebook-messenger' ); ?></option>
									<option value="et_EE" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'et_EE' ); ?>><?php esc_html_e( 'Estonian', 'cresta-facebook-messenger' ); ?></option>
									<option value="fo_FO" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'fo_FO' ); ?>><?php esc_html_e( 'Faroese', 'cresta-facebook-messenger' ); ?></option>
									<option value="tl_PH" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'tl_PH' ); ?>><?php esc_html_e( 'Filipino', 'cresta-facebook-messenger' ); ?></option>
									<option value="fi_FI" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'fi_FI' ); ?>><?php esc_html_e( 'Finnish', 'cresta-facebook-messenger' ); ?></option>
									<option value="fr_CA" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'fr_CA' ); ?>><?php esc_html_e( 'French (Canada)', 'cresta-facebook-messenger' ); ?></option>
									<option value="fr_FR" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'fr_FR' ); ?>><?php esc_html_e( 'French (France)', 'cresta-facebook-messenger' ); ?></option>
									<option value="fy_NL" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'fy_NL' ); ?>><?php esc_html_e( 'Frisian', 'cresta-facebook-messenger' ); ?></option>
									<option value="ff_NG" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'ff_NG' ); ?>><?php esc_html_e( 'Fula', 'cresta-facebook-messenger' ); ?></option>
									<option value="gl_ES" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'gl_ES' ); ?>><?php esc_html_e( 'Galician', 'cresta-facebook-messenger' ); ?></option>
									<option value="ka_GE" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'ka_GE' ); ?>><?php esc_html_e( 'Georgian', 'cresta-facebook-messenger' ); ?></option>
									<option value="de_DE" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'de_DE' ); ?>><?php esc_html_e( 'German', 'cresta-facebook-messenger' ); ?></option>
									<option value="el_GR" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'el_GR' ); ?>><?php esc_html_e( 'Greek', 'cresta-facebook-messenger' ); ?></option>
									<option value="gn_PY" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'gn_PY' ); ?>><?php esc_html_e( 'Guarani', 'cresta-facebook-messenger' ); ?></option>
									<option value="gu_IN" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'gu_IN' ); ?>><?php esc_html_e( 'Gujarati', 'cresta-facebook-messenger' ); ?></option>
									<option value="ha_NG" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'ha_NG' ); ?>><?php esc_html_e( 'Hausa', 'cresta-facebook-messenger' ); ?></option>
									<option value="he_IL" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'he_IL' ); ?>><?php esc_html_e( 'Hebrew', 'cresta-facebook-messenger' ); ?></option>
									<option value="hi_IN" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'hi_IN' ); ?>><?php esc_html_e( 'Hindi', 'cresta-facebook-messenger' ); ?></option>
									<option value="hu_HU" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'hu_HU' ); ?>><?php esc_html_e( 'Hungarian', 'cresta-facebook-messenger' ); ?></option>
									<option value="is_IS" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'is_IS' ); ?>><?php esc_html_e( 'Icelandic', 'cresta-facebook-messenger' ); ?></option>
									<option value="id_ID" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'id_ID' ); ?>><?php esc_html_e( 'Indonesian', 'cresta-facebook-messenger' ); ?></option>
									<option value="ga_IE" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'ga_IE' ); ?>><?php esc_html_e( 'Irish', 'cresta-facebook-messenger' ); ?></option>
									<option value="it_IT" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'it_IT' ); ?>><?php esc_html_e( 'Italian', 'cresta-facebook-messenger' ); ?></option>
									<option value="ja_JP" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'ja_JP' ); ?>><?php esc_html_e( 'Japanese', 'cresta-facebook-messenger' ); ?></option>
									<option value="ja_KS" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'ja_KS' ); ?>><?php esc_html_e( 'Japanese (Kansai)', 'cresta-facebook-messenger' ); ?></option>
									<option value="jv_ID" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'jv_ID' ); ?>><?php esc_html_e( 'Javanese', 'cresta-facebook-messenger' ); ?></option>
									<option value="kn_IN" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'kn_IN' ); ?>><?php esc_html_e( 'Kannada', 'cresta-facebook-messenger' ); ?></option>
									<option value="kk_KZ" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'kk_KZ' ); ?>><?php esc_html_e( 'Kazakh', 'cresta-facebook-messenger' ); ?></option>
									<option value="km_KH" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'km_KH' ); ?>><?php esc_html_e( 'Khmer', 'cresta-facebook-messenger' ); ?></option>
									<option value="rw_RW" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'rw_RW' ); ?>><?php esc_html_e( 'Kinyarwanda', 'cresta-facebook-messenger' ); ?></option>
									<option value="ko_KR" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'ko_KR' ); ?>><?php esc_html_e( 'Korean', 'cresta-facebook-messenger' ); ?></option>
									<option value="ku_TR" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'ku_TR' ); ?>><?php esc_html_e( 'Kurdish (Kurmanji)', 'cresta-facebook-messenger' ); ?></option>
									<option value="la_VA" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'la_VA' ); ?>><?php esc_html_e( 'Latin', 'cresta-facebook-messenger' ); ?></option>
									<option value="lv_LV" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'lv_LV' ); ?>><?php esc_html_e( 'Latvian', 'cresta-facebook-messenger' ); ?></option>
									<option value="fb_LT" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'fb_LT' ); ?>><?php esc_html_e( 'Leet Speak', 'cresta-facebook-messenger' ); ?></option>
									<option value="lt_LT" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'lt_LT' ); ?>><?php esc_html_e( 'Lithuanian', 'cresta-facebook-messenger' ); ?></option>
									<option value="mk_MK" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'mk_MK' ); ?>><?php esc_html_e( 'Macedonian', 'cresta-facebook-messenger' ); ?></option>
									<option value="mg_MG" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'mg_MG' ); ?>><?php esc_html_e( 'Malagasy', 'cresta-facebook-messenger' ); ?></option>
									<option value="ms_MY" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'ms_MY' ); ?>><?php esc_html_e( 'Malay', 'cresta-facebook-messenger' ); ?></option>
									<option value="ml_IN" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'ml_IN' ); ?>><?php esc_html_e( 'Malayalam', 'cresta-facebook-messenger' ); ?></option>
									<option value="mt_MT" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'mt_MT' ); ?>><?php esc_html_e( 'Maltese', 'cresta-facebook-messenger' ); ?></option>
									<option value="mr_IN" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'mr_IN' ); ?>><?php esc_html_e( 'Marathi', 'cresta-facebook-messenger' ); ?></option>
									<option value="mn_MN" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'mn_MN' ); ?>><?php esc_html_e( 'Mongolian', 'cresta-facebook-messenger' ); ?></option>
									<option value="ne_NP" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'ne_NP' ); ?>><?php esc_html_e( 'Nepali', 'cresta-facebook-messenger' ); ?></option>
									<option value="nb_NO" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'nb_NO' ); ?>><?php esc_html_e( 'Norvegian (bokmal)', 'cresta-facebook-messenger' ); ?></option>
									<option value="nn_NO" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'nn_NO' ); ?>><?php esc_html_e( 'Norvegian (nynorsk)', 'cresta-facebook-messenger' ); ?></option>
									<option value="or_IN" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'or_IN' ); ?>><?php esc_html_e( 'Oriya', 'cresta-facebook-messenger' ); ?></option>
									<option value="ps_AF" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'ps_AF' ); ?>><?php esc_html_e( 'Pashto', 'cresta-facebook-messenger' ); ?></option>
									<option value="fa_IR" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'fa_IR' ); ?>><?php esc_html_e( 'Persian', 'cresta-facebook-messenger' ); ?></option>
									<option value="pl_PL" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'pl_PL' ); ?>><?php esc_html_e( 'Polish', 'cresta-facebook-messenger' ); ?></option>
									<option value="pt_BR" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'pt_BR' ); ?>><?php esc_html_e( 'Portuguese (Brazil)', 'cresta-facebook-messenger' ); ?></option>
									<option value="pt_PT" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'pt_PT' ); ?>><?php esc_html_e( 'Portuguese (Portugal)', 'cresta-facebook-messenger' ); ?></option>
									<option value="pa_IN" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'pa_IN' ); ?>><?php esc_html_e( 'Punjabi', 'cresta-facebook-messenger' ); ?></option>
									<option value="ro_RO" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'ro_RO' ); ?>><?php esc_html_e( 'Romanian', 'cresta-facebook-messenger' ); ?></option>
									<option value="ru_RU" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'ru_RU' ); ?>><?php esc_html_e( 'Russian', 'cresta-facebook-messenger' ); ?></option>
									<option value="sc_IT" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'sc_IT' ); ?>><?php esc_html_e( 'Sardinian', 'cresta-facebook-messenger' ); ?></option>
									<option value="sr_RS" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'sr_RS' ); ?>><?php esc_html_e( 'Serbian', 'cresta-facebook-messenger' ); ?></option>
									<option value="sz_PL" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'sz_PL' ); ?>><?php esc_html_e( 'Silesian', 'cresta-facebook-messenger' ); ?></option>
									<option value="zh_CN" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'zh_CN' ); ?>><?php esc_html_e( 'Semplified Chinese (China)', 'cresta-facebook-messenger' ); ?></option>
									<option value="si_LK" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'si_LK' ); ?>><?php esc_html_e( 'Sinhala', 'cresta-facebook-messenger' ); ?></option>
									<option value="sk_SK" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'sk_SK' ); ?>><?php esc_html_e( 'Slovak', 'cresta-facebook-messenger' ); ?></option>
									<option value="sl_SI" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'sl_SI' ); ?>><?php esc_html_e( 'Slovenian', 'cresta-facebook-messenger' ); ?></option>
									<option value="so_SO" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'so_SO' ); ?>><?php esc_html_e( 'Somali', 'cresta-facebook-messenger' ); ?></option>
									<option value="cb_IQ" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'cb_IQ' ); ?>><?php esc_html_e( 'Sorani Kurdish', 'cresta-facebook-messenger' ); ?></option>
									<option value="es_LA" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'es_LA' ); ?>><?php esc_html_e( 'Spanish', 'cresta-facebook-messenger' ); ?></option>
									<option value="es_ES" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'es_ES' ); ?>><?php esc_html_e( 'Spanish (Spain)', 'cresta-facebook-messenger' ); ?></option>
									<option value="sw_KE" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'sw_KE' ); ?>><?php esc_html_e( 'Swahili', 'cresta-facebook-messenger' ); ?></option>
									<option value="sv_SE" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'sv_SE' ); ?>><?php esc_html_e( 'Swedish', 'cresta-facebook-messenger' ); ?></option>
									<option value="sy_SY" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'sy_SY' ); ?>><?php esc_html_e( 'Syriac', 'cresta-facebook-messenger' ); ?></option>
									<option value="tg_TJ" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'tg_TJ' ); ?>><?php esc_html_e( 'Tajik', 'cresta-facebook-messenger' ); ?></option>
									<option value="tz_MA" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'tz_MA' ); ?>><?php esc_html_e( 'Tamazight', 'cresta-facebook-messenger' ); ?></option>
									<option value="ta_IN" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'ta_IN' ); ?>><?php esc_html_e( 'Tamil', 'cresta-facebook-messenger' ); ?></option>
									<option value="te_IN" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'te_IN' ); ?>><?php esc_html_e( 'Telugu', 'cresta-facebook-messenger' ); ?></option>
									<option value="th_TH" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'th_TH' ); ?>><?php esc_html_e( 'Thai', 'cresta-facebook-messenger' ); ?></option>
									<option value="zh_HK" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'zh_HK' ); ?>><?php esc_html_e( 'Traditional Chinese (Hong Kong)', 'cresta-facebook-messenger' ); ?></option>
									<option value="zh_TW" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'zh_TW' ); ?>><?php esc_html_e( 'Traditional Chinese (Taiwan)', 'cresta-facebook-messenger' ); ?></option>
									<option value="tr_TR" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'tr_TR' ); ?>><?php esc_html_e( 'Turkish', 'cresta-facebook-messenger' ); ?></option>
									<option value="uk_UA" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'uk_UA' ); ?>><?php esc_html_e( 'Ukrainian', 'cresta-facebook-messenger' ); ?></option>
									<option value="ur_PK" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'ur_PK' ); ?>><?php esc_html_e( 'Urdu', 'cresta-facebook-messenger' ); ?></option>
									<option value="uz_UZ" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'uz_UZ' ); ?>><?php esc_html_e( 'Uzbek', 'cresta-facebook-messenger' ); ?></option>
									<option value="vi_VN" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'vi_VN' ); ?>><?php esc_html_e( 'Vietnamese', 'cresta-facebook-messenger' ); ?></option>
									<option value="cy_GB" <?php selected( $cfm_options['cresta_facebook_messenger_box_language'], 'cy_GB' ); ?>><?php esc_html_e( 'Welsh', 'cresta-facebook-messenger' ); ?></option>
								</select>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Boz Z-Index:', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<input type='number' name='crestafacebookmessenger_settings[cresta_facebook_messenger_zindex]' value='<?php echo intval($cfm_options['cresta_facebook_messenger_zindex']); ?>' min="0" max="999999">
								<span class="description"><?php esc_html_e('Increase this number if the box is covered by other items on the screen.', 'cresta-facebook-messenger'); ?></span>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Box Width:', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<span class="description getPRO"><?php esc_html_e('PRO Version', 'cresta-facebook-messenger'); ?></span>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Box Height:', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<span class="description getPRO"><?php esc_html_e('PRO Version', 'cresta-facebook-messenger'); ?></span>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Show Facebook Messenger button:', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<select name="crestafacebookmessenger_settings[cresta_facebook_messenger_show_option]" id="crestafacebookmessenger_settings[cresta_facebook_messenger_show_option]">
									<option value="onBoth" <?php selected( $cfm_options['cresta_facebook_messenger_show_option'], 'onBoth' ); ?>><?php esc_html_e( 'Both mobile and desktop', 'cresta-facebook-messenger' ); ?></option>
									<option value="onMobile" <?php selected( $cfm_options['cresta_facebook_messenger_show_option'], 'onMobile' ); ?>><?php esc_html_e( 'Only on mobile', 'cresta-facebook-messenger' ); ?></option>
									<option value="onDesktop" <?php selected( $cfm_options['cresta_facebook_messenger_show_option'], 'onDesktop' ); ?>><?php esc_html_e( 'Only on desktop', 'cresta-facebook-messenger' ); ?></option>
								</select>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'On Mobile:', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<select name="crestafacebookmessenger_settings[cresta_facebook_messenger_mobile_option]" id="crestafacebookmessenger_settings[cresta_facebook_messenger_mobile_option]">
									<option value="onApp" <?php selected( $cfm_options['cresta_facebook_messenger_mobile_option'], 'onApp' ); ?>><?php esc_html_e( 'Open Facebook Messenger APP', 'cresta-facebook-messenger' ); ?></option>
									<option value="onBrowser" <?php selected( $cfm_options['cresta_facebook_messenger_mobile_option'], 'onBrowser' ); ?>><?php esc_html_e( 'Use the browser', 'cresta-facebook-messenger' ); ?></option>
								</select>
							</td>
						</tr>
					</tbody>
				</table>
				<h3><div class="dashicons dashicons-info space"></div><?php esc_html_e( 'Floating Box', 'cresta-facebook-messenger' ); ?></h3>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Show floating box', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<input type='checkbox' class='crestaisPossbile <?php if($cfm_options['cresta_facebook_messenger_show_floating_box'] == '1') { echo 'active'; }?>' name='crestafacebookmessenger_settings[cresta_facebook_messenger_show_floating_box]' value="1" <?php checked( $cfm_options['cresta_facebook_messenger_show_floating_box'], '1' ); ?>>
							</td>
						</tr>
						<tr valign="top" class="chkifpossible">
							<th scope="row"><?php esc_html_e( 'Show the floating box on', 'cresta-facebook-messenger' ); ?></th>
							<td>
							<?php
								$box_show_on = explode (',',$cfm_options['cresta_facebook_messenger_selected_page'] );
								echo '<ul>'; ?>
									<li>
										<input class="ifwebsite <?php if(in_array( 'website', $box_show_on )) { echo 'active'; }?>" type="checkbox" <?php if(in_array( 'website' ,$box_show_on)) { echo 'checked="checked"'; }?> name="crestafacebookmessenger_settings[cresta_facebook_messenger_selected_page][]" value="website"/><?php esc_html_e( 'Entire website', 'cresta-facebook-messenger' ); ?>
									</li>
									<li class="yesiswebsite">
										<input type="checkbox" <?php if(in_array( 'homepage' ,$box_show_on)) { echo 'checked="checked"'; }?> name="crestafacebookmessenger_settings[cresta_facebook_messenger_selected_page][]" value="homepage"/><?php esc_html_e( 'Home page', 'cresta-facebook-messenger' ); ?>
									</li>
									<li class="yesiswebsite">
										<input type="checkbox" <?php if(in_array( 'blogpage' ,$box_show_on)) { echo 'checked="checked"'; }?> name="crestafacebookmessenger_settings[cresta_facebook_messenger_selected_page][]" value="blogpage"/><?php esc_html_e( 'Blog page', 'cresta-facebook-messenger' ); ?>
									</li>
									<?php if (function_exists( 'is_woocommerce' )) : ?>
										<li class="yesiswebsite">
											<input type="checkbox" <?php if(in_array( 'shoppage' ,$box_show_on)) { echo 'checked="checked"'; }?> name="crestafacebookmessenger_settings[cresta_facebook_messenger_selected_page][]" value="shoppage"/><?php esc_html_e( 'WooCommerce Shop page', 'cresta-facebook-messenger' ); ?>
										</li>
										<li class="yesiswebsite">
											<input type="checkbox" <?php if(in_array( 'woocatpage' ,$box_show_on)) { echo 'checked="checked"'; }?> name="crestafacebookmessenger_settings[cresta_facebook_messenger_selected_page][]" value="woocatpage"/><?php esc_html_e( 'WooCommerce Product Catgory', 'cresta-facebook-messenger' ); ?>
										</li>
										<li class="yesiswebsite">
											<input type="checkbox" <?php if(in_array( 'wootagpage' ,$box_show_on)) { echo 'checked="checked"'; }?> name="crestafacebookmessenger_settings[cresta_facebook_messenger_selected_page][]" value="wootagpage"/><?php esc_html_e( 'WooCommerce Product Tag', 'cresta-facebook-messenger' ); ?>
										</li>
									<?php endif; ?>
									<li class="yesiswebsite">
										<input type="checkbox" <?php if(in_array( 'catpage' ,$box_show_on)) { echo 'checked="checked"'; }?> name="crestafacebookmessenger_settings[cresta_facebook_messenger_selected_page][]" value="catpage"/><?php esc_html_e( 'Category pages', 'cresta-facebook-messenger' ); ?>
									</li>
									<li class="yesiswebsite">
										<input type="checkbox" <?php if(in_array( 'tagpage' ,$box_show_on)) { echo 'checked="checked"'; }?> name="crestafacebookmessenger_settings[cresta_facebook_messenger_selected_page][]" value="tagpage"/><?php esc_html_e( 'Tag pages', 'cresta-facebook-messenger' ); ?>
									</li>
									<li class="yesiswebsite">
										<input type="checkbox" <?php if(in_array( 'authorpage' ,$box_show_on)) { echo 'checked="checked"'; }?> name="crestafacebookmessengerpro_settings[cresta_facebook_messenger_selected_page][]" value="authorpage"/><?php esc_html_e( 'Author pages', 'cresta-facebook-messenger' ); ?>
									</li>
									<li class="yesiswebsite">
										<input type="checkbox" <?php if(in_array( 'datepage' ,$box_show_on)) { echo 'checked="checked"'; }?> name="crestafacebookmessengerpro_settings[cresta_facebook_messenger_selected_page][]" value="datepage"/><?php esc_html_e( 'Date pages', 'cresta-facebook-messenger' ); ?>
									</li>
									<li class="yesiswebsite">
										<input type="checkbox" <?php if(in_array( 'searchpage' ,$box_show_on)) { echo 'checked="checked"'; }?> name="crestafacebookmessenger_settings[cresta_facebook_messenger_selected_page][]" value="searchpage"/><?php esc_html_e( 'Search pages', 'cresta-facebook-messenger' ); ?>
									</li>
								<?php
								$args = array(
									'public'   => true,
								);
								$post_types = get_post_types( $args, 'names', 'and' ); 
								foreach ( $post_types  as $post_type ) { 
									$post_type_name = get_post_type_object( $post_type );
									?>
									<li class="yesiswebsite">
										<input type="checkbox" <?php if(in_array( $post_type ,$box_show_on)) { echo 'checked="checked"'; }?> name="crestafacebookmessenger_settings[cresta_facebook_messenger_selected_page][]" value="<?php echo esc_attr($post_type); ?>"/><?php echo esc_html($post_type_name->labels->singular_name); ?>
									</li>
								<?php
								}
								echo '</ul>';
							?>
							<span class="description"><?php esc_html_e( 'If active, post, page and custom post type can be managed individually via metabox when you edit a page or post. You can choose to hide the Facebook Messenger box in a specific post, page or custom post type.', 'cresta-facebook-messenger' ); ?></span>
							</td>
						</tr>
						<tr valign="top" class="chkifpossible">
							<th scope="row"><?php esc_html_e( 'Close the floating box by clicking anywhere on the page:', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<input type='checkbox' name='crestafacebookmessenger_settings[cresta_facebook_messenger_click_to_close]' value="1" <?php checked( $cfm_options['cresta_facebook_messenger_click_to_close'], '1' ); ?>>
							</td>
						</tr>
						<tr valign="top" class="chkifpossible">
							<th scope="row"><?php esc_html_e( 'Icon color:', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<span class="description getPRO"><?php esc_html_e('PRO Version', 'cresta-facebook-messenger'); ?></span>
							</td>
						</tr>
						<tr valign="top" class="chkifpossible">
							<th scope="row"><?php esc_html_e( 'Icon background:', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<span class="description getPRO"><?php esc_html_e('PRO Version', 'cresta-facebook-messenger'); ?></span>
							</td>
						</tr>
						<tr valign="top" class="chkifpossible">
							<th scope="row"><?php esc_html_e( 'Icon animation:', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<span class="description getPRO"><?php esc_html_e('PRO Version', 'cresta-facebook-messenger'); ?></span>
							</td>
						</tr>
						<tr valign="top" class="chkifpossible">
							<th scope="row"><?php esc_html_e( 'Distance from left/right:', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<span class="description getPRO"><?php esc_html_e('PRO Version', 'cresta-facebook-messenger'); ?></span>
							</td>
						</tr>
						<tr valign="top" class="chkifpossible">
							<th scope="row"><?php esc_html_e( 'Distance from top/bottom:', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<span class="description getPRO"><?php esc_html_e('PRO Version', 'cresta-facebook-messenger'); ?></span>
							</td>
						</tr>
						<tr valign="top" class="chkifpossible">
							<th scope="row"><?php esc_html_e( 'Tooltip text:', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<span class="description getPRO"><?php esc_html_e('PRO Version', 'cresta-facebook-messenger'); ?></span>
							</td>
						</tr>
						<tr valign="top" class="chkifpossible">
							<th scope="row"><?php esc_html_e( 'Text next to the Messenger icon:', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<span class="description getPRO"><?php esc_html_e('PRO Version', 'cresta-facebook-messenger'); ?></span>
							</td>
						</tr>
						<tr valign="top" class="chkifpossible">
							<th scope="row"><?php esc_html_e( 'Auto open the box after N seconds (only on desktop):', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<span class="description getPRO"><?php esc_html_e('PRO Version', 'cresta-facebook-messenger'); ?></span>
							</td>
						</tr>
					</tbody>
				</table>
				<h3><div class="dashicons dashicons-hammer space"></div><?php esc_html_e( 'Shortcode and PHP code', 'cresta-facebook-messenger' ); ?></h3>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Attributes for shortcode and PHP code', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<span class="description attributes"><strong>text</strong> - <?php esc_html_e( 'The text you want to display in the button', 'cresta-facebook-messenger' ); ?></span>
								<span class="description attributes"><strong>icon</strong> - <?php esc_html_e( 'Choose whether to display the Facebook Messenger icon next to the text (yes or no)', 'cresta-facebook-messenger' ); ?></span>
								<span class="description attributes"><strong>position</strong> - <?php esc_html_e( 'The position of the box after the user has clicked on the button (top, bottom, left or right)', 'cresta-facebook-messenger' ); ?></span>
								<span class="description attributes"><strong>fbpage</strong> - <?php esc_html_e( 'Use it if you want to use a different Facebook Page ID than the one used in the plugin options page.', 'cresta-facebook-messenger' ); ?></span>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Shortcode', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<span class="description"><?php esc_html_e('You can place the shortcode in posts or pages you want to display the Facebook Messenger button:', 'cresta-facebook-messenger'); ?>
<pre><code>[cresta-facebook-messenger text="Need Help?" icon="yes" position="top"]</code></pre>
								</span>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'PHP Code', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<span class="description"><?php esc_html_e('If you want to add the Facebook Messenger button in the theme code you can use this PHP code:', 'cresta-facebook-messenger'); ?>
<pre><code>&lt;?php
	if(function_exists(&#039;cresta_facebook_messenger_shortcode&#039;)) {
		echo do_shortcode('[cresta-facebook-messenger text="Need Help? Click Here!" icon="yes" position="left"]');
	}
?&gt;</code></pre>
								</span>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Button color:', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<span class="description getPRO"><?php esc_html_e('PRO Version', 'cresta-facebook-messenger'); ?></span>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php esc_html_e( 'Button background:', 'cresta-facebook-messenger' ); ?></th>
							<td>
								<span class="description getPRO"><?php esc_html_e('PRO Version', 'cresta-facebook-messenger'); ?></span>
							</td>
						</tr>
					</tbody>
				</table>
				<?php submit_button(); ?>
			</form>
			</div> <!-- .inside -->
			</div> <!-- .postbox -->
			</div> <!-- .meta-box-sortables .ui-sortable -->
			</div> <!-- post-body-content -->
			<!-- sidebar -->
				<div id="postbox-container-1" class="postbox-container">
					<div class="meta-box-sortables">
						<div class="postbox">
							<h3><span><div class="dashicons dashicons-star-filled"></div> <?php esc_html_e( 'Rate it!', 'cresta-facebook-messenger' ); ?></span></h3>
							<div class="inside">
								<?php echo wp_kses_post( 'Do not forget to rate <strong>Cresta Social Messenger</strong> on WordPress Pugins Directory.<br/>We really appreciate it ;)', 'cresta-facebook-messenger' ); ?>
								<br/>
								<img src="<?php echo esc_url( plugins_url( '/images/5-stars.png' , __FILE__ )); ?>">
								<br/>
								<a class="crestaButton" href="https://wordpress.org/support/plugin/cresta-facebook-messenger/reviews/#new-post"title="<?php esc_attr_e( 'Rate Cresta Social Messenger on Wordpress Plugins Directory', 'cresta-facebook-messenger' ); ?>" class="btn btn-primary" target="_blank"><?php esc_html_e( 'Rate Cresta Social Messenger', 'cresta-facebook-messenger' ); ?></a>
							</div> <!-- .inside -->
						</div> <!-- .postbox -->

						<div class="postbox" style="border: 4px solid #1182fc;">
							
							<h3><span><div class="dashicons dashicons-megaphone"></div> <?php esc_html_e( 'Need More? Get the PRO version', 'cresta-facebook-messenger' ); ?></span></h3>
							<div class="inside">
								<a href="https://crestaproject.com/downloads/cresta-facebook-messenger/" target="_blank" alt="Get Cresta Social Messenger PRO"><img src="<?php echo esc_url(plugins_url( '/images/banner-cresta-facebook-messenger-pro.png' , __FILE__ )); ?>"></a><br/>
								<?php echo wp_kses_post( 'Get <strong>Cresta Social Messenger PRO</strong> for only', 'cresta-facebook-messenger' ); ?> <strong>9,99&euro;</strong><br/>
								<ul>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> <?php esc_html_e( 'Change position of the box button', 'cresta-facebook-messenger' ); ?></li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> <?php esc_html_e( 'Change colors of the button, shortcode and text', 'cresta-facebook-messenger' ); ?></li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> <?php esc_html_e( 'Add tooltip on the box button', 'cresta-facebook-messenger' ); ?></li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> <?php esc_html_e( 'Change box size', 'cresta-facebook-messenger' ); ?></li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> <?php esc_html_e( 'Facebook Messenger widget', 'cresta-facebook-messenger' ); ?></li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> <?php esc_html_e( '4 Click animations and 9 icon animations', 'cresta-facebook-messenger' ); ?></li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> <?php esc_html_e( 'Live Chat Mode (beta)', 'cresta-facebook-messenger' ); ?></li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> <?php esc_html_e( '20% discount code for all CrestaProject Themes', 'cresta-facebook-messenger' ); ?></li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> <?php esc_html_e( '1 year updates and support', 'cresta-facebook-messenger' ); ?></li>
									<li><div class="dashicons dashicons-yes crestaGreen"></div> <?php esc_html_e( 'and Much More...', 'cresta-facebook-messenger' ); ?></li>
								</ul>
								<a class="crestaButton" href="https://crestaproject.com/downloads/cresta-facebook-messenger/" target="_blank" title="<?php esc_attr_e( 'More Details', 'cresta-facebook-messenger' ); ?>"><?php esc_html_e( 'More Details', 'cresta-facebook-messenger' ); ?></a>
							</div> <!-- .inside -->
						 </div> <!-- .postbox -->
						<div class="postbox" style="border: 4px solid #3cdb65;">
                            
                            <h3><span><div class="dashicons dashicons-admin-plugins"></div> Cresta Help Chat Plugin</span></h3>
                            <div class="inside">
                                <a href="https://crestaproject.com/downloads/cresta-help-chat/" target="_blank" alt="Get Cresta Help Chat"><img src="<?php echo plugins_url( '/images/banner-cresta-whatsapp-chat.png' , __FILE__ ); ?>"></a><br/>
								With <strong>Cresta Help Chat</strong> you can allow your users or customers to contact you via <strong>WhatsApp</strong> simply by clicking on a button.<br/>
								Users may contact you directly in private messages on your WhatsApp number and continue the conversation on WhatsApp web or WhatsApp application (from mobile).
								<a class="crestaButton" href="https://crestaproject.com/downloads/cresta-help-chat/" target="_blank" title="Cresta Help Chat">Available in FREE and PRO version</a>
                            </div> <!-- .inside -->
                         </div> <!-- .postbox -->
						<div class="postbox" style="border: 4px solid #d54e21;">
							
							<h3><span><div class="dashicons dashicons-admin-plugins"></div> <?php esc_html_e( 'Cresta Social Share Counter Plugin', 'cresta-facebook-messenger' ); ?></span></h3>
							<div class="inside">
								<a href="https://crestaproject.com/downloads/cresta-social-share-counter/" target="_blank" alt="Get Cresta Social Share Counter"><img src="<?php echo plugins_url( '/images/banner-cresta-social-share-counter.png' , __FILE__ ); ?>"></a><br/>
								<?php esc_html_e( 'Share your posts and pages quickly and easily with Cresta Social Share Counter showing the share count.', 'cresta-facebook-messenger' ); ?>
								<a class="crestaButton" href="https://crestaproject.com/downloads/cresta-social-share-counter/" target="_blank" title="<?php esc_attr_e( 'Cresta Social Share Counter', 'cresta-facebook-messenger' ); ?>"><?php esc_html_e( 'Available in FREE and PRO version', 'cresta-facebook-messenger' ); ?></a>
							</div> <!-- .inside -->
						 </div> <!-- .postbox -->
					</div> <!-- .meta-box-sortables -->
				</div> <!-- #postbox-container-1 .postbox-container -->
			</div> <!-- #post-body .metabox-holder .columns-2 -->
			<br class="clear">
		</div> <!-- #poststuff -->
	</div>
	<?php
	echo ob_get_clean();
}

/* Validate options */
function crestafacebookmessenger_options_validate($input) {
	$new_input = array();
	if($input['cresta_facebook_messenger_tabs'] != '' && is_array($input['cresta_facebook_messenger_tabs'])) {
		$tabs_to_show = implode(',',$input['cresta_facebook_messenger_tabs']);
		$new_input['cresta_facebook_messenger_tabs'] = wp_filter_nohtml_kses($tabs_to_show); 
	} else {
		$new_input['cresta_facebook_messenger_tabs'] = 'messages'; 
	}
	$new_input['cresta_facebook_messenger_page_url'] = sanitize_text_field($input['cresta_facebook_messenger_page_url']);
	$new_input['cresta_facebook_messenger_box_text'] = sanitize_text_field($input['cresta_facebook_messenger_box_text']);
	if( isset( $input['cresta_facebook_messenger_hide_cover'] ) ) {
		$new_input['cresta_facebook_messenger_hide_cover'] = true;
	} else {
		$new_input['cresta_facebook_messenger_hide_cover'] = false;
	}
	if( isset( $input['cresta_facebook_messenger_small_header'] ) ) {
		$new_input['cresta_facebook_messenger_small_header'] = true;
	} else {
		$new_input['cresta_facebook_messenger_small_header'] = false;
	}
	if( isset( $input['cresta_facebook_messenger_show_facepile'] ) ) {
		$new_input['cresta_facebook_messenger_show_facepile'] = true;
	} else {
		$new_input['cresta_facebook_messenger_show_facepile'] = false;
	}
	if( isset( $input['cresta_facebook_messenger_show_floating_box'] ) ) {
		$new_input['cresta_facebook_messenger_show_floating_box'] = true;
	} else {
		$new_input['cresta_facebook_messenger_show_floating_box'] = false;
	}
	if( isset( $input['cresta_facebook_messenger_click_to_close'] ) ) {
		$new_input['cresta_facebook_messenger_click_to_close'] = true;
	} else {
		$new_input['cresta_facebook_messenger_click_to_close'] = false;
	}
	$new_input['cresta_facebook_messenger_box_language'] = sanitize_text_field(wp_unslash($input['cresta_facebook_messenger_box_language']));
	$new_input['cresta_facebook_messenger_zindex'] = sanitize_text_field(absint($input['cresta_facebook_messenger_zindex']));
	$new_input['cresta_facebook_messenger_mobile_option'] = sanitize_text_field(wp_unslash($input['cresta_facebook_messenger_mobile_option']));
	$new_input['cresta_facebook_messenger_show_option'] = sanitize_text_field(wp_unslash($input['cresta_facebook_messenger_show_option']));
	$new_input['cresta_facebook_messenger_what_icon'] = sanitize_text_field(wp_unslash($input['cresta_facebook_messenger_what_icon']));
	if($input['cresta_facebook_messenger_selected_page'] != '' && is_array($input['cresta_facebook_messenger_selected_page'])) {
		$box_show_on = implode(',',$input['cresta_facebook_messenger_selected_page']);
		$new_input['cresta_facebook_messenger_selected_page'] = wp_filter_nohtml_kses($box_show_on); 
	} else {
		$new_input['cresta_facebook_messenger_selected_page'] = 'homepage,blogpage,post,page'; 
	}
	return $new_input;
}
?>