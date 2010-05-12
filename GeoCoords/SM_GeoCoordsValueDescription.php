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
 */
class SMGeoCoordsValueDescription extends SMWValueDescription {
	
	/**
	 * @param SMGeoCoordsValue $dataValue
	 */
	public function __construct( SMGeoCoordsValue $dataValue, $comparator ) {
		parent::__construct( $dataValue, $comparator );	
	}

	/**
	 * @see SMWDescription::getQueryString
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
	 * @see SMWDescription::getSQLCondition
	 * 
	 * @param string $tableName
	 * @param string $fieldName
	 * @param DatabaseBase $dbs
	 * 
	 * @return true
	 */
	public function getSQLCondition( $tableName, $fieldName, DatabaseBase $dbs ) {
		$dataValue = $this->getDatavalue();
		
		// Only execute the query when the description's type is geographical coordinates,
		// the description is valid, and the near comparator is used.
		if ( $dataValue->getTypeID() != '_geo'
			|| !$dataValue->isValid()
			|| ( $this->getComparator() != SMW_CMP_EQ && $this->getComparator() != SMW_CMP_NEQ )
			) return true;

		$coordinates = $dataValue->getCoordinateSet();
		
		$comparator = $this->getComparator() == SMW_CMP_EQ ? '=' : '!=';
		
		// TODO: Would be safer to have a solid way of determining what's the lat and lon field, instead of assuming it's in this order.
		$conditions = array();
		$conditions[] = "{$tableName}.lat {$comparator} {$coordinates['lat']}";
		$conditions[] = "{$tableName}.lon {$comparator} {$coordinates['lon']}";
		
		$whereSQL .= implode( ' && ', $conditions );		

		return true;
	}		
	
}