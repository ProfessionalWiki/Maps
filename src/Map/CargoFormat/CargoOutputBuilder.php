<?php

declare( strict_types = 1 );

namespace Maps\Map\CargoFormat;

use CargoFieldDescription;
use DataValues\Geo\Values\LatLongValue;
use Maps\Map\MapOutput;
use Maps\Map\MapOutputBuilder;
use Maps\Map\Marker;
use Maps\MappingService;
use Maps\MappingServices;
use ParamProcessor\ParamDefinitionFactory;
use ParamProcessor\Processor;

class CargoOutputBuilder {

	private $outputBuilder;
	private $services;
	private $paramDefinitionFactory;

	public function __construct( MapOutputBuilder $outputBuilder, MappingServices $services, ParamDefinitionFactory $paramDefinitionFactory ) {
		$this->outputBuilder = $outputBuilder;
		$this->services = $services;
		$this->paramDefinitionFactory = $paramDefinitionFactory;
	}

	public function buildOutputFromCargoData( array $values, array $formattedValues, array $fieldDescriptions, array $displayParams ): MapOutput {
		$service = $this->services->getDefaultService();

		$mapData = $service->newMapDataFromProcessingResult( $this->processParameters( $service, $displayParams ) );

		$mapData->setMarkers( $this->cargoValuesToMarkers(
			$formattedValues,
			$fieldDescriptions
		) );

		return $this->outputBuilder->buildOutput(
			$service,
			$mapData
		);
	}

	private function processParameters( MappingService $service, array $displayParams ) {
		$processor = Processor::newDefault();

		$processor->setParameters( $displayParams );
		$processor->setParameterDefinitions( $this->getParameterDefinitions( $service ) );

		return $processor->processParameters();
	}

	private function getParameterDefinitions( MappingService $service ): array {
		return $this->paramDefinitionFactory->newDefinitionsFromArrays( $service->getParameterInfo() ) ;
	}

	/**
	 * @return Marker[]
	 */
	private function cargoValuesToMarkers( array $formattedValues, array $fieldDescriptions ): array {
		$coordinateFields = $this->getCoordinateFieldNames( $fieldDescriptions );
		$markers = [];

		foreach ( $formattedValues as $valuesRow ) {
			foreach ( $coordinateFields as $coordinateField ) {
				$markers[] = $this->newMarker( $valuesRow, $coordinateField, $coordinateFields );
			}
		}

		return $markers;
	}

	/**
	 * @param CargoFieldDescription[] $fieldDescriptions
	 * @return string[]
	 */
	private function getCoordinateFieldNames( array $fieldDescriptions ): array {
		$names = [];

		foreach ( $fieldDescriptions as $fieldName => $field ) {
			if ( $field->mType === 'Coordinates' ) {
				$names[] = str_replace( ' ', '_', $fieldName );
			}
		}

		return $names;
	}

	private function newMarker( array $valuesRow, string $coordinateField, array $coordinateFields ): Marker {
		$marker = new Marker( new LatLongValue(
			(float)$valuesRow[$coordinateField . '  lat'],
			(float)$valuesRow[$coordinateField . '  lon']
		) );

		$title = array_shift( $valuesRow ) ?? '';
		$valueList = $this->getPropertyValueList( $valuesRow, $coordinateFields );
		$separator = $title === '' || $valueList === '' ? '' : '<br><br>';

		$marker->setText( '<strong>' . $title . '</strong>' . $separator . $valueList );

		return $marker;
	}

	private function getPropertyValueList( array $valuesRow, array $coordinateFields ): string {
		$lines = [];

		foreach ( $valuesRow as $name => $value ) {
			if ( $this->shouldDisplayValue( $name, $coordinateFields ) ) {
				$lines[] = $name . ': ' . $value;
			}
		}

		return implode( '<br>', $lines );
	}

	private function shouldDisplayValue( string $name, array $coordinateFields ): bool {
		$hiddenFields = [];

		foreach ( $coordinateFields as $field ) {
			$hiddenFields[] = $field;
			$hiddenFields[] = $field . '  lat';
			$hiddenFields[] = $field . '  lon';
		}

		return !in_array( $name, $hiddenFields );
	}

}
