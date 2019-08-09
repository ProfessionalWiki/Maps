<?php

declare( strict_types = 1 );

namespace Maps\Presentation;

use FormatJson;
use Html;

class MapHtmlBuilder {

	public function getMapHTML( array $params, string $mapName, string $serviceName ): string {
		if ( is_int( $params['height'] ) ) {
			$params['height'] = (string)$params['height'] . 'px';
		}

		if ( is_int( $params['width'] ) ) {
			$params['width'] = (string)$params['width'] . 'px';
		}

		return Html::rawElement(
			'div',
			[
				'id' => $mapName,
				'style' => "width: {$params['width']}; height: {$params['height']}; background-color: #cccccc; overflow: hidden;",
				'class' => 'maps-map maps-' . $serviceName
			],
			wfMessage( 'maps-loading-map' )->inContentLanguage()->escaped() .
			Html::element(
				'div',
				[ 'style' => 'display:none', 'class' => 'mapdata' ],
				FormatJson::encode( $params )
			)
		);
	}

}
