<?php


//FONCTION POUR CRÉE LES TABLE À L'ACTIVATION DU PLUGIN
function mapmarker_plugin_activation(){
    global $wpdb;

    //Load le 'upgrade.php' pour le call de la fonction dbDelta()
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    //Encodage
    $charset_collate = $wpdb->get_charset_collate();

    //Nom des tables
    $table_option = $wpdb->prefix . 'mapmarker_option';
    $table_marker = $wpdb->prefix . 'mapmarker_marker';
    $table_api    = $wpdb->prefix . 'mapmarker_api';


    // Req. de création de table pour le "options"
    $sql1
        = "CREATE TABLE IF NOT EXISTS $table_option (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    map_id INT(11) NOT NULL,
    map_name VARCHAR(255) NOT NULL,
    height_map INT(11) NOT NULL,
    height_valeur_type CHAR(2) NOT NULL,
    width_map INT(11) NOT NULL,
    width_valeur_type CHAR(2) NOT NULL,
    streetview INT(1) NOT NULL,
    maptype VARCHAR(50) DEFAULT '' NOT NULL,
    zoom INT(2) NOT NULL,
    lightbox INT(0) NOT NULL,
    scrollwheel INT(1) NOT NULL,
    latitude_initial DECIMAL(10, 8) NOT NULL,
    longitude_initial DECIMAL(11, 8) NOT NULL,
    fiels_to_display VARCHAR(50) NOT NULL,
    default_marker_img_url VARCHAR(255) DEFAULT '' NOT NULL,
    default_desc_img_url VARCHAR(255) DEFAULT '' NOT NULL,
    UNIQUE KEY id (id)
    ) $charset_collate;";
    // Call dbDelta pour la requete
    dbDelta($sql1);


    // Req de selection sql
    $check_option = $wpdb->get_results("SELECT * FROM " . $table_option, ARRAY_A);

    // Si la requete est vide on inseret les option par default
    if(empty($check_option)){
        // Insert les valeur des options par defaut en bdd
        $wpdb->insert($table_option,
            array(
                'map_id'                 => '1',
                'map_name'               => __('Example map', 'map-multi-marker'),
                'height_map'             => '500',
                'height_valeur_type'     => 'px',
                'width_map'              => '100',
                'width_valeur_type'      => '%',
                'streetview'             => 0,
                'maptype'                => 'TERRAIN',
                'zoom'                   => '5',
                'lightbox'               => 0,
                'scrollwheel'            => 1,
                'latitude_initial'       => '46.437857',
                'longitude_initial'      => '2.570801',
                'fiels_to_display'       => 'image,titre,description,adresse,telephone,weblink',
                'default_marker_img_url' => 0,
                'default_desc_img_url'   => 0,
            )
        );
    }


    // Req. de création de table pour les "marqueurs"
    $sql2
        = "CREATE TABLE IF NOT EXISTS $table_marker (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    marker_id INT(11) NOT NULL,
    titre VARCHAR(50) DEFAULT '' NOT NULL,
    description VARCHAR(255) DEFAULT '' NOT NULL,
    adresse VARCHAR(255) DEFAULT '' NOT NULL,
    telephone VARCHAR(20) DEFAULT '' NOT NULL,
    weblink VARCHAR(255) DEFAULT '' NOT NULL,
    img_desc_marker VARCHAR(255) DEFAULT '0' NOT NULL,
    img_icon_marker VARCHAR(255) DEFAULT '0' NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    UNIQUE KEY id (id)
    ) $charset_collate;";
    // Call dbDelta pour la requete
    dbDelta($sql2);


    // Req de selection sql
    $check_marqueur = $wpdb->get_results("SELECT * FROM " . $table_marker, ARRAY_A);

    // Si la requete est vide on insert un marqueurs par default
    if(empty($check_option)){
        // Insert un marqueur par defaut en bdd
        $wpdb->insert($table_marker, array(
            'marker_id'       => '1',
            'titre'           => __("Eiffel Tower", "map-multi-marker"),
            'description'     => __("Constructed in 1889 as the entrance to the 1889 World's Fair, it was initially criticized by some of France's leading artists and intellectuals for its design, but it has become a global cultural icon of France...", "map-multi-marker"),
            'adresse'         => 'Champ de Mars, 5 Avenue Anatole',
            'telephone'       => '0123456789',
            'weblink'         => 'http://www.toureiffel.paris/en/home.html',
            'img_desc_marker' => 0,
            'img_icon_marker' => 0,
            'latitude'        => '48.8583701',
            'longitude'       => '2.2922926'
        ));
    }


    // Req. de création de table pour l'API de google
    $sql3
        = "CREATE TABLE IF NOT EXISTS $table_api (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    apikey VARCHAR(255) DEFAULT '' NOT NULL,
    language VARCHAR(10) DEFAULT '' NOT NULL,
    UNIQUE KEY id (id)
    ) $charset_collate;";
    // Call dbDelta pour la requete
    dbDelta($sql3);


    // Req de selection sql
    $check_apikey = $wpdb->get_results("SELECT apikey FROM " . $table_api, ARRAY_A);

    // Si la requete est vide on inseret les option par default
    if(empty($check_apikey)){
        // Insert une valeur vide
        $wpdb->insert($table_api, array(
            'apikey'   => '',
            'language' => mapmarkerGetLanguage()
        ));
    }

    //Ajoute les version d'upgrade
    update_option('map_multi_marker_add_table_2_1', TRUE);
    update_option('map_multi_marker_add_table_2_2', TRUE);
    update_option('map_multi_marker_add_table_2_3', TRUE);
    update_option('map_multi_marker_add_table_2_4', TRUE);
    update_option('map_multi_marker_add_table_2_9', TRUE);
}


