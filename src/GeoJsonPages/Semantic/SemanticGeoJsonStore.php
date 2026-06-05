<?php

declare( strict_types = 1 );

namespace Maps\GeoJsonPages\Semantic;

use Maps\GeoJsonPages\GeoJsonStore;
use MediaWiki\Title\Title;
use SMW\DataItems\Property;
use SMW\EventDispatcher\EventDispatcher;
use SMW\ParserData;
use SMW\DataItems\Container;

class SemanticGeoJsonStore implements GeoJsonStore {

	private ParserData $parserData;
	private Title $subjectPage;
	private EventDispatcher $smwEventDispatcher;
	private SubObjectBuilder $subObjectBuilder;

	public function __construct( ParserData $parserData, Title $subjectPage, EventDispatcher $smwEventDispatcher, SubObjectBuilder $subObjectBuilder ) {
		$this->parserData = $parserData;
		$this->subjectPage = $subjectPage;
		$this->smwEventDispatcher = $smwEventDispatcher;
		$this->subObjectBuilder = $subObjectBuilder;
	}

	public function storeGeoJson( string $geoJson ) {
		foreach ( $this->subObjectBuilder->getSubObjectsFromGeoJson( $geoJson ) as $subObject ) {
			$this->parserData->getSemanticData()->addPropertyObjectValue(
				new Property( Property::TYPE_SUBOBJECT ),
				new Container( $subObject->toContainerSemanticData( $this->subjectPage->getTitleValue() ) )
			);
		}

		$this->parserData->copyToParserOutput();

		$this->smwEventDispatcher->dispatch(
			'InvalidateEntityCache',
			[ 'title' => $this->subjectPage, 'context' => 'GeoJsonContent' ]
		);
	}

}
