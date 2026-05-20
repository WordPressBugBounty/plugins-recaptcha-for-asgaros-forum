<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
$languages = array(
    'en'    => 'English',
    'fr'    => 'French',
    'fr-CA' => 'French (Canadian)',
    'zh-HK' => 'Chinese (Hong Kong)',
    'zh-CN' => 'Chinese (Simplified)',
    'zh-TW' => 'Chinese (Traditional)',
    'nl'    => 'Dutch',
    'hi'    => 'Hindi',
    'es'    => 'Spanish',
);
$recaptcha_version_2 = "";
$recaptcha_version_3 = "";
$captcha_turnstile = "";
if ( esc_attr( get_option( 'rfaf_captcha_type', 'google-v2' ) ) == "google-v2" ) {
    $recaptcha_version_2 = "checked='checked'";
} else {
    if ( esc_attr( get_option( 'rfaf_captcha_type', 'google-v2' ) ) == "google-v3" ) {
        $recaptcha_version_3 = "checked='checked'";
    } else {
        if ( esc_attr( get_option( 'rfaf_captcha_type', 'google-v2' ) ) == "turnstile" ) {
            $captcha_turnstile = "checked='checked'";
        }
    }
}
?>
<div class="wrap">
	<?php 
if ( isset( $_POST['rfaf_recaptcha_submit'] ) ) {
    ?>
		<div id="message" class="updated notice notice-success is-dismissible">
			<p>Updated</p>
			<button type="button" class="notice-dismiss">
				<span class="screen-reader-text">Dismiss this notice.</span>
			</button>
		</div>
	<?php 
}
?>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder">
			<div class="postbox-container">
				<form method="post">
					<?php 
wp_nonce_field( 'rfaf_recaptcha_submit_nonce' );
?>
					<div class="postbox">
						<h2 class="hndle">Goolge reCAPTCHA</h2>
						<div class="inside">
							<p>
								<a href="https://www.google.com/recaptcha/admin" target="_blank" tabindex="-1">Don't have keys? Get reCAPTCHA Api Keys</a>
							</p>
							<p>
								<label>
									<input type="radio" name="rfaf_captcha_type" value="google-v2" <?php 
echo $recaptcha_version_2;
?>>reCAPTCHA V2
								</label>	
							</p>
							<p>
								<label for="forum_title">Site Key (v2):</label><br>
								<input class="regular-text" type="text" name="rfaf_recaptcha_site_key" value="<?php 
echo esc_attr( get_option( 'rfaf_recaptcha_site_key' ) );
?>" placeholder="**************">
							</p>
							<p>
								<label for="forum_title">Server Key (v2):</label><br>
								<input class="regular-text" type="text" name="rfaf_recaptcha_server_key" value="<?php 
echo esc_attr( get_option( 'rfaf_recaptcha_server_key' ) );
?>" placeholder="**************">
							</p>
							<p>
								<label for="rfaf_recaptcha_language">Language:</label><br>
								<select name="rfaf_recaptcha_language" id="rfaf_recaptcha_language">
									<?php 
foreach ( $languages as $k => $l ) {
    ?>
										<option value="<?php 
    echo $k;
    ?>" <?php 
    echo ( get_option( 'rfaf_recaptcha_language', 'en' ) == $k ? 'selected="selected"' : '' );
    ?>><?php 
    echo $l;
    ?></option>
									<?php 
}
?>
								</select>
							</p>
						</div>
						<hr>
						<div class="inside">
							<p>
								<label>
									<input type="radio" name="rfaf_captcha_type" value="google-v3" <?php 
echo $recaptcha_version_3;
?>>reCAPTCHA V3
								</label>	
							</p>
							<p>
								<label for="forum_title">Site Key (v3):</label><br>
								<input class="regular-text" type="text" name="rfaf_recaptcha_v3_site_key" value="<?php 
echo esc_attr( get_option( 'rfaf_recaptcha_v3_site_key' ) );
?>" placeholder="**************">
							</p>
							<p>
								<label for="forum_title">Server Key (v3):</label><br>
								<input class="regular-text" type="text" name="rfaf_recaptcha_v3_server_key" value="<?php 
echo esc_attr( get_option( 'rfaf_recaptcha_v3_server_key' ) );
?>" placeholder="**************">
							</p>
							<p>
								<label for="forum_title">Score (v3):</label><br>
								<input class="regular-text" type="number" step="0.05" min="0" max="1" name="rfaf_recaptcha_v3_score" value="<?php 
echo esc_attr( get_option( 'rfaf_recaptcha_v3_score', 0.5 ) );
?>">
								<p>reCAPTCHA v3 returns a score (1.0 is very likely a good interaction, 0.0 is very likely a bot).</p>
							</p>
						</div>
							<hr>
							<h2 class="">Cloudflare Turnstile</h2>
							<div class="inside">
								<p>
									<a href="https://www.cloudflare.com/en-in/application-services/products/turnstile/" target="_blank" tabindex="-1">Don't have keys? Get Cloudflare Turnstile Api Keys</a>
								</p>
								<?php 
if ( !rfaf_fs()->is_premium() || rfaf_fs()->is_not_paying() ) {
    ?>
									<p>
										<label>
											<input type="radio" name="rfaf_captcha_type" disabled="disabled" value="turnstile" <?php 
    echo $captcha_turnstile;
    ?>>Turnstile <strong><?php 
    echo sprintf( '<a href="%s"><small>Unlock Pro</small></a>', rfaf_fs()->checkout_url() );
    ?></strong>
										</label>	
									</p>
								<?php 
}
?> 
								<?php 
?>
							</div>
						
						
						<div class="inside">
							<hr>
							<p>
								<input type="checkbox" name="rfaf_recaptcha_registerd_user" id="require_login" value="1" <?php 
checked( esc_attr( get_option( 'rfaf_recaptcha_registerd_user', false ) ) );
?>>
								<label for="require_login">Enable for logged-in users?</label>
							</p>
							<p>
								<input type="submit" name="rfaf_recaptcha_submit" class="button button-primary" value="Save">
							</p>
						</div>

					</div>
					
				</form>
			</div>
		</div>
	</div>
</div>