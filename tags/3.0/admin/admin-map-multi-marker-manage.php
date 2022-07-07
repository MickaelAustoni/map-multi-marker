<?php

global $wpdb;

checkPersonalApiKey();

//Script for copy with Clipboard
if(isset($_GET[ 'create_map' ]) || isset($_GET[ 'edit_map' ]) || isset($_GET[ 'create_map' ])){
    ?>
    <script type="text/javascript">
        jQuery(function () {

            //FONCTION POUR COPIER LE SHORTCODE
            //Get l'id du shortcode clické et l'ajoute dans le shortcode
            new Clipboard('.copy-shortcode', {
                text: function (trigger) {
                    return '[map-multi-marker id="' + trigger.getAttribute('data-id-copy') + '"]';
                }
            });

        });
    </script>
    <?php
}

//Si on crée une nouvelle map on insert une nouvelle row en bdd et on ajoute les options par default
if(isset($_GET[ 'create_map' ])){
    global $wpdb;
    global $mapmarker_info;

    //Get tout les "map_id" stocké en bdd et les store dans un array multidimensionnel
    $get_map_id = $wpdb->get_results("SELECT map_id FROM " . $wpdb->prefix . 'mapmarker_option', ARRAY_A);

    //Init. l'array
    $arr_map_id = array();

    //Boucle l'array multidimensionnel pour avoir un simple array des "map_id"
    foreach($get_map_id as $result){
        $arr_map_id[] = $result[ 'map_id' ];
    }

    //Si "$_GET['map_id']" n'est pas dans l'array "$arr_map_id" alors on crée une nouvelle row dans la base avec des options par default
    if(!in_array($_GET[ 'map_id' ], $arr_map_id)){
        $wpdb->insert($wpdb->prefix . 'mapmarker_option',
            array(
                'map_id'                 => $_GET[ 'map_id' ],
                'map_name'               => __('Untitled map', 'map-multi-marker') . ' ' . $_GET[ 'map_id' ],
                'height_map'             => $mapmarker_info[ 'default_height_map' ],
                'height_valeur_type'     => $mapmarker_info[ 'default_height_valeur_type' ],
                'width_map'              => $mapmarker_info[ 'default_width_map' ],
                'width_valeur_type'      => $mapmarker_info[ 'default_width_valeur_type' ],
                'streetview'             => $mapmarker_info[ 'default_streetview' ],
                'maptype'                => $mapmarker_info[ 'default_maptype' ],
                'zoom'                   => $mapmarker_info[ 'default_zoom' ],
                'scrollwheel'            => $mapmarker_info[ 'default_scrollwheel' ],
                'latitude_initial'       => $mapmarker_info[ 'default_latitude_initial' ],
                'longitude_initial'      => $mapmarker_info[ 'default_longitude_initial' ],
                'fiels_to_display'       => $mapmarker_info[ 'default_fiels_to_display' ],
                'default_desc_img_url'   => 0,
                'default_marker_img_url' => 0
            )
        );
    }
}

