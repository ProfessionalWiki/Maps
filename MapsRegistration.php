<?php

use Maps\MapsSetup;

class MapsRegistration {

	public static function onRegistration( array $credits ) {
		if ( defined( 'Maps_COORDS_FLOAT' ) ) {
			// Do not initialize more than once.
			return true;
		}

		define( 'Maps_VERSION', $credits['version'] );
		define( 'SM_VERSION', Maps_VERSION );

		// The different coordinate notations.
		define( 'Maps_COORDS_FLOAT', 'float' );
		define( 'Maps_COORDS_DMS', 'dms' );
		define( 'Maps_COORDS_DM', 'dm' );
		define( 'Maps_COORDS_DD', 'dd' );

		require_once __DIR__ . '/Maps_Settings.php';

		// Internationalization
		$GLOBALS['wgMessagesDirs']['Maps.class'] = __DIR__ . '/i18n';
		$GLOBALS['wgExtensionMessagesFiles']['MapsMagic'] = __DIR__ . '/Maps.i18n.magic.php';
		$GLOBALS['wgExtensionMessagesFiles']['MapsAlias'] = __DIR__ . '/Maps.i18n.alias.php';

		$GLOBALS['wgExtensionFunctions'][] = function() {
			if ( $GLOBALS['egMapsDisableExtension'] ) {
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

			( new MapsSetup( $GLOBALS ) )->setup();

			return true;
		};

		return true;
	}

}



