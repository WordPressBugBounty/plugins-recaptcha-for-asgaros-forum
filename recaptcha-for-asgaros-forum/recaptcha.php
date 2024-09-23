<?php 
	$languages = array( 'en' => 'English', 'fr'=> 'French', 'fr-CA' => 'French (Canadian)', 'zh-HK' => 'Chinese (Hong Kong)', 'zh-CN' => 'Chinese (Simplified)', 'zh-TW' => 'Chinese (Traditional)', 'nl' => 'Dutch', 'hi' => 'Hindi', 'es' => 'Spanish' );
	$recaptcha_version_2 = "";
	$recaptcha_version_3 = "";

	if ( esc_attr( get_option( 'rfaf_recaptcha_version',  'version 2') )  == "version 2" ) {
		$recaptcha_version_2 = "checked='checked'";
	} else if ( esc_attr( get_option( 'rfaf_recaptcha_version',  'version 2') ) == "version 3" ) { 
		$recaptcha_version_3 = "checked='checked'";
	}

?>
<div class="wrap">
	<?php if(isset($_REQUEST['nonce_error'])):?>
		<div id="message" class="error notice notice-success is-dismissible">
			<p>Something went wrong!</p>
			<button type="button" class="notice-dismiss">
				<span class="screen-reader-text">Dismiss this notice.</span>
			</button>
		</div>
	<?php elseif(isset($_POST['rfaf_recaptcha_submit'])):?>
		<div id="message" class="updated notice notice-success is-dismissible">
			<p>Updated</p>
			<button type="button" class="notice-dismiss">
				<span class="screen-reader-text">Dismiss this notice.</span>
			</button>
		</div>
	<?php endif;?>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder">
			<div class="postbox-container">
				<form method="post">
					<?php wp_nonce_field( 'rfaf_recaptcha_submit_nonce');?>
					<div class="postbox">
						<h2 class="hndle">Goolge reCAPTCHA</h2>
						<div class="inside">
							<p>
								<a href="https://www.google.com/recaptcha/admin" target="_blank" tabindex="-1">Don't have keys? Get reCAPTCHA Api Keys</a>
							</p>
							<p>
								<label>
									<input type="radio" name="rfaf_recaptcha_version" value="version 2" <?php echo $recaptcha_version_2; ?>>reCAPTCHA V2
								</label>	
							</p>
							<p>
								<label for="forum_title">Site Key (v2):</label><br>
								<input class="regular-text" type="text" name="rfaf_recaptcha_site_key" value="<?php echo esc_attr( get_option( 'rfaf_recaptcha_site_key' ) ); ?>" placeholder="**************">
							</p>
							<p>
								<label for="forum_title">Server Key (v2):</label><br>
								<input class="regular-text" type="text" name="rfaf_recaptcha_server_key" value="<?php echo esc_attr( get_option('rfaf_recaptcha_server_key'));?>" placeholder="**************">
							</p>
							<p>
								<label for="rfaf_recaptcha_language">Language:</label><br>
								<select name="rfaf_recaptcha_language" id="rfaf_recaptcha_language">
									<?php foreach( $languages as $k => $l): ?>
										<option value="<?php echo $k;?>" <?php echo ( get_option( 'rfaf_recaptcha_language', 'en' ) == $k ? 'selected="selected"' : '' ); ?>><?php echo $l; ?></option>
									<?php endforeach; ?>
								</select>
							</p>

							<hr>
							<p>
								<label>
									<input type="radio" name="rfaf_recaptcha_version" value="version 3" <?php echo $recaptcha_version_3; ?>>reCAPTCHA V3
								</label>	
							</p>
							<p>
								<label for="forum_title">Site Key (v3):</label><br>
								<input class="regular-text" type="text" name="rfaf_recaptcha_v3_site_key" value="<?php echo esc_attr(get_option('rfaf_recaptcha_v3_site_key'));?>" placeholder="**************">
							</p>
							<p>
								<label for="forum_title">Server Key (v3):</label><br>
								<input class="regular-text" type="text" name="rfaf_recaptcha_v3_server_key" value="<?php echo esc_attr(get_option('rfaf_recaptcha_v3_server_key'));?>" placeholder="**************">
							</p>
							<p>
								<label for="forum_title">Score (v3):</label><br>
								<input class="regular-text" type="number" step="0.05" min="0" max="1" name="rfaf_recaptcha_v3_score" value="<?php echo esc_attr(get_option('rfaf_recaptcha_v3_score', 0.5)); ?>">
								<p>reCAPTCHA v3 returns a score (1.0 is very likely a good interaction, 0.0 is very likely a bot).</p>
							</p>
							<hr>
							<p>
								<input type="checkbox" name="rfaf_recaptcha_registerd_user" id="require_login" value="1" <?php checked(esc_attr(get_option('rfaf_recaptcha_registerd_user',false)));?>>
								<label for="require_login">Enable for logged-in users?</label>
							</p>
							<p>
								<hr>
								<input type="submit" name="rfaf_recaptcha_submit" class="button button-primary" value="Save">
							</p>
						</div>
					</div>
					
				</form>
			</div>
		</div>
	</div>
</div>