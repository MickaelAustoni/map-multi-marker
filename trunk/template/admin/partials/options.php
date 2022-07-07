<div class="wrap">
    <?php
    
    global $wpdb;
    
    //If on save les options
    if (isset($_POST['securite_nonce_option'])) {
        
        // Verification des nonce du formulaire des options
        if (wp_verify_nonce($_POST['securite_nonce_option'], 'securite_nonce_option')) {
            
            
            //Intit. la variable des champs à afficher
            $fiels_to_display = null;
            
            
            //Si au moins 1 champs à afficher est coché
            if (!empty($_POST['fiels_to_display'])) {
                
                // Récup les donné cocher
                foreach ($_POST['fiels_to_display'] as $value) {
                    $fiels_to_display .= $value . ',';
                }
                
                //Supprime la dernière virgule
                $fiels_to_display = substr($fiels_to_display, 0, -1);
            }
            
            //Si $scrollwheel exist (coché) sinon $scrollwheel vaut 0
            $lightbox = (isset($_POST['lightbox'])) ? 1 : 0;
            
            //Si $scrollwheel exist (coché) sinon $scrollwheel vaut 0
            $scrollwheel = (isset($_POST['scrollwheel'])) ? 1 : 0;
            
            //Si $streetview exist (coché) sinon $streetview vaut 0
            $streetview = (isset($_POST['streetview'])) ? 0 : 1;
            
            
            // Requete sql d'update
            $wpdb->update($wpdb->prefix . 'mapmarker_option', [
                'map_name'               => sanitize_text_field($_POST['map_name']),
                'height_map'             => sanitize_text_field($_POST['height_map']),
                'height_valeur_type'     => sanitize_text_field($_POST['height_valeur_type']),
                'width_map'              => sanitize_text_field($_POST['width_map']),
                'streetview'             => sanitize_text_field($streetview),
                'width_valeur_type'      => sanitize_text_field($_POST['width_valeur_type']),
                'maptype'                => sanitize_text_field($_POST['maptype']),
                'zoom'                   => sanitize_text_field($_POST['zoom']),
                'lightbox'               => sanitize_text_field($lightbox),
                'scrollwheel'            => sanitize_text_field($scrollwheel),
                'latitude_initial'       => sanitize_text_field($_POST['latitude_initial']),
                'longitude_initial'      => sanitize_text_field($_POST['longitude_initial']),
                'fiels_to_display'       => sanitize_text_field($fiels_to_display),
                'default_desc_img_url'   => sanitize_text_field($_POST['default_desc_img_id']),
                'default_marker_img_url' => sanitize_text_field($_POST['default_marker_img_id'])
            ], //where
                [
                    'map_id' => sanitize_text_field($_GET['map_id'])
                ]
            );
            
            //Affiche le message d'alert
            MapMultiMarker::instance()->mmm_alert_message(__('Your options have been saved.', MapMultiMarker::DEFAULT_DESC_IMG_URL), 'updated');
            
        } // End if wp_verify_nonce
        else {
            // If form refused display message
            MapMultiMarker::instance()->mmm_alert_message(__('Error in the form.', MapMultiMarker::DEFAULT_DESC_IMG_URL), 'error');
            exit;
        }
        
    }// End if save les options
    
    
    //Req. pour récupérer les options de la carte
    $data = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . 'mapmarker_option WHERE map_id="' . $_GET['map_id'] . '"', ARRAY_A);
    
    ?>
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingOne">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                       aria-expanded="false" aria-controls="collapseOne" class="action-accordeon collapsed">
                        <i class="fa fa-cogs" aria-hidden="true"></i> <?php _e('Map option', MapMultiMarker::TEXT_DOMAIN); ?>
                        <i class="fa fa-caret-down" aria-hidden="true"></i>
                    </a>
                </h4>
            </div>
            <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                <div class="panel-body">
                    <form method="POST" action="">
                        <input type="hidden" name="securite_nonce_option" value="<?php echo wp_create_nonce('securite_nonce_option'); ?>"/>
                        <input type="hidden" name="id" value="<?php echo $data[0]['id'] ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="form-table">
                                    <tr>
                                        <th scope="row">
                                            <label for="map_name"><?php _e('Map name', MapMultiMarker::TEXT_DOMAIN); ?></label>
                                            <span class="help_tooltip" id="help_map_name"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
                                        </th>
                                        <td>
                                            <input id="map_name" type="text" name="map_name" maxlength="255" value="<?php echo esc_html($data[0]['map_name']) ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="height_map"><?php _e('Height', MapMultiMarker::TEXT_DOMAIN); ?></label>
                                            <span class="help_tooltip" id="help_map_height"><i
                                                        class="fa fa-question-circle" aria-hidden="true"></i></span>
                                        </th>
                                        <td>
                                            <input id="height_map" type="text" name="height_map" maxlength="11" value="<?php echo esc_html($data[0]['height_map']) ?>">
                                            <select name="height_valeur_type">
                                                <option value="px" <?php if ($data[0]['height_valeur_type'] == "px") {
                                                    echo "selected";
                                                } ?>>px
                                                </option>
                                                <option value="%" <?php if ($data[0]['height_valeur_type'] == "%") {
                                                    echo "selected";
                                                } ?>>%
                                                </option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="width_map"><?php _e('Width', MapMultiMarker::TEXT_DOMAIN); ?></label>
                                            <span class="help_tooltip" id="help_map_width"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
                                        </th>
                                        <td>
                                            <input id="width_map" type="text" name="width_map" maxlength="11"
                                                   value="<?php echo esc_html($data[0]['width_map']) ?>">
                                            <select name="width_valeur_type">
                                                <option value="px" <?php if ($data[0]['width_valeur_type'] == "px") {
                                                    echo "selected";
                                                } ?>>px
                                                </option>
                                                <option value="%" <?php if ($data[0]['width_valeur_type'] == "%") {
                                                    echo "selected";
                                                } ?>>%
                                                </option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="zoom"><?php _e('Initial zoom', MapMultiMarker::TEXT_DOMAIN); ?></label>
                                            <span class="help_tooltip" id="help_map_zoom"><i
                                                        class="fa fa-question-circle" aria-hidden="true"></i></span>
                                        </th>
                                        <td>
                                            <input id="zoom" type="text" name="zoom" maxlength="2"
                                                   value="<?php echo esc_html($data[0]['zoom']) ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label
                                                    for="latitude_initial"><?php _e('Initial latitude', MapMultiMarker::TEXT_DOMAIN); ?></label>
                                            <span class="help_tooltip" id="help_map_latitude_initial"><i
                                                        class="fa fa-question-circle" aria-hidden="true"></i></span>
                                        </th>
                                        <td>
                                            <input id="latitude_initial" type="text" name="latitude_initial"
                                                   value="<?php echo esc_html($data[0]['latitude_initial']) ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label
                                                    for="longitude_initial"><?php _e('Initial longitude', MapMultiMarker::TEXT_DOMAIN); ?></label>
                                            <span class="help_tooltip" id="help_map_longitude_initial"><i
                                                        class="fa fa-question-circle" aria-hidden="true"></i></span>
                                        </th>
                                        <td>
                                            <input id="longitude_initial" type="text" name="longitude_initial"
                                                   value="<?php echo esc_html($data[0]['longitude_initial']) ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="maptype"><?php _e('Map type', MapMultiMarker::TEXT_DOMAIN); ?></label>
                                            <span class="help_tooltip" id="help_map_maptype"><i
                                                        class="fa fa-question-circle" aria-hidden="true"></i></span>
                                        </th>
                                        <td>
                                            <select name="maptype" id="maptype">
                                                <option value="TERRAIN" <?php if ($data[0]['maptype'] == "TERRAIN") {
                                                    echo "selected";
                                                } ?>>TERRAIN
                                                </option>
                                                <option value="ROADMAP" <?php if ($data[0]['maptype'] == "ROADMAP") {
                                                    echo "selected";
                                                } ?>>ROADMAP
                                                </option>
                                                <option
                                                        value="SATELLITE" <?php if ($data[0]['maptype'] == "SATELLITE") {
                                                    echo "selected";
                                                } ?>>SATELLITE
                                                </option>
                                                <option value="HYBRID" <?php if ($data[0]['maptype'] == "HYBRID") {
                                                    echo "selected";
                                                } ?>>HYBRID
                                                </option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </div><!-- End col-md-6 -->
                            <div class="col-md-6">
                                <table class="form-table">
                                    <tr>
                                        <th scope="row">
                                            <label for="lightbox"><?php _e('Lightbox', MapMultiMarker::TEXT_DOMAIN); ?></label>
                                            <span class="help_tooltip" id="help_map_lightbox"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
                                        </th>
                                        <td>
                                            <input id="lightbox" type="checkbox" name="lightbox" <?php if ($data[0]['lightbox'] == 1) {
                                                echo 'checked';
                                            } ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="scrollwheel"><?php _e('Scrollwheel zoom', MapMultiMarker::TEXT_DOMAIN); ?></label>
                                            <span class="help_tooltip" id="help_map_scroolwheel"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
                                        </th>
                                        <td>
                                            <input id="scrollwheel" type="checkbox" name="scrollwheel" <?php if ($data[0]['scrollwheel'] == 1) {
                                                echo 'checked';
                                            } ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="streetview"><?php _e('Streetview', MapMultiMarker::TEXT_DOMAIN); ?></label>
                                            <span class="help_tooltip" id="help_map_streetview"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
                                        </th>
                                        <td>
                                            <input id="streetview" type="checkbox" name="streetview" <?php if ($data[0]['streetview'] == 0) {
                                                echo 'checked';
                                            } ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="fiels_to_display"><?php _e('Fields to display in tooltip', MapMultiMarker::TEXT_DOMAIN); ?></label>
                                            <span class="help_tooltip" id="help_map_fiels_to_display"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
                                        </th>
                                        <td>
                                            <input id="fiels_to_display"
                                                   type="checkbox"
                                                   name="fiels_to_display[]"
                                                   value="image" <?php map_multi_marker_utility::mmm_e_is_checked_field($data[0]['fiels_to_display'], 'image') ?>><?php _e('Image', MapMultiMarker::TEXT_DOMAIN); ?>
                                            <br>
                                            <input id="fiels_to_display"
                                                   type="checkbox"
                                                   name="fiels_to_display[]"
                                                   value="titre" <?php map_multi_marker_utility::mmm_e_is_checked_field($data[0]['fiels_to_display'], 'titre') ?>><?php _e('Title', MapMultiMarker::TEXT_DOMAIN); ?>
                                            <br>
                                            <input id="fiels_to_display"
                                                   type="checkbox"
                                                   name="fiels_to_display[]"
                                                   value="description" <?php map_multi_marker_utility::mmm_e_is_checked_field($data[0]['fiels_to_display'], 'description') ?>><?php _e('Description',
                                                MapMultiMarker::TEXT_DOMAIN
                                            ); ?>
                                            <br>
                                            <input id="fiels_to_display"
                                                   type="checkbox"
                                                   name="fiels_to_display[]"
                                                   value="adresse" <?php map_multi_marker_utility::mmm_e_is_checked_field($data[0]['fiels_to_display'], 'adresse') ?>><?php _e('Address', MapMultiMarker::TEXT_DOMAIN
                                            ); ?>
                                            <br>
                                            <input id="fiels_to_display"
                                                   type="checkbox"
                                                   name="fiels_to_display[]"
                                                   value="telephone" <?php map_multi_marker_utility::mmm_e_is_checked_field($data[0]['fiels_to_display'], 'telephone') ?>><?php _e('Phone', MapMultiMarker::TEXT_DOMAIN
                                            ); ?>
                                            <br>
                                            <input id="fiels_to_display"
                                                   type="checkbox"
                                                   name="fiels_to_display[]"
                                                   value="weblink" <?php map_multi_marker_utility::mmm_e_is_checked_field($data[0]['fiels_to_display'], 'weblink') ?>><?php _e('Web link', MapMultiMarker::TEXT_DOMAIN
                                            ); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="default_desc_img">
                                                <?php _e('Default image tooltip when a new marker is created', MapMultiMarker::TEXT_DOMAIN); ?>
                                            </label>
                                            <span class="help_tooltip" id="help_map_default_desc_img"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
                                        </th>
                                        <td>
                                            <div class="wrap-set-default-img">
                                                <img class="default_img_preview thumb-img-admin"
                                                     src="<?php if ($data[0]['default_desc_img_url'] == 0) {
                                                         echo MapMultiMarker::instance()->plugin_url . MapMultiMarker::DEFAULT_DESC_IMG_URL;
                                                     } else {
                                                         echo wp_get_attachment_url($data[0]['default_desc_img_url']);
                                                     } ?>" alt="">
                                                <input type='hidden' name='default_desc_img_id' class='default_img_id' value='<?php echo esc_html($data[0]['default_desc_img_url']) ?>'>
                                                <input id="upload_desc_img_button" type="button" class="button" value="<?php _e('Change image', MapMultiMarker::TEXT_DOMAIN); ?>"/>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label for="default_marker_img"><?php _e('Default marker icon when a new marker is created', MapMultiMarker::TEXT_DOMAIN); ?></label>
                                            <span class="help_tooltip" id="help_map_default_marker_img"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
                                        </th>
                                        <td>
                                            <div class="wrap-set-default-img">
                                                <img class="default_img_preview thumb-img-admin"
                                                     src="<?php if ($data[0]['default_marker_img_url'] == 0) {
                                                         echo MapMultiMarker::instance()->plugin_url . MapMultiMarker::DEFAULT_MARKER_IMG_URL;
                                                     } else {
                                                         echo wp_get_attachment_url($data[0]['default_marker_img_url']);
                                                     } ?>" alt="">
                                                <input type='hidden' name='default_marker_img_id' class='default_img_id'
                                                       value='<?php echo esc_html($data[0]['default_marker_img_url']) ?>'>
                                                <input id="upload_marker_img_button" type="button" class="button"
                                                       value="<?php _e('Change image', MapMultiMarker::TEXT_DOMAIN); ?>"/>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div><!-- End col-md-6 -->
                        </div><!-- End row -->

                        <p class="submit">
                            <button type="submit" name="save_option" id="save_option" class="button button-primary">
                                <i class="fa fa-floppy-o"
                                   aria-hidden="true"></i> <?php _e('Save options', MapMultiMarker::TEXT_DOMAIN) ?>
                            </button>
                        </p>
                    </form>
                </div>
            </div>
        </div>