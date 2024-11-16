<?php
/*
Plugin Name: reCAPTCHA (v2 & v3) for Asgaros Forum
Plugin URI: http://wordpress.org/plugins/recaptcha-for-asgaros-forum/
Description: Protect your Asgaros forum from spam using Googles reCAPTCHA v2 and v3. This plugin prevent bots to spam your forum and has option to enabe reCAPTCHA for guest users & logged-in users.
Author: Hitesh Chandwani
Version: 1.0.8
Author URI: https://hiteshchandwani.com
*/

$plugin = plugin_basename(__FILE__); 

function rfaf_clear_data() {
	delete_option( 'rfaf_recaptcha_version');
	delete_option( 'rfaf_recaptcha_site_key');
	delete_option( 'rfaf_recaptcha_server_key');
	delete_option( 'rfaf_recaptcha_language');
	delete_option( 'rfaf_recaptcha_v3_site_key');
	delete_option( 'rfaf_recaptcha_v3_server_key');
	delete_option( 'rfaf_recaptcha_v3_score');
	delete_option( 'rfaf_recaptcha_registerd_user');
}
register_deactivation_hook( __FILE__, 'rfaf_clear_data' );

function rfaf_add_admin_submenu() {
	add_submenu_page( 'asgarosforum-structure', 'reCAPTCHA', 'reCAPTCHA', 'read', 'asgaros-recaptcha', 'rfaf_recaptcha_callback' );
}
add_action( 'admin_menu', 'rfaf_add_admin_submenu' );

function rfaf_recaptcha_callback() {
	require( 'recaptcha.php' );
}

function rfaf_save_recaptcha_setting() {

	if( isset( $_POST['rfaf_recaptcha_submit'] ) ):
		$nonce = $_REQUEST['_wpnonce'];
		if(wp_verify_nonce($nonce, 'rfaf_recaptcha_submit_nonce')) {
			$rfaf_recaptcha_version = sanitize_text_field($_POST['rfaf_recaptcha_version']);
			$rfaf_recaptcha_site_key = sanitize_text_field($_POST['rfaf_recaptcha_site_key']);
			$rfaf_recaptcha_server_key = sanitize_text_field($_POST['rfaf_recaptcha_server_key']);
			$rfaf_recaptcha_language = sanitize_text_field($_POST['rfaf_recaptcha_language']);
			$rfaf_recaptcha_v3_site_key = sanitize_text_field($_POST['rfaf_recaptcha_v3_site_key']);
			$rfaf_recaptcha_v3_server_key = sanitize_text_field($_POST['rfaf_recaptcha_v3_server_key']);
			$rfaf_recaptcha_v3_score = sanitize_text_field($_POST['rfaf_recaptcha_v3_score']);
			$rfaf_recaptcha_registerd_user = 0;
			if( isset( $_POST['rfaf_recaptcha_registerd_user'] ) ){
				$rfaf_recaptcha_registerd_user = sanitize_text_field( $_POST['rfaf_recaptcha_registerd_user'] );
			}

			update_option( 'rfaf_recaptcha_version', $rfaf_recaptcha_version);
			update_option( 'rfaf_recaptcha_site_key', $rfaf_recaptcha_site_key);
			update_option( 'rfaf_recaptcha_server_key', $rfaf_recaptcha_server_key);
			update_option( 'rfaf_recaptcha_language', $rfaf_recaptcha_language);
			update_option( 'rfaf_recaptcha_v3_site_key', $rfaf_recaptcha_v3_site_key);
			update_option( 'rfaf_recaptcha_v3_server_key', $rfaf_recaptcha_v3_server_key);
			update_option( 'rfaf_recaptcha_v3_score', $rfaf_recaptcha_v3_score);
			update_option( 'rfaf_recaptcha_registerd_user', $rfaf_recaptcha_registerd_user);
		} else {
			$_REQUEST['nonce_error'] = true;
		}
	endif;

}
add_action('wp_loaded', 'rfaf_save_recaptcha_setting');

function rfaf_plugin_settings_link($links){
	if(is_plugin_active('asgaros-forum/asgaros-forum.php')){

		$settings_link = '<a href="admin.php?page=asgaros-recaptcha">Enable reCAPTCHA</a>';
		array_unshift($links, $settings_link);

	}

	return $links;
}
add_filter("plugin_action_links_$plugin", 'rfaf_plugin_settings_link' );

