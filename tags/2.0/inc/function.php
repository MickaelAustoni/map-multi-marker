<?php

// FONCTION POUR CRÉE LES TABLE À L'ACTIVATION DU PLUGIN
function mapmarker_activation() {
    global $wpdb;   

    // Load le 'upgrade.php' pour le call de la fonction dbDelta()
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    // Encodage
    $charset_collate = $wpdb->get_charset_collate();

    // Nom de la table
    $table_name1 = $wpdb->prefix.'mapmarker_option';
    // Req. de création de table pour le "options"
    $sql1 = "CREATE TABLE IF NOT EXISTS $table_name1 (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    height_map INT(11) NOT NULL,
    height_valeur_type CHAR(2) NOT NULL,
    width_map INT(11) NOT NULL,
    width_valeur_type CHAR(2) NOT NULL,
    streetview INT(1) NOT NULL,
    maptype VARCHAR(50) DEFAULT '' NOT NULL,
    zoom INT(2) NOT NULL,
    latitude_initial DECIMAL(10, 8) NOT NULL,
    longitude_initial DECIMAL(11, 8) NOT NULL,
    fiels_to_display VARCHAR(50) NOT NULL,
    UNIQUE KEY id (id)
    ) $charset_collate;";
    // Call dbDelta pour la requete
    dbDelta($sql1);

    // Req de selection sql
    $check_option = $wpdb->get_results( "SELECT * FROM ".$table_name1, ARRAY_A);

    // Si la requete est vide on inseret les option par default
    if (empty($check_option)) {
        // Insert les valeur des options par defaut en bdd
        $wpdb->insert($table_name1, array(
            'height_map' => '500',
            'height_valeur_type' => 'px',
            'width_map' => '100',
            'width_valeur_type' => '%',
            'streetview' => '0', 
            'maptype' => 'TERRAIN', 
            'zoom' => '5',
            'latitude_initial' => '46.437857',
            'longitude_initial' => '2.570801',
            'fiels_to_display' => 'image,titre,description,adresse,telephone,weblink'
        ));
    }


    // Nom de la table
    $table_name2 = $wpdb->prefix.'mapmarker_marker';
    // Req. de création de table pour les "marqueurs"
    $sql2 = "CREATE TABLE IF NOT EXISTS $table_name2 (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    titre VARCHAR(50) DEFAULT '' NOT NULL,
    description VARCHAR(255) DEFAULT '' NOT NULL,
    adresse VARCHAR(255) DEFAULT '' NOT NULL,
    telephone VARCHAR(12) DEFAULT '' NOT NULL,
    weblink VARCHAR(255) DEFAULT '' NOT NULL,
    img_desc_marker VARCHAR(255) DEFAULT '' NOT NULL,
    img_icon_marker VARCHAR(255) DEFAULT '' NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    UNIQUE KEY id (id)
    ) $charset_collate;";
    // Call dbDelta pour la requete
    dbDelta($sql2);

    // Req de selection sql
    $check_marqueur = $wpdb->get_results( "SELECT * FROM ".$table_name1, ARRAY_A);

    // Si la requete est vide on insert un marqueurs par default
    if (empty($check_option)) {

        // Insert un marqueur par defaut en bdd
        $wpdb->insert($table_name2, array(
            'titre' => __("Eiffel Tower", "map-multi-marker"), 
            'description' => __("Constructed in 1889 as the entrance to the 1889 World's Fair, it was initially criticized by some of France's leading artists and intellectuals for its design, but it has become a global cultural icon of France...", "map-multi-marker"),
            'adresse' => 'Champ de Mars, 5 Avenue Anatole',
            'telephone' => '0123456789',
            'weblink' => 'http://www.toureiffel.paris/en/home.html',
            'img_desc_marker' => plugin_dir_url(__DIR__).'img/desc-marker.jpg',
            'img_icon_marker' => plugin_dir_url(__DIR__).'img/icon-marker.png',
            'latitude' => '48.8583701',
            'longitude' => '2.2922926'
        ));
    }


    // Nom de la table
    $table_name3 = $wpdb->prefix.'mapmarker_api';
    // Req. de création de table pour l'API de google
    $sql3 = "CREATE TABLE IF NOT EXISTS $table_name3 (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    apikey VARCHAR(255) DEFAULT '' NOT NULL,
    language VARCHAR(10) DEFAULT '' NOT NULL,
    UNIQUE KEY id (id)
    ) $charset_collate;";
    // Call dbDelta pour la requete
    dbDelta($sql3);

    // Req de selection sql
    $check_apikey = $wpdb->get_results( "SELECT apikey FROM ".$table_name3, ARRAY_A);

    // Si la requete est vide on inseret les option par default
    if (empty($check_apikey)) {
        // Insert une valeur vide
        $wpdb->insert($table_name3, array(
            'apikey' => '',
            'language' => mapmarkerGetLanguage()
        ));
    }
}


