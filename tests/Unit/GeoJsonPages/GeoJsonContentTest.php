<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\GeoJsonPages;

use Maps\GeoJsonPages\GeoJsonContent;
use PHPUnit\Framework\TestCase;

class GeoJsonContentTest extends TestCase {

	public function testEmptyJsonIsNotValidContent() {
		$this->assertFalse( ( new GeoJsonContent( '{}' ) )->isValid() );
	}

	public function testMinimalGeoJsonIsValid() {
		$this->assertTrue( ( new GeoJsonContent(
			'{"type": "FeatureCollection", "features": []}'
		) )->isValid() );
	}

}
