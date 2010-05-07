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
	 * Custom SQL query extension for matching geographic coordinates.
	 * 
	 * @param string $whereSQL The SQL where condition to expand.
	 * @param SMWDescription $description
	 * @param string $tableName
	 * @param array $fieldNames
	 * @param DatabaseBase $dbs
	 * 
	 * @return true
	 */
	public static function getSQLCondition( &$whereSQL, SMWDescription $description, $tableName, array $fieldNames, DatabaseBase $dbs ) {
		$dataValue = $description->getDatavalue();
		
		// Only execute the query when the description's type is geographical coordinates,
		// the description is valid, and the near comparator is used.
		if ( $dataValue->getTypeID() != '_geo'
			|| !$dataValue->isValid()
			|| !$description instanceof SMGeoCoordsValueDescription
			//|| ( $description->getComparator() != SMW_CMP_EQ && $description->getComparator() != SMW_CMP_NEQ )
			) return true;
		
		$coordinates = $dataValue->getCoordinateSet();
		
		$comparator = $description->getComparator() == SMW_CMP_EQ ? '=' : '!=';
		
		// TODO: Would be safer to have a solid way of determining what's the lat and lon field, instead of assuming it's in this order.
		$conditions = array();
		$conditions[] = "{$tableName}.{$fieldNames[0]} {$comparator} {$coordinates['lat']}";
		$conditions[] = "{$tableName}.{$fieldNames[1]} {$comparator} {$coordinates['lon']}";
		
		$whereSQL .= implode( ' && ', $conditions );		
		
		return true;
	}		
	
}