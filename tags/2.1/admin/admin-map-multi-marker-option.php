<?php

global $wpdb;

//If on save les options
if (isset($_POST['securite_nonce_option'])) {

	// Verification des nonce du formulaire des options
	if( wp_verify_nonce($_POST['securite_nonce_option'], 'securite-nonce') ) {

			//Intit. la variable des champs à afficher
			$fiels_to_display = null;

			//Si au moins 1 champs à afficher est coché
			if(!empty($_POST['fiels_to_display'])){

				// Récup les donné cocher
				foreach ($_POST['fiels_to_display'] as $value) {
					$fiels_to_display .= $value.',';
				}

				//Supprime la dernière virgule
				$fiels_to_display = substr($fiels_to_display, 0, -1);
			}

		    // Requete sql d'update
		    $wpdb->update($wpdb->prefix.'mapmarker_option',
		        array(
			        'map_name' => sanitize_text_field($_POST['map_name']),
		        	'height_map' => sanitize_text_field($_POST['height_map']),
		        	'height_valeur_type' => sanitize_text_field($_POST['height_valeur_type']),
		        	'width_map' => sanitize_text_field($_POST['width_map']),
		        	'width_valeur_type' => sanitize_text_field($_POST['width_valeur_type']),
		            'maptype' => sanitize_text_field($_POST['maptype']),
		            'zoom' => sanitize_text_field($_POST['zoom']),
		            'latitude_initial' => sanitize_text_field($_POST['latitude_initial']),
		            'longitude_initial' => sanitize_text_field($_POST['longitude_initial']),
		            'fiels_to_display' => sanitize_text_field($fiels_to_display)
		            ),
		        //where
		        array(
		            'map_id' => sanitize_text_field($_GET['map_id'])
		            )
		        );

			//Affiche le message d'alert
		    mapmarker_alert_msg('success', __('Your options have been saved.', 'map-multi-marker'));

	} // End if wp_verify_nonce
	else {
		// Le formulaire est refusé et on affiche le message d'erreur
		mapmarker_alert_msg('error', __('Error in the form.', 'map-multi-marker'));
		exit;
	}

}// End if save les options


//Req. pour récupérer les options de la carte
$data = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . 'mapmarker_option WHERE map_id="'.$_GET['map_id'].'"', ARRAY_A);


