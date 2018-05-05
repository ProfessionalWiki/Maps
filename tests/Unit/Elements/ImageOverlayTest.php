<?php

namespace Maps\Tests\Elements;

use DataValues\Geo\Values\LatLongValue;
use Maps\Elements\ImageOverlay;
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

}



