<?php

declare( strict_types = 1 );

namespace Maps\GeoJsonPages;

use Maps\GeoJsonPages\GeoJsonContent as GeoJsonPagesGeoJsonContent;
use Maps\MapsFactory;
use Maps\Presentation\OutputFacade;
use ParserOptions;
use ParserOutput;
use Title;

/**
 * @deprecated This class should be removed when Maps drops support for MediaWiki 1.37.
 */
class GeoJsonLegacyContent extends GeoJsonPagesGeoJsonContent {

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