?>
<div class="wrap">
	<h2><?php _e('Map option', 'map-multi-marker'); ?></h2>
	<form method="POST" action="">
		<input type="hidden" name="securite_nonce_option" value="<?php echo wp_create_nonce('securite-nonce'); ?>"/>
	    <input type="hidden" name="id" value="<?php echo $data[0]['id']?>">
	    <table class="form-table">
	    	<tr>
	    		<th scope="row">
	    			<?php _e('Map name', 'map-multi-marker'); ?>
	    		</th>
	    		<td>
	    			<input id="map_name" type="text" name="map_name" maxlength="255" value="<?php echo esc_html($data[0]['map_name'])?>">
	    		</td>
	    	</tr>
	    	<tr>
	    		<th scope="row">
	    			<?php _e('Height', 'map-multi-marker'); ?>
	    		</th>
	    		<td>
	    			<input id="height_map" type="text" name="height_map" maxlength="11" value="<?php echo esc_html($data[0]['height_map'])?>">
	    			<select name="height_valeur_type">
	    				<option value="px" <?php if($data[0]['height_valeur_type'] == "px") { echo "selected";} ?>>px</option>
	    				<option value="%" <?php if($data[0]['height_valeur_type'] == "%") { echo "selected";} ?>>%</option>
	    			</select>
	    		</td>
	    	</tr>
	    	<tr>
	    		<th scope="row">
	    			<?php _e('Width', 'map-multi-marker'); ?>
	    		</th>
	    		<td>
	    			<input id="width_map" type="text" name="width_map" maxlength="11" value="<?php echo esc_html($data[0]['width_map'])?>">
	    			<select name="width_valeur_type">
	    				<option value="px" <?php if($data[0]['width_valeur_type'] == "px") { echo "selected";} ?>>px</option>
	    				<option value="%" <?php if($data[0]['width_valeur_type'] == "%") { echo "selected";} ?>>%</option>
	    			</select>
	    		</td>
	    	</tr>
	        <tr>
	            <th scope="row">
	                <label for="maptype"><?php _e('Map type', 'map-multi-marker'); ?></label></th>
	            <td>
	                <select name="maptype" id="maptype">
	                    <option value="TERRAIN" <?php if($data[0]['maptype'] == "TERRAIN") { echo "selected";} ?>>TERRAIN</option>
	                    <option value="ROADMAP" <?php if($data[0]['maptype'] == "ROADMAP") { echo "selected";} ?>>ROADMAP</option>
	                    <option value="SATELLITE" <?php if($data[0]['maptype'] == "SATELLITE") { echo "selected";} ?>>SATELLITE</option>
	                    <option value="HYBRID" <?php if($data[0]['maptype'] == "HYBRID") { echo "selected";} ?>>HYBRID</option>
	                </select>
	            </td>
	        </tr>
	        <tr>
	            <th scope="row">
	                <label for="zoom"><?php _e('Zoom', 'map-multi-marker'); ?></label></th>
	            <td>
	                <input id="zoom" type="text" name="zoom" maxlength="2" value="<?php echo esc_html($data[0]['zoom'])?>">
	            </td>
	        </tr>
	        <tr>
	            <th scope="row">
	                <label for="latitude_initial"><?php _e('Initial latitude', 'map-multi-marker'); ?></label></th>
	            <td>
	                <input id="latitude_initial" type="text" name="latitude_initial" value="<?php echo esc_html($data[0]['latitude_initial'])?>">
	            </td>
	        </tr>
	        <tr>
	            <th scope="row">
	                <label for="longitude_initial"><?php _e('Initial longitude', 'map-multi-marker'); ?></label></th>
	            <td>
	                <input id="longitude_initial" type="text" name="longitude_initial" value="<?php echo esc_html($data[0]['longitude_initial'])?>">
	            </td>
	        </tr>
	        <tr>
	            <th scope="row">
	                <label for="fiels_to_display"><?php _e('Fields to display', 'map-multi-marker'); ?></label></th>
	            <td>
	            	<input id="fiels_to_display" type="checkbox" name="fiels_to_display[]" value="image" <?php mapmarker_check_checked($data[0]['fiels_to_display'], 'image')?>><?php _e('Image', 'map-multi-marker'); ?><br>
	                <input id="fiels_to_display" type="checkbox" name="fiels_to_display[]" value="titre" <?php mapmarker_check_checked($data[0]['fiels_to_display'], 'titre')?>><?php _e('Title', 'map-multi-marker'); ?><br>
	                <input id="fiels_to_display" type="checkbox" name="fiels_to_display[]" value="description" <?php mapmarker_check_checked($data[0]['fiels_to_display'], 'description')?>><?php _e('Description', 'map-multi-marker'); ?><br>
	                <input id="fiels_to_display" type="checkbox" name="fiels_to_display[]" value="adresse" <?php mapmarker_check_checked($data[0]['fiels_to_display'], 'adresse')?>><?php _e('Address', 'map-multi-marker'); ?><br>
					<input id="fiels_to_display" type="checkbox" name="fiels_to_display[]" value="telephone" <?php mapmarker_check_checked($data[0]['fiels_to_display'], 'telephone')?>><?php _e('Phone', 'map-multi-marker'); ?><br>
					<input id="fiels_to_display" type="checkbox" name="fiels_to_display[]" value="weblink" <?php mapmarker_check_checked($data[0]['fiels_to_display'], 'weblink')?>><?php _e('Web link', 'map-multi-marker'); ?>
	            </td>
	        </tr>
	    </table>

	    <p class="submit">
	        <button type="submit" name="save_option" id="save_option" class="button button-primary">
	        	<i class="fa fa-floppy-o" aria-hidden="true"></i> <?php _e('Save options', 'map-multi-marker') ?>
	        </button>
	    </p>
	</form>
</div>