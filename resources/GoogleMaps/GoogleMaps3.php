<?php

/**
 * This group contains all Google Maps v3 related files of the Maps extension.
 *
 * @defgroup Maps\MapsGoogleMaps3 Google Maps v3
 */

/**
 * This file holds the hook and initialization for the Google Maps v3 service.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

call_user_func(
	function () {
		global $wgResourceModules;

		$pathParts = explode( '/', str_replace( DIRECTORY_SEPARATOR, '/', __DIR__ ) );
		$remoteExtPath = implode( DIRECTORY_SEPARATOR, array_slice( $pathParts, -4 ) );

		
	}
);