//FONCTION CALL QUAND LES TOUS LES PLUGINS SONT CHARGÉ
function mapmarker_plugin_loaded(){
    global $mapmarker_info;

    //Ajoute la version du plugin dans les options de wordpress en bdd
    if(get_option('map_multi_marker_version') == FALSE OR get_option('map_multi_marker_version') != $mapmarker_info[ 'version' ]){
        update_option('map_multi_marker_version', $mapmarker_info[ 'version' ]);
    }

    //Création des news tables de la version 2.1
    if(get_option('map_multi_marker_add_table_2_1') == FALSE){
        global $wpdb;

        //Nom des tables
        $table_option = $wpdb->prefix . 'mapmarker_option';
        $table_marker = $wpdb->prefix . 'mapmarker_marker';

        //Crée les nouvelle table de la version 2.1
        $wpdb->query("ALTER TABLE $table_marker ADD marker_id VARCHAR(255) NOT NULL DEFAULT 1 AFTER id");
        $wpdb->query("ALTER TABLE $table_option ADD map_id INT(11) NOT NULL DEFAULT 1 AFTER id");
        $wpdb->query("ALTER TABLE $table_option ADD map_name VARCHAR(255) NOT NULL DEFAULT 'Example map' AFTER map_id");

        update_option('map_multi_marker_add_table_2_1', TRUE);
    }

    //Création de colonne de la version 2.2
    if(get_option('map_multi_marker_add_table_2_2') == FALSE){
        global $wpdb;

        //Nom de la table
        $table_option = $wpdb->prefix . 'mapmarker_option';

        //Ajoute la colonne de la version 2.2
        $wpdb->query("ALTER TABLE $table_option ADD scrollwheel INT(1) NOT NULL DEFAULT 1 AFTER zoom");

        update_option('map_multi_marker_add_table_2_2', TRUE);
    }

    //Création de colonne de la version 2.3
    if(get_option('map_multi_marker_add_table_2_3') == FALSE){
        global $wpdb;

        //Nom de la table
        $table_option = $wpdb->prefix . 'mapmarker_option';

        //Ajoute la colonne de la version 2.3
        $wpdb->query("ALTER TABLE $table_option ADD default_marker_img_url VARCHAR(255) DEFAULT '0' NOT NULL");
        $wpdb->query("ALTER TABLE $table_option ADD default_desc_img_url VARCHAR(255) DEFAULT '0' NOT NULL");

        update_option('map_multi_marker_add_table_2_3', TRUE);
    }

    //Création de colonne de la version 2.4 (add lightbox col)
    if(get_option('map_multi_marker_add_table_2_4') == FALSE){
        global $wpdb;

        //Nom de la table
        $table_option = $wpdb->prefix . 'mapmarker_option';

        //Ajoute la colonne de la version 2.4
        $wpdb->query("ALTER TABLE $table_option ADD lightbox INT(1) NOT NULL DEFAULT 0 AFTER zoom");

        update_option('map_multi_marker_add_table_2_4', TRUE);
    }

    //Modify columns version 2.9
    if(get_option('map_multi_marker_add_table_2_9') == FALSE){
        global $wpdb;

        //Nom de la table
        $table_marker = $wpdb->prefix . 'mapmarker_marker';

        //Modify columns version 2.9
        $wpdb->query("ALTER TABLE $table_marker CHANGE COLUMN `telephone` `telephone` VARCHAR(20) NOT NULL DEFAULT '' , CHANGE COLUMN `img_desc_marker` `img_desc_marker` VARCHAR(255) NOT NULL DEFAULT 0 ,CHANGE COLUMN `img_icon_marker` `img_icon_marker` VARCHAR(255) NOT NULL DEFAULT 0");


        update_option('map_multi_marker_add_table_2_9', TRUE);
    }

    //Charge le dossiser de language
    load_plugin_textdomain('map-multi-marker', FALSE, plugin_basename(dirname(dirname(__FILE__))) . '/language');
}


