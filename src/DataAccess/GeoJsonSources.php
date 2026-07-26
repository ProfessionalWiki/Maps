<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

/**
 * The GeoJSON of a single map, gathered from the one or more sources given via the geojson parameter.
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
	 * The editor saves the whole map layer to this page, so a map showing several sources must not have
	 * one: saving would replace a single page with the combined content of all of them. Which sources
	 * could be fetched deliberately plays no role, so that creating or deleting an unrelated page does
	 * not make the editor appear or disappear.
	 */
	public function getEditablePageName(): ?string {
		return $this->getEditableResult()?->getTitleValue()?->getText();
	}

	public function getEditableRevisionId(): ?int {
		return $this->getEditableResult()?->getRevisionId();
	}

	private function getEditableResult(): ?GeoJsonFetcherResult {
		if ( count( $this->results ) !== 1 ) {
			return null;
		}

		return $this->results[0]->getTitleValue() === null ? null : $this->results[0];
	}

}
