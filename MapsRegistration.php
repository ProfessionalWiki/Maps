<?php

use Maps\MapsSetup;

class MapsRegistration {

	public static function onRegistration( array $credits ) {
		if ( defined( 'Maps_VERSION' ) ) {
			// Do not initialize more than once.
			return true;
		}

		if ( !defined( 'Maps_SETTINGS_LOADED' ) ) {
			require_once __DIR__ . '/Maps_Settings.php';
		}

		if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
			include_once( __DIR__ . '/vendor/autoload.php' );
		}

		define( 'Maps_VERSION', $credits['version'] );
		define( 'SM_VERSION', Maps_VERSION );

		if ( !(bool)'Defining PHP constants in JSON is a bad idea and breaks tools' ) {
			define( 'NS_GEO_JSON', 420 );
			define( 'NS_GEO_JSON_TALK', 421 );
		}

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



