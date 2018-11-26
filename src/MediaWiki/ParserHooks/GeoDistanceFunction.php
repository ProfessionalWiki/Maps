<?php

namespace Maps\MediaWiki\ParserHooks;

use Maps\GeoFunctions;
use Maps\Presentation\MapsDistanceParser;
use MWException;
use ParserHook;

/**
 * Class for the 'geodistance' parser hooks, which can
 * calculate the geographical distance between two points.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class GeoDistanceFunction extends ParserHook {

	/**
	 * Renders and returns the output.
	 *
	 * @see ParserHook::render
	 *
	 * @param array $parameters
	 *
	 * @return string
	 * @throws MWException
	 */
	public function render( array $parameters ) {
		/**
		 * @var \DataValues\Geo\Values\LatLongValue $coordinates1
		 * @var \DataValues\Geo\Values\LatLongValue $coordinates2
		 */
		$coordinates1 = $parameters['location1']->getCoordinates();
		$coordinates2 = $parameters['location2']->getCoordinates();

		$distance = GeoFunctions::calculateDistance( $coordinates1, $coordinates2 );
		$output = MapsDistanceParser::formatDistance( $distance, $parameters['unit'], $parameters['decimals'] );

		return $output;
	}

	/**
	 * @see ParserHook::getMessage
	 */
	public function getMessage() {
		return 'maps-geodistance-description';
	}

	/**
	 * Gets the name of the parser hook.
	 *
	 * @see ParserHook::getName
	 *
	 * @return string
	 */
	protected function getName() {
		return 'geodistance';
	}

	/**
	 * Returns an array containing the parameter info.
	 *
	 * @see ParserHook::getParameterInfo
	 *
	 * @return array
	 */
	protected function getParameterInfo( $type ) {
		global $egMapsDistanceUnit, $egMapsDistanceDecimals;

		$params = [];

		$params['unit'] = [
			'default' => $egMapsDistanceUnit,
			'values' => MapsDistanceParser::getUnits(),
		];

		$params['decimals'] = [
			'type' => 'integer',
			'default' => $egMapsDistanceDecimals,
		];

		$params['location1'] = [
			'type' => 'mapslocation',
			'aliases' => 'from',
		];

		$params['location2'] = [
			'type' => 'mapslocation',
			'aliases' => 'to',
		];

		// Give grep a chance to find the usages:
		// maps-geodistance-par-mappingservice, maps-geodistance-par-geoservice,
		// maps-geodistance-par-unit, maps-geodistance-par-decimals,
		// maps-geodistance-par-location1, maps-geodistance-par-location2
		foreach ( $params as $name => &$param ) {
			$param['message'] = 'maps-geodistance-par-' . $name;
		}

		return $params;
	}

	/**
	 * Returns the list of default parameters.
	 *
	 * @see ParserHook::getDefaultParameters
	 *
	 * @param $type
	 *
	 * @return array
	 */
	protected function getDefaultParameters( $type ) {
		return [ 'location1', 'location2', 'unit', 'decimals' ];
	}

}