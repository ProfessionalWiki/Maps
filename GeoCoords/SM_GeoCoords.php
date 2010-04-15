<?php

/**
 * File containing the registration and initialization code for the Semantic MediaWiki
 * Geographical Coordinates data type, and things it's dependent on.
 * 
 * @file SM_GeoCoords.php
 * @ingroup SemanticMaps
 * 
 * @author Jeroen De Dauw
 * @author Markus Krötzsch
 */

// Registration of the GoeCoords class.
$wgAutoloadClasses['SMGeoCoords'] = __FILE__;

// Registration of the Geographical Coordinate type.
$wgAutoloadClasses['SMGeoCoordsValue'] = dirname( __FILE__ ) . '/SM_GeoCoordsValue.php';

// Registration of the Geographical Coordinate value description class.
$wgAutoloadClasses['SMAreaValueDescription'] = dirname( __FILE__ ) . '/SM_AreaValueDescription.php';

// Hook for initializing the Geographical Coordinate type.
$wgHooks['smwInitDatatypes'][] = 'SMGeoCoordsValue::initGeoCoordsType';

$wgHooks['SMWPropertyTables'][] = 'SMGeoCoordsValue::initGeoCoordsTable';

// Hook for initializing the Geographical Proximity query support.
$wgHooks['smwGetSQLConditionForValue'][] = 'SMGeoCoords::getGeoProximitySQLCondition';

define( 'SM_CMP_NEAR', 101 ); // Define the near comparator for proximity queries.

final class SMGeoCoords {
	
	/**
	 * Custom SQL query extension for matching geographic coordinates.
	 * 
	 * @param string $whereSQL The SQL where condition to expand.
	 * @param SMAreaValueDescription $description The description of center coordinate.
	 * @param string $tablename
	 * @param string $fieldname
	 * @param DatabaseBase $dbs
	 * 
	 * @return true
	 */
	public static function getGeoProximitySQLCondition( &$whereSQL, SMAreaValueDescription $description, $tablename, $fieldname, DatabaseBase $dbs ) {
		$dataValue = $description->getDatavalue();
		
		// Only execute the query when the description's type is geographical coordinates,
		// the description is valid, and the near comparator is used.
		if ( ( $dataValue->getTypeID() != '_geo' ) 
			|| ( !$dataValue->isValid() ) 
			|| ( $description->getComparator() != SM_CMP_NEAR )
			) return true;
		
		$boundingBox = $description->getBounds();
			
		$north = $dbs->addQuotes( $boundingBox['north'] );
		$east = $dbs->addQuotes( $boundingBox['east'] );
		$south = $dbs->addQuotes( $boundingBox['south'] );
		$west = $dbs->addQuotes( $boundingBox['west'] );
		
		$whereSQL .= "{$tablename}.lat < $north && {$tablename}.lat > $south && {$tablename}.lon < $east && {$tablename}.lon > $west";
		
		return true;
	}


}