//Si on edit/crée un nouvelle map
if(isset($_GET[ 'edit_map' ]) || isset($_GET[ 'create_map' ])){
    ?>
    <div class="wrap">
        <div class="head-map-detail">
            <div class="row">
                <div class="col-md-4">
                    <a href="?page=map-multi-marker" class="button"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i> <?php _e("Back", "map-multi-marker") ?>
                    </a>
                </div>
                <div class="col-md-4">
                    <div id="map-name-detail"></div>
                </div>
                <div class="col-md-4">
                    <div class="pull-right">
                        <div class="button copy-shortcode" data-id-copy="<?php echo $_GET[ 'map_id' ] ?>" title="<?php _e("Click to copy the shotcode", "map-multi-marker") ?>">
                            <?php _e("Copy", "map-multi-marker") ?> : <b>[map-multi-marker
                                id="<?php echo $_GET[ 'map_id' ] ?>"]</b>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php

    // Charge les options de la map
    include_once dirname(__DIR__) . '/admin/admin-map-multi-marker-option.php';

    // Charge les marqueurs de la map
    include_once dirname(__DIR__) . '/admin/admin-map-multi-marker.php';
} //Sinon on affiche la liste des diffrente maps déjà crée
else{
    global $wpdb;

    //Si securite_nonce est posté
    if(isset($_POST[ 'securite_nonce_delete_map' ])){

        //Vérification du nonce
        if(wp_verify_nonce($_POST[ 'securite_nonce_delete_map' ], 'securite-nonce')){

            //Requete des marqueur de la map selectionné
            $wpdb->delete($wpdb->prefix . 'mapmarker_marker', array('marker_id' => $_POST[ 'id_delete_map' ]), $where_format = NULL);

            //Requete des options de la map selectionné
            $wpdb->delete($wpdb->prefix . 'mapmarker_option', array('map_id' => $_POST[ 'id_delete_map' ]), $where_format = NULL);

            //Affiche le message d'alert
            echo '<div class="wrap">';
            mapmarker_message_alert(__('Your map been deleted.', 'map-multi-marker'), 'updated');
            echo '</div>';
        }
    }

    $map_id = $wpdb->get_results("SELECT map_id, map_name FROM " . $wpdb->prefix . 'mapmarker_option', ARRAY_A);

    ?>
    <div class="wrap">
        <h2><?php _e("Manage Map", "map-multi-marker") ?></h2>
        <p></p>
        <table class="widefat" id="table_map">
            <thead>
            <tr>
                <th><strong><?php _e("Map Name", "map-multi-marker") ?></strong></th>
                <th><strong><?php _e("Shortcode", "map-multi-marker") ?></strong></th>
                <th></th>
            </tr>

            </thead>
            <tbody>
            <?php foreach($map_id as $value): ?>
                <tr>
                    <td><?php echo $value[ 'map_name' ] ?></td>
                    <td>
                        <div>
                            <pre>[map-multi-marker id="<?php echo $value[ 'map_id' ] ?>"]</pre>
                        </div>
                        <a href="#" class="copy-shortcode"
                           data-id-copy="<?php echo $value[ 'map_id' ] ?>"><?php _e('Copy', 'map-multi-marker') ?></a>
                    </td>
                    <td>
                        <a href="?page=map-multi-marker&edit_map&map_id=<?php echo $value[ 'map_id' ] ?>" class="button-primary"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> <?php _e("Edit", "map-multi-marker") ?>
                        </a>
                        <button id="delete_map" name="delete_map" class="button-secondary" value="<?php echo $value[ 'map_id' ] ?>">
                            <i class="fa fa-times" aria-hidden="true"></i> <?php _e("Delete", "map-multi-marker") ?>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <td>
                    <a href="?page=map-multi-marker&create_map&map_id=<?php mapMarkerGetNewMapId(); ?>" class="button-primary button-success"><i class="fa fa-map-o" aria-hidden="true"></i> <?php _e("Create new map", "map-multi-marker") ?>
                    </a></td>
                <td></td>
                <td></td>
            </tr>
            </tfoot>
        </table>
    </div>

    <script type="text/javascript">

        jQuery(function () {

            //CALL AJAX DU DU BOUTTON SUPPRIMÉ
            jQuery("#table_map").each(function (index) {

                // Au click du bouton "Supprimer"
                jQuery("#table_map  #delete_map").click(function () {
                    // Récupère l'id clické
                    var id = jQuery(this).val();

                    // FONCTION AJAX
                    jQuery.ajax({
                        method: "POST",
                        url: "<?php echo admin_url('admin-ajax.php')?>",
                        dataType: "html",
                        data: {
                            action: "mapMarkerModalDelete",
                            id: id,
                            modal: 'delete_map'
                        },

                        success: function (data) {
                            jQuery('body').prepend(data);
                        }
                    });//End AJAX

                    return false;
                });
            }); //END CALL AJAX DU DU BOUTTON SUPPRIMÉ


            //FONCTION POUR COPIER LE SHORTCODE
            //Annule l'action du click du lien
            jQuery(".copy-shortcode").click(function (event) {
                event.preventDefault();
            });
            //Get l'id du shortcode clické et l'ajoute dans le shortcode
            new Clipboard('.copy-shortcode', {
                text: function (trigger) {
                    return '[map-multi-marker id="' + trigger.getAttribute('data-id-copy') + '"]';
                }
            });

        });

    </script>
    <?php
}