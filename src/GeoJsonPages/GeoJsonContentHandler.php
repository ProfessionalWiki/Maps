<?php

declare( strict_types = 1 );

namespace Maps\GeoJsonPages;

use Content;
use Maps\MapsFactory;
use Maps\Presentation\OutputFacade;
use MediaWiki\Content\Renderer\ContentParseParams;
use ParserOutput;

class GeoJsonContentHandler extends \JsonContentHandler {

	public function __construct( $modelId = GeoJsonContent::CONTENT_MODEL_ID ) {
		parent::__construct( $modelId );
	}

	protected function getContentClass(): string {
		return version_compare( MW_VERSION, '1.38', '<' ) ? GeoJsonLegacyContent::class : GeoJsonContent::class;
	}

	public function makeEmptyContent(): GeoJsonContent {
		$class = $this->getContentClass();
		return new $class( $class::newEmptyContentString() );
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

		if ( !$cpoParams->getGenerateHtml() || !$content->isValid() ) {
			$parserOutput->setText( '' );
			return;
		}

		GeoJsonMapPageUi::forExistingPage( $content->beautifyJSON() )
			->addToOutput( OutputFacade::newFromParserOutput( $parserOutput ) );

		if ( MapsFactory::globalInstance()->smwIntegrationIsEnabled() && $parserOutput->hasText() ) {
			MapsFactory::globalInstance()
				->newSemanticGeoJsonStore( $parserOutput, $cpoParams->getPage() )
				->storeGeoJson( $parserOutput->getRawText() );
		}
	}
}