//CRÉATION DU MENU ADMIN DU PLUGIN
function mapmarker_admin_menu(){
    // Add. le menu principal
    //add_menu_page('Map Multi Marker', 'Map Multi Marker', 'manage_options', 'map-multi-marker-menu', '', 'dashicons-location-alt');
    add_menu_page('Map Multi Marker', __('Map Multi Marker'), 'manage_options', 'map-multi-marker', 'mapmarker_manage', 'dashicons-location-alt');
    // Add. le submenu
    add_submenu_page('map-multi-marker', __('Settings Map Multi Marker', 'map-multi-marker'), __('Settings', 'map-multi-marker'), 'manage_options', 'map-multi-marker-settings', 'mapmarker_settings');
    add_submenu_page('map-multi-marker', __('Help Map Multi Marker', 'map-multi-marker'), __('Help', 'map-multi-marker'), 'manage_options', 'map-multi-marker-help', 'mapmarker_help');
}

add_action('admin_menu', 'mapmarker_admin_menu');


//FONCTION POUR CHARGER DES SCRIPTS SUR L'ADMIN
function mapmarker_admin_script(){

    //Si la varible $page n'existe pas alors on l'init.
    if(!isset($_GET[ 'page' ])){
        $_GET[ 'page' ] = NULL;
    }

    // Enregistre les script & css
    wp_register_style('fontawesome-css', MMM_URL . 'css/font-awesome.min.css');
    wp_register_style('mmm-admin-css', MMM_URL . 'css/admin.css');
    wp_register_script('mmm-admin-js', MMM_URL . 'js/admin.js', array('jquery'), FALSE, TRUE);
    wp_register_script('bootstrap-min-js', MMM_URL . 'js/bootstrap.min.js', array('jquery'), FALSE, TRUE);
    wp_register_script('clipboard-js', MMM_URL . 'js/clipboard.min.js', array('jquery'));

    // Localize the script with new data fo translation into JS
    $translation_array = array(
        'msg_error_empty'     => __('Please choose a file.', 'map-multi-marker'),
        'mssg_error_required' => __('Required fields.', 'map-multi-marker')
    );
    wp_localize_script('mmm-admin-js', '_e', $translation_array);

    //Load sur toute les page du plugin
    if($_GET[ 'page' ] == 'map-multi-marker' OR $_GET[ 'page' ] == 'map-multi-marker-settings' OR $_GET[ 'page' ] == 'map-multi-marker-manage' OR $_GET[ 'page' ] == 'map-multi-marker-help'){
        // Load le script & css
        //Load boostrap
        wp_enqueue_style('fontawesome-css');
        wp_enqueue_style('mmm-admin-css');
        wp_enqueue_script('mmm-admin-js');
    }
    // Si les page est "map-multi-marker"
    if($_GET[ 'page' ] == 'map-multi-marker'){
        // Load le script pour copier le shortcode au click
        wp_enqueue_script('clipboard-js');
    }

    // Si les page est un edition ou une création de map
    if($_GET[ 'page' ] == 'map-multi-marker' AND isset($_GET[ 'edit_map' ]) OR $_GET[ 'page' ] == 'map-multi-marker' AND isset($_GET[ 'create_map' ])){
        //Load boostrap
        wp_enqueue_script('bootstrap-min-js');
    }

    //Si la variable $page contient le text domaine (map-multi-marker) et que la page est contient la variable create_map ou edit_map
    if(isset($_GET[ 'page' ]) AND isset($_GET[ 'create_map' ]) OR isset($_GET[ 'page' ]) AND isset($_GET[ 'edit_map' ])){
        //Load les script pour pouvoir utiliser l'API du media uploader de wordpress
        wp_enqueue_media();
        //Load le script perso pour init le media uploader
        add_action('admin_print_footer_scripts', 'mapmarker_modal_media_selector');

    }
}

add_action('admin_enqueue_scripts', 'mapmarker_admin_script');


