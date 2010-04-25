<?php

/**
 * This groupe contains all OpenStreetMap related files of the Semantic Maps extension.
 * 
 * @defgroup SMOSM OpenStreetMap
 * @ingroup SemanticMaps
 */

/**
 * This file holds the general information for the OpenStreetMap service.
 *
 * @file SM_OSM.php
 * @ingroup SMOSM
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$wgAutoloadClasses['SMOSMQP'] = dirname( __FILE__ ) . '/SM_SMOSMQP.php';

$egMapsServices['osm']['features']['qp'] = 'SMOSMQP';
// $egMapsServices['osm']['features']['fi'] = 'SMOSMFormInput';