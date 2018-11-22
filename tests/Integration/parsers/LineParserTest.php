<?php

namespace Maps\Tests\Integration\parsers;

use DataValues\Geo\Values\LatLongValue;
use Jeroen\SimpleGeocoder\Geocoders\InMemoryGeocoder;
use Maps\Elements\Line;
use Maps\Presentation\WikitextParsers\LineParser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Presentation\WikitextParsers\LineParser
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class LineParserTest extends TestCase {

	public function testGivenOneCoordinate_lineWithOneCoordinateIsReturned() {
		$parser = $this->newParser();

		$this->assertEquals(
			new Line( [ new LatLongValue( 4, 2 ) ] ),
			$parser->parse( '4,2' )
		);
	}

	/**
	 * @return \Maps\Presentation\WikitextParsers\LineParser
	 */
	private function newParser() {
		$parser = new LineParser();

		$parser->setGeocoder(
			new InMemoryGeocoder(
				[
					'4,2' => new LatLongValue( 4, 2 ),
					'2,3' => new LatLongValue( 2, 3 ),
				]
			)
		);

		return $parser;
	}

	public function testGivenTwoCoordinates_lineWithBothCoordinateIsReturned() {
		$parser = $this->newParser();

		$this->assertEquals(
			new Line(
				[
					new LatLongValue( 4, 2 ),
					new LatLongValue( 2, 3 )
				]
			),
			$parser->parse( '4,2:2,3' )
		);
	}

	public function testTitleAndTextGetSetWhenPresent() {
		$parser = $this->newParser();

		$expectedLine = new Line(
			[
				new LatLongValue( 4, 2 ),
				new LatLongValue( 2, 3 )
			]
		);
		$expectedLine->setTitle( 'title' );
		$expectedLine->setText( 'text' );

		$this->assertEquals(
			$expectedLine,
			$parser->parse( '4,2:2,3~title~text' )
		);
	}

}
