<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

class GeoJsonFetcherResult {

	private $content;
	private $revisionId;
	private $source;

	public function __construct( array $content, ?int $revisionId, ?\TitleValue $source ) {
		$this->content = $content;
		$this->revisionId = $revisionId;
		$this->source = $source;
	}

	public function getContent(): array {
		return $this->content;
	}

	public function getTitleValue(): ?\TitleValue {
		return $this->source;
	}

	public function getRevisionId(): ?int {
		return $this->revisionId;
	}

}
