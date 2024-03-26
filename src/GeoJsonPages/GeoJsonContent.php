<?php

declare( strict_types = 1 );

namespace Maps\GeoJsonPages;

use FormatJson;
use Status;

class GeoJsonContent extends \JsonContent {

	public const CONTENT_MODEL_ID = 'GeoJSON';

	public static function newEmptyContentString(): string {
		$text = '{"type": "FeatureCollection", "features": []}';
		return self::formatJson( FormatJson::parse( $text )->getValue() );
	}

	public static function formatJson( $value ): string {
		return FormatJson::encode( $value, true, FormatJson::UTF8_OK );
	}

	public function __construct( string $text, string $modelId = self::CONTENT_MODEL_ID ) {
		parent::__construct(
			$text,
			$modelId
		);
	}

	public function getData(): Status {
		$status = parent::getData();

		if ( $status->isGood() && !$this->isGeoJson( $status->getValue() ) ) {
			return Status::newFatal( 'Invalid GeoJson' );
		}

		return $status;
	}

	private function isGeoJson( $json ): bool {
		return property_exists( $json, 'type' )
			&& $json->type === 'FeatureCollection'
			&& property_exists( $json, 'features' )
			&& is_array( $json->features );
	}

}
