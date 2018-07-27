<?php

/**
 * Initialization file for the Maps extension.
 *
 * @links https://github.com/JeroenDeDauw/Maps/blob/master/README.md#maps Documentation
 * @links https://github.com/JeroenDeDauw/Maps/issues Support
 * @links https://github.com/JeroenDeDauw/Maps Source code
 *
 * @license https://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

use Maps\MapsSetup;

if ( defined( 'Maps_COORDS_FLOAT' ) ) {
	// Do not initialize more than once.
	return 1;
}

// The different coordinate notations.
define( 'Maps_COORDS_FLOAT', 'float' );
define( 'Maps_COORDS_DMS', 'dms' );
define( 'Maps_COORDS_DM', 'dm' );
define( 'Maps_COORDS_DD', 'dd' );

require_once __DIR__ . '/Maps_Settings.php';

// Include the composer autoloader if it is present.
if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
	include_once( __DIR__ . '/vendor/autoload.php' );
}

// Internationalization
$GLOBALS['wgMessagesDirs']['Maps'] = __DIR__ . '/i18n';
$GLOBALS['wgExtensionMessagesFiles']['MapsMagic'] = __DIR__ . '/Maps.i18n.magic.php';
$GLOBALS['wgExtensionMessagesFiles']['MapsAlias'] = __DIR__ . '/Maps.i18n.alias.php';

$GLOBALS['wgExtensionFunctions'][] = function() {
	if ( $GLOBALS['egMapsDisableExtension'] ) {
		return true;
	}

	if ( defined( 'Maps_VERSION' ) ) {
		// Do not initialize more than once.
		return true;
	}

	// Only initialize the extension when all dependencies are present.
	if ( !defined( 'Validator_VERSION' ) ) {
		throw new Exception( 'You need to have Validator installed in order to use Maps' );
	}

	if ( version_compare( $GLOBALS['wgVersion'], '1.27c', '<' ) ) {
		throw new Exception(
			'This version of Maps requires MediaWiki 1.27 or above; use Maps 4.2.x for older versions.'
			. ' More information at https://github.com/JeroenDeDauw/Maps/blob/master/INSTALL.md'
		);
	}

	define( 'Maps_VERSION', '5.7 alpha' );
	define( 'SM_VERSION', Maps_VERSION );

	( new MapsSetup( $GLOBALS ) )->setup();

	return true;
};

