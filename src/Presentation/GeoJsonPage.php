<?php

declare( strict_types = 1 );

namespace Maps\Presentation;

use Html;

class GeoJsonPage {

	private $json;

	public function __construct( string $json ) {
		$this->json = $json;
	}

	public function addToParserOutput( \ParserOutput $parserOutput ) {
		$parserOutput->setText( $this->getMapHtml() );
		$parserOutput->addModules( 'ext.maps.leaflet.editor' );
	}

	public function addToOutputPage( \OutputPage $output ) {
		$output->addHTML( $this->getMapHtml() );
		$output->addModules( 'ext.maps.leaflet.editor' );
	}

	private function getMapHtml(): string {
		return
			Html::element(
				'script',
				[],
				'var GeoJson =' . $this->json . ';'
			)
			. $this->wrapHtmlInThumbDivs(
				Html::rawElement(
					'div',
					[
						'id' => 'GeoJsonMap',
						'style' => "width: 100%; height: 600px; background-color: #eeeeee; overflow: hidden;",
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

}
