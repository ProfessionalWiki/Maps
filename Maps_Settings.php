<?php

/**
 * For instructions on how to configure Maps, see
 * https://www.semantic-mediawiki.org/wiki/Maps/Configuration
 *
 * For a list of all available settings and their default values,
 * see DefaultSettings.php in this directory.
 */

foreach ( include __DIR__ . '/DefaultSettings.php' as $key => $value ) {
	$GLOBALS[$key] = $value;
}

if ( !defined( 'Maps_SETTINGS_LOADED' ) ) {
	define( 'Maps_SETTINGS_LOADED', true );
}
