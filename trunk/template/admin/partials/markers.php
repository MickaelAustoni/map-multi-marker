<?php


global $wpdb;

// Déclare la table
$table_name = $wpdb->prefix . 'mapmarker_marker';


if (isset($_POST['securite_nonce_upload_csv'])) {
    if (wp_verify_nonce($_POST['securite_nonce_upload_csv'], 'securite_nonce_upload_csv')) {

        //Get options
        $options = $wpdb->get_results("SELECT default_desc_img_url, default_marker_img_url FROM " . $wpdb->prefix . 'mapmarker_option WHERE map_id="' . $_GET['map_id'] . '"', ARRAY_A);

        try {

            //Convert CSV into request
            map_multi_marker_csv_reader::insertCsv($_FILES['csv_file'], $_GET['map_id'], $options[0]);

            //Display success msg
            MapMultiMarker::instance()->mmm_alert_message(__('Your csv file has been successfully downloaded.', MapMultiMarker::TEXT_DOMAIN), 'updated');

        } catch (Exception $e) {
            //Error validation
            if ($e->getMessage() == 'ERROR_VALID') {
                //Display error msg
                MapMultiMarker::instance()->mmm_alert_message(__('Please choose a valid file.', MapMultiMarker::TEXT_DOMAIN), 'error');
            } else {
                if ($e->getMessage() == 'ERROR_SQL') {
                    MapMultiMarker::instance()->mmm_alert_message(__('Error SQL.', MapMultiMarker::TEXT_DOMAIN), 'error');
                } else {
                    //Display error msg
                    MapMultiMarker::instance()->mmm_alert_message(__('Error csv file upload.', MapMultiMarker::TEXT_DOMAIN), 'error');
                }
            }
        }

    } else {
        // Le formulaire est refusé et on affiche le message d'erreur
        MapMultiMarker::instance()->mmm_alert_message(__('Error in the form.', MapMultiMarker::TEXT_DOMAIN), 'error');
        exit;
    }
}

// If nonce manage or delete
if (isset($_POST['securite_nonce_manage_marker']) || isset($_POST['securite_nonce_delete_marker'])) {

    //Si la varible "securite_nonce_manage_marker" n'existe pas, alors on l'init.
    if (!isset($_POST['securite_nonce_manage_marker'])) {
        $_POST['securite_nonce_manage_marker'] = null;
    }

    //Si la varible "securite_nonce_delete_marker" n'existe pas, alors on l'init.
    if (!isset($_POST['securite_nonce_delete_marker'])) {
        $_POST['securite_nonce_delete_marker'] = null;
    }

    // Verification des nonce
    if (wp_verify_nonce($_POST['securite_nonce_manage_marker'], 'securite-nonce') OR wp_verify_nonce($_POST['securite_nonce_delete_marker'], 'securite-nonce')) {

        // Si on crée un marker
        if (isset($_POST['create_marker'])) {
            $this->mmm_create_marker();
        }

        // Si on supprime un marker
        if (isset($_POST['submit_delete'])) {
            $_POST = stripslashes_deep($_POST);
            $wpdb->delete($table_name, ['ID' => sanitize_text_field($_POST['id_delete_marker'])], $where_format = null);
        }


        // Si valid_edition est posté
        if (isset($_POST['valid_edition'])) {
            // Supprime les antislashe pour eviter les bugs
            $_POST = stripslashes_deep($_POST);

            //Intit. les variable si elle existe pas
            if (!isset($_POST['titre'])) {
                $_POST['titre'] = null;
            }
            if (!isset($_POST['description'])) {
                $_POST['description'] = null;
            }
            if (!isset($_POST['adresse'])) {
                $_POST['adresse'] = null;
            }
            if (!isset($_POST['telephone'])) {
                $_POST['telephone'] = null;
            }
            if (!isset($_POST['weblink'])) {
                $_POST['weblink'] = null;
            }
            if (!isset($_POST['edit_desc_img_id'])) {
                $option = $wpdb->get_results("SELECT default_desc_img_url FROM " . $wpdb->prefix . 'mapmarker_option WHERE map_id="' . $_GET['map_id'] . '"', ARRAY_A);
                $_POST['edit_desc_img_id'] = $option[0]['default_desc_img_url'];
            }

            // Update request
            $wpdb->update($table_name, [
                'marker_id' => sanitize_text_field($_GET['map_id']),
                'titre' => sanitize_text_field($_POST['titre']),
                'description' => implode("\n", array_map('sanitize_text_field', explode("\n", $_POST['description']))),
                'adresse' => sanitize_text_field($_POST['adresse']),
                'telephone' => sanitize_text_field($_POST['telephone']),
                'weblink' => sanitize_text_field($_POST['weblink']),
                'latitude' => sanitize_text_field($_POST['latitude']),
                'longitude' => sanitize_text_field($_POST['longitude']),
                'img_icon_marker' => sanitize_text_field($_POST['edit_marker_img_id']),
                'img_desc_marker' => sanitize_text_field($_POST['edit_desc_img_id'])

            ], [
                    'id' => sanitize_text_field($_POST['id'])
                ]
            );

        } // End valid_edition

    } // End wp_verify_nonce

    else {
        // Le formulaire est refusé et on affiche le message d'erreur
        MapMultiMarker::instance()->mmm_alert_message(__('Error in the form.', MapMultiMarker::TEXT_DOMAIN), 'error');
        exit;
    }

} // End if securite_nonce est posté

