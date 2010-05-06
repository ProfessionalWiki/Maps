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
	 * @param string $tablename
	 * @param string $fieldname
	 * @param DatabaseBase $dbs
	 * 
	 * @return true
	 */
	public static function getSQLCondition( &$whereSQL, SMWDescription $description, $tablename, $fieldname, DatabaseBase $dbs ) {
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
		//var_dump($comparator);exit;
		
		// TODO: The field names are hardcoded in, since SMW offers no support for selection based on multiple fields.
		// Ideally SMW's setup should be changed to allow for this. Now the query can break when other extensions
		// add their own semantic tables with similar signatures.
		$whereSQL .= "{$tablename}.lat {$comparator} {$coordinates['lat']} && {$tablename}.lon {$comparator} {$coordinates['lon']}";
		
		return true;
	}		
	
}