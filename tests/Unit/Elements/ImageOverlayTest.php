<?php

namespace Maps\Tests\Unit\Elements;

use DataValues\Geo\Values\LatLongValue;
use Jeroen\SimpleGeocoder\Geocoders\Decorators\CoordinateFriendlyGeocoder;
use Jeroen\SimpleGeocoder\Geocoders\NullGeocoder;
use Maps\Elements\ImageOverlay;
use Maps\Presentation\WikitextParsers\ImageOverlayParser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Elements\ImageOverlay
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ImageOverlayTest extends TestCase {

	public function testGetImage() {
		$imageOverlay = new ImageOverlay(
			new LatLongValue( 4, 2 ),
			new LatLongValue( -4, -2 ),
			'Foo.png'
		);

		$this->assertSame( 'Foo.png', $imageOverlay->getImage() );
	}

	public function testGivenMetaData_overlayHasProvidedMetaData() {
		$parser = new ImageOverlayParser( new CoordinateFriendlyGeocoder( new NullGeocoder() ) );

		$overlay = $parser->parse( "1,2:3,4:https://such.an/image.png~Semantic MediaWiki~World domination imminent!~https://such.link" );

		$this->assertSame( 'https://such.an/image.png', $overlay->getImage() );
		$this->assertSame( 'Semantic MediaWiki', $overlay->getTitle() );
		$this->assertSame( 'World domination imminent!', $overlay->getText() );
		$this->assertSame( 'https://such.link', $overlay->getLink() );
	}

	public function testGivenLinkWithPrefix_linkIsParsedAndPrefixIsRemoved() {
		$parser = new ImageOverlayParser( new CoordinateFriendlyGeocoder( new NullGeocoder() ) );

		$overlay = $parser->parse( "1,2:3,4:https://such.an/image.png~Semantic MediaWiki~World domination imminent!~link:https://such.link" );

		$this->assertSame( 'https://such.link', $overlay->getLink() );
	}

}



