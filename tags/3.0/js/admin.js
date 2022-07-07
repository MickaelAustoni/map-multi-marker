jQuery.noConflict();
(function ($) {

    var $map_name = $("#map_name");
    var $table_markers = $("#table_markers");

    /**
     * Duplicate name map in header on detail page
     */
    $("#map-name-detail").html($map_name.val());


    /**
     * On key up map name
     */
    $map_name.keyup(function () {
        $("#map-name-detail").html($(this).val());
    });


    /*
     ** AGRANDI LES INPUT ET LES TEXTAREA À SONT FOCUS
     */
    $("#table_markers input[type=text], #table_markers textarea").focus(function () {
        $(this).css("width", "250px");
    }).blur(function () {
        $(this).css("width", "");
    });


    /*
     ** AU CLICK DU BOUTON "AJOUTER UN MARKEUR"
     */
    $table_markers.on('click', '#create_marker', function (e) {

        // Pour chaque input avec la classe "requiered"
        $(".requiered").each(function () {

            // Si un input ".requiered" n'est pas vide alors on supprime la class qui affiche les bordure d'erreur
            if (!$(this).value) {
                $(this).removeClass("requiered-error");
            }

            // Si les inputs et textarea sont vide
            if ($(this).val() === '') {

                //Add la class 'requiered-error' pour la border rouge de l'input
                $(this).addClass('requiered-error');


                // Call la fonction des message d'alert
                mapmarker_alert_msg_js('error', _e.mssg_error_required);
                // Annule l'effet du boutton
                e.preventDefault();

            }
            // Sinon on valide le formulaire
            else {
                return true;
            }
        });// End Each

    });


    /*
     ** ACTION DES BUTTON "EDITER & SUPPRIMER" DE LA TABLE DE GESTION DES MARKEURS
     */
    $table_markers.each(function (index) {

        // Au click du bouton "Editer"
        $(this).on("click", "#edit_marker", function () {

            // Récupère l'id clické
            var id = $(this).siblings('#id').val();

            // Change le name et l'id du bouton d'edition
            $(this).attr({name: "valid_edition", id: "valid_edition", class: "button-primary button-success"});

            // Change l'icon du button et le text
            $(this).html("<i class='fa fa-check' aria-hidden='true'></i> Valider");

            // Selecteur de tous les input/textarea de la row focus et remove le "disable"
            $(this).parent().parent().children("td").children("input, textarea").removeAttr("disabled");

            $(this).parent().parent().children("td").children("input, textarea, a").addClass("edit-active");

            return false;

        });

    });


    /*
     ** CHANGEMENT DE SENS DES FLÈCHE DES ARCODEON DE MAP OPTION ET MANAGE MARQUEUR
     */
    $('.action-accordeon').click(function () {
        //Selection la flèche du tire de l'acordeon
        var icon_fleche = $(this).children('i').last();

        //Si la flèche est vers le haut
        if (icon_fleche.attr('class') === 'fa fa-caret-up') {
            //Remplace par la flèche du bas
            icon_fleche.removeClass('fa fa-caret-up').addClass('fa fa-caret-down');
        }
        else {
            if (icon_fleche.attr('class') === 'fa fa-caret-down') {
                //Remplace par la flèche du haut
                icon_fleche.removeClass('fa fa-caret-down').addClass('fa fa-caret-up');
            }
        }

        //Remplace toute les flèche par une flèche du bas sauf pour celle selectionné
        $('.action-accordeon').not(this).children('i').last().each(function () {
            //Si l'attribut des autres flèches est pas en bas
            if ($(this).attr('class') !== 'fa fa-caret-down') {
                //Remplace par la flèche du bas
                $(this).removeClass('fa fa-caret-up').addClass('fa fa-caret-down');
            }
        });

    });


    /**
     * CLICK BUTTON UPLOAD CSV
     */
    $('#upload_csv_submit').click(function (e) {

        if ($('#upload_csv').val() === '') {
            $('#form-upload-csv').css('border', 'solid 1px red');
            mapmarker_alert_msg_js('error', _e.msg_error_empty);
            e.preventDefault();
        }
        else {
            return true;
        }

    });


    /**
     * DISMISS ERROR MESSAGE ON CLICK
     */
    $("button.notice-dismiss").live("click", function () {
        $(".wrap:first .is-dismissible").remove();
    });


    /*
     ** MESSAGE D'ALERT WORDPRESS DYNAMIQUE
     */
    function mapmarker_alert_msg_js(alert, msg) {
        /*
         **
         alert color green = "success"
         alert color blue = "info"
         alert color orange = "warning"
         alert color red = "error"
         **
         */

        //Clean old error
        $(".wrap:first .is-dismissible").remove();

        // Prepend error message with wordpress style
        $(".wrap:first").prepend('<div class="notice notice-' + alert + ' is-dismissible"><p><strong>' + msg + '</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Ne pas tenir compte de ce message.</span></button></div>');
    }


})(jQuery);