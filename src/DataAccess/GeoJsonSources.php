<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

/**
 * The GeoJSON of a single map, gathered from the sources given via the geojson parameter.
 */
class GeoJsonSources {

	/**
	 * @var GeoJsonFetcherResult[]
	 */
	private array $results;

	/**
	 * @param GeoJsonFetcherResult[] $results One result per given source, including sources that could not be fetched.
	 */
	public function __construct( array $results ) {
		$this->results = array_values( $results );
	}

	/**
	 * The content of the sources that could be fetched. L.geoJSON() takes this list as is.
	 *
	 * @return array[]
	 */
	public function getContents(): array {
		return array_values(
			array_filter(
				array_map(
					static fn ( GeoJsonFetcherResult $result ): array => $result->getContent(),
					$this->results
				),
				static fn ( array $content ): bool => $content !== []
			)
		);
	}

	/**
	 * The GeoJson page the visual editor may write to, or null when there is none.
	 *
	 * The editor replaces the whole page with the layer it was opened on, so a map showing more than one
	 * source must not have an editable page: the save would overwrite it with just one of the sources.
	 * Whether the sources could be fetched deliberately plays no role in the count, so a source becoming
	 * reachable or unreachable cannot turn the editor on for a map that shows several.
	 */
	public function getEditablePageName(): ?string {
		return $this->getEditableResult()?->getTitleValue()?->getText();
	}

	public function getEditableRevisionId(): ?int {
		return $this->getEditableResult()?->getRevisionId();
	}

	private function getEditableResult(): ?GeoJsonFetcherResult {
		if ( count( $this->results ) === 1 && $this->isGeoJsonPage( $this->results[0] ) ) {
			return $this->results[0];
		}

		return null;
	}

	/**
	 * The editor saves to `GeoJson:` plus this name, so a page in any other namespace, which the fetcher
	 * also accepts as long as its content is JSON, must not be editable.
	 */
	private function isGeoJsonPage( GeoJsonFetcherResult $result ): bool {
		return $result->getTitleValue()?->getNamespace() === NS_GEO_JSON;
	}

}
