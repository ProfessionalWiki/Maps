<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration\Parser;

use Maps\Tests\MapsTestFactory;
use Maps\Tests\TestDoubles\ImageValueObject;
use Maps\Tests\Util\TestFactory;
use PHPUnit\Framework\TestCase;

class LeafletTest extends TestCase {

	private function parse( string $textToParse ): string {
		return TestFactory::newInstance()->parse( $textToParse );
	}

	public function testLeafletImageLayersIgnoresNotFoundImages() {
		$this->assertStringContainsString(
			'"imageLayers":[]',
			$this->parse(
				"{{#leaflet:image layers=404.png}}"
			)
		);
	}

	public function testLeafletImageLayersIgnoresImageUrls() {
		$this->assertStringContainsString(
			'"imageLayers":[]',
			$this->parse(
				"{{#leaflet:image layers=https://user-images.githubusercontent.com/62098559/76514021-3fa9be80-647d-11ea-82ae-715420a5c432.png}}"
			)
		);
	}

	public function testLeafletImageLayer() {
		$factory = MapsTestFactory::newTestInstance();

		$factory->imageRepo->addImage(
			'MyImage.png',
			new ImageValueObject( '/tmp/example/image.png', 40, 20 )
		);

		$html = $this->parse( "{{#leaflet:image layers=MyImage.png}}" );

		$this->assertStringContainsString( '"name":"MyImage.png"', $html );
		$this->assertStringContainsString( '"url":"/tmp/example/image.png"', $html );
		$this->assertStringContainsString( '"width":100', $html );
		$this->assertStringContainsString( '"height":50', $html );
	}

}
