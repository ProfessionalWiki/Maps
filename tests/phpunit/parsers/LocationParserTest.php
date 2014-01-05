<?php

namespace Maps\Test;

use DataValues\LatLongValue;
use Maps\Elements\Location;

/**
 * @covers Maps\LocationParser
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class LocationParserTest extends \ValueParsers\Test\StringValueParserTest {

	/**
	 * @see ValueParserTestBase::validInputProvider
	 *
	 * @since 3.0
	 *
	 * @return array
	 */
	public function validInputProvider() {
		$argLists = array();

		$valid = array(
			'55.7557860 N, 37.6176330 W' => array( 55.7557860, -37.6176330 ),
			'55.7557860, -37.6176330' => array( 55.7557860, -37.6176330 ),
			'55 S, 37.6176330 W' => array( -55, -37.6176330 ),
			'-55, -37.6176330' => array( -55, -37.6176330 ),
			'5.5S,37W ' => array( -5.5, -37 ),
			'-5.5,-37 ' => array( -5.5, -37 ),
			'4,2' => array( 4, 2 ),
		);

		foreach ( $valid as $value => $expected ) {
			$expected = new Location( new LatLongValue( $expected[0], $expected[1] ) );
			$argLists[] = array( (string)$value, $expected );
		}

		$location = new Location( new LatLongValue( 4, 2 ) );
		$location->setTitle( 'Title' );
		$location->setText( 'some description' );
		$argLists[] = array( '4,2~Title~some description', $location );

		return $argLists;
	}

	/**
	 * @see ValueParserTestBase::getParserClass
	 *
	 * @since 3.0
	 *
	 * @return string
	 */
	protected function getParserClass() {
		return 'Maps\LocationParser';
	}

	/**
	 * @see ValueParserTestBase::requireDataValue
	 *
	 * @since 3.0
	 *
	 * @return boolean
	 */
	protected function requireDataValue() {
		return false;
	}

}
