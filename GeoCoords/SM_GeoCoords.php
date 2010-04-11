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
$wgAutoloadClasses['SMGeoCoordsValueDescription'] = dirname( __FILE__ ) . '/SM_GeoCoordsValueDescription.php';

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
	 * TODO: Change the way coords are stored in the db from a string field to 2 float fields.
	 * The geographic coordinate value object should provide functions to access the lat and lon data directly.
	 * 
	 * TODO: Add support for a per-coordinate set distance parameter.
	 */
	public static function getGeoProximitySQLCondition( &$whereSQL, SMGeoCoordsValueDescription $description, $tablename, $fieldname, $dbs ) {
		// If the MapsGeoFunctions class is not loaded, we can not create the bounding box, so don't add any conditions.
		if ( !self::geoFunctionsAreAvailable() ) {
			return true;
		}

		$dataValue = $description->getDatavalue();
		
		// Only execute the query when the description's type is geographical coordinates,
		// the description is valid, and the near comparator is used.
		if ( ( $dataValue->getTypeID() != '_geo' ) 
			|| ( !$dataValue->isValid() ) 
			|| ( $description->getComparator() != SM_CMP_NEAR )
			) return true; 
			
		$dbKeys = $dataValue->getDBkeys();

		global $smgGeoCoordDistance;
		$distance = $smgGeoCoordDistance; // TODO: get user provided distance
		
		$boundingBox = self::getBoundingBox(
			array(
				'lat' => $dbKeys[0],
				'lon' => $dbKeys[1]
			),
			$distance
		); 
		
		$north = $dbs->addQuotes( $boundingBox['north'] );
		$east = $dbs->addQuotes( $boundingBox['east'] );
		$south = $dbs->addQuotes( $boundingBox['south'] );
		$west = $dbs->addQuotes( $boundingBox['west'] );
		
		$whereSQL .= "{$tablename}lat < $north && {$tablename}lat > $south && {$tablename}lon < $east && {$tablename}lon > $west";
		
		return true;
	}

	private static function getBoundingBox( $centerCoordinates, $circleRadius ) {
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
	private static function geoFunctionsAreAvailable() {
		global $wgAutoloadClasses;
		return array_key_exists( 'MapsGeoFunctions', $wgAutoloadClasses );
	}	
}

