<?php

declare( strict_types = 1 );

namespace Maps\GeoJsonPages;

use FormatJson;
use Maps\MapsFactory;
use Maps\Presentation\OutputFacade;
use ParserOptions;
use ParserOutput;
use Status;
use Title;

class GeoJsonContent extends \JsonContent {

	public const CONTENT_MODEL_ID = 'GeoJSON';

	public static function newEmptyContentString(): string {
		$text = '{"type": "FeatureCollection", "features": []}';
		return FormatJson::encode( FormatJson::parse( $text )->getValue(), true, FormatJson::UTF8_OK );
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

	protected function fillParserOutput( Title $title, $revId, ParserOptions $options,
		$generateHtml, ParserOutput &$output ) {

		if ( !$generateHtml || !$this->isValid() ) {
			$output->setText( '' );
			return;
		}

		$this->addMapHtmlToOutput( $output );

		$this->storeSemanticValues( $title, $output );
	}

	private function addMapHtmlToOutput( ParserOutput $output ) {
		( GeoJsonMapPageUi::forExistingPage( $this->beautifyJSON() ) )->addToOutput( OutputFacade::newFromParserOutput( $output ) );
	}

	private function storeSemanticValues( Title $title, ParserOutput $output ) {
		if ( MapsFactory::globalInstance()->smwIntegrationIsEnabled() ) {
			MapsFactory::globalInstance()->newSemanticGeoJsonStore( $output, $title )->storeGeoJson( $this->mText );
		}
	}

}
