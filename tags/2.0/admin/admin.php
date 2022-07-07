<?php

// CALLBACK DE LA FONCTION 1 DU MENU
function map_marker(){
	include_once dirname(__DIR__).'/admin/admin-map-multi-marker.php';
}


// CALLBACK FUNCTION POUR AFFICHER ET MANAGER LES OPTION DE MAP DANS LE BACK OFFICE
function map_marker_option(){
	include_once dirname(__DIR__).'/admin/admin-map-multi-marker-option.php';
}


// CALLBACK FUNCTION POUR L'AIDE DU PLUGIN
function mapmarker_help(){
	include_once dirname(__DIR__).'/admin/admin-map-multi-marker-help.php';
}


// CALLBACK FUNCTION POUR L'AIDE DU PLUGIN
function mapmarker_google_api(){
	include_once dirname(__DIR__).'/admin/admin-map-multi-marker-google-api.php';
}