//FONCTION POUR CHARGER DES SCRIPTS SUR LE FRONT
function mapmarker_front_script($shorcode_id){
    global $wpdb;

    wp_enqueue_script('mmm-googlemap-js', 'https://maps.googleapis.com/maps/api/js?key=' . mapmarkerGetApiKey() . '&language=' . mapmarkerGetLanguage(), FALSE, NULL, FALSE);

    if($shorcode_id){
        $option = $wpdb->get_results("SELECT lightbox FROM " . $wpdb->prefix . 'mapmarker_option WHERE map_id="' . $shorcode_id . '"', ARRAY_A);

        // Enqueue Script & Style
        wp_enqueue_style('mmm-front-css', MMM_URL . 'css/front.css');
        wp_enqueue_style('fontawesome-css', MMM_URL . 'css/font-awesome.min.css');

        //If light box option is checked
        if($option[ 0 ][ 'lightbox' ] == 1){
            wp_enqueue_style('featherlight-css', MMM_URL . 'css/featherlight.min.css');
            wp_enqueue_script('featherlight-js', MMM_URL . 'js/featherlight.min.js', array('jquery'));
        }
    }
}

add_action('wp_enqueue_scripts', 'mapmarker_front_script');


// RÉCUPÈRE LA CLÉ API DE GOOGLE
function mapmarkerGetApiKey(){
    global $wpdb;

    $api = $wpdb->get_results("SELECT apikey FROM " . $wpdb->prefix . 'mapmarker_api', ARRAY_A);

    //Si l'api key est vide alors on charge l'api key par default
    if(empty($api[ 0 ][ 'apikey' ])){
        global $mapmarker_info;

        return $mapmarker_info[ 'default_api_key' ];
    } // Sinon on charge sont api key perso
    else{
        return $api[ 0 ][ 'apikey' ];
    }
}


//RÉCUPÈRE LA LANGUE
function mapmarkerGetLanguage(){
    global $wpdb;

    $api = $wpdb->get_results("SELECT language FROM " . $wpdb->prefix . 'mapmarker_api', ARRAY_A);

    // Si la requete est vide on return le language locale
    if(empty($api)){
        return substr(get_locale(), 0, 2);
    } // Sinon on return la valeur stocké en bdd
    else{
        return $api[ 0 ][ 'language' ];
    }
}


//FONCTION POUR RÉCUPÉRER ET PRÉPARER LES OPTIONS POUR LES INTERGRER DANS LE JAVASCRIPT
function mapmarker_get_options($shorcode_attribut, $options = FALSE){
    global $wpdb;

    // Option de base pour init la map google
    if($options == 'base_map'){
        // Requete SQL et stock dans un tableau associatif avec "ARRAY_A"
        $data = $wpdb->get_results("SELECT latitude_initial, longitude_initial, maptype, streetview, zoom, scrollwheel FROM " . $wpdb->prefix . 'mapmarker_option WHERE map_id="' . $shorcode_attribut . '"', ARRAY_A);

        //Si streetview est egal à 0 on change la valeur en true
        if($data[ 0 ][ 'streetview' ] == 0){
            $data[ 0 ][ 'streetview' ] = 'true';
        } else{
            $data[ 0 ][ 'streetview' ] = 'false';
        }

        // Récup les donné du tableau multidimensionel et les formate pour l'API Google
        $base_map
            = '{
        center:{lat:' . $data[ 0 ][ 'latitude_initial' ] . ', lng:' . $data[ 0 ][ 'longitude_initial' ] . '},
        mapTypeId:google.maps.MapTypeId.' . $data[ 0 ][ 'maptype' ] . ',
        streetViewControl:' . $data[ 0 ][ 'streetview' ] . ',
        scrollwheel:' . $data[ 0 ][ 'scrollwheel' ] . ',
        zoom:' . $data[ 0 ][ 'zoom' ]
            . '}';

        echo $base_map;
    }

    // Option pour la taille de la map
    if($options == 'size_map'){
        // Requete SQL et stock dans un tableau associatif avec "ARRAY_A"
        $data = $wpdb->get_results("SELECT height_map, width_map, height_valeur_type, width_valeur_type FROM " . $wpdb->prefix . 'mapmarker_option WHERE map_id="' . $shorcode_attribut . '"', ARRAY_A);

        $size_map = 'style="height:' . $data[ 0 ][ 'height_map' ] . $data[ 0 ][ 'height_valeur_type' ] . ';width:' . $data[ 0 ][ 'width_map' ] . $data[ 0 ][ 'width_valeur_type' ] . '"';

        echo $size_map;
    }
}


