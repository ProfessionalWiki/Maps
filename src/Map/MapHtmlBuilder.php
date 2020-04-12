<?php

declare( strict_types = 1 );

namespace Maps\Map;

use FormatJson;
use Html;

class MapHtmlBuilder {

	public function getMapHTML( array $json, string $mapId, string $serviceName ): string {
		if ( is_int( $json['height'] ) ) {
			$json['height'] = (string)$json['height'] . 'px';
		}

		if ( is_int( $json['width'] ) ) {
			$json['width'] = (string)$json['width'] . 'px';
		}

		return Html::rawElement(
			'div',
			[
				'id' => $mapId,
				'style' => "width: {$json['width']}; height: {$json['height']}; background-color: #eeeeee; overflow: hidden;",
				'class' => 'maps-map maps-' . $serviceName
			],
			Html::element(
				'div',
				[
					'class' => 'maps-loading-message'
				],
				wfMessage( 'maps-loading-map' )->inContentLanguage()->text()
			)
			. Html::element(
				'div',
				[ 'style' => 'display:none', 'class' => 'mapdata' ],
				FormatJson::encode( $json )
			)
		);
	}

}
