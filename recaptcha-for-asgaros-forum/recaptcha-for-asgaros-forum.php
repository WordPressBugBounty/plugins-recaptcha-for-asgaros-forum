<?php
/*
Plugin Name: reCAPTCHA for Asgaros Forum
Plugin URI: http://wordpress.org/plugins/recaptcha-for-asgaros-forum/
Description: Protect your Asgaros forum from spam using Googles reCAPTCHA v2 and v3. This plugin prevent bots to spam your forum and has option to enabe reCAPTCHA for guest users & logged-in users.
Author: Hitesh Chandwani
Version: 2.0.1
Author URI: https://hiteshchandwani.com
*/
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( function_exists( 'rfaf_fs' ) ) {
    rfaf_fs()->set_basename( false, __FILE__ );
} else {
    if ( !function_exists( 'rfaf_fs' ) ) {
        // Create a helper function for easy SDK access.
        function rfaf_fs() {
            global $rfaf_fs;
            if ( !isset( $rfaf_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/vendor/freemius/start.php';
                $rfaf_fs = fs_dynamic_init( array(
                    'id'               => '30148',
                    'slug'             => 'recaptcha-for-asgaros-forum',
                    'type'             => 'plugin',
                    'public_key'       => 'pk_dacae7f630e6ebac30b2f15b2176c',
                    'is_premium'       => false,
                    'premium_suffix'   => 'Pro',
                    'has_addons'       => false,
                    'has_paid_plans'   => true,
                    'is_org_compliant' => true,
                    'menu'             => array(
                        'slug'    => 'asgaros-recaptcha',
                        'support' => false,
                        'parent'  => array(
                            'slug' => 'asgarosforum-structure',
                        ),
                    ),
                    'is_live'          => true,
                ) );
            }
            return $rfaf_fs;
        }

        // Init Freemius.
        rfaf_fs();
        // Signal that SDK was initiated.
        do_action( 'rfaf_fs_loaded' );
    }
    define( 'RFAF_PLUGIN_VERSION', '2.0.0' );
    function rfaf_update_options() {
        $installed_version = get_option( 'rfaf_plugin_version', '1.1.1' );
        if ( version_compare( $installed_version, RFAF_PLUGIN_VERSION, '<' ) ) {
            update_option( 'rfaf_plugin_version', RFAF_PLUGIN_VERSION );
            // version 1.x check
            if ( esc_attr( get_option( 'rfaf_recaptcha_version', 'version 2' ) ) == "version 2" ) {
                update_option( 'rfaf_captcha_type', "google-v2" );
                delete_option( 'rfaf_recaptcha_version' );
            } else {
                if ( esc_attr( get_option( 'rfaf_recaptcha_version', 'version 2' ) ) == "version 3" ) {
                    update_option( 'rfaf_captcha_type', "google-v3" );
                    delete_option( 'rfaf_recaptcha_version' );
                }
            }
        }
    }

    // version 1.x check
    add_action( 'plugins_loaded', 'rfaf_update_options' );
    $plugin = plugin_basename( __FILE__ );
    function rfaf_clear_data() {
        delete_option( 'rfaf_captcha_type' );
        delete_option( 'rfaf_recaptcha_site_key' );
        delete_option( 'rfaf_recaptcha_server_key' );
        delete_option( 'rfaf_recaptcha_language' );
        delete_option( 'rfaf_recaptcha_v3_site_key' );
        delete_option( 'rfaf_recaptcha_v3_server_key' );
        delete_option( 'rfaf_recaptcha_v3_score' );
        delete_option( 'rfaf_recaptcha_registerd_user' );
    }

    register_deactivation_hook( __FILE__, 'rfaf_clear_data' );
    function rfaf_add_admin_submenu() {
        add_submenu_page(
            'asgarosforum-structure',
            'Captcha',
            'Captcha',
            'manage_options',
            'asgaros-recaptcha',
            'rfaf_recaptcha_callback'
        );
    }

    add_action( 'asgarosforum_add_admin_submenu_page', 'rfaf_add_admin_submenu' );
    function rfaf_recaptcha_callback() {
        require 'captcha.php';
    }

    function rfaf_save_recaptcha_setting() {
        if ( !isset( $_POST['rfaf_recaptcha_submit'] ) ) {
            return;
        }
        if ( !current_user_can( 'manage_options' ) ) {
            wp_die( 'Unauthorized' );
        }
        check_admin_referer( 'rfaf_recaptcha_submit_nonce' );
        if ( isset( $_POST['rfaf_captcha_type'] ) ) {
            $rfaf_captcha_type = sanitize_text_field( wp_unslash( $_POST['rfaf_captcha_type'] ) );
        } else {
            $rfaf_captcha_type = "google-v2";
        }
        $rfaf_recaptcha_site_key = sanitize_text_field( wp_unslash( $_POST['rfaf_recaptcha_site_key'] ) );
        $rfaf_recaptcha_server_key = sanitize_text_field( wp_unslash( $_POST['rfaf_recaptcha_server_key'] ) );
        $rfaf_recaptcha_language = sanitize_text_field( wp_unslash( $_POST['rfaf_recaptcha_language'] ) );
        $rfaf_recaptcha_v3_site_key = sanitize_text_field( wp_unslash( $_POST['rfaf_recaptcha_v3_site_key'] ) );
        $rfaf_recaptcha_v3_server_key = sanitize_text_field( wp_unslash( $_POST['rfaf_recaptcha_v3_server_key'] ) );
        $rfaf_recaptcha_v3_score = sanitize_text_field( wp_unslash( $_POST['rfaf_recaptcha_v3_score'] ) );
        $rfaf_turnstile_site_key = sanitize_text_field( wp_unslash( $_POST['rfaf_turnstile_site_key'] ) );
        $rfaf_turnstile_server_key = sanitize_text_field( wp_unslash( $_POST['rfaf_turnstile_server_key'] ) );
        $rfaf_recaptcha_registerd_user = 0;
        if ( isset( $_POST['rfaf_recaptcha_registerd_user'] ) ) {
            $rfaf_recaptcha_registerd_user = sanitize_text_field( wp_unslash( $_POST['rfaf_recaptcha_registerd_user'] ) );
        }
        update_option( 'rfaf_captcha_type', $rfaf_captcha_type );
        update_option( 'rfaf_recaptcha_site_key', $rfaf_recaptcha_site_key );
        update_option( 'rfaf_recaptcha_server_key', $rfaf_recaptcha_server_key );
        update_option( 'rfaf_recaptcha_language', $rfaf_recaptcha_language );
        update_option( 'rfaf_recaptcha_v3_site_key', $rfaf_recaptcha_v3_site_key );
        update_option( 'rfaf_recaptcha_v3_server_key', $rfaf_recaptcha_v3_server_key );
        update_option( 'rfaf_recaptcha_v3_score', $rfaf_recaptcha_v3_score );
        update_option( 'rfaf_turnstile_site_key', $rfaf_turnstile_site_key );
        update_option( 'rfaf_turnstile_server_key', $rfaf_turnstile_server_key );
        update_option( 'rfaf_recaptcha_registerd_user', $rfaf_recaptcha_registerd_user );
    }

    add_action( 'wp_loaded', 'rfaf_save_recaptcha_setting' );
    function rfaf_plugin_settings_link(  $links  ) {
        if ( is_plugin_active( 'asgaros-forum/asgaros-forum.php' ) ) {
            $settings_link = '<a href="admin.php?page=asgaros-recaptcha">Enable Captcha</a>';
            array_unshift( $links, $settings_link );
        }
        return $links;
    }

    add_filter( "plugin_action_links_{$plugin}", 'rfaf_plugin_settings_link' );
    function rfaf_bbp_captcha_integrate() {
        global $asgarosforum;
        $is_guest_enable = $asgarosforum->options['allow_guest_postings'];
        $captcha_type = get_option( 'rfaf_captcha_type', 'google-v2' );
        $site_key = get_option( 'rfaf_recaptcha_site_key', false );
        $server_key = get_option( 'rfaf_recaptcha_server_key', false );
        $site_key_v3 = get_option( 'rfaf_recaptcha_v3_site_key', false );
        $server_key_v3 = get_option( 'rfaf_recaptcha_v3_server_key', false );
        $turnstile_site_key = get_option( 'rfaf_turnstile_site_key', false );
        $turnstile_server_key = get_option( 'rfaf_turnstile_server_key', false );
        $enable_for_registerd_user = get_option( 'rfaf_recaptcha_registerd_user', false );
        if ( $captcha_type == "google-v3" && $server_key_v3 != false && $site_key_v3 != false && ($is_guest_enable != false && !is_user_logged_in() or $enable_for_registerd_user != false) ) {
            echo "<input type='hidden' class='g-recaptcha-response' name='g-recaptcha-response'>";
            wp_enqueue_script( 'rfaf-google-reCaptcha', "https://www.google.com/recaptcha/api.js?render=" . rawurlencode( $site_key_v3 ) );
            wp_add_inline_script( 'rfaf-google-reCaptcha', "grecaptcha.ready(function() { grecaptcha.execute('" . wp_json_encode( $site_key_v3 ) . "', {action: 'social'}).then(function(token) { jQuery('.g-recaptcha-response').val(token); }); });" );
        } else {
            if ( $captcha_type == "google-v2" && $server_key != false && $site_key != false && ($is_guest_enable != false && !is_user_logged_in() or $enable_for_registerd_user != false) ) {
                echo '<div class="editor-row editor-row-captcha">';
                echo "<div class='g-recaptcha' data-sitekey='" . esc_attr( $site_key ) . "'></div>";
                echo '</div>';
                $lang = esc_attr( get_option( 'rfaf_recaptcha_language', 'en' ) );
                wp_enqueue_script( 'rfaf-google-reCaptcha', "https://www.google.com/recaptcha/api.js?hl={$lang}" );
            } else {
                if ( $captcha_type == "turnstile" && $turnstile_server_key != false && $turnstile_site_key != false && ($is_guest_enable != false && !is_user_logged_in() or $enable_for_registerd_user != false) ) {
                    echo '<div class="editor-row editor-row-captcha">';
                    echo "<div class='cf-turnstile' data-sitekey='" . esc_attr( $turnstile_site_key ) . "'></div>";
                    echo '</div>';
                    wp_enqueue_script( 'rfaf-turnstile-captcha', "https://challenges.cloudflare.com/turnstile/v0/api.js" );
                }
            }
        }
    }

    add_action( 'asgarosforum_editor_custom_content_bottom', 'rfaf_bbp_captcha_integrate' );
    function rfaf_validate_recaptcha(  $status  ) {
        global $asgarosforum;
        $is_guest_enable = $asgarosforum->options['allow_guest_postings'];
        $captcha_type = get_option( 'rfaf_captcha_type', 'google-v2' );
        $site_key = get_option( 'rfaf_recaptcha_site_key', false );
        $server_key = get_option( 'rfaf_recaptcha_server_key', false );
        $site_key_v3 = get_option( 'rfaf_recaptcha_v3_site_key', false );
        $server_key_v3 = get_option( 'rfaf_recaptcha_v3_server_key', false );
        $recaptcha_v3_score = (float) get_option( 'rfaf_recaptcha_v3_score', 0.5 );
        $turnstile_site_key = get_option( 'rfaf_turnstile_site_key', false );
        $turnstile_server_key = get_option( 'rfaf_turnstile_server_key', false );
        $enable_for_registerd_user = get_option( 'rfaf_recaptcha_registerd_user', false );
        if ( $captcha_type == "google-v3" && $server_key_v3 != false && $site_key_v3 != false && ($is_guest_enable != false && !is_user_logged_in() or $enable_for_registerd_user != false) ) {
            include plugin_dir_path( __FILE__ ) . 'src/autoload.php';
            $recaptcha = new \ReCaptcha\ReCaptcha($server_key_v3);
            $gRecaptchaResponse = ( isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '' );
            $remoteIp = filter_var( wp_unslash( $_SERVER['REMOTE_ADDR'] ), FILTER_VALIDATE_IP );
            $resp = $recaptcha->setExpectedAction( 'social' )->setScoreThreshold( $recaptcha_v3_score )->verify( $gRecaptchaResponse, $remoteIp );
            if ( !$resp->isSuccess() ) {
                $asgarosforum->add_notice( __( 'You must enter the correct captcha.', 'asgaros-forum' ) );
                return false;
            }
        } else {
            if ( $captcha_type == "google-v2" && $server_key != false && $site_key != false && ($is_guest_enable != false && !is_user_logged_in() or $enable_for_registerd_user != false) ) {
                include plugin_dir_path( __FILE__ ) . 'src/autoload.php';
                $recaptcha = new \ReCaptcha\ReCaptcha($server_key);
                $gRecaptchaResponse = ( isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '' );
                $remoteIp = filter_var( wp_unslash( $_SERVER['REMOTE_ADDR'] ), FILTER_VALIDATE_IP );
                $resp = $recaptcha->verify( $gRecaptchaResponse, $remoteIp );
                if ( !$resp->isSuccess() ) {
                    $asgarosforum->add_notice( __( 'You must enter the correct captcha.', 'asgaros-forum' ) );
                    return false;
                }
            } else {
                if ( $captcha_type == "turnstile" && $turnstile_server_key != false && $turnstile_site_key != false && ($is_guest_enable != false && !is_user_logged_in() or $enable_for_registerd_user != false) ) {
                    if ( !rfaf_validate_turnstile() ) {
                        $asgarosforum->add_notice( __( 'You must complete the captcha.', 'asgaros-forum' ) );
                        return false;
                    }
                }
            }
        }
        return $status;
    }

    function rfaf_validate_turnstile() {
        $turnstile_server_key = get_option( 'rfaf_turnstile_server_key', false );
        $token = $_POST['cf-turnstile-response'] ?? '';
        $remoteip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
        $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
        $data = [
            'secret'   => $turnstile_server_key,
            'response' => $token,
        ];
        if ( $remoteip ) {
            $data['remoteip'] = $remoteip;
        }
        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query( $data ),
            ],
        ];
        $context = stream_context_create( $options );
        $response = file_get_contents( $url, false, $context );
        if ( $response === FALSE ) {
            return false;
            //['success' => false, 'error-codes' => ['internal-error']];
        }
        $validation = json_decode( $response, true );
        if ( $validation['success'] ) {
            return true;
        }
        return false;
    }

    add_filter( 'asgarosforum_filter_insert_custom_validation', 'rfaf_validate_recaptcha' );
}
?>