// FONCTION QUI CHARGE LES FICHIERS LANGUE
function mapmarker_load_textdomain() {
    load_plugin_textdomain( 'map-multi-marker', false, plugin_basename( dirname(dirname( __FILE__ )) ) . '/language' );
}


// CRÉATION DU MENU ADMIN DU PLUGIN
function mapmarker_admin_menu(){
    // Add. le menu principal 
    add_menu_page('Map Multi Marker', 'Map Multi Marker', 'manage_options', 'map-multi-marker', 'map_marker', 'dashicons-location-alt');
    // Add. le submenu
    add_submenu_page( 'map-multi-marker', __('Map Marker Options', 'map-multi-marker'), __('Options', 'map-multi-marker'), 'manage_options', 'map-multi-marker-option', 'map_marker_option');
    add_submenu_page( 'map-multi-marker', __('Google API Map Marker', 'map-multi-marker'), __('Google API', 'map-multi-marker'), 'manage_options', 'map-multi-marker-google-api', 'mapmarker_google_api');
    add_submenu_page( 'map-multi-marker', __('Help Map Marker', 'map-multi-marker'), __('Help', 'map-multi-marker'), 'manage_options', 'map-multi-marker-help', 'mapmarker_help');
}
add_action('admin_menu', 'mapmarker_admin_menu');


// FONCTION POUR CHARGER DES SCRIPTS SUR L'ADMIN
function mapmarker_admin_script() {
    // Enregistre les script & css
    wp_register_style('fontawesome-css', plugin_dir_url(__DIR__).'css/font-awesome.min.css');
    wp_register_style( 'admin-css', plugin_dir_url(__DIR__).'css/admin.css');
    wp_register_script('admin-js', plugin_dir_url(__DIR__).'js/admin.js', array('jquery'), false, true);
    wp_register_script('clipboard-js', plugin_dir_url(__DIR__) .'js/clipboard.min.js', array('jquery') );

    // Si les page sont "map-multi-marker" ou "map-multi-marker-option" alors ont charge les style & script
    if ( $_GET['page'] == 'map-multi-marker' OR $_GET['page'] == 'map-multi-marker-option' ){
        // Load le script & css
        wp_enqueue_style('fontawesome-css');
        wp_enqueue_style( 'admin-css' );
        wp_enqueue_script('admin-js');
    }
    // Si les page est "Aide"
    if ( $_GET['page'] == 'map-multi-marker-help' ){
        // Load le script & css
        wp_enqueue_style( 'admin-css' );
        wp_enqueue_script('clipboard-js');
    }
}
add_action( 'admin_enqueue_scripts', 'mapmarker_admin_script' );


// FONCTION POUR CHARGER DES SCRIPTS SUR LE FRONT
function mapmarker_front_script() {
    // Enregistre les script & css
    wp_register_style('front-css', plugin_dir_url(__DIR__).'css/front.css');
    wp_register_style('fontawesome-css', plugin_dir_url(__DIR__).'css/font-awesome.min.css');
    wp_register_script('googlemap-js', 'https://maps.googleapis.com/maps/api/js?key='.mapmarkerGetApiKey().'&language='.mapmarkerGetLanguage().'&callback=initMap');

    // Load le script & css
    wp_enqueue_style('front-css');
    wp_enqueue_style('fontawesome-css');
    wp_enqueue_script('googlemap-js');
}


// RÉCUPÈRE LA CLÉ API DE GOOGLE
function mapmarkerGetApiKey(){
    global $wpdb;

    $api = $wpdb->get_results( "SELECT apikey FROM " . $wpdb->prefix . 'mapmarker_api', ARRAY_A);

    return $api[0]['apikey'];
}


