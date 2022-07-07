<div class="wrap">
    <?php

    global $wpdb;

    // Si securite_nonce est posé
    if(isset($_POST[ 'securite_nonce' ])){

        // Verification des nonce
        if(wp_verify_nonce($_POST[ 'securite_nonce' ], 'securite-nonce')){

            // Requete sql d'update
            $wpdb->update($wpdb->prefix . 'mapmarker_api',
                array(
                    'apikey'   => sanitize_text_field($_POST[ 'apikey' ]),
                    'language' => sanitize_text_field($_POST[ 'language' ])
                ),
                //where
                array(
                    'id' => '1'
                )
            );

            //Affiche le message d'alert
            mapmarker_message_alert(__('Your changes have been saved.', 'map-multi-marker'), 'updated');

        } // End if wp_verify_nonce
        else{
            // Le formulaire est refusé et on affiche le message d'erreur
            mapmarker_message_alert(__('Error in the form.', 'map-multi-marker'), 'error');
            exit;
        }
    }

    checkPersonalApiKey();

    ?>
    <form method="POST" action="">
        <h1><?php _e('Google API', 'map-multi-marker'); ?></h1>

        <input type="hidden" name="securite_nonce" value="<?php echo wp_create_nonce('securite-nonce'); ?>"/>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <?php _e('Map language', 'map-multi-marker'); ?>
                </th>
                <td>
                    <input id="language" type="text" name="language"
                           value="<?php echo esc_html(mapmarkerGetLanguage()) ?>">
                    <p class="description"
                       id="tagline-description"><?php _e('(English = en) , (French=fr) , (Russian=ru) , ...', 'map-multi-marker') ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php _e('Google API key', 'map-multi-marker'); ?>
                </th>
                <td>
                    <input id="apikey" type="text" name="apikey" value="<?php echo esc_html(mapmarkerGetApiKey()) ?>">
                    <p class="description">
                        <a target="_blank" href="https://console.developers.google.com/flows/enableapi?apiid=maps_backend%2Cgeocoding_backend%2Cdirections_backend%2Cdistance_matrix_backend%2Celevation_backend%2Cplaces_backend&reusekey=true&hl=<?php echo mapmarkerGetLanguage(); ?>"><?php _e('Get a google API key', 'map-multi-marker') ?></a>
                        <br>
                        <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key?hl=<?php echo mapmarkerGetLanguage(); ?>"><?php _e('Help', 'map-multi-marker') ?></a>
                    </p>
                </td>
            </tr>
        </table>
        <p class="submit">
            <button type="submit" name="save_g_api" id="save_g_api" class="button button-primary">
                <i class="fa fa-floppy-o" aria-hidden="true"></i> <?php _e('Save', 'map-multi-marker') ?>
            </button>

        </p>
    </form>
</div>