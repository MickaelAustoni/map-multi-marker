/* global mmm_localize */

(function ($) {
    var $map_name      = $("#map_name");
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
     ** Extend input on focus
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

        $(".requiered").each(function () {

            if (!$(this).value) {
                // Reset error required
                $(this).removeClass("requiered-error");
            }

            // If input or textarea is empty
            if ($(this).val() === '') {
                $(this).addClass('requiered-error');
                mapmarker_alert_msg_js('error', mmm_localize.msg_error_required);
                e.preventDefault();
            } else { // Valid form
                return true;
            }
        });// End Each

    });

    /*
     ** Edit marker
     */
    $table_markers.each(function () {
        $(this).on("click", "#edit_marker", function (e) {
            e.preventDefault();
            $(this).attr({name: "valid_edition", id: "valid_edition", class: "button-primary button-success"});
            $(this).html("<i class='fa fa-check' aria-hidden='true'></i> Valider");
            $(this).parent().parent().children("td").children("input, textarea").removeAttr("disabled");
            $(this).parent().parent().children("td").children("input, textarea, a").addClass("edit-active");

            return false;
        });
    });

    /*
     ** Arrow accordion
     */
    $('.action-accordeon').on("click", function () {
        var icon_fleche = $(this).children('i').last();

        if (icon_fleche.attr('class') === 'fa fa-caret-up') {
            icon_fleche.removeClass('fa fa-caret-up').addClass('fa fa-caret-down');
        } else {
            if (icon_fleche.attr('class') === 'fa fa-caret-down') {
                icon_fleche.removeClass('fa fa-caret-down').addClass('fa fa-caret-up');
            }
        }

        // Replace arrow
        $('.action-accordeon').not(this).children('i').last().each(function () {
            //Si l'attribut des autres flèches est pas en bas
            if ($(this).attr('class') !== 'fa fa-caret-down') {
                //Remplace par la flèche du bas
                $(this).removeClass('fa fa-caret-up').addClass('fa fa-caret-down');
            }
        });
    });

    /**
     * Upload csv
     */
    $('#upload_csv_submit').on("click", function (e) {
        if ($('#upload_csv').val() === '') {
            $('#form-upload-csv').css('border', 'solid 1px red');
            mapmarker_alert_msg_js('error', mmm_localize.msg_error_empty);
            e.preventDefault();
        } else {
            return true;
        }

    });

    /**
     * Dismiss error
     */
    $("button.notice-dismiss").on("click", function () {
        $(".wrap:first .is-dismissible").remove();
    });

    /*
     ** Alert message
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

    /**
     * Delete map
     */
    $('#table_map').on('click', '#delete_map', function () {
        var id = jQuery(this).val();

        $.ajax({
            method: "POST",
            url: mmm_localize.ajax_url,
            dataType: "html",
            data: {
                action: "mmm_async_modal_delete",
                id: id,
                modal: 'delete_map'
            },
            success: function (data) {
                $('body').prepend(data);

                var modalConfirm = $('.modal-confirm');

                modalConfirm.slideDown("fast");
                modalConfirm.css('display', 'inline-block');

                $('#cancel-supp').click(function () {
                    $('.wrap-modal-parent').remove();
                    return false;
                });
            }
        });//End AJAX

        return false;
    });


    /**
     * Copy short code
     */
    $(".copy-shortcode").on("click", function (event) {
        event.preventDefault();
    });
    new Clipboard('.copy-shortcode', {
        text: function (trigger) {
            return '[map-multi-marker id="' + trigger.getAttribute('data-id-copy') + '"]';
        }
    });

    /**
     * Media native uploader wordpress
     */
    $(document).on('click', '#upload_desc_img_button, #upload_marker_img_button, #edit_img_icon_marker_link.edit-active, #edit_img_desc_marker_link.edit-active, #add_icon_marker_link, #add_icon_desc_link', function (event) {
        event.preventDefault();

        var select_url       = undefined;
        var select_id        = undefined;
        var wp_media_post_id = wp.media.model.settings.post.id;

        var file_frame = wp.media.frames.file_frame = wp.media({title: "Select a image to upload", button: {text: "Use this image"}, multiple: false});

        if (jQuery(this).is('#upload_desc_img_button, #upload_marker_img_button')) {
            select_url = jQuery(this).siblings('.default_img_preview');
            select_id  = jQuery(this).siblings('.default_img_id');
        }
        if (jQuery(this).is('#edit_img_icon_marker_link.edit-active, #edit_img_desc_marker_link.edit-active')) {
            select_url = jQuery(this).children('img');
            select_id  = jQuery(this).siblings('.edit_img_id');
        }
        if (jQuery(this).is('#add_icon_marker_link, #add_icon_desc_link')) {
            select_url = jQuery(this).children('img');
            select_id  = jQuery(this).parent().siblings('input');
        }

        file_frame.on("select", function () {
            var attachment = file_frame.state().get('selection').first().toJSON();
            jQuery(select_url).attr('src', attachment.url);
            jQuery(select_id).val(attachment.id);
            wp.media.model.settings.post.id = wp_media_post_id;
        });

        file_frame.open();
    });

    /**
     * Delete marker
     */
    $table_markers.on('click', '#delete_marker', function () {
        var id = $(this).siblings("#id").val();

        $.ajax({
            method: "POST",
            url: mmm_localize.ajax_url,
            dataType: "html",
            data: {
                action: "mmm_async_modal_delete",
                id: id,
                modal: 'delete_marker'
            },

            success: function (data) {
                $('body').prepend(data);

                var modalConfirm = $('.modal-confirm');
                modalConfirm.slideDown("fast");
                modalConfirm.css('display', 'inline-block');

                $('#cancel-supp').on("click", function () {
                    $('.wrap-modal-parent').remove();
                    return false;
                });
            }
        });//End AJAX

        return false;

    }); //END CALL AJAX

})(jQuery);