//FONCTIUON POUR RÉCUPÉRER ET PRÉPARER LES MARKERS POUR LES INTERGRER DANS LE JAVASCRIPT
function mapmarker_get_makers($shorcode_attribut){

    global $wpdb;
    global $mapmarker_info;


    // Requete SQL et stock dans un tableau associatif avec "ARRAY_A"
    $data = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . 'mapmarker_marker WHERE marker_id="' . $shorcode_attribut . '"', ARRAY_A);

    // Start de la chaine (bracket open)
    $marker = '[';

    //Si il y à un ou plusieur marqueur présent en bdd
    if(!empty($data)){
        // Boucle
        foreach($data as $result){
            // Assignation concaténant des data bouclé
            $marker
                .= "{
	        id:" . $result[ 'id' ] . ",
	        icon:" . '"' . mapmarker_get_image_marker_src($result[ 'img_icon_marker' ]) . '"' . ",
	        lat:" . $result[ 'latitude' ] . ",
	        lng:" . $result[ 'longitude' ] . "},";
        };

        // Supprime la dernière virgule
        $marker = substr($marker, 0, -1);
    }


    // Assignation concaténant la fermeture du bracket
    $marker .= ']';

    echo $marker;

}


//FONCTION QUI RECHERCHE SI LA VALEUR STOCKÉ EN BASE EST PRÉSENTE ET LA COCHE
function mapmarker_check_checked($str, $findme){
    // Recherche la chaine($findme) dans une chaine($str)
    $pos = strpos($str, $findme);

    // Si la chaine est trouvé, on coche le selecteur
    if($pos !== FALSE){
        echo 'checked';
    }
}


//FONCTION QUI RECHERCHE SI LA VALEUR STOCKÉ EN BASE EST PRÉSENTE ET LA COCHE
function mapmarker_get_checked_field($str, $findme){
    // Recherche la chaine($findme) dans une chaine($str)
    $pos = strpos($str, $findme);

    // Si la chaine est trouvé, on coche le selecteur
    if($pos !== FALSE){
        return TRUE;
    } else{
        return FALSE;
    }
}


//GET LE CONTENU DES MARQUEURS
function mapmarkerMarkerContent(){
    global $wpdb;

    $map_id = $wpdb->get_results("SELECT marker_id FROM " . $wpdb->prefix . 'mapmarker_marker WHERE id="' . $_POST[ "id" ] . '"', ARRAY_A);

    // Requete SQL et stock dans un tableau associatif avec "ARRAY_A" pour récupe les champs selectionné dans les option de la map afficher sur la page
    $option = $wpdb->get_results("SELECT fiels_to_display, lightbox FROM " . $wpdb->prefix . 'mapmarker_option WHERE map_id="' . $map_id[ 0 ][ 'marker_id' ] . '"', ARRAY_A);

    // Requete SQL et stock dans un tableau associatif avec "ARRAY_A" pour récup les marqueurs de la map afficher
    $data = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . 'mapmarker_marker WHERE id="' . $_POST[ 'id' ] . '"', ARRAY_A);


    //Si le champ de "l'image" est coché
    if(mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'image') == TRUE){
        // if lightbox is checked
        if($option[ 0 ][ 'lightbox' ] == 1){
            ?>
            <a href="<?php echo mapmarker_get_image_desc_src($data[ 0 ][ 'img_desc_marker' ], 'large') ?>"
               data-featherlight="image"><img
                    src="<?php echo mapmarker_get_image_desc_src($data[ 0 ][ 'img_desc_marker' ]) ?>" alt=""
                    class="img-in-marqueur <?php if(mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'image') == TRUE AND mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'titre') == FALSE AND mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'description') == FALSE AND mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'adresse') == FALSE AND mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'telephone') == FALSE AND mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'weblink') == FALSE){
                        echo "full-width-desc";
                    } ?>"></a>
            <?php
        } else{
            ?>
            <img src="<?php echo mapmarker_get_image_desc_src($data[ 0 ][ 'img_desc_marker' ]) ?>" alt=""
                 class="img-in-marqueur <?php if(mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'image') == TRUE AND mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'titre') == FALSE AND mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'description') == FALSE AND mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'adresse') == FALSE AND mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'telephone') == FALSE AND mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'weblink') == FALSE){
                     echo "full-width-desc";
                 } ?>">
            <?php
        }
    }

    //Si au moins un champs est coché dans la desc

    if(mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'titre') == TRUE OR mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'description') == TRUE OR mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'adresse') == TRUE OR mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'telephone') == TRUE OR mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'weblink') == TRUE){
        ?>
        <!-- Description des marqueurs -->
        <div
            class="wrap-desc-markeur <?php if(mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'image') == FALSE){
                echo "full-width-desc-markeur";
            } ?>">
            <!-- Si le champ du "titre" est coché -->
            <?php if(mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'titre') == TRUE AND !empty($data[ 0 ][ 'titre' ])): ?>
                <h2><?php echo html_entity_decode(esc_html($data[ 0 ][ 'titre' ])) ?></h2>
            <?php endif ?>

            <!-- Si le champ de la "description" est coché -->
            <?php if(mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'description') == TRUE AND !empty($data[ 0 ][ 'description' ])): ?>
                <p class="description-marker"><?php echo html_entity_decode(esc_textarea($data[ 0 ][ 'description' ])) ?></p>
            <?php endif ?>

            <!-- Si le champ de la "adresse" ou "telephone" est coché -->
            <?php if(mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'adresse') == TRUE OR mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'telephone') == TRUE OR mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'weblink') == TRUE): ?>
                <ul class="contact-list">
                    <!-- Si le champ "adresse" est coché -->
                    <?php if(mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'adresse') == TRUE AND !empty($data[ 0 ][ 'adresse' ])): ?>
                        <li class="adresse">
                            <strong><?php echo html_entity_decode(esc_html($data[ 0 ][ 'adresse' ])) ?></strong></li>
                    <?php endif ?>
                    <!-- Si le champ de la "description" est coché -->
                    <?php if(mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'telephone') == TRUE AND !empty($data[ 0 ][ 'telephone' ])): ?>
                        <li class="telephone"><strong><a
                                    href="tel:<?php echo str_replace(' ', '', esc_html($data[ 0 ][ 'telephone' ])) ?>"><?php echo esc_html($data[ 0 ][ 'telephone' ], 2, ' ') ?></a></strong>
                        </li>
                    <?php endif ?>
                    <!-- Si le champ du "lien" est coché -->
                    <?php if(mapmarker_get_checked_field($option[ 0 ][ 'fiels_to_display' ], 'weblink') == TRUE AND !empty($data[ 0 ][ 'weblink' ])): ?>
                        <li class="weblink"><strong><a href="<?php echo esc_html($data[ 0 ][ 'weblink' ]) ?>"
                                                       target="_blank"><?php echo $data[ 0 ][ 'weblink' ] ?></a></strong>
                        </li>
                    <?php endif ?>
                </ul>
            <?php endif ?>
        </div>
        <?php
    }

    wp_die();
}

