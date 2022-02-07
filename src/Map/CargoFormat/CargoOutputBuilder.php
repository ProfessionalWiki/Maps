<?php

declare( strict_types = 1 );

namespace Maps\Map\CargoFormat;

use CargoFieldDescription;
use DataValues\Geo\Values\LatLongValue;
use Maps\DataAccess\ImageRepository;
use Maps\Map\MapData;
use Maps\Map\MapOutput;
use Maps\Map\MapOutputBuilder;
use Maps\Map\Marker;
use Maps\Map\StructuredPopup;
use Maps\MappingService;
use Maps\MappingServices;
use ParamProcessor\ParamDefinitionFactory;
use ParamProcessor\Processor;

class CargoOutputBuilder {

	private MapOutputBuilder $outputBuilder;
	private MappingServices $services;
	private ParamDefinitionFactory $paramDefinitionFactory;
	private ImageRepository $imageRepository;

	public function __construct( MapOutputBuilder $outputBuilder, MappingServices $services,
		ParamDefinitionFactory $paramDefinitionFactory, ImageRepository $imageRepository ) {

		$this->outputBuilder = $outputBuilder;
		$this->services = $services;
		$this->paramDefinitionFactory = $paramDefinitionFactory;
		$this->imageRepository = $imageRepository;
	}

	public function buildOutputFromCargoData( array $values, array $formattedValues, array $fieldDescriptions, array $displayParams ): MapOutput {
		$service = $this->services->getDefaultService();

		$mapData = $service->newMapDataFromProcessingResult( $this->processParameters( $service, $displayParams ) );
		$mapData->setMarkers( $this->createMarkers( $formattedValues, $fieldDescriptions, $mapData ) );

		return $this->outputBuilder->buildOutput(
			$service,
			$mapData
		);
	}

	private function createMarkers( array $formattedValues, array $fieldDescriptions, MapData $mapData ): array {
		$markers = $this->cargoValuesToMarkers(
			$formattedValues,
			$fieldDescriptions
		);

		$this->setIconUrl( $markers, $mapData->getParameters()['icon'] );

		return $markers;
	}

	private function setIconUrl( array $markers, string $iconParameter ): void {
		if ( $iconParameter !== '' ) {
			$iconUrl = $this->imageRepository->getByName( $iconParameter )->getUrl();

			foreach ( $markers as $marker ) {
				$marker->setIconUrl( $iconUrl );
			}
		}
	}

	private function processParameters( MappingService $service, array $displayParams ) {
		$processor = Processor::newDefault();

		$processor->setParameters( $displayParams );
		$processor->setParameterDefinitions( $this->getParameterDefinitions( $service ) );

		return $processor->processParameters();
	}

	private function getParameterDefinitions( MappingService $service ): array {
		return $this->paramDefinitionFactory->newDefinitionsFromArrays( $service->getParameterInfo() );
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

		$marker->setText(
			( new StructuredPopup(
				array_shift( $valuesRow ) ?? '',
				$this->getPropertyValuesToDisplay( $valuesRow, $coordinateFields )
			) )->getHtml()
		);

		return $marker;
	}

	private function getPropertyValuesToDisplay( array $valuesRow, array $coordinateFields ): array {
		$propertyValues = [];

		foreach ( $valuesRow as $name => $value ) {
			if ( $this->shouldDisplayValue( $name, $coordinateFields ) ) {
				$propertyValues[$name] = $value;
			}
		}

		return $propertyValues;
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
