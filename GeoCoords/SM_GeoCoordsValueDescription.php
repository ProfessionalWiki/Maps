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
	 * TODO: re-add the coparator parameter once support for this is implemented in the query hook.
	 * 
	 * @param SMGeoCoordsValue $dataValue
	 */
	public function __construct( SMGeoCoordsValue $dataValue ) {
		parent::__construct( $dataValue );	
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
			) return true;
		
		$coordinates = $dataValue->getCoordinateSet();
			
		// TODO: The field names are hardcoded in, since SMW offers no support for selection based on multiple fields.
		// Ideally SMW's setup should be changed to allow for this. Now the query can break when other extensions
		// add their own semantic tables with similar signatures.
		$whereSQL .= "{$tablename}.lat = {$coordinates['lat']} && {$tablename}.lon = {$coordinates['lon']}";
		
		return true;
	}		
	
}