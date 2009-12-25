<?php

/**
 * This groupe contains all Google Maps related files of the Semantic Maps extension.
 * 
 * @defgroup SMGoogleMaps Google Maps
 * @ingroup SemanticMaps
 */

/**
 * This file holds the general information for the Google Maps service.
 *
 * @file SM_GoogleMaps.php
 * @ingroup SMGoogleMaps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$egMapsServices['googlemaps']['qp'] = array('class' => 'SMGoogleMapsQP', 'file' => 'extensions/SemanticMaps/GoogleMaps/SM_GoogleMapsQP.php', 'local' => false);
$egMapsServices['googlemaps']['fi'] = array('class' => 'SMGoogleMapsFormInput', 'file' => 'extensions/SemanticMaps/GoogleMaps/SM_GoogleMapsFormInput.php', 'local' => false);