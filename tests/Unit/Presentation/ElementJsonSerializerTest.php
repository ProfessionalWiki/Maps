<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\Presentation;

use Maps\Presentation\ElementJsonSerializer;
use Maps\Presentation\WikitextParser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Presentation\ElementJsonSerializer
 */
class ElementJsonSerializerTest extends TestCase {

	/**
	 * Google Maps shows inline labels as HTML, and wikitext can render to raw HTML, for instance through extensions.
	 */
	public function testInlineLabelLosesEventHandlersOfItsRenderedHtml(): void {
		$json = [ 'inlineLabel' => 'Picture' ];

		$this->newSerializerRenderingWikitextAs( '<img src="x" onerror="alert(1)">' )->inlineLabel( $json );

		$this->assertStringContainsString( '<img', $json['inlineLabel'] );
		$this->assertStringNotContainsString( 'onerror', $json['inlineLabel'] );
	}

	private function newSerializerRenderingWikitextAs( string $html ): ElementJsonSerializer {
		return new ElementJsonSerializer(
			new class( $html ) extends WikitextParser {
				public function __construct( private string $html ) {
				}

				public function wikitextToHtml( string $text ): string {
					return $this->html;
				}
			}
		);
	}

}
