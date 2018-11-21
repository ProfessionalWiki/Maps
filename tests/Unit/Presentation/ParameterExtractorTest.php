<?php

namespace Maps\Tests\Unit\Presentation;

use Maps\Presentation\ParameterExtractor;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Presentation\ParameterExtractor
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ParameterExtractorTest extends TestCase {

	public function testGivenNoParameters_nullIsReturned() {
		$this->assertNull( ( new ParameterExtractor() )->extract( [ 'name' ], [] ) );
	}

	public function testGivenWhenPrimaryNameIsPresent_itsValueIsReturned() {
		$this->assertSame(
			'value',
			( new ParameterExtractor() )->extract(
				[ 'name' ],
				[ 'foo' => 'bar', 'name' => 'value', 'baz' => 'bah' ]
			)
		);
	}

	public function testGivenAliasIsPresent_itsValueIsReturned() {
		$this->assertSame(
			'value',
			( new ParameterExtractor() )->extract(
				[ 'name', 'secondary', 'alias' ],
				[ 'foo' => 'bar', 'alias' => 'value', 'baz' => 'bah' ]
			)
		);
	}

	public function testWhenAliasAndPrimaryArePresent_thePrimariesValueIsReturned() {
		$this->assertSame(
			'value',
			( new ParameterExtractor() )->extract(
				[ 'name', 'secondary', 'alias' ],
				[ 'foo' => 'bar', 'alias' => 'wrong', 'name' => 'value' ]
			)
		);
	}

	public function testValueIsTrimmed() {
		$this->assertSame(
			'value',
			( new ParameterExtractor() )->extract(
				[ 'name' ],
				[ 'name' => "  value\t  " ]
			)
		);
	}

	public function testWhenUpperCaseIsUsedInTheName_itIsStillFound() {
		$this->assertSame(
			'value',
			( new ParameterExtractor() )->extract(
				[ 'name' ],
				[ 'nAmE' => 'value' ]
			)
		);
	}

	public function testNameHasSpacesAroundIt_itIsStillFound() {
		$this->assertSame(
			'value',
			( new ParameterExtractor() )->extract(
				[ 'name' ],
				[ '  name   ' => 'value' ]
			)
		);
	}

}
