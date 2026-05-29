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

	public function testEventHandlerAttributeIsRemovedFromImage() {
		$html = $this->getHtml( 'MyTitle', [ 'P1' => '<img src="photo.jpg" onerror="alert(1)">' ] );

		$this->assertStringNotContainsString( 'onerror', $html );
		$this->assertStringContainsString( 'src="photo.jpg"', $html );
	}

	public function testJavascriptUrlIsRemovedFromLink() {
		$html = $this->getHtml( 'MyTitle', [ 'P1' => '<a href="javascript:alert(1)">click</a>' ] );

		$this->assertStringNotContainsString( 'javascript:', $html );
		$this->assertStringContainsString( 'click', $html );
	}

	public function testObfuscatedJavascriptUrlIsRemovedFromLink() {
		$html = $this->getHtml( 'MyTitle', [ 'P1' => '<a href="Java&#9;Script:alert(1)">click</a>' ] );

		$this->assertStringNotContainsString( 'alert(1)', $html );
	}

	public function testDataUrlIsRemovedFromLink() {
		$html = $this->getHtml( 'MyTitle', [ 'P1' => '<a href="data:text/html;base64,PHN2Zz4=">click</a>' ] );

		$this->assertStringNotContainsString( 'data:', $html );
	}

	public function testEventHandlerInTitleIsRemoved() {
		$html = $this->getHtml( '<img src="logo.png" onerror="alert(1)">', [] );

		$this->assertStringNotContainsString( 'onerror', $html );
		$this->assertStringContainsString( 'src="logo.png"', $html );
	}

	public function testRelativeLinkUrlIsPreserved() {
		$html = $this->getHtml( 'MyTitle', [ 'P1' => '<a href="/wiki/Berlin">Berlin</a>' ] );

		$this->assertStringContainsString( 'href="/wiki/Berlin"', $html );
	}

	public function testRelativeImageUrlIsPreserved() {
		$html = $this->getHtml( 'MyTitle', [ 'P1' => '<img src="/images/Berlin.jpg">' ] );

		$this->assertStringContainsString( 'src="/images/Berlin.jpg"', $html );
	}

	public function testEventHandlerIsRemovedWhenAttributeValueContainsAngleBracket() {
		$html = $this->getHtml( 'MyTitle', [ 'P1' => '<img src="ok.jpg" alt="a>b" onerror="alert(1)">' ] );

		$this->assertStringNotContainsString( 'onerror', $html );
		$this->assertStringContainsString( 'src="ok.jpg"', $html );
	}

	public function testEntityEncodedColonInJavascriptUrlIsRemoved() {
		$html = $this->getHtml( 'MyTitle', [ 'P1' => '<a href="javascript&#58;alert(1)">click</a>' ] );

		$this->assertStringNotContainsString( 'javascript', $html );
	}

	public function testUppercaseJavascriptUrlIsRemoved() {
		$html = $this->getHtml( 'MyTitle', [ 'P1' => '<a href="JAVASCRIPT:alert(1)">click</a>' ] );

		$this->assertStringNotContainsString( 'alert(1)', $html );
	}

	public function testVbscriptUrlIsRemoved() {
		$html = $this->getHtml( 'MyTitle', [ 'P1' => '<a href="vbscript:msgbox(1)">click</a>' ] );

		$this->assertStringNotContainsString( 'vbscript', $html );
	}

	public function testEachElementIsSanitizedWhenValueHasMultiple() {
		$html = $this->getHtml( 'MyTitle', [ 'P1' => '<a href="/wiki/A">A</a><img src="/b.jpg" onerror="alert(1)">' ] );

		$this->assertStringNotContainsString( 'onerror', $html );
		$this->assertStringContainsString( 'href="/wiki/A"', $html );
		$this->assertStringContainsString( 'src="/b.jpg"', $html );
	}

}
