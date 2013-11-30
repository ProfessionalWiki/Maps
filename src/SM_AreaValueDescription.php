<?php

use DataValues\LatLongValue;
use SMW\DataValueFactory;

/**
 * Description of a geographical area defined by a coordinates set and a distance to the bounds.
 * The bounds are a 'rectangle' (but bend due to the earths curvature), as the resulting query
 * would otherwise be to resource intensive.
 *
 * @since 0.6
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com
 * 
 * TODO: would be awesome to use Spatial Extensions to select coordinates
 */
class SMAreaValueDescription extends SMWValueDescription {
	
	/**
	 * Associative array containing the bounds of the area, or false when not set.
	 * 
	 * @since 0.6
	 * 
	 * @var mixed
	 */
	protected $bounds = false;

	/**
	 * @since 3.0
	 *
	 * @var SMWDIGeoCoord
	 */
	protected $center;

	/**
	 * @since 3.0
	 *
	 * @var string
	 */
	protected $radius;

	/**
	 * Constructor.
	 * 
	 * @since 0.6
	 * 
	 * @param SMWDataItem $areaCenter
	 * @param string $comparator
	 * @param string $radius
	 * @param SMWDIProperty $property
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( SMWDataItem $areaCenter, $comparator, $radius, SMWDIProperty $property = null ) {
		if ( !( $areaCenter instanceof SMWDIGeoCoord ) ) {
			throw new InvalidArgumentException( '$areaCenter needs to be a SMWDIGeoCoord' );
		}

		parent::__construct( $areaCenter, $property, $comparator );

		$this->radius = MapsDistanceParser::parseDistance( $radius );
		$this->center = $areaCenter;

		$this->bounds = $this->getBoundingBox();
	}

	/**
	 * @see SMWDescription:getQueryString
	 * 
	 * @since 0.6
	 * 
	 * @param Boolean $asValue
	 */
	public function getQueryString( $asValue = false ) {
		if ( $this->getDataItem() !== null ) {
			$queryString = DataValueFactory::newDataItemValue( $this->getDataItem(), $this->m_property )->getWikiValue();
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
    	return $this->bounds;
    }    
	
	/**
	 * @see SMWDescription::getSQLCondition
	 *
	 * FIXME: store specific code should be in the store component
	 *
	 * @since 0.6
	 * 
	 * @param string $tableName
	 * @param array $fieldNames
	 * @param DatabaseBase $dbs
	 * 
	 * @return string or false
	 */
	public function getSQLCondition( $tableName, array $fieldNames, DatabaseBase $dbs ) {
		// Only execute the query when the description's type is geographical coordinates,
		// the description is valid, and the near comparator is used.
		if ( $this->getDataItem()->getDIType() != SMWDataItem::TYPE_GEO
			|| ( $this->getComparator() != SMW_CMP_EQ && $this->getComparator() != SMW_CMP_NEQ )
			) {
			return false;
		}
		
		$north = $dbs->addQuotes( $this->bounds['north'] );
		$east = $dbs->addQuotes( $this->bounds['east'] );
		$south = $dbs->addQuotes( $this->bounds['south'] );
		$west = $dbs->addQuotes( $this->bounds['west'] );

		$isEq = $this->getComparator() == SMW_CMP_EQ;
		
        $conditions = array();

        $smallerThen = $isEq ? '<' : '>=';
        $biggerThen = $isEq ? '>' : '<=';
        $joinCond = $isEq ? 'AND' : 'OR';

        $conditions[] = "{$tableName}.$fieldNames[1] $smallerThen $north";
        $conditions[] = "{$tableName}.$fieldNames[1] $biggerThen $south";
        $conditions[] = "{$tableName}.$fieldNames[2] $smallerThen $east";
        $conditions[] = "{$tableName}.$fieldNames[2] $biggerThen $west";

        $sql = implode( " $joinCond ", $conditions );

		return $sql;
	}

	/**
	 * @return float[] An associative array containing the limits with keys north, east, south and west.
	 */
	protected function getBoundingBox() {
		$center = new LatLongValue(
			$this->center->getLatitude(),
			$this->center->getLongitude()
		);

		$north = MapsGeoFunctions::findDestination( $center, 0, $this->radius );
		$east = MapsGeoFunctions::findDestination( $center, 90, $this->radius );
		$south = MapsGeoFunctions::findDestination( $center, 180, $this->radius );
		$west = MapsGeoFunctions::findDestination( $center, 270, $this->radius );

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
	protected function geoFunctionsAreAvailable() {
		return class_exists( 'MapsGeoFunctions' );
	}	
	
}