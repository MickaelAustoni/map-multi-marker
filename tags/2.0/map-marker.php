<?php
/*
Plugin Name: Map Multi Marker
Plugin URI: http://mickael.austoni.fr
Description: Easily create multiple markers on a google map !
Version: 2.0
Author: Mickael Austoni
Author URI: http://mickael.austoni.fr
Text Domain: map-marker
License: GPL2
*/


// Déclare les globals d'information du plugin
$mapmarker_info = array(
	'plugin_name' => 'Map Multi Marker',
	'version' => '2.0',
    'shortcode' => 'map-multi-marker'
);


// Callback de plugin install
register_activation_hook( __FILE__, 'mapmarker_activation' );


// // Callback de chargement des langues
add_action( 'plugins_loaded', 'mapmarker_load_textdomain' );


// Include
include_once __DIR__ . '/inc/function.php';

if ( is_admin() ) {
	include_once __DIR__.'/admin/admin.php';
}
else{
	include_once __DIR__.'/front/front.php';
}