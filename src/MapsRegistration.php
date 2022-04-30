<?php

namespace Maps;

use Exception;

class MapsRegistration {

	private static $initialized = false;

	public static function onRegistration(): bool {
		if ( self::$initialized ) {
			// Do not initialize more than once.
			return true;
		}

		self::$initialized = true;

		if ( !defined( 'Maps_SETTINGS_LOADED' ) ) {
			require_once __DIR__ . '/../Maps_Settings.php';
		}

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