// RÉCUPÈRE LA LANGUE
function mapmarkerGetLanguage(){
    global $wpdb;

    $api = $wpdb->get_results( "SELECT language FROM " . $wpdb->prefix . 'mapmarker_api', ARRAY_A);

    // Si la requete est vide on return le language locale
    if ( empty($api) ) {
        return substr(get_locale(), 0, 2);
    }
    // Sinon on return la valeur stocké en bdd
    else{
        return $api[0]['language'];
    }
}


// FUNCTION MESSAGE D'ALERT : SUCCESS, INFO, , WARNING, ERROR
function mapmarker_alert_msg($alert = array() , $msg = false){
    /*
    **
      alert color vert = "success"
      alert color blue = "info"
      alert color orange = "warning"
      alert color red = "error"
    **
    */
    extract($alert);

    $dom = '<div class="notice notice-'.$alert.' is-dismissible"><p><strong>';

    $dom .= $msg;

    $dom .= '</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Ne pas tenir compte de ce message.</span></button></div>';

    echo $dom;
}


// FONCTION POUR RÉCUPÉRER ET PRÉPARER LES OPTIONS POUR LES INTERGRER DANS LE JAVASCRIPT
function mapmarker_get_options($options = false){
    global $wpdb;

    // Option de base pour init la map google
    if ($options == 'base_map') {
        // Requete SQL et stock dans un tableau associatif avec "ARRAY_A"
        $data = $wpdb->get_results( "SELECT latitude_initial, longitude_initial, maptype, zoom FROM " . $wpdb->prefix . 'mapmarker_option', ARRAY_A);

        // Récup les donné du tableau multidimensionel et les formate pour l'API Google
        $base_map = '{
        center:{lat:'.$data[0]['latitude_initial'].',
        lng:'.$data[0]['longitude_initial'].'},
        mapTypeId:google.maps.MapTypeId.'.$data[0]['maptype'].',
        zoom:'.$data[0]['zoom']
        .'}';

        echo $base_map;
    }

    // Option pour la taille de la map
    if ($options == 'size_map') {
        // Requete SQL et stock dans un tableau associatif avec "ARRAY_A"
        $data = $wpdb->get_results( "SELECT height_map, width_map, height_valeur_type, width_valeur_type FROM " . $wpdb->prefix . 'mapmarker_option', ARRAY_A);

        $size_map = 'style="height:'.$data[0]['height_map'].$data[0]['height_valeur_type'].';width:'.$data[0]['width_map'].$data[0]['width_valeur_type'].'"';

        echo $size_map;
    }
}


// FONCTIUON POUR RÉCUPÉRER ET PRÉPARER LES MARKERS POUR LES INTERGRER DANS LE JAVASCRIPT
function mapmarker_get_makers(){
    // Requete SQL et stock dans un tableau associatif avec "ARRAY_A"
    global $wpdb; $data = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . 'mapmarker_marker', ARRAY_A);

    // Start de la chaine (bracket open)
    $marker = '[';

    // Boucle
    foreach ($data as $result){
        // Assignation concaténant des data bouclé  
        $marker .="{
        id:".$result['id'].",
        icon:".'"'.$result['img_icon_marker'].'"'.",
        lat:".$result['latitude'].",
        lng:".$result['longitude']."},";
    };

    // Supprime la dernière virgule
    $marker = substr($marker, 0, -1);

    // Assignation concaténant la fermeture du bracket
    $marker .= ']';

    echo $marker;

}


// FONCTION QUI RECHERCHE SI LA VALEUR STOCKÉ EN BASE EST PRÉSENTE ET LA COCHE
function mapmarker_check_checked($str, $findme){
    // Recherche la chaine($findme) dans une chaine($str)
    $pos = strpos($str, $findme);

    // Si la chaine est trouvé, on coche le selecteur
    if ($pos !== false) {
        echo 'checked';
    }
}


// FONCTION QUI RECHERCHE SI LA VALEUR STOCKÉ EN BASE EST PRÉSENTE ET LA COCHE
function mapmarker_get_checked_field($str, $findme){
    // Recherche la chaine($findme) dans une chaine($str)
    $pos = strpos($str, $findme);

    // Si la chaine est trouvé, on coche le selecteur
    if ($pos !== false) {
        return true;
    }
    else{
        return false;  
    }
}


