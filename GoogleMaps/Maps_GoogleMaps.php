<?php

/**
 * This file holds the general information for the Google Maps service
 *
 * @file Maps_GoogleMaps.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

$egMapsServices['googlemaps'] = array(
									'pf' => array('class' => 'MapsGoogleMapsParserFunctions', 'file' => 'GoogleMaps/Maps_GoogleMapsParserFunctions.php', 'local' => true),
									'classes' => array(
											array('class' => 'MapsGoogleMapsUtils', 'file' => 'GoogleMaps/Maps_GoogleMapsUtils.php', 'local' => true)
											),
									'aliases' => array('google', 'googlemap', 'gmap', 'gmaps'),
									'parameters' => array(
											'type' => array('map-type', 'map type'),
											'types' => array('map-types', 'map types'),
											'earth' => array(),
											'autozoom' => array('auto zoom', 'mouse zoom', 'mousezoom'),
											'class' => array(),
											'style' => array()										
											)
									);