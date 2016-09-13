<?php

namespace Maps\Test;

use MapsMapper;

/**
 * @covers MapsMapper
 *
 * @since 3.6
 *
 * @group Maps
 *
 * @licence GNU GPL v2+
 * @author Peter Grassberger < petertheone@gmail.com >
 */
class MapsMapperTest extends \PHPUnit_Framework_TestCase {

    public function imageUrlProvider() {
        return [
            ['markerImage.png', 'markerImage.png'],
            ['/w/images/c/ce/Green_marker.png', '/w/images/c/ce/Green_marker.png'],
            ['//semantic-mediawiki.org/w/images/c/ce/Green_marker.png', '//semantic-mediawiki.org/w/images/c/ce/Green_marker.png'],
            ['Cat2.jpg', 'Cat2.jpg'],
        ];
    }

    /**
     * Tests MapsMapperTest::getFileUrl()
     *
     * @dataProvider imageUrlProvider
     */
    public function testGetFileUrl($file, $expected) {
        $this->assertSame( $expected, MapsMapper::getFileUrl($file) );
    }

    /**
     * Tests MapsMapperTest::getFileUrl()
     */
    public function testGivenNull_getFileUrlReturnsNull() {
        $this->assertNull( MapsMapper::getFileUrl(null));
    }

    // TODO test with existing imagePage
}
