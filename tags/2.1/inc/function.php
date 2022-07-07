<?php


//FONCTION POUR CRÉE LES TABLE À L'ACTIVATION DU PLUGIN
function mapmarker_plugin_activation() {
    global $wpdb;


    //Load le 'upgrade.php' pour le call de la fonction dbDelta()
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );


    //Encodage
    $charset_collate = $wpdb->get_charset_collate();


	//Nom des tables
    $table_option = $wpdb->prefix.'mapmarker_option';
    $table_marker = $wpdb->prefix.'mapmarker_marker';
    $table_api = $wpdb->prefix.'mapmarker_api';


    // Req. de création de table pour le "options"
    $sql1 = "CREATE TABLE IF NOT EXISTS $table_option (
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
    latitude_initial DECIMAL(10, 8) NOT NULL,
    longitude_initial DECIMAL(11, 8) NOT NULL,
    fiels_to_display VARCHAR(50) NOT NULL,
    UNIQUE KEY id (id)
    ) $charset_collate;";
    // Call dbDelta pour la requete
    dbDelta($sql1);


    // Req de selection sql
    $check_option = $wpdb->get_results( "SELECT * FROM ".$table_option, ARRAY_A);

    // Si la requete est vide on inseret les option par default
    if (empty($check_option)) {
        // Insert les valeur des options par defaut en bdd
        $wpdb->insert($table_option,
	        array(
		        'map_id' => '1',
		        'map_name' => __('Example map', 'map-multi-marker'),
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
	        )
	    );
    }


    // Req. de création de table pour les "marqueurs"
    $sql2 = "CREATE TABLE IF NOT EXISTS $table_marker (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    marker_id INT(11) NOT NULL,
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
    $check_marqueur = $wpdb->get_results( "SELECT * FROM ".$table_marker, ARRAY_A);

    // Si la requete est vide on insert un marqueurs par default
    if (empty($check_option)) {
        // Insert un marqueur par defaut en bdd
        $wpdb->insert($table_marker, array(
	        'marker_id' => '1',
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


    // Req. de création de table pour l'API de google
    $sql3 = "CREATE TABLE IF NOT EXISTS $table_api (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    apikey VARCHAR(255) DEFAULT '' NOT NULL,
    language VARCHAR(10) DEFAULT '' NOT NULL,
    UNIQUE KEY id (id)
    ) $charset_collate;";
    // Call dbDelta pour la requete
    dbDelta($sql3);


    // Req de selection sql
    $check_apikey = $wpdb->get_results( "SELECT apikey FROM ".$table_api, ARRAY_A);

    // Si la requete est vide on inseret les option par default
    if (empty($check_apikey)) {
        // Insert une valeur vide
        $wpdb->insert($table_api, array(
            'apikey' => '',
            'language' => mapmarkerGetLanguage()
        ));
    }

	//Ajoute les table de la version 2.1
    update_option('map_multi_marker_add_table_2_1', true );
}


//FONCTION CALL QUAND LES TOUS LES PLUGINS SONT CHARGÉ
function mapmarker_plugin_loaded() {
	global $mapmarker_info;

	//Ajoute la version du plugin dans les options de wordpress en bdd
	if( get_option('map_multi_marker_version') == false OR get_option('map_multi_marker_version') != $mapmarker_info['version'] ){
		update_option('map_multi_marker_version', $mapmarker_info['version'] );
	}

	//Création des news tables de la version 2.1
	if( get_option('map_multi_marker_add_table_2_1') == false ){
		global $wpdb;

		//Nom des tables
	    $table_option = $wpdb->prefix.'mapmarker_option';
	    $table_marker = $wpdb->prefix.'mapmarker_marker';
	    $table_api = $wpdb->prefix.'mapmarker_api';


		//Crée les nouvelle table de la version 2.1
		$wpdb->query("ALTER TABLE $table_marker ADD marker_id VARCHAR(255) NOT NULL DEFAULT 1 AFTER id");
		$wpdb->query("ALTER TABLE $table_option ADD map_id INT(11) NOT NULL DEFAULT 1 AFTER id");
		$wpdb->query("ALTER TABLE $table_option ADD map_name VARCHAR(255) NOT NULL DEFAULT 'Example map' AFTER map_id");

		update_option('map_multi_marker_add_table_2_1', true );
	}

	//Charge le dossiser de language
    load_plugin_textdomain( 'map-multi-marker', false, plugin_basename( dirname(dirname( __FILE__ )) ) . '/language' );
}


//CRÉATION DU MENU ADMIN DU PLUGIN
function mapmarker_admin_menu(){
    // Add. le menu principal
    //add_menu_page('Map Multi Marker', 'Map Multi Marker', 'manage_options', 'map-multi-marker-menu', '', 'dashicons-location-alt');
    add_menu_page( 'Map Multi Marker', __('Map Multi Marker'), 'manage_options', 'map-multi-marker', 'mapmarker_manage', 'dashicons-location-alt');
    // Add. le submenu
    add_submenu_page( 'map-multi-marker', __('Google API Map Multi Marker', 'map-multi-marker'), __('Google API', 'map-multi-marker'), 'manage_options', 'map-multi-marker-google-api', 'mapmarker_google_api');
    add_submenu_page( 'map-multi-marker', __('Help Map Multi Marker', 'map-multi-marker'), __('Help', 'map-multi-marker'), 'manage_options', 'map-multi-marker-help', 'mapmarker_help');
}
add_action('admin_menu', 'mapmarker_admin_menu');


//FONCTION POUR CHARGER DES SCRIPTS SUR L'ADMIN
function mapmarker_admin_script() {
    // Enregistre les script & css
    wp_register_style('fontawesome-css', plugin_dir_url(__DIR__).'css/font-awesome.min.css');
    wp_register_style( 'admin-css', plugin_dir_url(__DIR__).'css/admin.css');
    wp_register_script('admin-js', plugin_dir_url(__DIR__).'js/admin.js', array('jquery'), false, true);
    wp_register_script('clipboard-js', plugin_dir_url(__DIR__) .'js/clipboard.min.js', array('jquery') );

	//Si la varible $page n'existe pas alors on l'init.
    if(!isset($_GET['page'])){
	    $_GET['page']=null;
	}

    // Si les page sont "map-multi-marker" ou "map-multi-marker-option" alors ont charge les style & script
    if ( $_GET['page'] == 'map-multi-marker' OR $_GET['page'] == 'map-multi-marker-google-api' OR $_GET['page'] == 'map-multi-marker-manage' OR $_GET['page'] == 'map-multi-marker-help' ){
        // Load le script & css
        wp_enqueue_style('fontawesome-css');
        wp_enqueue_style( 'admin-css' );
        wp_enqueue_script('admin-js');
    }
    // Si les page est "Aide"
    if ( $_GET['page'] == 'map-multi-marker' ){
        // Load le script & css
        wp_enqueue_style( 'admin-css' );
        wp_enqueue_script('clipboard-js');
    }
}
add_action( 'admin_enqueue_scripts', 'mapmarker_admin_script' );


//FONCTION POUR CHARGER DES SCRIPTS SUR LE FRONT
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

	//Si l'api key est vide alors on charge l'api key par default
    if( empty($api[0]['apikey']) ){
	    global $mapmarker_info;
	    return $mapmarker_info['default_api_key'];
    }
	// Sinon on charge sont api key perso
    else{
		return $api[0]['apikey'];
    }
}


