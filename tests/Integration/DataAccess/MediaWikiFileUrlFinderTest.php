<?php

namespace Maps\Tests\Integration\DataAccess;

use Maps\DataAccess\MediaWikiFileUrlFinder;
use Maps\FileUrlFinder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\DataAccess\MediaWikiFileUrlFinder
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MediaWikiFileUrlFinderTest extends TestCase {

	/**
	 * @var FileUrlFinder
	 */
	private $urlFinder;

	public function setUp() {
		$this->urlFinder = new MediaWikiFileUrlFinder();
	}

	public function testGivenUrl_urlIsReturnedAsProvided() {
		$this->assertSame(
			'http://example.com/such',
			$this->urlFinder->getUrlForFileName( 'http://example.com/such' )
		);
	}

}
