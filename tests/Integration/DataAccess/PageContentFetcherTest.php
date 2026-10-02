<?php

declare( strict_types = 1 );

namespace Maps\Tests\Integration\DataAccess;

use Maps\DataAccess\PageContentFetcher;
use Maps\Tests\MapsTestFactory;
use Maps\Tests\Util\DeprecationWarnings;
use Maps\Tests\Util\PageCreator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\DataAccess\PageContentFetcher
 */
class PageContentFetcherTest extends TestCase {

	public function testReturnsTheContentOfAPageInTheDefaultNamespace(): void {
		PageCreator::instance()->createPage( 'MediaWiki:PageContentFetcherTest', 'Page text' );

		$this->assertSame(
			'Page text',
			$this->newFetcher()->getPageContent( 'PageContentFetcherTest', NS_MEDIAWIKI )?->serialize()
		);
	}

	private function newFetcher(): PageContentFetcher {
		return MapsTestFactory::newTestInstance()->getPageContentFetcher();
	}

	public function testReturnsTheCurrentContentOfAnEditedPage(): void {
		PageCreator::instance()->createPage( 'PageContentFetcherTest edited page', 'Earlier text' );
		PageCreator::instance()->createPage( 'PageContentFetcherTest edited page', 'Current text' );

		$this->assertSame(
			'Current text',
			$this->newFetcher()->getPageContent( 'PageContentFetcherTest edited page' )?->serialize()
		);
	}

	public function testReturnsNullWhenThePageDoesNotExist(): void {
		$this->assertNull(
			$this->newFetcher()->getPageContent( 'PageContentFetcherTest missing page', NS_MEDIAWIKI )
		);
	}

	public function testReadsThePageWithoutDeprecationWarnings(): void {
		PageCreator::instance()->createPage( 'MediaWiki:PageContentFetcherTest', 'Page text' );
		$fetcher = $this->newFetcher();

		$this->assertSame(
			[],
			DeprecationWarnings::during( fn () => $fetcher->getPageContent( 'PageContentFetcherTest', NS_MEDIAWIKI ) )
		);
	}

}
