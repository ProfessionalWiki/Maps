<?php

/**
 * This file holds the general information for the Leaflet service.
 *
 * @licence GNU GPL v2+
 * @author Bernhard Krabina < krabina@kdz.or.at>
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$GLOBALS['wgHooks']['MappingServiceLoad'][] = 'smfInitLeaflet';

function smfInitLeaflet() {
	MapsMappingServices::registerServiceFeature( 'leaflet', 'qp', 'SMMapPrinter' );

	return true;
}