//Call la fonction AJAX de Wordpress
add_action('wp_ajax_mapmarkerMarkerContent', 'mapmarkerMarkerContent');
//Call la fonction AJAX de Worpress pour le front-end
add_action('wp_ajax_nopriv_mapmarkerMarkerContent', 'mapmarkerMarkerContent');


//FONCTION AJAX QUI CRÉE LES MARQUEURS
function mapMarkerCreateMarker(){

    global $wpdb;

    // Déclare la table
    $table_name = $wpdb->prefix . 'mapmarker_marker';

    // Supprime les antislashe pour eviter les bugs
    $_POST = stripslashes_deep($_POST);

    // Si les variable des champs envoyé n'existe pas
    if(!isset($_POST[ 'add_titre' ])){
        $_POST[ 'add_titre' ] = '';
    }
    if(!isset($_POST[ 'add_description' ])){
        $_POST[ 'add_description' ] = '';
    }
    if(!isset($_POST[ 'add_adresse' ])){
        $_POST[ 'add_adresse' ] = '';
    }
    if(!isset($_POST[ 'add_telephone' ])){
        $_POST[ 'add_telephone' ] = '';
    }
    if(!isset($_POST[ 'add_weblink' ])){
        $_POST[ 'add_weblink' ] = '';
    }
    if(!isset($_POST[ 'add_img_desc_marker' ])){
        $option                         = $wpdb->get_results("SELECT default_desc_img_url FROM " . $wpdb->prefix . 'mapmarker_option WHERE map_id="' . $_GET[ 'map_id' ] . '"', ARRAY_A);
        $_POST[ 'add_img_desc_marker' ] = $option[ 0 ][ 'default_desc_img_url' ];
    }

    // Req. insert sql
    $wpdb->insert($table_name,
        array(
            'marker_id'       => sanitize_text_field($_GET[ 'map_id' ]),
            'titre'           => sanitize_text_field($_POST[ 'add_titre' ]),
            'description'     => implode("\n", array_map('sanitize_text_field', explode("\n", $_POST[ 'add_description' ]))),
            'adresse'         => sanitize_text_field($_POST[ 'add_adresse' ]),
            'telephone'       => sanitize_text_field($_POST[ 'add_telephone' ]),
            'weblink'         => sanitize_text_field($_POST[ 'add_weblink' ]),
            'latitude'        => sanitize_text_field($_POST[ 'add_latitude' ]),
            'longitude'       => sanitize_text_field($_POST[ 'add_longitude' ]),
            'img_desc_marker' => sanitize_text_field($_POST[ 'add_img_desc_marker' ]),
            'img_icon_marker' => sanitize_text_field($_POST[ 'add_img_icon_marker' ])
        )
    );

    //Affiche le message d'alert
    mapmarker_message_alert(__('Your marker been created.', 'map-multi-marker'), 'updated');
}


