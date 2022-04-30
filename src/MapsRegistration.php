<?php

namespace Maps;

use Exception;

class MapsRegistration {

	public static function onRegistration( array $credits ) {
		if ( defined( 'Maps_VERSION' ) ) {
			// Do not initialize more than once.
			return true;
		}

		if ( !defined( 'Maps_SETTINGS_LOADED' ) ) {
			require_once __DIR__ . '/../Maps_Settings.php';
		}

		if ( is_readable( __DIR__ . '/../vendor/autoload.php' ) ) {
			include_once( __DIR__ . '/../vendor/autoload.php' );
		}

		define( 'Maps_VERSION', $credits['version'] );
		define( 'SM_VERSION', Maps_VERSION );

		if ( !(bool)'Defining PHP constants in JSON is a bad idea and breaks tools' ) {
			define( 'NS_GEO_JSON', 420 );
			define( 'NS_GEO_JSON_TALK', 421 );
		}

		$GLOBALS['wgHooks']['SMW::Settings::BeforeInitializationComplete'][] = 'Maps\MapsHooks::addSmwSettings';

		$GLOBALS['wgExtensionFunctions'][] = function () {
			if ( $GLOBALS['egMapsDisableExtension'] ) {
				return true;
			}

			// Only initialize the extension when all dependencies are present.
			if ( !defined( 'Validator_VERSION' ) ) {
				throw new Exception( 'Maps needs to be installed via Composer.' );
			}

			if ( version_compare( $GLOBALS['wgVersion'], '1.35c', '<' ) ) {
				throw new Exception(
					'This version of Maps requires MediaWiki 1.35 or above; upgrade MediaWiki or use an older version of Maps.'
					. ' More information at https://github.com/JeroenDeDauw/Maps/blob/master/INSTALL.md'
				);
			}

			( new MapsSetup() )->setup();

			return true;
		};

		return true;
	}

}



