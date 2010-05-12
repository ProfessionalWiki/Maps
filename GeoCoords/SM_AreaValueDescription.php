<?php

/**
 * File holding the SMAreaValueDescription class.
 * 
 * @file SM_AreaValueDescription.php
 * @ingroup SemanticMaps
 * 
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Description of a geographical area defined by a coordinates set and a distance to the bounds.
 * The bounds are a 'rectangle' (but bend due to the earhs curvature), as the resulting query
 * would otherwise be to resource intensive.
 *
 * @author Jeroen De Dauw
 * 
 * @ingroup SemanticMaps
 */
class SMAreaValueDescription extends SMWValueDescription {
	protected $mBounds = false;

	public function __construct( SMGeoCoordsValue $dataValue, $radius ) {
		parent::__construct( $dataValue, SM_CMP_NEAR );	

		// If the MapsGeoFunctions class is not loaded, we can not create the bounding box,
		// so don't add any conditions.
		if ( self::geoFunctionsAreAvailable() ) {
			$dbKeys = $dataValue->getDBkeys();
			
			$this->mBounds = self::getBoundingBox(
				array(
					'lat' => $dbKeys[0],
					'lon' => $dbKeys[1]
				),
				$radius * 1000
			);
		}
	}

	/**
	 * @see SMWDescription:getQueryString
	 * 
	 * @param Boolean $asvalue
	 */
	public function getQueryString( $asValue = false ) {
		if ( $this->m_datavalue !== null ) {
			$queryString = $this->m_datavalue->getWikiValue();
			return $asValue ? $queryString : "[[$queryString]]";
		} else {
			return $asValue ? '+' : '';
		}
	}

	/**
	 * @see SMWDescription:prune
	 */
    public function prune( &$maxsize, &$maxdepth, &$log ) {
    	if ( ( $maxsize < $this->getSize() ) || ( $maxdepth < $this->getDepth() ) ) {
			$log[] = $this->getQueryString();
			$result = new SMWThingDescription();
			$result->setPrintRequests( $this->getPrintRequests() );
			return $result;
		} else {
			$maxsize = $maxsize - $this->getSize();
			$maxdepth = $maxdepth - $this->getDepth();
			return $this;
		}
    }
    
    /**
     * Returns the bounds of the area.
     */
    public function getBounds() {
    	return $this->mBounds;
    }    
    
	/**
	 * Returns the lat and lon limits of a bounding box around a circle defined by the provided parameters.
	 * 
	 * @param array $centerCoordinates Array containing non-directional float coordinates with lat and lon keys. 
	 * @param float $circleRadius The radidus of the circle to create a bounding box for, in km.
	 * 
	 * @return An associative array containing the limits with keys north, east, south and west.
	 */
	private static function getBoundingBox( array $centerCoordinates, $circleRadius ) {
		$north = MapsGeoFunctions::findDestination( $centerCoordinates, 0, $circleRadius );
		$east = MapsGeoFunctions::findDestination( $centerCoordinates, 90, $circleRadius );
		$south = MapsGeoFunctions::findDestination( $centerCoordinates, 180, $circleRadius );
		$west = MapsGeoFunctions::findDestination( $centerCoordinates, 270, $circleRadius );

		return array(
			'north' => $north['lat'],
			'east' => $east['lon'],
			'south' => $south['lat'],
			'west' => $west['lon'],
		);
	}
	
	/**
	 * Returns a boolean indicating if MapsGeoFunctions is available. 
	 */
	private static function geoFunctionsAreAvailable() {
		return class_exists( 'MapsGeoFunctions' );
	}
	
	/**
	 * @see SMWDescription::getSQLCondition
	 * 
	 * @param string $tableName
	 * @param array $fieldNames
	 * @param DatabaseBase $dbs
	 * 
	 * @return true
	 */
	public function getSQLCondition( $tableName, array $fieldNames, DatabaseBase $dbs ) {
		$dataValue = $this->getDatavalue();

		// Only execute the query when the description's type is geographical coordinates,
		// the description is valid, and the near comparator is used.
		if ( $dataValue->getTypeID() != '_geo'
			|| !$dataValue->isValid()
			) return false;
			
		$boundingBox = $this->getBounds();
			
		$north = $dbs->addQuotes( $boundingBox['north'] );
		$east = $dbs->addQuotes( $boundingBox['east'] );
		$south = $dbs->addQuotes( $boundingBox['south'] );
		$west = $dbs->addQuotes( $boundingBox['west'] );

		// TODO: Would be safer to have a solid way of determining what's the lat and lon field, instead of assuming it's in this order.
		$conditions = array();
		$conditions[] = "{$tableName}.{$fieldNames[0]} < $north";
		$conditions[] = "{$tableName}.{$fieldNames[0]} > $south";
		$conditions[] = "{$tableName}.{$fieldNames[1]} < $east";
		$conditions[] = "{$tableName}.{$fieldNames[1]} > $west";
		
		return implode( ' && ', $conditions );
	}	
}