//FONCTION POUR LE MODAL DE CONFIRMATION DE SUPPRESSION
function mapMarkerModalDelete(){
    //Si le modal est une suppression de map
    if($_POST[ 'modal' ] == 'delete_map'){
        ?>
        <div class="wrap-modal-parent">
            <div class="wrap-modal-child">
                <div class="modal-confirm">
                    <form action="" method="post">
                        <input type="hidden" name="securite_nonce_delete_map"
                               value="<?php echo wp_create_nonce('securite-nonce'); ?>"/>
                        <input type="hidden" name="id_delete_map" value="<?php echo $_POST[ 'id' ] ?>">
                        <h4><?php _e('Do you confirm the suppression of the map ?', 'map-multi-marker'); ?></h4>
                        <button id="cancel-supp"
                                class="button-secondary"><?php _e('Cancel', 'map-multi-marker'); ?></button>
                        <button type="submit" class="button-primary" name="submit_delete"
                                id="submit_delete"><?php _e('Confirm', 'map-multi-marker'); ?></button>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
    //Si le modal est une suppression de marker
    if($_POST[ 'modal' ] == 'delete_marker'){
        ?>
        <div class="wrap-modal-parent">
            <div class="wrap-modal-child">
                <div class="modal-confirm">
                    <form action="" method="post">
                        <input type="hidden" name="securite_nonce_delete_marker"
                               value="<?php echo wp_create_nonce('securite-nonce'); ?>"/>
                        <input type="hidden" name="id_delete_marker" value="<?php echo $_POST[ 'id' ] ?>">
                        <h4><?php _e('Do you confirm the suppression of the marker ?', 'map-multi-marker'); ?></h4>
                        <button id="cancel-supp"
                                class="button-secondary"><?php _e('Cancel', 'map-multi-marker'); ?></button>
                        <button type="submit" class="button-primary" name="submit_delete"
                                id="submit_delete"><?php _e('Confirm', 'map-multi-marker'); ?></button>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
    <script type="text/javascript">
        jQuery.noConflict();
        (function ($) {
            // slideDown du modal
            $('.modal-confirm').slideDown("fast");

            // Add. un display inline-blick en css
            $('.modal-confirm').css('display', 'inline-block');

            // Au click du boutton d'annulation on supprime le modal
            $('#cancel-supp').click(function () {
                $('.wrap-modal-parent').remove();
                return false;
            });
        })(jQuery);
    </script>
    <?php
}

add_action('wp_ajax_mapMarkerModalDelete', 'mapMarkerModalDelete');


//FUNCTION QUI OBTIENT L'ID D'UNE NOUVELLE MAP POUR LA CREATION
function mapMarkerGetNewMapId(){
    global $wpdb;

    //Req. sql
    $map_id = $wpdb->get_results("SELECT map_id FROM " . $wpdb->prefix . 'mapmarker_option', ARRAY_A);

    //Si il n'y a plus aucune map de crée, alors la next map crée aura une valeur de "1"
    if(empty($map_id[ 0 ][ 'map_id' ])){
        echo "1";
    } //Sinon on incremente de "1" l'id de map le plus elever
    else{
        //Déclare l'array
        $highest_id = array();

        //Récup les valeurs de map_id du tableau multidimensionel
        foreach($map_id as $value){
            $highest_id[] = $value[ 'map_id' ];
        }

        //Return le résultat et l'incrémente de 1
        echo $highest_id = max($highest_id) + 1;
    }


}


//AJOUTE LES LIENS DANS SUR LA PAGE DES PLUGINS
function mapMarker_add_action_links($links){
    global $mapmarker_info;

    $donate_link = array('<a style="font-weight:bold;color:#4CAF50;" href="' . $mapmarker_info[ 'donate_link' ] . '" target="_blank">' . __('Donate', 'map-multi-marker') . '</a>',);

    return array_merge($links, $donate_link);
}


//MESSAGE D'ALERT
function mapmarker_message_alert($message, $type){

    //add_settings_error( $setting, $code, $message, $type )
    add_settings_error('mapmarker_message_alert', '', $message, $type);

    //Affiche le message d'erreur avec les options de add_settings_error
    settings_errors('mapmarker_message_alert');
}


//RÉCUPÈRE LES IMAGES MARKER
function mapmarker_get_image_marker_src($id){
    if(is_numeric($id) AND $id != '0'){
        return wp_get_attachment_url($id);

    } else{
        if($id == '0'){
            global $mapmarker_info;

            return MMM_URL . $mapmarker_info[ 'default_marker_img_url' ];

        } else{
            return $id;
        }
    }
}


//RÉCUPÈRE LES IMAGES DESC
function mapmarker_get_image_desc_src($id, $size = NULL){
    if(is_numeric($id) AND $id != '0'){
        if($size == NULL){
            $tmp = wp_get_attachment_image_src($id);
            $url = $tmp[ 0 ];

            return $url;
        } else{
            $tmp = wp_get_attachment_image_src($id, $size);
            $url = $tmp[ 0 ];

            return $url;
        }
    } else{
        if($id == '0'){
            global $mapmarker_info;

            return MMM_URL . $mapmarker_info[ 'default_desc_img_url' ];
        } else{
            return $id;
        }
    }
}


//MODAL MEDIA SELECTEUR POUR UPLOAD
function mapmarker_modal_media_selector(){
    echo "<script type='text/javascript'>\n";

    echo "jQuery(function() {";

    //Au click de boutton pour upload une image
    echo "jQuery(document).on('click', '#upload_desc_img_button, #upload_marker_img_button, #edit_img_icon_marker_link.edit-active, #edit_img_desc_marker_link.edit-active, #add_icon_marker_link, #add_icon_desc_link' , function(event){";

    //Annule l'effet du click par default
    echo "event.preventDefault();";

    //Si on click sur l'icon dans l'option de la map
    echo "if(jQuery(this).is('#upload_desc_img_button, #upload_marker_img_button')){
					var select_url = jQuery(this).siblings('.default_img_preview');
					var select_id = jQuery(this).siblings('.default_img_id');
				}";

    //Si on click sur l'icone dans l'édition des marqueur
    echo "if(jQuery(this).is('#edit_img_icon_marker_link.edit-active, #edit_img_desc_marker_link.edit-active')){
					var select_url = jQuery(this).children('img');
					var select_id = jQuery(this).siblings('.edit_img_id');
				} ";

    //Si on click sur l'icon dans la création du markeur
    echo "if(jQuery(this).is('#add_icon_marker_link, #add_icon_desc_link')){
					var select_url = jQuery(this).children('img');
					var select_id = jQuery(this).parent().siblings('input');
				}";

    // Crée les media text de l'iframe
    echo "file_frame = wp.media.frames.file_frame = wp.media({";
    echo "title: " . wp_json_encode(__('Select a image to upload', 'map-multi-marker')) . ",";
    echo "button: {text: " . wp_json_encode(__('Use this image', 'map-multi-marker')) . ",},";
    echo "multiple: false";    // Set to true pour autorisé les multiple fichier selectionné
    echo "});";

    //Quand une image est selectionné
    echo "file_frame.on( 'select', function() {";
    // We set multiple to false so only get one image from the uploader
    echo "attachment = file_frame.state().get('selection').first().toJSON();";
    // Do something with attachment.id and/or attachment.url here
    echo "jQuery(select_url).attr( 'src', attachment.url );";
    echo "jQuery(select_id).val( attachment.id );";
    // Restore the main post ID
    echo "wp.media.model.settings.post.id = wp_media_post_id;";
    echo "});";

    //Open le modal
    echo "file_frame.open();";

    echo "});";//End Click function

    // Restore the main ID when the add media button is pressed
    echo "jQuery( 'a.add_media' ).on( 'click', function() {";
    echo "wp.media.model.settings.post.id = wp_media_post_id;";
    echo "});";

    echo "});";

    echo "\n</script>";
}