//RÉCUPÈRE LA LANGUE
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


//FUNCTION MESSAGE D'ALERT
function mapmarker_alert_msg($alert, $msg = false){
    /*
    **
      alert color vert = "success"
      alert color blue = "info"
      alert color orange = "warning"
      alert color red = "error"
    **
    */

    $notice = '<div class="notice notice-'.$alert.' is-dismissible"><p><strong>';

    $notice .= $msg;

    $notice .= '</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Ne pas tenir compte de ce message.</span></button></div>';

    ?>
    <script>
    	jQuery(function() {
			jQuery("#wpbody-content").prepend('<?php echo $notice  ; ?>');
		});
    </script>
    <?php
}


//FONCTION POUR RÉCUPÉRER ET PRÉPARER LES OPTIONS POUR LES INTERGRER DANS LE JAVASCRIPT
function mapmarker_get_options($shorcode_attribut, $options = false){
    global $wpdb;

    // Option de base pour init la map google
    if ($options == 'base_map') {
        // Requete SQL et stock dans un tableau associatif avec "ARRAY_A"
        $data = $wpdb->get_results( "SELECT latitude_initial, longitude_initial, maptype, zoom FROM " . $wpdb->prefix . 'mapmarker_option WHERE map_id="'.$shorcode_attribut.'"', ARRAY_A);

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
        $data = $wpdb->get_results( "SELECT height_map, width_map, height_valeur_type, width_valeur_type FROM " . $wpdb->prefix . 'mapmarker_option WHERE map_id="'.$shorcode_attribut.'"', ARRAY_A);

        $size_map = 'style="height:'.$data[0]['height_map'].$data[0]['height_valeur_type'].';width:'.$data[0]['width_map'].$data[0]['width_valeur_type'].'"';

        echo $size_map;
    }
}


