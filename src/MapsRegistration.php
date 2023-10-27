<?php

namespace Maps;

use Exception;

class MapsRegistration {

	private static $initialized = false;

	public static function onRegistration(): bool {
		if ( $GLOBALS['egMapsDisableExtension'] || self::$initialized ) {
			// Do not initialize more than once.
			return true;
		}

		self::$initialized = true;

		if ( !(bool)'Defining PHP constants in JSON is a bad idea and breaks tools' ) {
			define( 'NS_GEO_JSON', 420 );
			define( 'NS_GEO_JSON_TALK', 421 );
		}

		$hooks = [];
		$hooks['SMW::Settings::BeforeInitializationComplete'][] = 'Maps\MapsHooks::addSmwSettings';
		MapsHooks::registerHookHandlers( $hooks );

		$GLOBALS['wgExtensionFunctions'][] = function () {
			// Only initialize the extension when all dependencies are present.
			if ( !defined( 'Validator_VERSION' ) ) {
				throw new Exception( 'Maps needs to be installed via Composer.' );
			}

			( new MapsSetup() )->setup();

			return true;
		};

		return true;
	}

}
