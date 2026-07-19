<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit;

use Maps\AttributionSanitizer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\AttributionSanitizer
 */
class AttributionSanitizerTest extends TestCase {

	private function sanitize( string $attribution ): string {
		return ( new AttributionSanitizer() )->sanitize( $attribution );
	}

	public function testPlainTextIsUnchanged() {
		$this->assertSame(
			'Map data © OpenStreetMap contributors',
			$this->sanitize( 'Map data © OpenStreetMap contributors' )
		);
	}

	public function testPlainTextEntitiesArePreserved() {
		$this->assertSame( 'Tiles &amp; data', $this->sanitize( 'Tiles &amp; data' ) );
	}

	public function testHttpsLinkIsKept() {
		$html = $this->sanitize( '<a href="https://openstreetmap.org">OSM</a>' );

		$this->assertStringContainsString( 'href="https://openstreetmap.org"', $html );
		$this->assertStringContainsString( 'OSM', $html );
	}

	public function testHttpLinkIsKept() {
		$this->assertStringContainsString(
			'href="http://example.org"',
			$this->sanitize( '<a href="http://example.org">Example</a>' )
		);
	}

	public function testLinkTitleIsKept() {
		$this->assertStringContainsString(
			'title="OpenStreetMap"',
			$this->sanitize( '<a href="https://openstreetmap.org" title="OpenStreetMap">OSM</a>' )
		);
	}

	public function testImageTagIsStripped() {
		$this->assertSame( '', $this->sanitize( '<img src=x onerror="alert(1)">' ) );
	}

	public function testScriptTagIsReducedToInertText() {
		$html = $this->sanitize( '<script>alert(1)</script>' );

		$this->assertStringNotContainsString( '<script', $html );
		$this->assertStringNotContainsString( '<', $html );
	}

	public function testJavascriptHrefIsStripped() {
		$html = $this->sanitize( '<a href="javascript:alert(1)">click</a>' );

		$this->assertStringNotContainsString( 'javascript', $html );
		$this->assertStringContainsString( 'click', $html );
	}

	public function testObfuscatedJavascriptHrefIsStripped() {
		$this->assertStringNotContainsString(
			'alert(1)',
			$this->sanitize( '<a href="java&#9;script:alert(1)">click</a>' )
		);
	}

	public function testEntityEncodedColonInJavascriptHrefIsStripped() {
		$this->assertStringNotContainsString(
			'javascript',
			$this->sanitize( '<a href="javascript&#58;alert(1)">click</a>' )
		);
	}

	public function testUppercaseJavascriptHrefIsStripped() {
		$this->assertStringNotContainsString(
			'alert(1)',
			$this->sanitize( '<a href="JAVASCRIPT:alert(1)">click</a>' )
		);
	}

	public function testDataHrefIsStripped() {
		$this->assertStringNotContainsString(
			'data:',
			$this->sanitize( '<a href="data:text/html;base64,PHN2Zz4=">click</a>' )
		);
	}

	public function testVbscriptHrefIsStripped() {
		$this->assertStringNotContainsString(
			'vbscript',
			$this->sanitize( '<a href="vbscript:msgbox(1)">click</a>' )
		);
	}

	public function testEventHandlerAndOtherAttributesAreStripped() {
		$html = $this->sanitize(
			'<a href="https://example.org" onclick="steal()" class="x" style="color:red" target="_blank">L</a>'
		);

		$this->assertStringContainsString( 'href="https://example.org"', $html );
		$this->assertStringNotContainsString( 'onclick', $html );
		$this->assertStringNotContainsString( 'class', $html );
		$this->assertStringNotContainsString( 'style', $html );
		$this->assertStringNotContainsString( 'target', $html );
	}

	public function testNestedDisallowedTagsBecomeText() {
		$html = $this->sanitize( '<a href="https://example.org"><b>bold</b> plain</a>' );

		$this->assertStringContainsString( 'href="https://example.org"', $html );
		$this->assertStringContainsString( 'bold plain', $html );
		$this->assertStringNotContainsString( '<b>', $html );
	}

	public function testEachAnchorIsSanitizedWhenThereAreMultiple() {
		$html = $this->sanitize(
			'<a href="https://safe.example">Safe</a><a href="javascript:evil()">Evil</a>'
		);

		$this->assertStringContainsString( 'href="https://safe.example"', $html );
		$this->assertStringContainsString( 'Safe', $html );
		$this->assertStringContainsString( 'Evil', $html );
		$this->assertStringNotContainsString( 'javascript', $html );
	}

	public function testProtocolRelativeHrefIsStripped() {
		$this->assertStringNotContainsString(
			'href',
			$this->sanitize( '<a href="//evil.example/x">link</a>' )
		);
	}

}
