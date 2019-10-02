<?php

declare( strict_types = 1 );

namespace Maps\Presentation;

use Html;

class GeoJsonPageOutput {

	private $json;

	public static function forNewPage(): self {
		return new self( null );
	}

	public static function forExistingPage( string $mapJson ): self {
		return new self( $mapJson );
	}

	private function __construct( ?string $json ) {
		$this->json = $json;
	}

	public function addToOutput( OutputFacade $output ) {
		$leafletPath = $GLOBALS['wgScriptPath'] . '/extensions/Maps/resources/leaflet/leaflet';

		$output->addHeadItem(
			'MapsGeoJsonHeadItem',
			Html::linkedStyle( "$leafletPath/leaflet.css" ) . Html::linkedScript( "$leafletPath/leaflet.js" )
		);

		$output->addHTML( $this->getJavascript() . $this->getHtml() );
		$output->addModules( 'ext.maps.leaflet.editor' );
	}

	private function getJavascript(): string {
		return Html::element(
			'script',
			[],
			$this->getJsonJs()
		);
	}

	private function getJsonJs(): string {
		return 'var GeoJson ='
			. ( $this->json ?? 'null' )
			. ';';
	}

	private function getHtml(): string {
		if ( $this->isExistingPage() ) {
			return $this->getMapHtml();
		}

		return $this->getNewPageHtml();
	}

	private function isExistingPage(): bool {
		return $this->json !== null;
	}

	private function getMapHtml(): string {
		return $this->wrapHtmlInThumbDivs(
			Html::rawElement(
				'div',
				[
					'id' => 'GeoJsonMap',
					'style' => 'width: 100%; height: 600px; background-color: #eeeeee; overflow: hidden;',
					'class' => 'maps-map maps-leaflet maps-geojson-editor'
				],
				Html::element(
					'div',
					[
						'class' => 'maps-loading-message'
					],
					wfMessage( 'maps-loading-map' )->inContentLanguage()->text()
				)
			)
		);
	}

	private function wrapHtmlInThumbDivs( string $html ): string {
		return Html::rawElement(
			'div',
			[
				'class' => 'thumb'
			],
			Html::rawElement(
				'div',
				[
					'class' => 'thumbinner'
				],
				$html
			)
		);
	}

	private function getNewPageHtml(): string {
		return
			Html::element(
				'button',
				[
					'id' => 'maps-geojson-new'
				],
				wfMessage( 'maps-geo-json-create-page-button' )->inContentLanguage()->text()
			)
			. Html::rawElement(
				'div',
				[
					'style' => 'display: none;',
					'id' => 'maps-geojson-map-wrapper'
				],
				$this->getMapHtml()
			);
	}

}
