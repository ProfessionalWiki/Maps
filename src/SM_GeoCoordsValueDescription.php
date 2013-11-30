<?php

use SMW\DataValueFactory;

/**
 * Description of one data value of type Geographical Coordinates.
 * 
 * @since 0.6
 * @ingroup SemanticMaps
 * 
 * @author Jeroen De Dauw
 */
class SMGeoCoordsValueDescription extends SMWValueDescription {

	/**
	 * @see SMWDescription::getQueryString
	 * 
	 * @since 0.6
	 * 
	 * @param boolean $asValue
	 */
	public function getQueryString( $asValue = false ) {
		if ( $this->m_dataItem !== null ) {
			$queryString = DataValueFactory::newDataItemValue( $this->m_dataItem, $this->m_property )->getWikiValue();
			return $asValue ? $queryString : "[[$queryString]]";
		} else {
			return $asValue ? '+' : '';
		}
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
	 * @return boolean
	 */
	public function getSQLCondition( $tableName, array $fieldNames, DatabaseBase $dbs ) {
		$dataItem = $this->getDataItem();
		
		// Only execute the query when the description's type is geographical coordinates,
		// the description is valid, and the near comparator is used.
		if ( $dataItem instanceof SMWDIGeoCoord ) {
			switch ( $this->getComparator() ) {
				case SMW_CMP_EQ: $comparator = '='; break;
				case SMW_CMP_LEQ: $comparator = '<='; break;
				case SMW_CMP_GEQ: $comparator = '>='; break;
				case SMW_CMP_NEQ: $comparator = '!='; break;
				default: return false;
			}

			$lat = $dbs->addQuotes( $dataItem->getLatitude() );
			$lon = $dbs->addQuotes( $dataItem->getLongitude() );

			$conditions = array();

			$conditions[] = "{$tableName}.$fieldNames[1] $comparator $lat";
			$conditions[] = "{$tableName}.$fieldNames[2] $comparator $lon";

			return implode( ' && ', $conditions );
		}

		return false;
	}
	
}