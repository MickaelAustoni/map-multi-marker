<?php
/*
Plugin Name: Map Multi Marker
Plugin URI: http://mickael.austoni.fr
Description: Easily create multiple markers on a google map !
Version: 2.9.1
Author: Mickael Austoni
Author URI: http://mickael.austoni.fr
Text Domain: map-multi-marker
License: GPL2
*/


//Configuration par défaut du plugin
$mapmarker_info = array(
	'plugin_name' => 'Map Multi Marker',
	'version' => '2.9.1',
	'shortcode' => 'map-multi-marker',
    'text_domaine' => 'map-multi-marker',
    'default_map_id' => '1',
    'default_height_map' => '500',
    'default_height_valeur_type' => 'px',
    'default_width_map' => '100',
    'default_width_valeur_type' => '%',
    'default_streetview' => 0,
    'default_maptype' => 'TERRAIN',
    'default_zoom' => '2',
    'default_lightbox' => 1,
    'default_scrollwheel' => 1,
    'default_latitude_initial' => '46.437857',
    'default_longitude_initial' => '2.570801',
    'default_fiels_to_display' => 'image,titre,description,adresse,telephone,weblink',
    'default_api_key' => 'AIzaSyCRC7476v-ecw7Cp_9xT-cjbJi75sQhdhM',
    'default_desc_img_url' => 'img/desc-marker.jpg',
    'default_marker_img_url' => 'img/icon-marker.png',
    'donate_link' => 'https://www.paypal.me/MickaelAustoni'
);


//Define la constante de l'url du plugin
define("MMM_URL", plugin_dir_url(__FILE__));


//Load les fichiers
include_once __DIR__.'/inc/function.php';

if ( is_admin() ) {
	include_once __DIR__.'/admin/admin.php';
}
else{
	include_once __DIR__.'/front/front.php';
}


//Callback de la function à l'activation du plugin
register_activation_hook( __FILE__, 'mapmarker_plugin_activation' );


//Callback de la fonction quand tous les plugin sont chargé
add_action( 'plugins_loaded', 'mapmarker_plugin_loaded' );


//Callback lien dans la page plugins
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'mapMarker_add_action_links' );