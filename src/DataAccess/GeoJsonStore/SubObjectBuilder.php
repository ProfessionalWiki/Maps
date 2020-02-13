<?php

declare( strict_types = 1 );

namespace Maps\DataAccess\GeoJsonStore;

use Title;

class SubObjectBuilder {

	private $subjectPage;

	public function __construct( Title $subjectPage ) {
		$this->subjectPage = $subjectPage;
	}

	/**
	 * @return SubObject[]
	 */
	public function getSubObjectsFromGeoJson( string $geoJson ) {
		return [  ];
	}

	private function pointToSubobject(): SubObject {
		$subObject = new SubObject( 'MySubobjectName' );

		$subObject->addPropertyValuePair(
			'HasNumber',
			new \SMWDINumber( 455 )
		);

		$subObject->addPropertyValuePair(
			'HasNumber',
			new \SMWDINumber( 4555 )
		);

		return $subObject;
	}


}
