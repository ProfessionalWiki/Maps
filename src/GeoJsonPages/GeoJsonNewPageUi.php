<?php

declare( strict_types = 1 );

namespace Maps\GeoJsonPages;

use Maps\Presentation\OutputFacade;

class GeoJsonNewPageUi {

	private OutputFacade $output;

	public function __construct( OutputFacade $output ) {
		$this->output = $output;
	}

	public function addToOutput(): void {
		$this->output->addModules( 'ext.maps.geojson.new.page' );

		$this->output->addHtml(
			\Html::element(
				'button',
				[
					'id' => 'maps-geojson-new'
				],
				wfMessage( 'maps-geo-json-create-page-button' )->inContentLanguage()->text()
			)
		);
	}

}
