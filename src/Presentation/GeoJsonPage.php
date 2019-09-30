<?php

declare( strict_types = 1 );

namespace Maps\Presentation;

use Html;

class GeoJsonPage {

	public function getMapHtml(): string {
		return $this->wrapHtmlInThumbDivs(
			Html::rawElement(
				'div',
				[
					'id' => 'GeoJsonMap',
					'style' => "width: 100%; height: 600px; background-color: #eeeeee; overflow: hidden;",
					'class' => 'maps-map maps-leaflet GeoJsonMap'
				],
				wfMessage( 'maps-loading-map' )->inContentLanguage()->escaped()
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
