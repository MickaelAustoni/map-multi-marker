<?php

global $wpdb;

// Déclare la table
$table_name = $wpdb->prefix . 'mapmarker_marker';


if(isset($_POST[ 'securite_nonce_upload_csv' ])){
    if(wp_verify_nonce($_POST[ 'securite_nonce_upload_csv' ], 'securite_nonce_upload_csv')){

        //Load class for read CSV
        require dirname(__DIR__) . '/class/CsvReader.php';

        //Get options
        $options = $wpdb->get_results("SELECT default_desc_img_url, default_marker_img_url FROM " . $wpdb->prefix . 'mapmarker_option WHERE map_id="' . $_GET[ 'map_id' ] . '"', ARRAY_A);

        try{

            //Convert CSV into request
            CsvReader::insertCsv($_FILES[ 'csv_file' ], $_GET[ 'map_id' ], $options[ 0 ]);

            //Display success msg
            mapmarker_message_alert(__('Your csv file has been successfully downloaded.', 'map-multi-marker'), 'updated');

        } catch(Exception $e){
            //Error validation
            if($e->getMessage() == 'ERROR_VALID'){
                //Display error msg
                mapmarker_message_alert(__('Please choose a valid file.', 'map-multi-marker'), 'error');
            } elseif($e->getMessage() == 'ERROR_SQL'){
                mapmarker_message_alert(__('Error SQL.', 'map-multi-marker'), 'error');
            } else{
                //Display error msg
                mapmarker_message_alert(__('Error csv file upload.', 'map-multi-marker'), 'error');
            }
        }

    } else{
        // Le formulaire est refusé et on affiche le message d'erreur
        mapmarker_message_alert(__('Error in the form.', 'map-multi-marker'), 'error');
        exit;
    }
}

// Si securite_nonce est posté
if(isset($_POST[ 'securite_nonce_manage_marker' ]) OR isset($_POST[ 'securite_nonce_delete_marker' ])){

    //Si la varible "securite_nonce_manage_marker" n'existe pas, alors on l'init.
    if(!isset($_POST[ 'securite_nonce_manage_marker' ])){
        $_POST[ 'securite_nonce_manage_marker' ] = NULL;
    }

    //Si la varible "securite_nonce_delete_marker" n'existe pas, alors on l'init.
    if(!isset($_POST[ 'securite_nonce_delete_marker' ])){
        $_POST[ 'securite_nonce_delete_marker' ] = NULL;
    }

    // Verification des nonce
    if(wp_verify_nonce($_POST[ 'securite_nonce_manage_marker' ], 'securite-nonce') OR wp_verify_nonce($_POST[ 'securite_nonce_delete_marker' ], 'securite-nonce')){

        // Si on crée un marker
        if(isset($_POST[ 'create_marker' ])){
            mapMarkerCreateMarker();
        }

        // Si on supprime un marker
        if(isset($_POST[ 'submit_delete' ])){

            // Supprime les antislashe pour eviter les bugs
            $_POST = stripslashes_deep($_POST);

            // Requete de supp.
            $wpdb->delete($table_name, array('ID' => sanitize_text_field($_POST[ 'id_delete_marker' ])), $where_format = NULL);

            //Affiche le message d'alert
            mapmarker_message_alert(__('Your marker been deleted.', 'map-multi-marker'), 'updated');
        }


        // Si valid_edition est posté
        if(isset($_POST[ 'valid_edition' ])){
            // Supprime les antislashe pour eviter les bugs
            $_POST = stripslashes_deep($_POST);

            //Intit. les variable si elle existe pas
            if(!isset($_POST[ 'titre' ])){
                $_POST[ 'titre' ] = NULL;
            }
            if(!isset($_POST[ 'description' ])){
                $_POST[ 'description' ] = NULL;
            }
            if(!isset($_POST[ 'adresse' ])){
                $_POST[ 'adresse' ] = NULL;
            }
            if(!isset($_POST[ 'telephone' ])){
                $_POST[ 'telephone' ] = NULL;
            }
            if(!isset($_POST[ 'weblink' ])){
                $_POST[ 'weblink' ] = NULL;
            }
            if(!isset($_POST[ 'edit_desc_img_id' ])){
                $option                      = $wpdb->get_results("SELECT default_desc_img_url FROM " . $wpdb->prefix . 'mapmarker_option WHERE map_id="' . $_GET[ 'map_id' ] . '"', ARRAY_A);
                $_POST[ 'edit_desc_img_id' ] = $option[ 0 ][ 'default_desc_img_url' ];
            }

            // Requete sql d'update
            $wpdb->update($table_name,
                array(
                    'marker_id'       => sanitize_text_field($_GET[ 'map_id' ]),
                    'titre'           => sanitize_text_field($_POST[ 'titre' ]),
                    'description'     => implode("\n", array_map('sanitize_text_field', explode("\n", $_POST[ 'description' ]))),
                    'adresse'         => sanitize_text_field($_POST[ 'adresse' ]),
                    'telephone'       => sanitize_text_field($_POST[ 'telephone' ]),
                    'weblink'         => sanitize_text_field($_POST[ 'weblink' ]),
                    'latitude'        => sanitize_text_field($_POST[ 'latitude' ]),
                    'longitude'       => sanitize_text_field($_POST[ 'longitude' ]),
                    'img_icon_marker' => sanitize_text_field($_POST[ 'edit_marker_img_id' ]),
                    'img_desc_marker' => sanitize_text_field($_POST[ 'edit_desc_img_id' ])

                ),
                array(
                    'id' => sanitize_text_field($_POST[ 'id' ])
                )
            );

            //Affiche le message d'alert
            mapmarker_message_alert(__('Your marker been edited.', 'map-multi-marker'), 'updated');

        } // End valid_edition

    } // End wp_verify_nonce

    else{
        // Le formulaire est refusé et on affiche le message d'erreur
        mapmarker_message_alert(__('Error in the form.', 'map-multi-marker'), 'error');
        exit;
    }

} // End if securite_nonce est posté

