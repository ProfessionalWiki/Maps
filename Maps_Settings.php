<?php

/**
 * File defining the settings for the Maps extension.
 *
 *                          NOTICE:
 * Changing one of these settings can be done by copying or cutting it,
 * and placing it in LocalSettings.php, AFTER the inclusion of Maps.
 *
 * @author Jeroen De Dauw
 */

foreach ( include __DIR__ . '/DefaultSettings.php' as $key => $value ) {
	$GLOBALS[$key] = $value;
}

if ( !defined( 'Maps_SETTINGS_LOADED' ) ) {
	define( 'Maps_SETTINGS_LOADED', true );
}
