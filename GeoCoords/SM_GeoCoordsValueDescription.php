<?php

/**
 * File holding the SMGeoCoordsValueDescription class.
 * 
 * @file SM_GeoCoordsValueDescription.php
 * @ingroup SemanticMaps
 * 
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Description of one data value of type Goegraphical Coordinates.
 *
 * @author Jeroen De Dauw
 * 
 * @ingroup SemanticMaps
 * 
 * TODO: storing the distance here does not seem quite right
 */
class SMGeoCoordsValueDescription extends SMWValueDescription {
	protected $m_distance;

	public function __construct( SMGeoCoordsValue $datavalue, $distance, $comparator = SMW_CMP_EQ ) {
		parent::__construct( $datavalue, $comparator );
		$this->m_distance = $distance;
	}

	/**
	 * @see SMWDescription:getQueryString
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
    
    public function getDistance() {
    	return $this->m_distance;
    }
}