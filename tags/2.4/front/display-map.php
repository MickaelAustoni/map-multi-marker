<?php

// FUNCTION DU FRONT END
function mapmarkerDisplayMap($atts){
	// Global du plugin pour recup le shortcode
	global $mapmarker_info;

	// Enclenche la temporisation de sortie
	ob_start();

	// SI le shortcode existe dans la page en cours
	if ( shortcode_exists( $mapmarker_info['shortcode'] ) ) {

		$shorcode_attribut = shortcode_atts( array('id' => '1'), $atts );

		// Charge les script du font front
		mapmarker_front_script($shorcode_attribut);

		?>
		<!-- Div pour afficher la map -->
		<div id="map" class="mapmarker" <?php mapmarker_get_options($shorcode_attribut['id'], 'size_map'); ?>></div>

		<!-- Script Google Map -->
	    <script type="text/javascript">

			//FONCTION QUI INITIALISE LA MAP
			function initMap() {

				// Récup. les options de la map grace à la fonction php
				var mapOptions = <?php mapmarker_get_options($shorcode_attribut['id'], 'base_map'); ?>;

				// Affecte les option à la map
				map = new google.maps.Map(document.getElementById('map'), mapOptions);

				// Init. la fenetre d'info
				infoWindow = new google.maps.InfoWindow();

				// Event qui ferme l'info bulle au click
				google.maps.event.addListener(map, 'click', function() {
					infoWindow.close();
				});

				//Callback les markers
				get_Markers();

			} // End initMap()


			// FUNCTION QUI RÉCUP TOUS LES MARKERS
			function get_Markers() {
				// Déclare l'array des markers
				var marker_array = <?php mapmarker_get_makers($shorcode_attribut['id']); ?>;

				// Boucle les markers
				for (var i = 0; i < marker_array.length; i++) {

					// Assigne la position du markeur
					var latlng = new google.maps.LatLng(marker_array[i].lat, marker_array[i].lng);

					// Récup l"icon du marqueur (une url)
					var icon = marker_array[i].icon;

					// Récup l"id du marker
					var id = marker_array[i].id;

					// Callback de la fonction "set_Markers()" pour crée les marqueur en envoyant "id" et la "latlng"
					set_Markers(id, latlng, icon);
			  	}
			} // End "get_Markers()"


			// FUNCTION QUI RÉCUP LE CONTENU DES INFOBULLE EN AJAX
			function get_ajax_content_markeur(id){
				jQuery.noConflict();
				(function($) {
				    $.ajax({
				    	method: "POST",
				    	url: "<?php echo admin_url('admin-ajax.php')?>",
				    	data: {
				    			action: "mapmarkerMarkerContent",
				    			id : id,
				    			shorcode_attribut : <?php echo $shorcode_attribut['id'] ?>
				    	},

						beforeSend: function() {
							//Crée le loader
							$("#infobulle").append('<div class="loader-marker-c" style="padding: 30px 20px 30px 40px;text-align:center;"><i class="fa fa-refresh fa-spin fa-3x fa-fw" style="color:#C7C7C7;"></i><span class="sr-only"></span></div>');
						},

						success: function(data){
						  	//Calclul la hauteur du loader
							h = $('.loader-marker-c').height();

							//Ajoute un min-height de l'infobulle de la taille du loader pour un rendu optimal
							$('#infobulle').css('min-height', h);

							//Remove le loader
							$('.loader-marker-c').remove();

							// Add les donné et crée un content
							$('#infobulle').append('<div class="content-makrer" style="opacity:0;">'+data+'</div>');

							// Ajoute une opacity à "1" en animate
							$( ".content-makrer" ).animate({opacity:1});

						}
				    });// End .ajax

				})(jQuery);
			} // End "get_ajax_content_markeur()"


			// FUNCTION QUI AFFECTE TOUS LES MARKERS
			function set_Markers(id, latlng, icon) {

				// Affecte la position de chacque marker
				var marker = new google.maps.Marker({
				  position: latlng,
				  map: map,
				  icon: icon
				});

				// Au click du markeur on récup son contenu en AJAX
				google.maps.event.addListener(marker, 'click', function() {

					// Add. une div dans l'infobulle
					infoWindow.setContent('<div id="infobulle"></div>');

					// Ouvre l'info bulle avec les donnés du markeur
					infoWindow.open(map, marker);

					// Call la fonction pour récupèrer le contenu en AJAX du markeur clické
					get_ajax_content_markeur(id);

				});

			} // End setMarkers()

	    </script>
		<?php

	}// End if shortcode_exists

	// End output buffering
	$output = ob_get_contents();

	// Grab the buffer contents and empty the buffer
	ob_end_clean();

	return $output;
}// End "mapmarkerDisplayMap()"

// Add. le shortcode
add_shortcode($mapmarker_info['shortcode'], 'mapmarkerDisplayMap');