// Requete SQL et stock dans un tableau associatif avec "ARRAY_A" pour récupe les champs selectionné dans les option
$options = $wpdb->get_results("SELECT fiels_to_display, default_marker_img_url, default_desc_img_url FROM " . $wpdb->prefix . 'mapmarker_option WHERE map_id="' . $this->currentMapId . '"', ARRAY_A);

// Requete SQL et stock dans un tableau associatif avec "ARRAY_A"
$data = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . 'mapmarker_marker WHERE marker_id="' . $this->currentMapId . '" ORDER BY id ASC', ARRAY_A);

?>

<div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingTwo">
        <h4 class="panel-title">
            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="true"
               aria-controls="collapseTwo" class="action-accordeon">
                <i class="fa fa-map-marker"
                   aria-hidden="true"></i> <?php _e('Management marker', MapMultiMarker::TEXT_DOMAIN) ?>
                <i class="fa fa-caret-up" aria-hidden="true"></i></a>
        </h4>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingTwo">
        <div class="panel-body">
            <form method="POST" action="" enctype="multipart/form-data" id="form-upload-csv">
                <input type="hidden" name="securite_nonce_upload_csv"
                       value="<?php echo wp_create_nonce('securite_nonce_upload_csv'); ?>"/>
                <input id="upload_csv" name="csv_file" type="file">
                <button id="upload_csv_submit" type="submit" class="button-primary button-orange">
                    <i class="fa fa-plus"
                       aria-hidden="true"></i> <?php _e('Import .csv', MapMultiMarker::TEXT_DOMAIN) ?>
                </button>
            </form>

            <hr>

            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="securite_nonce_manage_marker"
                       value="<?php echo wp_create_nonce('securite-nonce'); ?>"/>
                <table class="widefat" id="table_markers">
                    <thead>
                    <tr>
                        <th style="text-align: center">
                            <strong><?php _e('Marker', MapMultiMarker::TEXT_DOMAIN) ?></strong></th>

                        <?php
                        if (map_multi_marker_utility::mmm_is_checked_field($options[0]['fiels_to_display'], 'image') == true) {
                            ?>
                            <th style="text-align: center">
                                <strong><?php _e('Image', MapMultiMarker::TEXT_DOMAIN) ?></strong>
                            </th>
                            <?php
                        }
                        ?>
                        <?php
                        if (map_multi_marker_utility::mmm_is_checked_field($options[0]['fiels_to_display'], 'titre') == true) {
                            ?>
                            <th><strong><?php _e('Title', MapMultiMarker::TEXT_DOMAIN) ?></strong></th>
                            <?php
                        }
                        ?>
                        <?php
                        if (map_multi_marker_utility::mmm_is_checked_field($options[0]['fiels_to_display'], 'description') == true) {
                            ?>
                            <th><strong><?php _e('Description', MapMultiMarker::TEXT_DOMAIN) ?></strong></th>
                            <?php
                        }
                        ?>
                        <?php
                        if (map_multi_marker_utility::mmm_is_checked_field($options[0]['fiels_to_display'], 'adresse') == true) {
                            ?>
                            <th><strong><?php _e('Address', MapMultiMarker::TEXT_DOMAIN) ?></strong></th>
                            <?php
                        }
                        ?>
                        <?php
                        if (map_multi_marker_utility::mmm_is_checked_field($options[0]['fiels_to_display'], 'telephone') == true) {
                            ?>
                            <th><strong><?php _e('Phone', MapMultiMarker::TEXT_DOMAIN) ?></strong></th>
                            <?php
                        }
                        ?>
                        <?php
                        if (map_multi_marker_utility::mmm_is_checked_field($options[0]['fiels_to_display'], 'weblink') == true) {
                            ?>
                            <th><strong><?php _e('Web link', MapMultiMarker::TEXT_DOMAIN) ?></strong></th>
                            <?php
                        }
                        ?>
                        <th><strong><?php _e('Latitude', MapMultiMarker::TEXT_DOMAIN) ?>*</strong></th>
                        <th><strong><?php _e('Longitude', MapMultiMarker::TEXT_DOMAIN) ?>*</strong></th>
                        <th><strong><?php _e('Action', MapMultiMarker::TEXT_DOMAIN) ?></strong></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    // Boucle les datas
                    foreach ($data as $item) {
                        ?>
                        <tr>
                            <td style="text-align: center">
                                <input type="hidden" name="edit_marker_img_id" class="edit_img_id"
                                       value="<?php echo esc_html($item['img_icon_marker']) ?>" disabled>
                                <a href="#" id="edit_img_icon_marker_link">
                                    <img class="thumb-img-admin"
                                         src="<?php echo map_multi_marker_utility::mmm_get_image_marker_src($item['img_icon_marker']); ?>"
                                         alt="">
                                </a>
                            </td>
                            <?php
                            if (map_multi_marker_utility::mmm_is_checked_field($options[0]['fiels_to_display'], 'image') == true) {
                                ?>
                                <td style="text-align: center">
                                    <input type="hidden" name="edit_desc_img_id" class="edit_img_id"
                                           value="<?php echo esc_html($item['img_desc_marker']) ?>" disabled>
                                    <a href="#" id="edit_img_desc_marker_link">
                                        <img class="thumb-img-admin"
                                             src="<?php echo map_multi_marker_utility::mmm_get_image_desc_src($item['img_desc_marker']); ?>"
                                             alt="">
                                    </a>
                                </td>
                                <?php
                            }
                            ?>
                            <?php
                            if (map_multi_marker_utility::mmm_is_checked_field($options[0]['fiels_to_display'], 'titre') == true) {
                                ?>
                                <td>
                                    <input maxlength="50" type="text" value="<?php echo esc_html($item['titre']) ?>"
                                           name="titre" disabled>
                                </td>
                                <?php
                            }
                            ?>
                            <?php
                            if (map_multi_marker_utility::mmm_is_checked_field($options[0]['fiels_to_display'], 'description') == true) {
                                ?>
                                <td>
                                    <textarea maxlength="255" name="description"
                                              disabled><?php echo html_entity_decode(esc_textarea($item['description'])) ?></textarea>
                                </td>
                                <?php
                            }
                            ?>
                            <?php
                            if (map_multi_marker_utility::mmm_is_checked_field($options[0]['fiels_to_display'], 'adresse') == true) {
                                ?>
                                <td>
                                    <textarea maxlength="255" name="adresse"
                                              disabled><?php echo html_entity_decode(esc_textarea($item['adresse'])) ?></textarea>
                                </td>
                                <?php
                            }
                            ?>
                            <?php
                            if (map_multi_marker_utility::mmm_is_checked_field($options[0]['fiels_to_display'], 'telephone') == true) {
                                ?>
                                <td>
                                    <input maxlength="20" type="text" value="<?php echo esc_html($item['telephone']) ?>"
                                           name="telephone" disabled>
                                </td>
                                <?php
                            }
                            ?>
                            <?php
                            if (map_multi_marker_utility::mmm_is_checked_field($options[0]['fiels_to_display'], 'weblink') == true) {
                                ?>
                                <td>
                                    <input maxlength="255" type="text" value="<?php echo esc_url($item['weblink']) ?>"
                                           name="weblink" disabled>
                                </td>
                                <?php
                            }
                            ?>
                            <td>
                                <input maxlength="11" type="text" value="<?php echo esc_html($item['latitude']) ?>"
                                       name="latitude" disabled>
                            </td>
                            <td>
                                <input maxlength="11" type="text" value="<?php echo esc_html($item['longitude']) ?>"
                                       name="longitude" disabled>
                            </td>
                            <td class="action">
                                <input type="hidden" name="id" id="id" value="<?php echo $item['id'] ?>" disabled>
                                <button type='submit' class='button-primary' name="edit_marker" id="edit_marker">
                                    <i class="fa fa-pencil-square-o"
                                       aria-hidden="true"></i> <?php _e('Edit', MapMultiMarker::TEXT_DOMAIN); ?>
                                </button>
                                <button type='submit' class='delete_marker button-secondary' name="delete_marker"
                                        id="delete_marker">
                                    <i class="fa fa-times"
                                       aria-hidden="true"></i> <?php _e('Delete', MapMultiMarker::TEXT_DOMAIN); ?>
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
                            <input type='hidden' name='add_img_icon_marker'
                                   value='<?php echo $options[0]['default_marker_img_url'] ?>'>
                            <div class="wrap-set-img">
                                <a href="#" id="add_icon_marker_link">
                                    <img class="thumb-img-admin" alt=""
                                         src="<?php echo map_multi_marker_utility::mmm_get_image_marker_src($options[0]['default_marker_img_url']) ?>">
                                </a>
                            </div>
                        </td>
                        <?php
                        if (map_multi_marker_utility::mmm_is_checked_field($options[0]['fiels_to_display'], 'image') == true) {
                            ?>
                            <td style="text-align: center">
                                <input type='hidden' name='add_img_desc_marker'
                                       value='<?php echo $options[0]['default_desc_img_url'] ?>'>
                                <div class="wrap-set-img">
                                    <a href="#" id="add_icon_desc_link">
                                        <img class="thumb-img-admin" alt=""
                                             src="<?php echo map_multi_marker_utility::mmm_get_image_desc_src($options[0]['default_desc_img_url']) ?>">
                                    </a>
                                </div>
                            </td>
                            <?php
                        }
                        ?>
                        <?php
                        if (map_multi_marker_utility::mmm_is_checked_field($options[0]['fiels_to_display'], 'titre') == true) {
                            ?>
                            <td>
                                <input maxlength="50" type="text" name="add_titre"
                                       placeholder="<?php _e('Title', MapMultiMarker::TEXT_DOMAIN) ?>">
                            </td>
                            <?php
                        }
                        ?>
                        <?php
                        if (map_multi_marker_utility::mmm_is_checked_field($options[0]['fiels_to_display'], 'description') == true) {
                            ?>
                            <td>
                                <textarea maxlength="255" style="width:100%" id="add_description" name="add_description"
                                          placeholder="<?php _e('Description', MapMultiMarker::TEXT_DOMAIN
                                          ) ?>"></textarea>
                            </td>
                            <?php
                        }
                        ?>
                        <?php
                        if (map_multi_marker_utility::mmm_is_checked_field($options[0]['fiels_to_display'], 'adresse') == true) {
                            ?>
                            <td>
                                <textarea maxlength="255" name="add_adresse"
                                          placeholder="<?php _e('Address', MapMultiMarker::TEXT_DOMAIN) ?>"></textarea>
                            </td>
                            <?php
                        }
                        ?>
                        <?php
                        if (map_multi_marker_utility::mmm_is_checked_field($options[0]['fiels_to_display'], 'telephone') == true) {
                            ?>
                            <td>
                                <input maxlength="12" type="text" name="add_telephone"
                                       placeholder="<?php _e('Phone', MapMultiMarker::TEXT_DOMAIN) ?>">
                            </td>
                            <?php
                        }
                        ?>
                        <?php
                        if (map_multi_marker_utility::mmm_is_checked_field($options[0]['fiels_to_display'], 'weblink') == true) {
                            ?>
                            <td>
                                <input maxlength="255" type="text" name="add_weblink"
                                       placeholder="<?php _e('Web link', MapMultiMarker::TEXT_DOMAIN) ?>">
                            </td>
                            <?php
                        }
                        ?>
                        <td>
                            <input maxlength="10" type="text" name="add_latitude" class="requiered"
                                   placeholder="<?php _e('Latitude', MapMultiMarker::TEXT_DOMAIN) ?>">
                        </td>
                        <td>
                            <input maxlength="11" type="text" name="add_longitude" class="requiered"
                                   placeholder="<?php _e('Longitude', MapMultiMarker::TEXT_DOMAIN) ?>">
                        </td>
                        <td>
                            <button type='submit' class='button-primary button-success' name="create_marker"
                                    id="create_marker">
                                <i class="fa fa-plus"
                                   aria-hidden="true"></i> <?php _e('Add', MapMultiMarker::TEXT_DOMAIN); ?>
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