<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\Map;

use Maps\Map\StructuredPopup;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Map\StructuredPopup
 */
class StructuredPopupTest extends TestCase {

	public function testWithoutPropertyValues() {
		$this->assertSame(
			'<h3 style="padding-top: 0">MyTitle</h3>',
			$this->getHtml( 'MyTitle', [] )
		);
	}

	private function getHtml( string $titleHtml, array $propertyValues ) {
		return ( new StructuredPopup( $titleHtml, $propertyValues ) )->getHtml();
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

	public function testLinksAreAllowed() {
		$this->assertStringContainsString(
			'<strong><a href="#">P1</a></strong>: <a href="#">P1</a>',
			$this->getHtml( 'MyTitle', [ '<a href="#">P1</a>' => '<a href="#">P1</a>' ] )
		);
	}

	public function testImagesAreAllowed() {
		$this->assertStringContainsString(
			'<img src="#">',
			$this->getHtml( 'MyTitle', [ 'P1' => '<img src="#">' ] )
		);
	}

	public function testRandomTagsAreFiltered() {
		$this->assertStringContainsString(
			'<strong>P1</strong>: abcde',
			$this->getHtml( 'MyTitle', [ 'P1<script>' => '<ul><li>abc</li></ul>de' ] )
		);
	}

}
