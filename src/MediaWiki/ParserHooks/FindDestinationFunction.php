<?php

namespace Maps\MediaWiki\ParserHooks;

use DataValues\Geo\Values\LatLongValue;
use Maps\MapsFactory;
use Maps\GeoFunctions;
use ParserHook;

/**
 * Class for the 'finddestination' parser hooks, which can find a
 * destination given a starting point, an initial bearing and a distance.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class FindDestinationFunction extends ParserHook {

	/**
	 * Renders and returns the output.
	 *
	 * @see ParserHook::render
	 *
	 * @param array $parameters
	 *
	 * @return string
	 */
	public function render( array $parameters ) {
		$destination = GeoFunctions::findDestination(
			$parameters['location']->getCoordinates(),
			$parameters['bearing'],
			$parameters['distance']
		);

		return MapsFactory::globalInstance()->getCoordinateFormatter()->format(
			new LatLongValue( $destination['lat'], $destination['lon'] ),
			$parameters['format'],
			$parameters['directional']
		);
	}

	/**
	 * @see ParserHook::getMessage()
	 */
	public function getMessage() {
		return 'maps-finddestination-description';
	}

	/**
	 * Gets the name of the parser hook.
	 *
	 * @see ParserHook::getName
	 *
	 * @return string
	 */
	protected function getName() {
		return 'finddestination';
	}

	/**
	 * Returns an array containing the parameter info.
	 *
	 * @see ParserHook::getParameterInfo
	 *
	 * @return array
	 */
	protected function getParameterInfo( $type ) {
		global $egMapsAvailableCoordNotations;
		global $egMapsCoordinateNotation, $egMapsCoordinateDirectional;

		$params = [];

		$params['location'] = [
			'type' => 'mapslocation',
		];

		$params['format'] = [
			'default' => $egMapsCoordinateNotation,
			'values' => $egMapsAvailableCoordNotations,
			'aliases' => 'notation',
			'tolower' => true,
		];

		$params['directional'] = [
			'type' => 'boolean',
			'default' => $egMapsCoordinateDirectional,
		];

		$params['bearing'] = [
			'type' => 'float',
		];

		$params['distance'] = [
			'type' => 'distance',
		];

		// Give grep a chance to find the usages:
		// maps-finddestination-par-location, maps-finddestination-par-format,
		// maps-finddestination-par-directional, maps-finddestination-par-bearing,
		// maps-finddestination-par-distance, maps-finddestination-par-mappingservice,
		// maps-finddestination-par-geoservice, maps-finddestination-par-allowcoordinates
		foreach ( $params as $name => &$param ) {
			$param['message'] = 'maps-finddestination-par-' . $name;
		}

		return $params;
	}

	/**
	 * Returns the list of default parameters.
	 *
	 * @see ParserHook::getDefaultParameters
	 *
	 * @return array
	 */
	protected function getDefaultParameters( $type ) {
		return [ 'location', 'bearing', 'distance' ];
	}

}