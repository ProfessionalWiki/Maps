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
class MapsMapperTest extends \MediaWikiTestCase {

    public static $imageUrls = [
        'markerImage.png' => 'markerImage.png',
        '/w/images/c/ce/Green_marker.png' => '/w/images/c/ce/Green_marker.png',
        '//semantic-mediawiki.org/w/images/c/ce/Green_marker.png' => '//semantic-mediawiki.org/w/images/c/ce/Green_marker.png',
        'Cat2.jpg' => 'Cat2.jpg'
    ];

    function getImagePage( $filename ) {
        $title = Title::makeTitleSafe( NS_FILE, $filename );
        $file = $this->dataFile( $filename );
        $iPage = new ImagePage( $title );
        $iPage->setFile( $file );
        return $iPage;
    }

    /**
     * Tests MapsMapperTest::getFileUrl()
     */
    public function testGetFileUrl() {
        foreach ( self::$imageUrls as $rawValue => $parsedValue ) {
            $this->assertEquals( $parsedValue, MapsMapper::getFileUrl($rawValue) );
        }
        $this->assertEquals( null, MapsMapper::getFileUrl(null));
        // TODO test with existing imagePage
    }
}
