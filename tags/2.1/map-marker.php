<?php
/*
Plugin Name: Map Multi Marker
Plugin URI: http://mickael.austoni.fr
Description: Easily create multiple markers on a google map !
Version: 2.1
Author: Mickael Austoni
Author URI: http://mickael.austoni.fr
Text Domain: map-multi-marker
License: GPL2
*/


// Déclare les globals d'information du plugin
$mapmarker_info = array(
	'plugin_name' => 'Map Multi Marker',
	'version' => '2.1',
    'shortcode' => 'map-multi-marker',
    'default_map_id' => '1',
    'default_height_map' => '500',
    'default_height_valeur_type' => 'px',
    'default_width_map' => '100',
    'default_width_valeur_type' => '%',
    'default_streetview' => '0',
    'default_maptype' => 'TERRAIN',
    'default_zoom' => '2',
    'default_latitude_initial' => '46.437857',
    'default_longitude_initial' => '2.570801',
    'default_fiels_to_display' => 'image,titre,description,adresse,telephone,weblink',
    'default_api_key' => 'AIzaSyCmU553m4ID1s2HeWB_ttF90l9NdFGwJSo'
);


//Include
include_once __DIR__.'/inc/function.php';

if ( is_admin() ) {
	include_once __DIR__.'/admin/admin.php';
}
else{
	include_once __DIR__.'/front/front.php';
}


//Callback de la fonction à l'activation du plugin
register_activation_hook( __FILE__, 'mapmarker_plugin_activation' );


//Callback de la fonction quand tous les plugin sont chargé
add_action( 'plugins_loaded', 'mapmarker_plugin_loaded' );




