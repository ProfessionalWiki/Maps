<?php

declare( strict_types = 1 );

namespace Maps\Map\CargoFormat;

use CargoDisplayFormat;
use Maps\MapsFactory;
use Parser;
use ParserOutput;

class CargoFormat extends CargoDisplayFormat {

	/**
	 * @var $parser Parser|ParserOutput|null
	 */
	private $parser;

	public function __construct( $output, $parser = null ) {
		parent::__construct( $output, $parser );
		$this->parser = $parser;
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

		if ( $this->parser !== null ) {
			$this->getParserOutput()->addHeadItem( $mapOutput->getHeadItems() );
			$this->getParserOutput()->addModules( $mapOutput->getResourceModules() );
		}

		return $mapOutput->getHtml();
	}

	private function getParserOutput(): ParserOutput {
		if ( $this->parser instanceof Parser ) {
			return $this->parser->getOutput();
		}

		return $this->parser;
	}

	private function getResultBuilder(): CargoOutputBuilder {
		return MapsFactory::globalInstance()->newCargoOutputBuilder();
	}

}
