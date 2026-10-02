<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

use MediaWiki\Content\Content;
use MediaWiki\Page\PageLookup;
use MediaWiki\Revision\RevisionLookup;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PageContentFetcher {

	private PageLookup $pageLookup;
	private RevisionLookup $revisionLookup;

	public function __construct( PageLookup $pageLookup, RevisionLookup $revisionLookup ) {
		$this->pageLookup = $pageLookup;
		$this->revisionLookup = $revisionLookup;
	}

	public function getPageContent( string $pageTitle, int $defaultNamespace = NS_MAIN ): ?Content {
		$page = $this->pageLookup->getExistingPageByText( $pageTitle, $defaultNamespace );

		if ( $page === null ) {
			return null;
		}

		$revision = $this->revisionLookup->getRevisionByTitle( $page );

		if ( $revision === null ) {
			return null;
		}

		return $revision->getContent( 'main' );
	}

}
