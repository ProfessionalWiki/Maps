<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\Presentation;

use Maps\Presentation\HtmlSanitizer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Presentation\HtmlSanitizer
 */
class HtmlSanitizerTest extends TestCase {

	private function sanitize( string $html ): string {
		return ( new HtmlSanitizer() )->sanitize( $html );
	}

	public function testPlainTextIsUnchanged() {
		$this->assertSame( 'Just text', $this->sanitize( 'Just text' ) );
	}

	public function testLinksAreAllowed() {
		$this->assertStringContainsString(
			'<a href="#">P1</a>',
			$this->sanitize( '<a href="#">P1</a>' )
		);
	}

	public function testImagesAreAllowed() {
		$this->assertStringContainsString(
			'<img src="#">',
			$this->sanitize( '<img src="#">' )
		);
	}

	public function testRandomTagsAreFiltered() {
		$this->assertSame( 'abcde', $this->sanitize( '<ul><li>abc</li></ul>de' ) );
	}

	public function testEventHandlerAttributeIsRemovedFromImage() {
		$html = $this->sanitize( '<img src="photo.jpg" onerror="alert(1)">' );

		$this->assertStringNotContainsString( 'onerror', $html );
		$this->assertStringContainsString( 'src="photo.jpg"', $html );
	}

	public function testJavascriptUrlIsRemovedFromLink() {
		$html = $this->sanitize( '<a href="javascript:alert(1)">click</a>' );

		$this->assertStringNotContainsString( 'javascript:', $html );
		$this->assertStringContainsString( 'click', $html );
	}

	public function testObfuscatedJavascriptUrlIsRemovedFromLink() {
		$this->assertStringNotContainsString(
			'alert(1)',
			$this->sanitize( '<a href="Java&#9;Script:alert(1)">click</a>' )
		);
	}

	public function testEntityEncodedColonInJavascriptUrlIsRemoved() {
		$this->assertStringNotContainsString(
			'javascript',
			$this->sanitize( '<a href="javascript&#58;alert(1)">click</a>' )
		);
	}

	public function testUppercaseJavascriptUrlIsRemoved() {
		$this->assertStringNotContainsString(
			'alert(1)',
			$this->sanitize( '<a href="JAVASCRIPT:alert(1)">click</a>' )
		);
	}

	public function testDataUrlIsRemovedFromLink() {
		$this->assertStringNotContainsString(
			'data:',
			$this->sanitize( '<a href="data:text/html;base64,PHN2Zz4=">click</a>' )
		);
	}

	public function testVbscriptUrlIsRemovedFromLink() {
		$this->assertStringNotContainsString(
			'vbscript',
			$this->sanitize( '<a href="vbscript:msgbox(1)">click</a>' )
		);
	}

	public function testEventHandlerIsRemovedWhenAttributeValueContainsAngleBracket() {
		$html = $this->sanitize( '<img src="ok.jpg" alt="a>b" onerror="alert(1)">' );

		$this->assertStringNotContainsString( 'onerror', $html );
		$this->assertStringContainsString( 'src="ok.jpg"', $html );
	}

	public function testRelativeLinkUrlIsPreserved() {
		$this->assertStringContainsString(
			'href="/wiki/Berlin"',
			$this->sanitize( '<a href="/wiki/Berlin">Berlin</a>' )
		);
	}

	public function testRelativeImageUrlIsPreserved() {
		$this->assertStringContainsString(
			'src="/images/Berlin.jpg"',
			$this->sanitize( '<img src="/images/Berlin.jpg">' )
		);
	}

	public function testEachElementIsSanitizedWhenThereAreMultiple() {
		$html = $this->sanitize( '<a href="/wiki/A">A</a><img src="/b.jpg" onerror="alert(1)">' );

		$this->assertStringNotContainsString( 'onerror', $html );
		$this->assertStringContainsString( 'href="/wiki/A"', $html );
		$this->assertStringContainsString( 'src="/b.jpg"', $html );
	}

}
