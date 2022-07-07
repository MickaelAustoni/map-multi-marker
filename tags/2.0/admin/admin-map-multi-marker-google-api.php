<?php

global $wpdb;

// Si securite_nonce est posé
if (isset($_POST['securite_nonce'])) {

	// Verification des nonce
	if( wp_verify_nonce($_POST['securite_nonce'], 'securite-nonce') ) {

		// Requete sql d'update
		$wpdb->update($wpdb->prefix.'mapmarker_api', 
		    array(
		    	'apikey' => sanitize_text_field($_POST['apikey']),
		    	'language' => sanitize_text_field($_POST['language'])
		        ),
		    //where
		    array(
		        'id' => '1' 
		        )
		    );

		$update = true;
	} // End if wp_verify_nonce
	else {
		// Le formulaire est refusé et on affiche le message d'erreur
		mapmarker_alert_msg(array('alert' => 'error' ), __('Error in the form.', 'map-multi-marker') );
		exit; 
	}
}


// Affiche le message d'information si necessaire
if ($update) {
    mapmarker_alert_msg(array('alert' => 'success' ), __('Your changes have been saved.', 'map-marker') );
}

?>

<div class="wrap">
	<!-- Affiche le nom du plugin -->
	<h1><?php _e('Google API ', 'map-marker'); echo esc_html($mapmarker_info['plugin_name'])?></h1>
	<form method="POST" action="">
		<input type="hidden" name="securite_nonce" value="<?php echo wp_create_nonce('securite-nonce'); ?>"/>
		<table class="form-table">
			<tr>
				<th scope="row">
					<?php _e('Language', 'map-marker'); ?>
				</th>
				<td>
					<input id="language" type="text" name="language" value="<?php echo esc_html(mapmarkerGetLanguage()) ?>">
					<p class="description" id="tagline-description"><?php _e('(English = en) , (French=fr) , (Russian=ru) , ...','map-marker') ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php _e('Google API key', 'map-marker'); ?>
				</th>
				<td>
					<input id="apikey" type="text" name="apikey" value="<?php echo esc_html(mapmarkerGetApiKey()) ?>">
				</td>
			</tr>
		</table>
		<p class="submit">
		    <button type="submit" name="save_g_api" id="save_g_api" class="button button-primary">
		    	<i class="fa fa-floppy-o" aria-hidden="true"></i> <?php _e('Save', 'map-marker') ?>
		    </button>
		    
		</p>
	</form>
</div>