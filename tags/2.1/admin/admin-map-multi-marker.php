<?php

global $wpdb;

// Déclare la table
$table_name = $wpdb->prefix.'mapmarker_marker';

// Si securite_nonce est posté
if ( isset($_POST['securite_nonce_manage_marker']) OR isset($_POST['securite_nonce_delete_marker']) ) {

	//Si la varible "securite_nonce_manage_marker" n'existe pas, alors on l'init.
	if(!isset($_POST['securite_nonce_manage_marker'])){
		$_POST['securite_nonce_manage_marker'] = null;
	}

	//Si la varible "securite_nonce_delete_marker" n'existe pas, alors on l'init.
	if(!isset($_POST['securite_nonce_delete_marker'])){
		$_POST['securite_nonce_delete_marker'] = null;
	}

	// Verification des nonce
	if( wp_verify_nonce($_POST['securite_nonce_manage_marker'], 'securite-nonce') OR wp_verify_nonce($_POST['securite_nonce_delete_marker'], 'securite-nonce') ) {

		// Si on crée un marker
		if ( isset($_POST['create_marker']) ) {
			mapMarkerCreateMarker();
		}

		// Si on supprime un marker
		if ( isset($_POST['submit_delete']) ) {

		    // Supprime les antislashe pour eviter les bugs
		    $_POST = stripslashes_deep($_POST);

		    // Requete de supp.
		    $wpdb->delete( $table_name, array('ID' => sanitize_text_field( $_POST['id_delete_marker']) ), $where_format = null );

			//Affiche le message d'alert
		    mapmarker_alert_msg('success', __('Your marker been deleted.', 'map-multi-marker'));

		}


		// Si valid_edition est posté
		if ( isset($_POST['valid_edition']) ) {
		    // Supprime les antislashe pour eviter les bugs
		    $_POST = stripslashes_deep($_POST);

		    // Load "file.php" pour la fonction "wp_handle_upload()"
		    if ( ! function_exists( 'wp_handle_upload' ) ) {
		        require_once( ABSPATH . 'wp-admin/includes/file.php' );
		    }

		    // Fonction "wp_handle_upload()" de wordpress pour upload l'image dans un array et ses proprièté
		    $img_desc = wp_handle_upload( $_FILES['edit_img_desc_marker'], array('test_form' => false) );

		    // Fonction "wp_handle_upload()" de wordpress pour upload l'image dans un array et ses proprièté
		    $img_icon = wp_handle_upload( $_FILES['edit_img_icon_marker'], array('test_form' => false) );

		    // Si une image de description a été upload
		    if ( $img_desc && !isset( $img_desc['error'] ) ) {
		    	// Requete sql d'update
		    	$wpdb->update($table_name,
		    	    array(
		    	        'img_desc_marker' => sanitize_text_field($img_desc['url'])
		    	        ),
		    	    array(
		    	        'id' => sanitize_text_field($_POST['id'])
		    	        )
		    	    );
		    }

		    // Si un icon de marqueur a été upload
		    if ( $img_icon && !isset( $img_icon['error'] ) ) {
		    	// Requete sql d'update
		    	$wpdb->update($table_name,
		    	    array(
		    	        'img_icon_marker' => sanitize_text_field($img_icon['url'])
		    	        ),
		    	    array(
		    	        'id' => sanitize_text_field($_POST['id'])
		    	        )
		    	    );
		    }

			//Intit. les variable si elle existe pas
			if(!isset($_POST['titre'])){
				$_POST['titre'] = null;
			}
			if(!isset($_POST['description'])){
				$_POST['description'] = null;
			}
			if(!isset($_POST['adresse'])){
				$_POST['adresse'] = null;
			}
			if(!isset($_POST['telephone'])){
				$_POST['telephone'] = null;
			}
			if(!isset($_POST['weblink'])){
				$_POST['weblink'] = null;
			}
			if(!isset($_POST['weblink'])){
				$_POST['weblink'] = null;
			}

		    // Requete sql d'update
		    $wpdb->update($table_name,
		        array(
			        'marker_id' => sanitize_text_field($_GET['map_id']),
		            'titre' => sanitize_text_field($_POST['titre']),
		            'description' => sanitize_text_field($_POST['description']),
		            'adresse' => sanitize_text_field($_POST['adresse']),
		            'telephone' => sanitize_text_field($_POST['telephone']),
		            'weblink' =>  sanitize_text_field($_POST['weblink']),
		            'latitude' => sanitize_text_field($_POST['latitude']),
		            'longitude' => sanitize_text_field($_POST['longitude'])
		            ),
		        array(
		            'id' => sanitize_text_field($_POST['id'])
		            )
		        );

			//Affiche le message d'alert
		    mapmarker_alert_msg('success', __('Your marker been edited.', 'map-multi-marker'));

		} // End valid_edition

	} // End wp_verify_nonce

	else {
		// Le formulaire est refusé et on affiche le message d'erreur
		mapmarker_alert_msg('error', __('Error in the form.', 'map-multi-marker'));
		exit;
	}

} // End if securite_nonce est posté

