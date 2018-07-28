<?php

namespace Maps\GeoJson;

use EditAction;

class GeoJsonContentHandler extends \JsonContentHandler {

	public function __construct( $modelId = GeoJsonContent::CONTENT_MODEL_ID ) {
		parent::__construct( $modelId );
	}

	protected function getContentClass() {
		return GeoJsonContent::class;
	}

	public function getActionOverrides() {
		return [
			//'edit' => EditAction::class,
		];
	}


}