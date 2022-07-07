jQuery.noConflict();
(function($) {

	// FONCTION DE MESSAGE D'ALERT WORDPRESS DYNAMIQUE
	function mapmarker_alert_msg_js(alert, msg){
		/*
		**
		  alert color vert = "success"
		  alert color blue = "info"
		  alert color orange = "warning"
		  alert color red = "error"
		**
		*/
		$(".wrap h2").after('<div class="notice notice-'+alert+' is-dismissible"><p><strong>'+msg+'</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Ne pas tenir compte de ce message.</span></button></div>');
	}

	// FONCTION QUI RECUP L'URL DU FICHIER UPLOAD ET CHANGE L'IMAGE SELECTIONNÉ EN LIVE POUR LA PRÉVISUALISER
	function liveImgUrl(input, select) {

	    if (input.files && input.files[0]) {
	        var reader = new FileReader();

	        reader.onload = function (e) {
	            $(select).siblings("a").children("img").attr("src", e.target.result);
	        }
	        reader.readAsDataURL(input.files[0]);
	    }
	}


	// AGRANDI LES INPUT ET LES TEXTAREA À SONT FOCUS
	$("#table_markers input[type=text], #table_markers textarea").focus(function(){
		$(this).css("width", "250px");
	}).blur(function(){
		$(this).css("width", "");
	});


	// GARDE L'EFFET DE L'INPUT TYPE FILE CAR IL EST EN DISPLAY NONE
	$("tfoot #img_desc_marker_link").click(function(e){
	    e.preventDefault();
	    $("#img_desc_marker:hidden").trigger('click');
	});


	// GARDE L'EFFET DE L'INPUT TYPE FILE CAR IL EST EN DISPLAY NONE
	$("tfoot #img_icon_marker_link").click(function(e){
	    e.preventDefault();
	    $("#img_icon_marker:hidden").trigger('click');
	});


	// GARDE L'EFFET DE L'INPUT TYPE FILE CAR IL EST EN DISPLAY NONE À L'EDITION D'UN MARQUEUR
	$("#table_markers #edit_img_desc_marker_link").each(function() {
		$(this).click(function(e){
			e.preventDefault();
			// Simule le click de l'input file frrère du lien clické
			$(this).siblings("#edit_img_desc_marker:hidden").trigger("click");
		});
	});


	// GARDE L'EFFET DE L'INPUT TYPE FILE CAR IL EST EN DISPLAY NONE À L'EDITION D'UN MARQUEUR
	$("#table_markers #edit_img_icon_marker_link").each(function() {
		$(this).click(function(e){
			e.preventDefault();
			// Simule le click de l'input file frrère du lien clické
			$(this).siblings("#edit_img_icon_marker:hidden").trigger("click");
		});
	});


	// AU CLICK DU BOUTON "AJOUTER UN MARKEUR"
	$("#table_markers #create_marker").click(function(e) {

		// Pour chaque input avec la classe "requiered"
		$("#table_markers tfoot tr td input.requiered, #table_markers tfoot tr td textarea.requiered").each(function() {

			// Si les inputs et textarea sont vide
			if ( $(this).val() == '' ) {
				// Récup. la lanqgue du navigateur
				var lang = $("html").attr('lang');

				// Si la langgue est francaise
				if ( lang == 'fr-FR' || lang == 'fr' || lang == 'FR' ) {
					// Si le message n'est pas affiché
					if ( $(".notice-error").length == 0 ) {
						// Call la fonction des message d'alert
						mapmarker_alert_msg_js('error', 'Veuillez remplir tous les champs obligatoire.');
					}

					// Annule l'effet du boutton
					e.preventDefault();
				}
				else{
					// Si le message n'est pas affiché
					if ( $(".notice-error").length == 0 ) {
						// Call la fonction des message d'alert
						mapmarker_alert_msg_js('error', 'Required fields.');
					}

					// Annule l'effet du boutton
					e.preventDefault();
				}
			}
			// Sinon on valide le formulaire
			else{
				return true
			}
		});// End Each

	});


	// FONCTION ACTION DES BUTTON "EDITER & SUPPRIMER" DE LA TABLE DE GESTION DES MARKEURS
	$( "#table_markers" ).each(function(index) {


		// Au click du bouton "Editer"
		$(this).on("click", "#edit_marker", function() {

			// Récupère l'id clické
			var id = $(this).siblings('#id').val();

			// Change le name et l'id du bouton d'edition
			$(this).attr({name:"valid_edition",id:"valid_edition"});

			// Change l'icon du button et le text
			$(this).html("<i class='fa fa-check' aria-hidden='true'></i> Valider");

			// Selecteur de tous les input/textarea de la row focus et remove le "disable"
			$(this).parent().parent().children("td").children("input, textarea").removeAttr("disabled");

			$(this).parent().parent().children("td").children("input, textarea, a").addClass("edit-active");

			return false;

		});


		// Au changement de l'image de description (upload)
		$("#table_markers #edit_img_desc_marker").change(function() {
			var select = $(this);
			liveImgUrl(this, select);
		});


		// Au changement de l'image du markeur (upload)
		$("#table_markers #edit_img_icon_marker").change(function() {
			var select = $(this);
			liveImgUrl(this, select);
		});

	}); // END FONCTION ACTION DES BUTTON "EDITER & SUPPRIMER"


})(jQuery);