function rfaf_bbp_captcha_integrate(){
	global $asgarosforum;
	
	$is_guest_enable 			= $asgarosforum->options['allow_guest_postings'];
	$recaptcha_version 			= get_option( 'rfaf_recaptcha_version', 'version 2' );
	$site_key 					= get_option( 'rfaf_recaptcha_site_key', false );
	$server_key					= get_option( 'rfaf_recaptcha_server_key', false );
	$site_key_v3 				= get_option( 'rfaf_recaptcha_v3_site_key', false );
	$server_key_v3				= get_option( 'rfaf_recaptcha_v3_server_key', false );
	$enable_for_registerd_user  = get_option( 'rfaf_recaptcha_registerd_user', false );

	if($recaptcha_version == "version 3" and $server_key_v3 != false & $site_key_v3 != false & (($is_guest_enable != false and !is_user_logged_in()) or $enable_for_registerd_user != false)) {
		
		echo "<input type='hidden' class='g-recaptcha-response' name='g-recaptcha-response'>";
		wp_enqueue_script('rfaf-google-reCaptcha', "https://www.google.com/recaptcha/api.js?render=$site_key_v3");
		wp_add_inline_script('rfaf-google-reCaptcha', "grecaptcha.ready(function() { grecaptcha.execute('$site_key_v3', {action: 'social'}).then(function(token) { jQuery('.g-recaptcha-response').val(token); }); });");

	} else if($server_key != false & $site_key != false & (($is_guest_enable != false and !is_user_logged_in()) or $enable_for_registerd_user!=false)){
		
		echo '<div class="editor-row editor-row-captcha">';
		echo "<div class='g-recaptcha' data-sitekey='$site_key'></div>";
		echo '</div>';
		$lang = esc_attr( get_option('rfaf_recaptcha_language', 'en') );
		wp_enqueue_script( 'rfaf-google-reCaptcha', "https://www.google.com/recaptcha/api.js?hl=$lang");	

	}
	
}
add_action('asgarosforum_editor_custom_content_bottom', 'rfaf_bbp_captcha_integrate');

function rfaf_validate_recaptcha( $status ){
	global $asgarosforum;

	$is_guest_enable 			= $asgarosforum->options['allow_guest_postings'];
	$recaptcha_version 			= get_option( 'rfaf_recaptcha_version', 'version 2' );
	$site_key 					= get_option( 'rfaf_recaptcha_site_key', false );
	$server_key					= get_option( 'rfaf_recaptcha_server_key', false );
	$site_key_v3 				= get_option( 'rfaf_recaptcha_v3_site_key', false );
	$server_key_v3				= get_option( 'rfaf_recaptcha_v3_server_key', false );
	$recaptcha_v3_score			= (float) get_option( 'rfaf_recaptcha_v3_score', 0.5 );
	$enable_for_registerd_user  = get_option( 'rfaf_recaptcha_registerd_user', false );

	if( $recaptcha_version == "version 3" and $server_key_v3 != false & $site_key_v3 != false & (($is_guest_enable != false and !is_user_logged_in()) or $enable_for_registerd_user!=false)){
		
		include ( plugin_dir_path( __FILE__ ) .'src/autoload.php');	
		
		$recaptcha = new \ReCaptcha\ReCaptcha($server_key_v3);
		$gRecaptchaResponse = $_POST['g-recaptcha-response'];
		$remoteIp = $_SERVER['REMOTE_ADDR']; 
		//echo gethostname();exit;
		$resp = $recaptcha->setExpectedAction('social')
						  ->setScoreThreshold( $recaptcha_v3_score )
						  ->verify($gRecaptchaResponse, $remoteIp); 
		if ( ! $resp->isSuccess() ) {
			$asgarosforum->add_notice(__('You must enter the correct captcha.', 'asgaros-forum'));
			return false;
		}

	} else if( $server_key != false & $site_key != false & (($is_guest_enable != false and !is_user_logged_in()) or $enable_for_registerd_user!=false)){
		
		include ( plugin_dir_path( __FILE__ ) .'src/autoload.php');	
		
		$recaptcha = new \ReCaptcha\ReCaptcha($server_key);
		$gRecaptchaResponse = $_POST['g-recaptcha-response'];
		$remoteIp = $_SERVER['REMOTE_ADDR']; 

		$resp = $recaptcha->verify($gRecaptchaResponse, $remoteIp); 
		if (!$resp->isSuccess()) {
			$asgarosforum->add_notice(__('You must enter the correct captcha.', 'asgaros-forum'));
			return false;
		}

	}

	return $status;
}
add_filter('asgarosforum_filter_insert_custom_validation', 'rfaf_validate_recaptcha');