// Requete SQL et stock dans un tableau associatif avec "ARRAY_A" pour récupe les champs selectionné dans les option
$options = $wpdb->get_results("SELECT fiels_to_display, default_marker_img_url, default_desc_img_url FROM " . $wpdb->prefix . 'mapmarker_option WHERE map_id="' . $_GET[ 'map_id' ] . '"', ARRAY_A);

// Requete SQL et stock dans un tableau associatif avec "ARRAY_A"
$data = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . 'mapmarker_marker WHERE marker_id="' . $_GET[ 'map_id' ] . '" ORDER BY id ASC', ARRAY_A);


?>

<div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingTwo">
        <h4 class="panel-title">
            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo" class="action-accordeon">
                <i class="fa fa-map-marker" aria-hidden="true"></i> <?php _e('Management marker', 'map-multi-marker') ?>
                <i class="fa fa-caret-up" aria-hidden="true"></i> </a>
        </h4>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingTwo">
        <div class="panel-body">
            <form method="POST" action="" enctype="multipart/form-data" id="form-upload-csv">
                <input type="hidden" name="securite_nonce_upload_csv" value="<?php echo wp_create_nonce('securite_nonce_upload_csv'); ?>"/>
                <input id="upload_csv" name="csv_file" type="file">
                <button id="upload_csv_submit" type="submit" class="button-primary button-orange">
                    <i class="fa fa-plus" aria-hidden="true"></i> <?php _e('Import .csv', 'map-multi-marker') ?>
                </button>
            </form>

            <hr>

            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="securite_nonce_manage_marker" value="<?php echo wp_create_nonce('securite-nonce'); ?>"/>
                <table class="widefat" id="table_markers">
                    <thead>
                    <tr>
                        <th style="text-align: center"><strong><?php _e('Marker', 'map-multi-marker') ?></strong></th>

                        <?php
                        if(mapmarker_get_checked_field($options[ 0 ][ 'fiels_to_display' ], 'image') == TRUE){
                            ?>
                            <th style="text-align: center"><strong><?php _e('Image', 'map-multi-marker') ?></strong>
                            </th>
                            <?php
                        }
                        ?>
                        <?php
                        if(mapmarker_get_checked_field($options[ 0 ][ 'fiels_to_display' ], 'titre') == TRUE){
                            ?>
                            <th><strong><?php _e('Title', 'map-multi-marker') ?></strong></th>
                            <?php
                        }
                        ?>
                        <?php
                        if(mapmarker_get_checked_field($options[ 0 ][ 'fiels_to_display' ], 'description') == TRUE){
                            ?>
                            <th><strong><?php _e('Description', 'map-multi-marker') ?></strong></th>
                            <?php
                        }
                        ?>
                        <?php
                        if(mapmarker_get_checked_field($options[ 0 ][ 'fiels_to_display' ], 'adresse') == TRUE){
                            ?>
                            <th><strong><?php _e('Address', 'map-multi-marker') ?></strong></th>
                            <?php
                        }
                        ?>
                        <?php
                        if(mapmarker_get_checked_field($options[ 0 ][ 'fiels_to_display' ], 'telephone') == TRUE){
                            ?>
                            <th><strong><?php _e('Phone', 'map-multi-marker') ?></strong></th>
                            <?php
                        }
                        ?>
                        <?php
                        if(mapmarker_get_checked_field($options[ 0 ][ 'fiels_to_display' ], 'weblink') == TRUE){
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
                    foreach($data as $item){
                        ?>
                        <tr>
                            <td style="text-align: center">
                                <input type="hidden" name="edit_marker_img_id" class="edit_img_id" value="<?php echo esc_html($item[ 'img_icon_marker' ]) ?>" disabled>
                                <a href="#" id="edit_img_icon_marker_link"><img class="thumb-img-admin" src="<?php echo mapmarker_get_image_marker_src($item[ 'img_icon_marker' ]); ?>" alt=""></a>
                            </td>
                            <?php
                            if(mapmarker_get_checked_field($options[ 0 ][ 'fiels_to_display' ], 'image') == TRUE){
                                ?>
                                <td style="text-align: center">
                                    <input type="hidden" name="edit_desc_img_id" class="edit_img_id" value="<?php echo esc_html($item[ 'img_desc_marker' ]) ?>" disabled>
                                    <a href="#" id="edit_img_desc_marker_link"><img class="thumb-img-admin" src="<?php echo mapmarker_get_image_desc_src($item[ 'img_desc_marker' ]); ?>" alt=""></a>
                                </td>
                                <?php
                            }
                            ?>
                            <?php
                            if(mapmarker_get_checked_field($options[ 0 ][ 'fiels_to_display' ], 'titre') == TRUE){
                                ?>
                                <td>
                                    <input maxlength="50" type="text" value="<?php echo esc_html($item[ 'titre' ]) ?>" name="titre" disabled>
                                </td>
                                <?php
                            }
                            ?>
                            <?php
                            if(mapmarker_get_checked_field($options[ 0 ][ 'fiels_to_display' ], 'description') == TRUE){
                                ?>
                                <td>
                                    <textarea maxlength="255" name="description" disabled><?php echo html_entity_decode(esc_textarea($item[ 'description' ])) ?></textarea>
                                </td>
                                <?php
                            }
                            ?>
                            <?php
                            if(mapmarker_get_checked_field($options[ 0 ][ 'fiels_to_display' ], 'adresse') == TRUE){
                                ?>
                                <td>
                                    <textarea maxlength="255" name="adresse" disabled><?php echo html_entity_decode(esc_textarea($item[ 'adresse' ])) ?></textarea>
                                </td>
                                <?php
                            }
                            ?>
                            <?php
                            if(mapmarker_get_checked_field($options[ 0 ][ 'fiels_to_display' ], 'telephone') == TRUE){
                                ?>
                                <td>
                                    <input maxlength="20" type="text" value="<?php echo esc_html($item[ 'telephone' ]) ?>" name="telephone" disabled>
                                </td>
                                <?php
                            }
                            ?>
                            <?php
                            if(mapmarker_get_checked_field($options[ 0 ][ 'fiels_to_display' ], 'weblink') == TRUE){
                                ?>
                                <td>
                                    <input maxlength="255" type="text" value="<?php echo esc_url($item[ 'weblink' ]) ?>" name="weblink" disabled>
                                </td>
                                <?php
                            }
                            ?>
                            <td>
                                <input maxlength="11" type="text" value="<?php echo esc_html($item[ 'latitude' ]) ?>" name="latitude" disabled>
                            </td>
                            <td>
                                <input maxlength="11" type="text" value="<?php echo esc_html($item[ 'longitude' ]) ?>" name="longitude" disabled>
                            </td>
                            <td class="action">
                                <input type="hidden" name="id" id="id" value="<?php echo $item[ 'id' ] ?>" disabled>
                                <button type='submit' class='button-primary' name="edit_marker" id="edit_marker">
                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i> <?php _e('Edit', 'map-multi-marker'); ?>
                                </button>
                                <button type='submit' class='button-secondary' name="delete_marker" id="delete_marker">
                                    <i class="fa fa-times" aria-hidden="true"></i> <?php _e('Delete', 'map-multi-marker'); ?>
                                </button>
                            </td>
                        </tr>
                        <?php
                    }// End Boucle

                    ?>
                    </tbody>
                    <!-- Table pour la création du marqueurs -->
                    <tfoot>
                    <tr>
                        <td style="text-align: center">
                            <input type='hidden' name='add_img_icon_marker' value='<?php echo $options[ 0 ][ 'default_marker_img_url' ] ?>'>
                            <div class="wrap-set-img">
                                <a href="#" id="add_icon_marker_link">
                                    <img class="thumb-img-admin" alt="" src="<?php echo mapmarker_get_image_marker_src($options[ 0 ][ 'default_marker_img_url' ]) ?>">
                                </a>
                            </div>
                        </td>
                        <?php
                        if(mapmarker_get_checked_field($options[ 0 ][ 'fiels_to_display' ], 'image') == TRUE){
                            ?>
                            <td style="text-align: center">
                                <input type='hidden' name='add_img_desc_marker' value='<?php echo $options[ 0 ][ 'default_desc_img_url' ] ?>'>
                                <div class="wrap-set-img">
                                    <a href="#" id="add_icon_desc_link">
                                        <img class="thumb-img-admin" alt="" src="<?php echo mapmarker_get_image_desc_src($options[ 0 ][ 'default_desc_img_url' ]) ?>">
                                    </a>
                                </div>
                            </td>
                            <?php
                        }
                        ?>
                        <?php
                        if(mapmarker_get_checked_field($options[ 0 ][ 'fiels_to_display' ], 'titre') == TRUE){
                            ?>
                            <td>
                                <input maxlength="50" type="text" name="add_titre" placeholder="<?php _e('Title', 'map-multi-marker') ?>">
                            </td>
                            <?php
                        }
                        ?>
                        <?php
                        if(mapmarker_get_checked_field($options[ 0 ][ 'fiels_to_display' ], 'description') == TRUE){
                            ?>
                            <td>
                                <textarea maxlength="255" style="width:100%" id="add_description" name="add_description" placeholder="<?php echo _e('Description') ?>"></textarea>
                            </td>
                            <?php
                        }
                        ?>
                        <?php
                        if(mapmarker_get_checked_field($options[ 0 ][ 'fiels_to_display' ], 'adresse') == TRUE){
                            ?>
                            <td>
                                <textarea maxlength="255" name="add_adresse" placeholder="<?php _e('Address', 'map-multi-marker') ?>"></textarea>
                            </td>
                            <?php
                        }
                        ?>
                        <?php
                        if(mapmarker_get_checked_field($options[ 0 ][ 'fiels_to_display' ], 'telephone') == TRUE){
                            ?>
                            <td>
                                <input maxlength="12" type="text" name="add_telephone" placeholder="<?php _e('Phone', 'map-multi-marker') ?>">
                            </td>
                            <?php
                        }
                        ?>
                        <?php
                        if(mapmarker_get_checked_field($options[ 0 ][ 'fiels_to_display' ], 'weblink') == TRUE){
                            ?>
                            <td>
                                <input maxlength="255" type="text" name="add_weblink" placeholder="<?php _e('Web link', 'map-multi-marker') ?>">
                            </td>
                            <?php
                        }
                        ?>
                        <td>
                            <input maxlength="10" type="text" name="add_latitude" class="requiered" placeholder="<?php _e('Latitude', 'map-multi-marker') ?>">
                        </td>
                        <td>
                            <input maxlength="11" type="text" name="add_longitude" class="requiered" placeholder="<?php _e('Longitude', 'map-multi-marker') ?>">
                        </td>
                        <td>
                            <button type='submit' class='button-primary button-success' name="create_marker" id="create_marker">
                                <i class="fa fa-plus" aria-hidden="true"></i> <?php _e('Add', 'map-multi-marker'); ?>
                            </button>
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </form>
        </div>
    </div>
</div>
</div><!-- end wrap-->

<script type="text/javascript">
    jQuery(function () {
        //CALL AJAX DU DU BOUTTON SUPPRIMÉ
        jQuery("#table_markers").each(function (index) {

            // Au click du bouton "Supprimer"
            jQuery("#table_markers #delete_marker").click(function () {
                // Récupère l'id clické
                var id = jQuery(this).siblings("#id").val();

                // FONCTION AJAX
                jQuery.ajax({
                    method: "POST",
                    url: "<?php echo admin_url('admin-ajax.php')?>",
                    dataType: "html",
                    data: {
                        action: "mapMarkerModalDelete",
                        id: id,
                        modal: 'delete_marker'
                    },

                    success: function (data) {
                        jQuery('body').prepend(data);
                    }
                });//End AJAX

                return false;
            });
        }); //END CALL AJAX DU DU BOUTTON SUPPRIMÉ
    });
</script>