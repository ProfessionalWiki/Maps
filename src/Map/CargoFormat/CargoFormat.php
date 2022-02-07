<?php

declare( strict_types = 1 );

namespace Maps\Map\CargoFormat;

use CargoDisplayFormat;
use Maps\MapsFactory;

class CargoFormat extends CargoDisplayFormat {

	private \ParserOutput $parserOutput;

	public function __construct( \ParserOutput $parserOutput ) {
		parent::__construct( $parserOutput );
		$this->parserOutput = $parserOutput;
	}

	public static function allowedParameters() {
		return [
			'height' => [ 'type' => 'int', 'label' => wfMessage( 'cargo-viewdata-heightparam' )->parse() ],
			'width' => [ 'type' => 'int', 'label' => wfMessage( 'cargo-viewdata-widthparam' )->parse() ],
			'icon' => [ 'type' => 'string' ],
			'zoom' => [ 'type' => 'int' ]
		];
	}

	public function display( array $valuesTable, array $formattedValuesTable, array $fieldDescriptions, array $displayParams ) {
		$mapOutput = $this->getResultBuilder()->buildOutputFromCargoData(
			$valuesTable,
			$formattedValuesTable,
			$fieldDescriptions,
			$displayParams
		);

		$this->parserOutput->addHeadItem( $mapOutput->getHeadItems() );
		$this->parserOutput->addModules( $mapOutput->getResourceModules() );

		return $mapOutput->getHtml();
	}

	private function getResultBuilder(): CargoOutputBuilder {
		return MapsFactory::globalInstance()->newCargoOutputBuilder();
	}

}