/**
 * CHECK API KEY
 *
 * @return bool
 */
function checkPersonalApiKey(){
    global $wpdb;
    global $mapmarker_info;

    //Get API key
    $apiKey = $wpdb->get_results("SELECT apikey FROM " . $wpdb->prefix . 'mapmarker_api', ARRAY_A);

    //If api key is not personal
    if($apiKey[ 0 ][ 'apikey' ] == '' || $apiKey[ 0 ][ 'apikey' ] == NULL || $apiKey[ 0 ][ 'apikey' ] == $mapmarker_info[ 'default_api_key' ]){
        ?>
        <div class="wrap">
            <div class="notice notice-error is-dismissible"><p>
                    <strong>
                        <?php
                        _e('Now, before you start using Map Multi Marker, please note that it is necessary to register your API key', 'map-multi-marker');
                        if($_GET[ 'page' ] != 'map-multi-marker-settings'){
                            echo ' <a href="?page=map-multi-marker-settings">' . __('on the setting page', 'map-multi-marker') . '</a> ';
                        } else{
                            echo ' ';
                        }
                        _e('to work properly.', 'map-multi-marker');
                        ?>
                    </strong>
                </p>
                <button type="button" class="notice-dismiss"><span class="screen-reader-text">Ne pas tenir compte de ce message.</span>
                </button>
            </div>
        </div>
        <?php
        return FALSE;
    } else{
        return TRUE;
    }
}