// GET LE CONTENU DES MARQUEURS
function mapmarkerMarkerContent(){
    global $wpdb; 

    // Requete SQL et stock dans un tableau associatif avec "ARRAY_A" pour récupe les champs selectionné dans les option
    $get_field = $wpdb->get_results( "SELECT fiels_to_display FROM " . $wpdb->prefix . 'mapmarker_option', ARRAY_A);

    // Requete SQL et stock dans un tableau associatif avec "ARRAY_A"
    $data = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . 'mapmarker_marker WHERE id="'.$_POST['id'].'"', ARRAY_A);


    ?>
    <!-- Si le champ de "l'image" est coché -->
    <?php if (mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'image') == true): ?>
        <img src="<?php echo esc_url($data[0]['img_desc_marker'])?>" alt="" class="img-in-marqueur">
    <?php endif ?>

    <div class="wrap-desc-markeur">
        <!-- Si le champ du "titre" est coché -->
        <?php if ( mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'titre') == true ): ?>
            <h2><?php echo esc_html($data[0]['titre'])?></h2>
        <?php endif ?>

        <!-- Si le champ de la "description" est coché -->
        <?php if ( mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'description') == true ): ?>
            <p class="description-marker"><?php echo esc_html($data[0]['description'])?></p>
        <?php endif ?>
        
        <!-- Si le champ de la "adresse" ou "telephone" est coché -->
        <?php if ( mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'adresse') == true OR mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'telephone') == true OR mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'weblink') == true ): ?>
            <ul class="contact-list">
                <!-- Si le champ "adresse" est coché -->
                <?php if ( mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'adresse') == true ): ?>
                    <li class="adresse"><strong><?php echo esc_html($data[0]['adresse'])?></strong></li>
                <?php endif ?>
                <!-- Si le champ de la "description" est coché -->
                <?php if ( mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'telephone') == true ): ?>
                    <li class="telephone"><strong><a href="tel:<?php echo esc_html($data[0]['telephone'])?>"><?php echo esc_html(chunk_split($data[0]['telephone'],2,' '))?></a></strong></li>
                <?php endif ?>
                <!-- Si le champ du "lien" est coché -->
                <?php if ( mapmarker_get_checked_field($get_field[0]['fiels_to_display'], 'weblink') == true ): ?>
                    <li class="weblink"><strong><a href="<?php echo esc_html($data[0]['weblink'])?>" target="_blank"><?php echo $data[0]['weblink'] ?></a></strong></li>
                <?php endif ?>
            </ul>
        <?php endif ?>
    </div>
    <?php
    wp_die();
}
add_action( 'wp_ajax_mapmarkerMarkerContent', 'mapmarkerMarkerContent' );
add_action( 'wp_ajax_nopriv_mapmarkerMarkerContent', 'mapmarkerMarkerContent' );


// FONCTION POUR LE MODAL DE CONFIRMATION DE SUPPRESSION
function mapMarkerModalDelete(){
    ?>
    <div class="wrap-modal-parent">
        <div class="wrap-modal-child">
            <div class="modal-confirm">
                <form action="" method="post">
                    <input type="hidden" name="securite_nonce" value="<?php echo wp_create_nonce('securite-nonce'); ?>"/>
                    <input type="hidden" name="id" value="<?php echo $_POST['id']?>"><h4><?php _e('Do you confirm the suppression of the marker ?', 'map-marker'); ?></h4>
                    <button id="cancel-supp" class="button-secondary"><?php _e('Cancel', 'map-marker'); ?></button> 
                    <button type="submit" class="button-primary" name="submit_delete" id="submit_delete"><?php _e('Confirm', 'map-marker'); ?></button></form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        jQuery.noConflict();
        (function($) {
            // slideDown du modal
            $('.modal-confirm').slideDown("fast");

            // Add. un display inline-blick en css
            $('.modal-confirm').css('display', 'inline-block');

            // Au click du boutton d'annulation on supprime le modal
            $('#cancel-supp').click(function() {
                $('.wrap-modal-parent').remove();
                return false;
            });
        })(jQuery);
    </script>
    <?php
}
add_action( 'wp_ajax_mapMarkerModalDelete', 'mapMarkerModalDelete' );