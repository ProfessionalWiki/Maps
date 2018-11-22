<?php

namespace Maps\Tests\Integration;

use Maps\MapsFunctions;
use PHPUnit\Framework\TestCase;

/**
 * @covers MapsFunctions
 *
 * @licence GNU GPL v2+
 * @author Peter Grassberger < petertheone@gmail.com >
 */
class MapsMapperTest extends TestCase {

	public function setUp() {
		if ( !defined( 'MEDIAWIKI' ) ) {
			$this->markTestSkipped( 'MediaWiki is not available' );
		}
	}

	public function imageUrlProvider() {
		return [
			[ 'markerImage.png', 'markerImage.png' ],
			[ '/w/images/c/ce/Green_marker.png', '/w/images/c/ce/Green_marker.png' ],
			[
				'//semantic-mediawiki.org/w/images/c/ce/Green_marker.png',
				'//semantic-mediawiki.org/w/images/c/ce/Green_marker.png'
			],
			[ 'Cat2.jpg', 'Cat2.jpg' ],
		];
	}

	/**
	 * Tests MapsMapperTest::getFileUrl()
	 *
	 * @dataProvider imageUrlProvider
	 */
	public function testGetFileUrl( $file, $expected ) {
		$this->assertSame( $expected, MapsFunctions::getFileUrl( $file ) );
	}
}
