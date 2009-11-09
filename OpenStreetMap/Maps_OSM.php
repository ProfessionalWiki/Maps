<?php

/**
 * This groupe contains all OpenStreetMap related files of the Maps extension.
 * 
 * @defgroup MapsOpenStreetMap OpenStreetMap
 * @ingroup Maps
 */

/**
 * This file holds the general information for the OSM optimized OpenLayers service
 *
 * @file Maps_OSM.php
 * @ingroup MapsOpenStreetMap
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$egMapsServices['osm'] = array(
									'pf' => array(
										'display_point' => array('class' => 'MapsOSMDispPoint', 'file' => 'OpenStreetMap/Maps_OSMDispPoint.php', 'local' => true),
										'display_map' => array('class' => 'MapsOSMDispMap', 'file' => 'OpenStreetMap/Maps_OSMDispMap.php', 'local' => true),
										),
									'classes' => array(
											array('class' => 'MapsOSMUtils', 'file' => 'OpenStreetMap/Maps_OSMUtils.php', 'local' => true)
											),
									'aliases' => array('openstreetmap', 'openstreetmaps'),
									'parameters' => array(
											)
									);