<?php

namespace Maps\Test;

use DataValues\LatLongValue;
use ParamProcessor\ParamDefinition;

/**
 * @covers MapsCoordinates
 *
 * @group Maps
 * @group ParserHook
 * @group CoordinatesTest
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class CoordinatesTest extends ParserHookTest {

	/**
	 * @see ParserHookTest::getInstance
	 * @since 2.0
	 * @return \ParserHook
	 */
	protected function getInstance() {
		return new \MapsCoordinates();
	}

	/**
	 * @see ParserHookTest::parametersProvider
	 * @since 2.0
	 * @return array
	 */
	public function parametersProvider() {
		$paramLists = array();

		$paramLists[] = array(
			array(
				'location' => '4,2'
			),
			'4° 0\' 0", 2° 0\' 0"'
		);

		$paramLists[] = array(
			array(
				'location' => '55 S, 37.6176330 W'
			),
			'-55° 0\' 0", -37° 37\' 3.4788"'
		);

		$paramLists[] = array(
			array(
				'location' => '4,2',
				'format' => 'float',
			),
			'4, 2'
		);

//		$paramLists[] = array(
//			array(
//				'location' => '-4,-2',
//				'format' => 'float',
//				'directional' => 'yes',
//			),
//			'4 W, 2 S'
//		);
//
//		$paramLists[] = array(
//			array(
//				'location' => '55 S, 37.6176330 W',
//				'directional' => 'yes',
//			),
//			'55° 0\' 0" W, 37° 37\' 3.4788" S'
//		);

		return $paramLists;
	}

	/**
	 * @see ParserHookTest::processingProvider
	 * @since 3.0
	 * @return array
	 */
	public function processingProvider() {
		$definitions = ParamDefinition::getCleanDefinitions( $this->getInstance()->getParamDefinitions() );
		$argLists = array();

		$values = array(
			'location' => '4,2',
		);

		$expected = array(
			'location' => new LatLongValue( 4, 2 ),
		);

		$argLists[] = array( $values, $expected );

		$values = array(
			'location' => '4,2',
			'directional' => $definitions['directional']->getDefault() ? 'no' : 'yes',
			'format' => 'dd',
		);

		$expected = array(
			'location' => new LatLongValue( 4, 2 ),
			'directional' => !$definitions['directional']->getDefault(),
			'format' => Maps_COORDS_DD,
		);

		$argLists[] = array( $values, $expected );

		$values = array(
			'location' => '4,2',
			'directional' => $definitions['directional']->getDefault() ? 'NO' : 'YES',
			'format' => ' DD ',
		);

		$expected = array(
			'location' => new LatLongValue( 4, 2 ),
			'directional' => !$definitions['directional']->getDefault(),
			'format' => Maps_COORDS_DD,
		);

		$argLists[] = array( $values, $expected );

		return $argLists;
	}

}