// Requete SQL et stock dans un tableau associatif avec "ARRAY_A" pour récupe les champs selectionné dans les option
$get_field = $wpdb->get_results( "SELECT fiels_to_display FROM " . $wpdb->prefix . 'mapmarker_option', ARRAY_A);

// Requete SQL et stock dans un tableau associatif avec "ARRAY_A"
$data = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . 'mapmarker_marker WHERE marker_id="'.$_GET['map_id'].'" ORDER BY id ASC', ARRAY_A);


?>
<div class="wrap">
	<h2 id="title-manage-marker"><?php _e('Management marker', 'map-multi-marker') ?></h2>
	<p></p>
	<form method="POST" action="" enctype="multipart/form-data">
		<input type="hidden" name="securite_nonce_manage_marker" value="<?php echo wp_create_nonce('securite-nonce'); ?>"/>
	    <table class="widefat" id="table_markers">
	        <thead>
				<tr>
				   <th style="text-align: center"><strong><?php _e('Marker', 'map-multi-marker') ?></strong></th>

				   <?php
				   	if (mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'image') == true) {
				   		?>
				   		<th style="text-align: center"><strong><?php _e('Image', 'map-multi-marker') ?></strong></th>
				   		<?php
				   	}
				   ?>
				   <?php
				   	if (mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'titre') == true) {
				   		?>
				   		<th><strong><?php _e('Title', 'map-multi-marker') ?></strong></th>
				   		<?php
				   	}
				   ?>
				   <?php
				   	if (mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'description') == true) {
				   		?>
				   		<th><strong><?php _e('Description', 'map-multi-marker') ?></strong></th>
				   		<?php
				   	}
				   ?>
				   <?php
				   	if (mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'adresse') == true) {
				   		?>
				   		<th><strong><?php _e('Address', 'map-multi-marker') ?></strong></th>
				   		<?php
				   	}
				   ?>
				   <?php
				   	if (mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'telephone') == true) {
				   		?>
				   		<th><strong><?php _e('Phone', 'map-multi-marker') ?></strong></th>
				   		<?php
				   	}
				   ?>
				   <?php
				   	if (mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'weblink') == true) {
				   		?>
				   		<th><strong><?php _e('Web link', 'map-multi-marker') ?></strong></th>
				   		<?php
				   	}
				   ?>
				   <th><strong><?php _e('Latitude', 'map-multi-marker') ?>*</strong></th>
				   <th><strong><?php _e('Longitude', 'map-multi-marker') ?>*</strong></th>
				   <th><strong><?php _e('Action', 'map-multi-marker') ?></strong></th>
				</tr>
	        </thead>
	        <tbody>
	        <?php

	        // Boucle les datas
	        foreach ($data as $item){
	            ?>
	            <tr>
	            	<td style="text-align: center">
	            		<input type="file" name="edit_img_icon_marker" id="edit_img_icon_marker" accept="image/*" disabled="">
	            		<a href="#" id="edit_img_icon_marker_link"><img src="<?php echo esc_url($item['img_icon_marker'])?>" alt=""></a>
	            	</td>
	            	<?php
	            		if (mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'image') == true) {
	            			?>
	            			<td style="text-align: center">
	            				<input type="file" name="edit_img_desc_marker" id="edit_img_desc_marker" accept="image/*" disabled="">
	            				<a href="#" id="edit_img_desc_marker_link"><img src="<?php echo esc_url($item['img_desc_marker'])?>" alt=""></a>
	            			</td>
	            			<?php
	            		}
	            	?>
	                <?php
	                	if (mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'titre') == true) {
	                		?>
	                		<td>
	                			<input maxlength="50" type="text" value="<?php echo esc_html($item['titre'])?>" name="titre" disabled>
	                		</td>
	                		<?php
	                	}
	                ?>
	                <?php
	                	if (mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'description') == true) {
	                		?>
	                		<td>
	                			<textarea maxlength="255" name="description" disabled><?php echo esc_textarea($item['description'])?></textarea>
	                		</td>
	                		<?php
	                	}
	                ?>
	                <?php
	                	if (mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'adresse') == true) {
	                		?>
	                		<td>
	                			<textarea maxlength="255" name="adresse" disabled><?php echo esc_textarea($item['adresse'])?></textarea>
	                		</td>
	                		<?php
	                	}
	                ?>
	                <?php
	                	if (mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'telephone') == true) {
	                		?>
	                		<td>
	                			<input maxlength="12" type="text" value="<?php echo esc_html($item['telephone'])?>" name="telephone" disabled>
	                		</td>
	                		<?php
	                	}
	                ?>
	                <?php
	                	if (mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'weblink') == true) {
	                		?>
	                		<td>
	                			<input maxlength="255" type="text" value="<?php echo esc_url($item['weblink'])?>" name="weblink" disabled>
	                		</td>
	                		<?php
	                	}
	                ?>
	                <td>
	                    <input maxlength="10" type="text" value="<?php echo esc_html($item['latitude'])?>" name="latitude" disabled>
	                </td>
	                <td>
	                	<input maxlength="11" type="text" value="<?php echo esc_html($item['longitude'])?>" name="longitude" disabled>
	                </td>
	                <td class="action">
		                <input type="hidden" name="id" id="id" value="<?php echo $item['id']?>" disabled>
		                <button type='submit' class='button-primary' name="edit_marker" id="edit_marker"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> <?php _e('Edit', 'map-multi-marker'); ?></button>
		                <button type='submit' class='button-secondary' name="delete_marker" id="delete_marker"><i class="fa fa-times" aria-hidden="true"></i> <?php _e('Delete', 'map-multi-marker'); ?></button>
	                </td>
	            </tr>
	            <?php
	        }// End Boucle

	       ?>
	      </tbody>
	      <tfoot>
	       <tr>
	       	    <td style="text-align: center">
	       	     <input type="file" name="img_icon_marker" id="img_icon_marker" accept="image/*">
	       	   	 <a href="#" id="img_icon_marker_link" class="button-primary"><i class="fa fa-map-marker" aria-hidden="true"></i> <?php _e('Marker', 'map-multi-marker'); ?></a>
	       	    </td>
	       	   <?php
	       	   	if (mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'image') == true) {
	       	   		?>
	       	   		 <td style="text-align: center">
	       	   		 	<input type="file" name="img_desc_marker" id="img_desc_marker" accept="image/*">
	       	   			<a href="#" id="img_desc_marker_link" class="button-primary"><i class="fa fa-picture-o" aria-hidden="true"></i> <?php _e('Image', 'map-multi-marker'); ?></a>
	       	   		 </td>
	       	   		<?php
	       	   	}
	       	   ?>
	           <?php
	           	if (mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'titre') == true) {
	           		?>
	           		<td><input maxlength="50" type="text" name="add_titre" placeholder="<?php _e('Title', 'map-multi-marker') ?>"></td>
	           		<?php
	           	}
	           ?>
	           <?php
	           	if (mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'description') == true) {
	           		?>
	           		<td><textarea maxlength="255" style="width:100%" id="add_description" name="add_description" placeholder="<?php echo _e('Description') ?>"></textarea></td>
	           		<?php
	           	}
	           ?>
	           <?php
	           	if (mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'adresse') == true) {
	           		?>
	           		<td><textarea maxlength="255" name="add_adresse" placeholder="<?php _e('Address', 'map-multi-marker') ?>"></textarea></td>
	           		<?php
	           	}
	           ?>
	           <?php
	           	if (mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'telephone') == true) {
	           		?>
	           		<td><input maxlength="12" type="text" name="add_telephone" placeholder="<?php _e('Phone', 'map-multi-marker') ?>"></td>
	           		<?php
	           	}
	           ?>
	           <?php
	           	if (mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'weblink') == true) {
	           		?>
	           		<td><input maxlength="255" type="text" name="add_weblink" placeholder="<?php _e('Web link', 'map-multi-marker') ?>"></td>
	           		<?php
	           	}
	           ?>
	           <td><input maxlength="10" type="text" name="add_latitude" class="requiered" placeholder="<?php _e('Latitude', 'map-multi-marker') ?>"></td>
	           <td><input maxlength="11" type="text" name="add_longitude" class="requiered" placeholder="<?php _e('Longitude', 'map-multi-marker') ?>"></td>
	           <td>
	               <button type='submit' class='button-primary' name="create_marker" id="create_marker"><i class="fa fa-plus" aria-hidden="true"></i> <?php _e('Add', 'map-multi-marker'); ?></button>
	           </td>
	       </tr>
	      </tfoot>
	    </table>
	</form>
</div>

<script type="text/javascript">

jQuery.noConflict();

(function($) {

	//CALL AJAX DU DU BOUTTON SUPPRIMÉ
	$( "#table_markers" ).each(function(index) {

		// Au click du bouton "Supprimer"
		$("#table_markers #delete_marker").click(function() {
			// Récupère l'id clické
			var id = $(this).siblings("#id").val();

			// FONCTION AJAX
			$.ajax({
			  method: "POST",
			  url: "<?php echo admin_url('admin-ajax.php')?>",
			  dataType: "html",
			  data:{
			  	action:"mapMarkerModalDelete",
			  	id : id,
			  	modal : 'delete_marker'
			  },

			  success: function(data){
			  	$('body').prepend(data);
			  }
			});//End AJAX

			return false;
		});

	}); //END CALL AJAX DU DU BOUTTON SUPPRIMÉ

})(jQuery);

</script>