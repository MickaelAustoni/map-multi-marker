<?php
// If add or edit map
if ($this->isEditMap || $this->isCreateMap) {
    ?>
    <div class="wrap">
        <?php if (empty($this->apiSetting['api_key']) || $this->apiSetting['api_key'] === MapMultiMarker::DEFAULT_API_KEY): ?>
            <div class="error notice">
                <p><strong><?php echo __('Now, before you start using Map Multi Marker, please note that it is necessary to register your API key', MapMultiMarker::TEXT_DOMAIN
                            ) . ' ' . __('to work properly.', MapMultiMarker::TEXT_DOMAIN
                            ) ?></strong></p>
            </div>
        <?php endif; ?>
        <div class="head-map-detail">
            <div class="row">
                <div class="col-md-4">
                    <a href="?page=map-multi-marker-page-manage" class="button"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i> <?php _e("Back", MapMultiMarker::TEXT_DOMAIN) ?>
                    </a>
                </div>
                <div class="col-md-4">
                    <div id="map-name-detail"></div>
                </div>
                <div class="col-md-4">
                    <div class="pull-right">
                        <div class="button copy-shortcode" data-id-copy="<?php echo $this->currentMapId ?>" title="<?php _e("Click to copy the shotcode", MapMultiMarker::TEXT_DOMAIN) ?>">
                            <?php _e("Copy", MapMultiMarker::TEXT_DOMAIN) ?> : <b>[map-multi-marker id="<?php echo $this->currentMapId ?>"]</b>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if (isset($_POST['create_marker'])) {
            // Display message
            MapMultiMarker::instance()->mmm_alert_message(__('Your marker been created.', MapMultiMarker::TEXT_DOMAIN), 'updated');
        } elseif (isset($_POST['valid_edition'])) {
            // Display message
            MapMultiMarker::instance()->mmm_alert_message(__('Your marker been edited.', MapMultiMarker::TEXT_DOMAIN), 'updated');
        } elseif (isset($_POST['submit_delete'])) {
            MapMultiMarker::instance()->mmm_alert_message(__('Your marker been deleted.', MapMultiMarker::TEXT_DOMAIN), 'updated');
        }
        ?>
    </div>
    <?php
    include_once MapMultiMarker::instance()->plugin_path . 'template/admin/partials/options.php';
    include_once MapMultiMarker::instance()->plugin_path . 'template/admin/partials/markers.php';
} else { // Display map list
    global $wpdb;
    
    // If nonce
    if (isset($_POST['securite_nonce_delete_map'])) {
        
        // Check nonce
        if (wp_verify_nonce($_POST['securite_nonce_delete_map'], 'securite-nonce')) {
            
            //Requete des marqueur de la map selectionné
            $wpdb->delete($wpdb->prefix . 'mapmarker_marker', ['marker_id' => $_POST['id_delete_map']], $where_format = null);
            
            //Requete des options de la map selectionné
            $wpdb->delete($wpdb->prefix . 'mapmarker_option', ['map_id' => $_POST['id_delete_map']], $where_format = null);
            
            //Affiche le message d'alert
            echo '<div class="wrap">';
            MapMultiMarker::instance()->mmm_alert_message(__('Your map been deleted.', MapMultiMarker::TEXT_DOMAIN), 'updated');
            echo '</div>';
        }
    }
    
    $map_id = $wpdb->get_results("SELECT map_id, map_name FROM " . $wpdb->prefix . 'mapmarker_option', ARRAY_A);
    
    ?>
    <div class="wrap">
        <?php if (empty($this->apiSetting['api_key']) || $this->apiSetting['api_key'] === MapMultiMarker::DEFAULT_API_KEY): ?>
            <div class="error notice">
                <p><strong><?php echo __('Now, before you start using Map Multi Marker, please note that it is necessary to register your API key', MapMultiMarker::TEXT_DOMAIN
                            ) . ' ' . __('to work properly.', MapMultiMarker::TEXT_DOMAIN
                            ) ?></strong></p>
            </div>
        <?php endif; ?>

        <h2><?php _e("Manage Map", MapMultiMarker::TEXT_DOMAIN) ?></h2>
        <p></p>
        <table class="widefat" id="table_map">
            <thead>
            <tr>
                <th><strong><?php _e("Map Name", MapMultiMarker::TEXT_DOMAIN) ?></strong></th>
                <th><strong><?php _e("Shortcode", MapMultiMarker::TEXT_DOMAIN) ?></strong></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($map_id as $value): ?>
                <tr>
                    <td><?php echo $value['map_name'] ?></td>
                    <td>
                        <div>
                            <pre>[map-multi-marker id="<?php echo $value['map_id'] ?>"]</pre>
                        </div>
                        <a href="#" class="copy-shortcode"
                           data-id-copy="<?php echo $value['map_id'] ?>"><?php _e('Copy', MapMultiMarker::TEXT_DOMAIN) ?></a>
                    </td>
                    <td>
                        <a href="?page=map-multi-marker-page-manage&edit_map&map_id=<?php echo $value['map_id'] ?>" class="button-primary">
                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i> <?php _e("Edit", MapMultiMarker::TEXT_DOMAIN) ?>
                        </a>
                        <button id="delete_map" name="delete_map" class="button-secondary" value="<?php echo $value['map_id'] ?>">
                            <i class="fa fa-times" aria-hidden="true"></i> <?php _e("Delete", MapMultiMarker::TEXT_DOMAIN) ?>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <td>
                    <a href="?page=map-multi-marker-page-manage&create_map&map_id=<?php $this->mmm_get_new_id(); ?>" class="button-primary button-success">
                        <i class="fa fa-map-o" aria-hidden="true"></i> <?php _e("Create new map", MapMultiMarker::TEXT_DOMAIN) ?>
                    </a>
                </td>
                <td></td>
                <td></td>
            </tr>
            </tfoot>
        </table>
    </div>
    <?php
}