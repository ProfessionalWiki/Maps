<?php

namespace Maps\GeoJson;

class GeoJsonContent extends \JsonContent {

	/* public */ const CONTENT_MODEL_ID = 'GeoJSON';

	public function __construct( $text, $modelId = self::CONTENT_MODEL_ID ) {
		parent::__construct( $text, $modelId );
	}

}