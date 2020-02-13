<?php

declare( strict_types = 1 );

namespace Maps\DataAccess;

use Onoi\EventDispatcher\EventDispatcher;
use SMW\DataModel\ContainerSemanticData;
use SMW\DIProperty;
use SMW\DIWikiPage;
use SMW\ParserData;
use SMWDIContainer;
use Title;

class SemanticGeoJsonStore implements GeoJsonStore {

	private $parserData;
	private $subjectPage;
	private $smwEventDispatcher;

	public function __construct( ParserData $parserData, Title $subjectPage, EventDispatcher $smwEventDispatcher ) {
		$this->parserData = $parserData;
		$this->subjectPage = $subjectPage;
		$this->smwEventDispatcher = $smwEventDispatcher;
	}

	public function storeGeoJson( string $geoJson ) {
		$this->parserData->getSemanticData()->addPropertyObjectValue(
			new DIProperty( 'HasNumber' ),
			new \SMWDINumber( 44 )
		);

//		$dataValue = DataValueFactory::getInstance()->newDataValueByText(
//			'HAsSuchMuh',
//			'123'
//		);
//
//		$this->parserData->getSemanticData()->addDataValue( $dataValue );

		$subObject = new ContainerSemanticData(
			new DIWikiPage(
				$this->subjectPage->getDBkey(),
				$this->subjectPage->getNamespace(),
				$this->subjectPage->getInterwiki(),
				'MySubobjectName'
			)
		);

		$subObject->addPropertyObjectValue(
			new DIProperty( 'HasNumber' ),
			new \SMWDINumber( 42 )
		);

		$this->parserData->getSemanticData()->addPropertyObjectValue(
			new DIProperty( DIProperty::TYPE_SUBOBJECT ),
			new SMWDIContainer( $subObject )
		);

		$this->parserData->copyToParserOutput();

		$this->smwEventDispatcher->dispatch(
			'InvalidateEntityCache',
			[ 'title' => $this->subjectPage, 'context' => 'GeoJsonContent' ]
		);
	}

}
