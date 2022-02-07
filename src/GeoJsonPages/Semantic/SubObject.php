<?php

declare( strict_types = 1 );

namespace Maps\GeoJsonPages\Semantic;

use SMW\DataModel\ContainerSemanticData;
use SMW\DIProperty;
use SMW\DIWikiPage;
use TitleValue;

class SubObject {

	private string $name;

	/**
	 * @var array<string, array<int, \SMWDataItem>>
	 */
	private array $values = [];

	public function __construct( string $name ) {
		$this->name = $name;
	}

	public function addPropertyValuePair( string $propertyName, \SMWDataItem $dataItem ) {
		$this->values[$propertyName][] = $dataItem;
	}

	public function toContainerSemanticData( TitleValue $subjectPage ): ContainerSemanticData {
		$container = $this->newContainerSemanticData( $subjectPage );

		foreach ( $this->values as $propertyName => $dataItems ) {
			foreach ( $dataItems as $dataItem ) {
				$container->addPropertyObjectValue(
					new DIProperty( $propertyName ),
					$dataItem
				);
			}
		}

		return $container;
	}

	private function newContainerSemanticData( TitleValue $subjectPage ): ContainerSemanticData {
		return new ContainerSemanticData(
			new DIWikiPage(
				$subjectPage->getDBkey(),
				$subjectPage->getNamespace(),
				$subjectPage->getInterwiki(),
				$this->name
			)
		);
	}

	public function getName(): string {
		return $this->name;
	}

	public function getValues(): array {
		return $this->values;
	}

}
