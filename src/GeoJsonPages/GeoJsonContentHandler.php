<?php

/**
 * @reviewer thomas-topway-it for KM-A
 */

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
		
		// @see JsonContentHandler -> fillParserOutput
		if ( $cpoParams->getGenerateHtml() ) {
			if ( $content->isValid() ) {
				$text = $content->getData()->getValue();
				$parserOutput->setText( $content->rootValueTable( $content->getData()->getValue() ) );
				
			} else {
				$error = wfMessage( 'invalid-json-data' )->parse();
				$parserOutput->setText( $error );
			}

			$parserOutput->addModuleStyles( [ 'mediawiki.content.json' ] );
		} else {
			$parserOutput->setText( null );
		}

		// @FIXME alternatively decode $this->mText in GeoJsonLegacyContent
		// to avoid decoding it again in SubObjectBuilder -> getSubObjectsFromGeoJson
		$text = json_encode( $content->getData()->getValue() );

		if ( MapsFactory::globalInstance()->smwIntegrationIsEnabled() && $parserOutput->hasText() ) {
			MapsFactory::globalInstance()
				->newSemanticGeoJsonStore( $parserOutput, $cpoParams->getPage() )
				->storeGeoJson( $text );
		}
	}
}
