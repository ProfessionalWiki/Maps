<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

class GeoJsonFetcherResult {

	private $content;
	private $source;

	public function __construct( array $content, ?\TitleValue $source ) {
		$this->content = $content;
		$this->source = $source;
	}

	public function getContent(): array {
		return $this->content;
	}

	public function getTitleValue(): ?\TitleValue {
		return $this->source;
	}

}
