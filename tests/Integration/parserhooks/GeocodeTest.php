<?php

namespace Maps\Test;

use DataValues\Geo\Values\LatLongValue;
use Maps\Elements\Location;

/**
 * @covers MapsGeocode
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class GeocodeTest extends ParserHookTest {

	/**
	 * @see ParserHookTest::getInstance
	 */
	protected function getInstance() {
		return new \MapsGeocode();
	}

	/**
	 * @see ParserHookTest::parametersProvider
	 */
	public function parametersProvider() {
		$paramLists = [];

		$paramLists[] = [ 'location' => 'new york city' ];

		return $this->arrayWrap( $paramLists );
	}

	/**
	 * @see ParserHookTest::processingProvider
	 */
	public function processingProvider() {
		$argLists = [];

		$argLists[] = [
			[
				'location' => '4,2',
				'allowcoordinates' => 'yes',
			],
			[
				'location' => '4,2',
				'allowcoordinates' => true,
			]
		];

		return $argLists;
	}

}