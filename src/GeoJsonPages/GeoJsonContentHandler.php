<?php

declare( strict_types = 1 );

namespace Maps\GeoJsonPages;

use Content;
use Maps\MapsFactory;
use Maps\Presentation\OutputFacade;
use MediaWiki\Content\Renderer\ContentParseParams;
use MediaWiki\Title\Title;
use ParserOutput;

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

	/**
	 * @inheritdoc
	 */
	protected function fillParserOutput(
		Content $content,
		ContentParseParams $cpoParams,
		ParserOutput &$parserOutput
	) {
		'@phan-var GeoJsonContent $content';

		if ( $cpoParams->getGenerateHtml() && $content->isValid() ) {

			// display map
			( GeoJsonMapPageUi::forExistingPage( GeoJsonContent::formatJson( $content->getData()->getValue() ) ) )
				->addToOutput( OutputFacade::newFromParserOutput( $parserOutput ) );

			if ( MapsFactory::globalInstance()->smwIntegrationIsEnabled() ) {
				$text = json_encode( $content->getData()->getValue() );

				$subjectPage = $cpoParams->getPage();
				if ( !$subjectPage instanceof Title ) {
					// all underlying methods expect a Title, so cast it to one
					$subjectPage = Title::newFromPageReference( $subjectPage );
				}
				MapsFactory::globalInstance()
					->newSemanticGeoJsonStore( $parserOutput, $subjectPage )
					->storeGeoJson( $text );
			}

		} else {
			parent::fillParserOutput( $content, $cpoParams, $parserOutput );
		}
	}
}