//FONCTIUON POUR RÉCUPÉRER ET PRÉPARER LES MARKERS POUR LES INTERGRER DANS LE JAVASCRIPT
function mapmarker_get_makers($shorcode_attribut){

    // Requete SQL et stock dans un tableau associatif avec "ARRAY_A"
    global $wpdb; $data = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . 'mapmarker_marker WHERE marker_id="'.$shorcode_attribut.'"', ARRAY_A);

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


//FONCTION QUI RECHERCHE SI LA VALEUR STOCKÉ EN BASE EST PRÉSENTE ET LA COCHE
function mapmarker_check_checked($str, $findme){
    // Recherche la chaine($findme) dans une chaine($str)
    $pos = strpos($str, $findme);

    // Si la chaine est trouvé, on coche le selecteur
    if ($pos !== false) {
        echo 'checked';
    }
}


//FONCTION QUI RECHERCHE SI LA VALEUR STOCKÉ EN BASE EST PRÉSENTE ET LA COCHE
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


//GET LE CONTENU DES MARQUEURS
function mapmarkerMarkerContent(){
    global $wpdb;

    // Requete SQL et stock dans un tableau associatif avec "ARRAY_A" pour récupe les champs selectionné dans les option de la map afficher sur la page
    $get_field = $wpdb->get_results( "SELECT fiels_to_display FROM " . $wpdb->prefix . 'mapmarker_option WHERE map_id="'.$_POST["shorcode_attribut"].'"', ARRAY_A);

    // Requete SQL et stock dans un tableau associatif avec "ARRAY_A" pour récup les marqueurs de la map afficher
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
//Call la fonction AJAX de Wordpress
add_action( 'wp_ajax_mapmarkerMarkerContent', 'mapmarkerMarkerContent' );
//Call la fonction AJAX de Worpress pour le front-end
add_action( 'wp_ajax_nopriv_mapmarkerMarkerContent', 'mapmarkerMarkerContent' );


//FONCTION AJAX QUI CRÉE LES MARQUEURS
function mapMarkerCreateMarker(){

	global $wpdb;

	// Déclare la table
	$table_name = $wpdb->prefix.'mapmarker_marker';

	// Supprime les antislashe pour eviter les bugs
	$_POST = stripslashes_deep($_POST);

	// Load "file.php" pour la fonction "wp_handle_upload()"
	if ( ! function_exists( 'wp_handle_upload' ) ) {
	    require_once( ABSPATH . 'wp-admin/includes/file.php' );
	}

	// Fonction "wp_handle_upload()" de wordpress pour upload l'image dans un array et ses proprièté
	$img_desc = wp_handle_upload( $_FILES['img_desc_marker'], array('test_form' => false) );

	// Fonction "wp_handle_upload()" de wordpress pour upload l'image dans un array et ses proprièté
	$img_icon = wp_handle_upload( $_FILES['img_icon_marker'], array('test_form' => false) );

	// Si aucune image de description a été upload
	if ( $img_desc && isset( $img_desc['error'] ) ) {
		// Défini une image générique
		$img_desc = array('url' => plugin_dir_url(__DIR__).'img/desc-marker.jpg');
	}

	// Si aucun icon de marqueur a été upload
	if ( $img_icon && isset( $img_icon['error'] ) ) {
		// Défini une image générique
		$img_icon = array('url' => plugin_dir_url(__DIR__).'img/icon-marker.png');
	}

	// Si les variable des champs envoyé n'existe pas
	if ( !isset($_POST['add_titre']) ){
		$_POST['add_titre'] = '';
	}
	if ( !isset($_POST['add_description']) ){
		$_POST['add_description'] = '';
	}
	if ( !isset($_POST['add_adresse']) ){
		$_POST['add_adresse'] = '';
	}
	if ( !isset($_POST['add_telephone']) ){
		$_POST['add_telephone'] = '';
	}
	if ( !isset($_POST['add_weblink']) ){
		$_POST['add_weblink'] = '';
	}

	// Req. insert sql
	$wpdb->insert($table_name,
	    array(
		    'marker_id' => sanitize_text_field($_GET['map_id']),
	    	'titre' => sanitize_text_field($_POST['add_titre']),
	        'description' => sanitize_text_field($_POST['add_description']),
	        'adresse' => sanitize_text_field($_POST['add_adresse']),
	        'telephone' => sanitize_text_field($_POST['add_telephone']),
	        'weblink' => sanitize_text_field($_POST['add_weblink']),
	        'latitude' => sanitize_text_field($_POST['add_latitude']),
	        'longitude' => sanitize_text_field($_POST['add_longitude']),
	        'img_desc_marker' => sanitize_text_field($img_desc['url']),
	        'img_icon_marker' => sanitize_text_field($img_icon['url'])
	        )
	    );

	//Affiche le message d'alert
	mapmarker_alert_msg('success', __('Your marker been created.', 'map-multi-marker'));
}


//FONCTION POUR LE MODAL DE CONFIRMATION DE SUPPRESSION
function mapMarkerModalDelete(){
	//Si le modal est une suppression de map
	if($_POST['modal'] == 'delete_map'){
	    ?>
		<div class="wrap-modal-parent">
	        <div class="wrap-modal-child">
	            <div class="modal-confirm">
	                <form action="" method="post">
	                    <input type="hidden" name="securite_nonce_delete_map" value="<?php echo wp_create_nonce('securite-nonce'); ?>"/>
	                    <input type="hidden" name="id_delete_map" value="<?php echo $_POST['id']?>"><h4><?php _e('Do you confirm the suppression of the map ?', 'map-multi-marker'); ?></h4>
	                    <button id="cancel-supp" class="button-secondary"><?php _e('Cancel', 'map-marker'); ?></button>
	                    <button type="submit" class="button-primary" name="submit_delete" id="submit_delete"><?php _e('Confirm', 'map-marker'); ?></button>
	                </form>
	            </div>
	        </div>
	    </div>
	    <?php
    }
    //Si le modal est une suppression de marker
	if($_POST['modal'] == 'delete_marker'){
	    ?>
		<div class="wrap-modal-parent">
	        <div class="wrap-modal-child">
	            <div class="modal-confirm">
	                <form action="" method="post">
	                    <input type="hidden" name="securite_nonce_delete_marker" value="<?php echo wp_create_nonce('securite-nonce'); ?>"/>
	                    <input type="hidden" name="id_delete_marker" value="<?php echo $_POST['id']?>"><h4><?php _e('Do you confirm the suppression of the marker ?', 'map-multi-marker'); ?></h4>
	                    <button id="cancel-supp" class="button-secondary"><?php _e('Cancel', 'map-marker'); ?></button>
	                    <button type="submit" class="button-primary" name="submit_delete" id="submit_delete"><?php _e('Confirm', 'map-marker'); ?></button>
	                </form>
	            </div>
	        </div>
	    </div>
	    <?php
    }
    ?>
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


//FUNCTION QUI OBTIENT L'ID D'UNE NOUVELLE MAP POUR LA CREATION
function mapMarkerGetNewMapId(){
	global $wpdb;

	//Req. sql
    $map_id = $wpdb->get_results( "SELECT map_id FROM " . $wpdb->prefix . 'mapmarker_option', ARRAY_A);

	//Si il n'y a plus aucune map de crée, alors la next map crée aura une valeur de "1"
    if(empty($map_id[0]['map_id'])){
	    echo "1";
    }
	//Sinon on incremente de "1" l'id de map le plus elever
    else{
		//Déclare l'array
		$highest_id = array();

		//Récup les valeurs de map_id du tableau multidimensionel
		foreach($map_id as $value){
			$highest_id[] = $value['map_id'];
		}

		//Return le résultat et l'incrémente de 1
		echo $highest_id = max($highest_id) + 1;
    }


}