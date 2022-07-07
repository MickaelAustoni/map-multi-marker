<?php


// CALLBACK FUNCTION POUR AFFICHER ET MANAGER LES OPTION DE MAP DANS LE BACK OFFICE
function map_marker_option(){
	include_once dirname(__DIR__).'/admin/admin-map-multi-marker-option.php';
}


// CALLBACK FUNCTION POUR L'AIDE DU PLUGIN
function mapmarker_help(){
	include_once dirname(__DIR__).'/admin/admin-map-multi-marker-help.php';
}


// CALLBACK FUNCTION POUR L'AIDE DU PLUGIN
function mapmarker_settings(){
	include_once dirname(__DIR__).'/admin/admin-map-multi-marker-settings.php';
}


// CALLBACK FUNCTION POUR MANAGER LES CARTE & MARKER
function mapmarker_manage(){
	include_once dirname(__DIR__).'/admin/admin-map-multi-marker-manage.php';
}