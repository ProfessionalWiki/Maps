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
 * TODO: would be awesome to use Spatial Extensions to select coordinates
 * 
 * @ingroup SemanticMaps
 */
class SMAreaValueDescription extends SMWValueDescription {
	protected $mBounds = false;

	public function __construct( SMGeoCoordsValue $dataValue, $comparator, $radius ) {
		parent::__construct( $dataValue, $comparator );	

		if ( self::geoFunctionsAreAvailable() ) {
			$this->calculateBounds();
		}
		
		// Parse the radius to the actual value and the optional unit.
		$radius = preg_replace('/\s\s+/', ' ', $radius);
		$parts = explode( ' ', $radius );
		$radius = (float)array_shift( $parts );
		
		// If there is a unit, find it's ratio and apply it to the radius value.
		if ( count( $parts ) > 0 ) {
			$unit = strtolower( implode( ' ', $parts ) );
			
			$ratio = array(
				'km' => 1000,
				'kilometers' => 1000,
				'kilometres' => 1000,
				'mi' => 1609.344,
				'mile' => 1609.344,
				'miles' => 1609.344,
				'nm' => 1852,
				'nautical mile' => 1852,
				'nautical miles' => 1852,
			);
			
			if ( array_key_exists( $unit, $ratio ) ) {
				$radius = $radius * $ratio[$unit];
			}
		}
		
		// If the MapsGeoFunctions class is not loaded, we can not create the bounding box,
		// so don't add any conditions.
		if ( self::geoFunctionsAreAvailable() ) {
			$this->mBounds = self::getBoundingBox(
				$dataValue->getCoordinateSet(),
				$radius
			);
		}
	}

	protected function calculateBounds() {
		
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
	 * @see SMWDescription::getSQLCondition
	 * 
	 * @param string $tableName
	 * @param array $fieldNames
	 * @param DatabaseBase or Database $dbs
	 * 
	 * @return true
	 */
	public function getSQLCondition( $tableName, array $fieldNames, $dbs ) {
		global $smgUseSpatialExtensions;
		
		$dataValue = $this->getDatavalue();

		// Only execute the query when the description's type is geographical coordinates,
		// the description is valid, and the near comparator is used.
		if ( $dataValue->getTypeID() != '_geo' 
			|| !$dataValue->isValid()
			|| ( $this->getComparator() != SMW_CMP_EQ && $this->getComparator() != SMW_CMP_NEQ )
			) {
			return false;
		}
		
		$boundingBox = $this->getBounds();
		
		$north = $dbs->addQuotes( $boundingBox['north'] );
		$east = $dbs->addQuotes( $boundingBox['east'] );
		$south = $dbs->addQuotes( $boundingBox['south'] );
		$west = $dbs->addQuotes( $boundingBox['west'] );

		$isEq = $this->getComparator() == SMW_CMP_EQ;
		
		$conditions = array();
		
		if ( $smgUseSpatialExtensions ) {
			// TODO
		}
		else {
			$smallerThen = $isEq ? '<' : '>=';
			$biggerThen = $isEq ? '>' : '<=';
			$joinCond = $isEq ? '&&' : '||';
			
			$conditions[] = "{$tableName}.$fieldNames[0] $smallerThen $north";
			$conditions[] = "{$tableName}.$fieldNames[0] $biggerThen $south";
			$conditions[] = "{$tableName}.$fieldNames[1] $smallerThen $east";
			$conditions[] = "{$tableName}.$fieldNames[1] $biggerThen $west";			
		}
		
		return implode( " $joinCond ", $conditions );
	}

	/**
	 * Returns the lat and lon limits of a bounding box around a circle defined by the provided parameters.
	 * 
	 * @param array $centerCoordinates Array containing non-directional float coordinates with lat and lon keys. 
	 * @param float $circleRadius The radidus of the circle to create a bounding box for, in km.
	 * 
	 * @return An associative array containing the limits with keys north, east, south and west.
	 */
	protected static function getBoundingBox( array $centerCoordinates, $circleRadius ) {
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
	protected static function geoFunctionsAreAvailable() {
		return class_exists( 'MapsGeoFunctions' );
	}	
}