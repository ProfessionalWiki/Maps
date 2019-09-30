<?php

namespace Maps\MediaWiki\Content;

use FormatJson;

class GeoJsonContentHandler extends \JsonContentHandler {

	public function __construct( $modelId = GeoJsonContent::CONTENT_MODEL_ID ) {
		parent::__construct( $modelId );
	}

	protected function getContentClass() {
		return GeoJsonContent::class;
	}

	public function makeEmptyContent() {
		$text = '{"type": "FeatureCollection", "features": []}';

		return new GeoJsonContent(
			FormatJson::encode( FormatJson::parse( $text )->getValue(), true, FormatJson::UTF8_OK )
		);
	}

}
