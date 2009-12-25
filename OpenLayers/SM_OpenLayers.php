<?php

/**
 * This groupe contains all OpenLayers related files of the Semantic Maps extension.
 * 
 * @defgroup SMOpenLayers OpenLayers
 * @ingroup SemanticMaps
 */

/**
 * This file holds the general information for the OpenLayers service.
 *
 * @file SM_OpenLayers.php
 * @ingroup SMOpenLayers
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$egMapsServices['openlayers']['qp'] = array('class' => 'SMOpenLayersQP', 'file' => 'extensions/SemanticMaps/OpenLayers/SM_OpenLayersQP.php', 'local' => false);
$egMapsServices['openlayers']['fi'] = array('class' => 'SMOpenLayersFormInput', 'file' => 'extensions/SemanticMaps/OpenLayers/SM_OpenLayersFormInput.php', 'local' => false);