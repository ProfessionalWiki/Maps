<?php

namespace Maps\MediaWiki\Content;

use Html;
use ParserOptions;
use ParserOutput;
use Title;

class GeoJsonContent extends \JsonContent {

	public const CONTENT_MODEL_ID = 'GeoJSON';

	public function __construct( string $text, string $modelId = self::CONTENT_MODEL_ID ) {
		parent::__construct( $text, $modelId );
	}

	protected function fillParserOutput( Title $title, $revId, ParserOptions $options,
		$generateHtml, ParserOutput &$output ) {

		if ( $generateHtml && $this->isValid() ) {
			$output->setText( $this->getMapHtml( $this->beautifyJSON() ) );
			$output->addModules( 'ext.maps.leaflet.editor' );
		} else {
			$output->setText( '' );
		}
	}

	private function getMapHtml( string $jsonString ): string {
		return Html::rawElement(
				'div',
				[
					'id' => 'GeoJsonMap',
					'style' => "width: 100%; height: 600px; background-color: #eeeeee; overflow: hidden;",
					'class' => 'maps-map maps-leaflet GeoJsonMap'
				],
				wfMessage( 'maps-loading-map' )->inContentLanguage()->escaped()
			) .
			Html::element(
				'script',
				[],
				'var GeoJson =' . $jsonString . ';'
			);
	}

}
