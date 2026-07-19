<?php

declare( strict_types = 1 );

namespace Maps\Tests\TestDoubles;

use Maps\DataAccess\PageContentFetcher;
use MediaWiki\Content\Content;

class StubPageContentFetcher extends PageContentFetcher {

	public function __construct(
		private ?Content $content
	) {
	}

	public function getPageContent( string $pageTitle, int $defaultNamespace = NS_MAIN ): ?Content {
		return $this->content;
	}

}
