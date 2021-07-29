<?php

declare( strict_types = 1 );

namespace Maps\ParserHooks;

use Jeroen\SimpleGeocoder\Geocoder;
use Maps\MapsFactory;
use ParamProcessor\ProcessingResult;
use Parser;
use ParserHooks\HookDefinition;
use ParserHooks\HookHandler;

/**
 * Class for the 'geocode' parser hooks, which can turn
 * human readable locations into sets of coordinates.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class GeocodeFunction implements HookHandler {

	private $geocoder;

	public function __construct( Geocoder $geocoder ) {
		$this->geocoder = $geocoder;
	}

	public function handle( Parser $parser, ProcessingResult $result ) {
		foreach ( $result->getErrors() as $error ) {
			if ( $error->isFatal() ) {
				return '<div><span class="errorbox">' .
					wfMessage( 'validator-fatal-error', $error->getMessage() )->parse() .
					'</span></div><br /><br />';
			}
		}

		$parameters = $result->getParameters();

		$coordinates = $this->geocoder->geocode( $parameters['location']->getValue() );

		if ( $coordinates === null ) {
			return 'Geocoding failed'; // TODO: i18n
		}

		return MapsFactory::globalInstance()->getCoordinateFormatter()->format(
			$coordinates,
			$parameters['format']->getValue(),
			$parameters['directional']->getValue()
		);
	}

	public static function getHookDefinition(): HookDefinition {
		return new HookDefinition(
			'geocode',
			self::getParameterInfo(),
			[ 'location' ]
		);
	}

	private static function getParameterInfo() {
		global $egMapsAvailableCoordNotations;
		global $egMapsCoordinateNotation;
		global $egMapsCoordinateDirectional;

		$params = [];

		$params['location'] = [
			'type' => 'string',
			'message' => 'maps-geocode-par-location',
		];

		$params['format'] = [
			'default' => $egMapsCoordinateNotation,
			'values' => $egMapsAvailableCoordNotations,
			'aliases' => 'notation',
			'tolower' => true,
			'message' => 'maps-geocode-par-format',
		];

		$params['directional'] = [
			'type' => 'boolean',
			'default' => $egMapsCoordinateDirectional,
			'message' => 'maps-geocode-par-directional',
		];

		return $params;
	}

}
