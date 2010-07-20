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
 * @since 0.6
 * 
 * @ingroup SemanticMaps
 */
class SMAreaValueDescription extends SMWValueDescription {
	protected $mBounds = false;

	/**
	 * Constructor.
	 * 
	 * @since 0.6
	 * 
	 * @param SMGeoCoordsValue $dataValue
	 * @param string $comparator
	 * @param string $radius
	 */
	public function __construct( SMGeoCoordsValue $dataValue, $comparator, $radius ) {
		parent::__construct( $dataValue, $comparator );	

		// Only if the MapsGeoFunctions class is  loaded, we can create the bounding box.
		if ( self::geoFunctionsAreAvailable() ) {
			$this->calculateBounds( $dataValue, $radius );
		}
	}

	/**
	 * Sets the mBounds fields to an array returned by SMAreaValueDescription::getBoundingBox.
	 * 
	 * @since 0.6
	 * 
	 * @param SMGeoCoordsValue $dataValue
	 * @param string $radius
	 */
	protected function calculateBounds( SMGeoCoordsValue $dataValue, $radius ) {
		$this->mBounds = self::getBoundingBox(
			$dataValue->getCoordinateSet(),
			MapsDistanceParser::parseDistance( $radius )
		);		
	}
	
	/**
	 * @see SMWDescription:getQueryString
	 * 
	 * @since 0.6
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
	 * 
	 * @since 0.6
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
     * 
     * @since 0.6
     * 
     * @return array
     */
    public function getBounds() {
    	return $this->mBounds;
    }    
	
	/**
	 * @see SMWDescription::getSQLCondition
	 * 
	 * @since 0.6
	 * 
	 * @param string $tableName
	 * @param array $fieldNames
	 * @param DatabaseBase or Database $dbs
	 * 
	 * @return string or false
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
		
		
		
		if ( $smgUseSpatialExtensions ) {
			// TODO
			$sql = '';
		}
		else {
			$conditions = array();
			
			$smallerThen = $isEq ? '<' : '>=';
			$biggerThen = $isEq ? '>' : '<=';
			$joinCond = $isEq ? '&&' : '||';
			
			$conditions[] = "{$tableName}.$fieldNames[0] $smallerThen $north";
			$conditions[] = "{$tableName}.$fieldNames[0] $biggerThen $south";
			$conditions[] = "{$tableName}.$fieldNames[1] $smallerThen $east";
			$conditions[] = "{$tableName}.$fieldNames[1] $biggerThen $west";

			$sql = implode( " $joinCond ", $conditions );
		}
		
		return $sql;
	}

	/**
	 * Returns the lat and lon limits of a bounding box around a circle defined by the provided parameters.
	 * 
	 * @since 0.6
	 * 
	 * @param array $centerCoordinates Array containing non-directional float coordinates with lat and lon keys. 
	 * @param float $circleRadius The radidus of the circle to create a bounding box for, in m.
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
	 * 
	 * @since 0.6
	 * 
	 * @return boolean
	 */
	protected static function geoFunctionsAreAvailable() {
		return class_exists( 'MapsGeoFunctions' );
	}	
	
}