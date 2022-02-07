<?php

declare( strict_types = 1 );

namespace Maps\GeoJsonPages;

class GeoJsonContentHandler extends \JsonContentHandler {

	public function __construct( $modelId = GeoJsonContent::CONTENT_MODEL_ID ) {
		parent::__construct( $modelId );
	}

	protected function getContentClass(): string {
		return GeoJsonContent::class;
	}

	public function makeEmptyContent(): GeoJsonContent {
		return new GeoJsonContent( GeoJsonContent::newEmptyContentString() );
	}

}
