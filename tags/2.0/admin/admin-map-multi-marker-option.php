<?php

global $wpdb;

// Si securite_nonce est posté
if (isset($_POST['securite_nonce'])) {

	// Verification des nonce
	if( wp_verify_nonce($_POST['securite_nonce'], 'securite-nonce') ) {
	
			// Stock l'array "fiels_to_display" dans la variable $fiels_to_display
			$fiels = $_POST['fiels_to_display'];

			// Récup les donné cocher
			foreach ($fiels as $value) {
				$fiels_to_display .= $value.',';
			}

			//Supprime la dernière virgule
			$fiels_to_display = substr($fiels_to_display, 0, -1);


		    // Requete sql d'update 
		    $wpdb->update($wpdb->prefix.'mapmarker_option', 
		        array(
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
		            'id' => sanitize_text_field($_POST['id']) 
		            )
		        );
		    
		    // Déclare la valeur "True" pour afficher le message d'info à la supression
		    $update = true;
	
	} // End if wp_verify_nonce 
	else {
		// Le formulaire est refusé et on affiche le message d'erreur
		mapmarker_alert_msg(array('alert' => 'error' ), __('Error in the form.', 'map-multi-marker') );
		exit; 
	}

} // End securite_nonce est posté

// Requete SQL et stock dans un tableau associatif avec "ARRAY_A"
$data = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . 'mapmarker_option', ARRAY_A);

?>
<div class="wrap">
	<h2><?php _e('Map option', 'map-multi-marker'); ?></h2>
	<?php
	// Affiche le message d'information si necessaire
	if ($update) {
	    mapmarker_alert_msg(array('alert' => 'success' ), __('Your changes have been saved.', 'map-multi-marker') );
	}
	?>
	<form method="POST" action="">
		<input type="hidden" name="securite_nonce" value="<?php echo wp_create_nonce('securite-nonce'); ?>"/>
	    <input type="hidden" name="id" value="<?php echo $data[0]['id']?>">
	    <table class="form-table">
	    	<tr>
	    		<th scope="row">
	    			<?php _e('Height', 'map-multi-marker'); ?>
	    		</th>
	    		<td>
	    			<input id="height_map" type="text" name="height_map" value="<?php echo esc_html($data[0]['height_map'])?>">
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
	    			<input id="width_map" type="text" name="width_map" value="<?php echo esc_html($data[0]['width_map'])?>">
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
	                <input id="zoom" type="text" name="zoom" value="<?php echo esc_html($data[0]['zoom'])?>">
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
	        	<i class="fa fa-floppy-o" aria-hidden="true"></i> <?php _e('Save', 'map-multi-marker') ?>
	        </button>
	        
	    </p>
	</form>
</div>