<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\Map;

use Maps\Map\StructuredPopup;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Map\StructuredPopup
 */
class StructuredPopupTest extends TestCase {

	private function getHtml( string $titleHtml, array $propertyValues ): string {
		return ( new StructuredPopup( $titleHtml, $propertyValues ) )->getHtml();
	}

	public function testWithoutPropertyValues() {
		$this->assertSame(
			'<h3 style="padding-top: 0">MyTitle</h3>',
			$this->getHtml( 'MyTitle', [] )
		);
	}

	public function testTitleAndListAreSeparated() {
		$this->assertStringContainsString(
			'</h3><br><strong>',
			$this->getHtml( 'MyTitle', [ 'P1' => 'V1' ] )
		);
	}

	public function testListItems() {
		$this->assertStringContainsString(
			'<strong>P1</strong>: V1<br><strong>P2</strong>: V2',
			$this->getHtml( 'MyTitle', [ 'P1' => 'V1', 'P2' => 'V2' ] )
		);
	}

	public function testPropertyValuesAreSanitized() {
		$this->assertStringNotContainsString(
			'onerror',
			$this->getHtml( 'MyTitle', [ 'P1' => '<img src="x.jpg" onerror="alert(1)">' ] )
		);
	}

	public function testPropertyNamesAreSanitized() {
		$this->assertStringNotContainsString(
			'onerror',
			$this->getHtml( 'MyTitle', [ '<img src="x.jpg" onerror="alert(1)">' => 'V1' ] )
		);
	}

	public function testTitleIsSanitized() {
		$this->assertStringNotContainsString(
			'onerror',
			$this->getHtml( '<img src="x.jpg" onerror="alert(1)">', [] )
		);
	}

}
