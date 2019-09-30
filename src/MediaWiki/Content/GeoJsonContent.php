<?php

namespace Maps\MediaWiki\Content;

use Html;
use Maps\Presentation\GeoJsonPage;
use ParserOptions;
use ParserOutput;
use Status;
use Title;

class GeoJsonContent extends \JsonContent {

	public const CONTENT_MODEL_ID = 'GeoJSON';

	public function __construct( string $text, string $modelId = self::CONTENT_MODEL_ID ) {
		parent::__construct( $text, $modelId );
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

	protected function fillParserOutput( Title $title, $revId, ParserOptions $options,
		$generateHtml, ParserOutput &$output ) {

		if ( $generateHtml && $this->isValid() ) {
			$output->setText(
				( new GeoJsonPage() )->getMapHtml()
				.
				Html::element(
					'script',
					[],
					'var GeoJson =' . $this->beautifyJSON() . ';'
				)
			);
			$output->addModules( 'ext.maps.leaflet.editor' );
		} else {
			$output->setText( '' );
		}
	}



}
