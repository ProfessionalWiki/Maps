<?php

namespace Maps\Test;

/**
 * @covers MapsDistance
 *
 * @group Maps
 * @group ParserHook
 * @group MapsDistanceTest
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DistanceTest extends ParserHookTest {

	/**
	 * @see ParserHookTest::getInstance
	 * @since 2.0
	 * @return \ParserHook
	 */
	protected function getInstance() {
		return new \MapsDistance();
	}

	/**
	 * @since 3.0
	 * @var array
	 */
	protected $distances = [
		'42' => 42,
		'42m' => 42,
		'42 m' => 42,
		'42 km' => 42000,
		'4.2 km' => 4200,
		'4.2 m' => 4.2,
	];

	/**
	 * @see ParserHookTest::parametersProvider
	 * @since 2.0
	 * @return array
	 */
	public function parametersProvider() {
		$paramLists = [];

		foreach ( array_keys( $this->distances ) as $distance ) {
			$paramLists[] = [ 'distance' => (string)$distance ];
		}

		return $this->arrayWrap( $paramLists );
	}

	/**
	 * @see ParserHookTest::processingProvider
	 * @since 3.0
	 * @return array
	 */
	public function processingProvider() {
		$argLists = [];

		foreach ( $this->distances as $input => $output ) {
			$values = [
				'distance' => (string)$input,
			];

			$expected = [
				'distance' => $output,
			];

			$argLists[] = [ $values, $expected ];
		}

		$values = [
			'distance' => '42m',
			'unit' => 'km',
			'decimals' => '1',
		];

		$expected = [
			'distance' => 42,
			'unit' => 'km',
			'decimals' => 1,
		];

		$argLists[] = [ $values, $expected ];

		$values = [
			'distance' => '42m',
			'unit' => '~=[,,_,,]:3',
			'decimals' => 'foobar',
		];

		$expected = [
			'distance' => 42,
		];

		$argLists[] = [ $values, $expected ];

		return $argLists;
	}

}