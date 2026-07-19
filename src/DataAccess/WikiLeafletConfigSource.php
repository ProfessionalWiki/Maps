<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

use Maps\LeafletConfigSource;
use MediaWiki\Content\JsonContent;
use MediaWiki\Json\FormatJson;

/**
 * Reads the raw Leaflet configuration from the MediaWiki:Maps JSON config page.
 *
 * Returns the decoded "leaflet" section, or null when the page is missing, is not JSON, cannot be
 * decoded, or the database is unavailable (such as during installation). Validation and hardening
 * of the returned data happen downstream, so this only needs to decode the page defensively.
 *
 * @licence GNU GPL v2+
 */
class WikiLeafletConfigSource implements LeafletConfigSource {

	public function __construct(
		private PageContentFetcher $contentFetcher,
		private string $configPageName
	) {
	}

	public function getLeafletConfig(): ?array {
		$data = $this->getPageData();

		if ( $data === null ) {
			return null;
		}

		$leaflet = $data['leaflet'] ?? null;

		return is_array( $leaflet ) ? $leaflet : null;
	}

	private function getPageData(): ?array {
		try {
			$content = $this->contentFetcher->getPageContent( $this->configPageName, NS_MEDIAWIKI );
		} catch ( \Throwable $e ) {
			return null;
		}

		if ( !$content instanceof JsonContent ) {
			return null;
		}

		$decoded = FormatJson::decode( $content->getText(), true );

		return is_array( $decoded ) ? $decoded : null;
	}

}
