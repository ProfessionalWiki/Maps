<?php

namespace Maps\Test;

use DataValues\Geo\Values\LatLongValue;
use Maps\Elements\Line;
use Maps\LineParser;
use ValueParsers\ValueParser;

/**
 * @covers Maps\LineParser
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class LineParserTest extends \ValueParsers\Test\StringValueParserTest {

	/**
	 * @see ValueParserTestBase::validInputProvider
	 *
	 * @since 3.0
	 *
	 * @return array
	 */
	public function validInputProvider() {
		$argLists = [];

		$valid = [];

		$valid[] = [
			[
				42,
				4.2
			],
		];

		$valid[] = [
			[
				49.83798245308486,
				2.724609375
			],
			[
				52.05249047600102,
				8.26171875
			],
			[
				46.37725420510031,
				6.15234375
			],
			[
				49.83798245308486,
				2.724609375
			],
		];

		foreach ( $valid as $values ) {
			$input = [];
			$output = [];

			foreach ( $values as $value ) {
				$input[] = implode( ',', $value );
				$output[] = new LatLongValue( $value[0], $value[1] );
			}

			$input = implode( ':', $input );

			$argLists[] = [ $input, new Line( $output ) ];
		}

		return $argLists;
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

	/**
	 * @since 0.1
	 *
	 * @return ValueParser
	 */
	protected function getInstance() {
		return new LineParser();
	}

}
