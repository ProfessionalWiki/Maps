<?php

/**
 * File containing the registration and initialization code for the Semantic MediaWiki
 * Geographical Coordinates data type, and things it's dependent on.
 * 
 * @file SM_GeoCoords.php
 * @ingroup SemanticMaps
 * 
 * @author Jeroen De Dauw
 */

// Registration of the GoeCoords class.
$wgAutoloadClasses['SMGeoCoords'] = __FILE__;

// Registration of the Geographical Coordinate type.
$wgAutoloadClasses['SMGeoCoordsValue'] = dirname( __FILE__ ) . '/SM_GeoCoordsValue.php';

// Registration of the Geographical Coordinate value description class.
$wgAutoloadClasses['SMGeoCoordsValueDescription'] = dirname( __FILE__ ) . '/SM_GeoCoordsValueDescription.php';

// Registration of the Geographical Coordinate are description class.
$wgAutoloadClasses['SMAreaValueDescription'] = dirname( __FILE__ ) . '/SM_AreaValueDescription.php';

// Hook for initializing the Geographical Coordinate type.
$wgHooks['smwInitDatatypes'][] = 'SMGeoCoordsValue::initGeoCoordsType';

// Hook for defining a table to store geographical coordinates in.
$wgHooks['SMWPropertyTables'][] = 'SMGeoCoordsValue::initGeoCoordsTable';

// Hook for initializing the Geographical Proximity query support.
$wgHooks['smwGetSQLConditionForValue'][] = 'SMAreaValueDescription::getSQLCondition';

// Hook for initializing the Geographical Proximity query support.
$wgHooks['smwGetSQLConditionForValue'][] = 'SMGeoCoordsValueDescription::getSQLCondition';

define( 'SM_CMP_NEAR', 101 ); // Define the near comparator for proximity queries.