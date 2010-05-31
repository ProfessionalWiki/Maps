<?php

/**
 * File holding class MapsDistanceParser.
 *
 * @file Maps_DistanceParser.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * Static class for distance validation and parsing. Internal representatations are in meters.
 * 
 * TODO:
 * This class has been quicly put together for 0.6 - with non generic code from Semantic Maps. 
 * It should be improved and made more generic, but is fine like this for now.
 * 
 * @ingroup Maps
 * 
 * @author Jeroen De Dauw
 */
class MapsDistanceParser {
	
	/**
	 * A list of units (keys) and how many meters they represent (value).
	 * 
	 * @var array
	 */
	protected static $mUnits = array(
		'km' => 1000,
		'kilometers' => 1000,
		'kilometres' => 1000,
		'mi' => 1609.344,
		'mile' => 1609.344,
		'miles' => 1609.344,
		'nm' => 1852,
		'nautical mile' => 1852,
		'nautical miles' => 1852,
	);
	
	/**
	 * Parses a distance optionaly containing a unit to a float value in meters.
	 * 
	 * @param string $distance
	 * 
	 * @return float The distance in meters.
	 */
	public static function parseDistance( $distance ) {
		if ( !self::isDistance() ) {
			return false;
		}
		
		// TODO: this is a temporary hax
		$parts = split( ' ', $distance, 2 );
		
		// This is the value.
		$parts[0] = (float)$parts[0];
		
		// Check for the precence of a supported unit, and if found, factor it in.
		if ( count( $parts ) > 1 && array_key_exists( $parts[1], self::$mUnits ) ) {
			$parts[0] *= self::$mUnits[$parts[1]];
		}
		
		return $parts[0];
	}
	
	public static function formatDistance( $meters, $unit = 'km' ) {
		if ( !array_key_exists( $unit, self::$mUnits ) ) {
			$unit = self::$mUnits[0];
		}
		
		$meters = round( $meters / self::$mUnits[$unit], 2 );
		
		return "$meters $unit";
	}
	
	public static function parseAndFormat( $distance, $unit ) {
		return self::formatDistance( self::parseDistance( $distance ), $unit );
	}
	
	public static function isDistance( $distance ) {
		return preg_match( '/^\d(+(\.|,)\d+)?\s*(.*)?$/', $distance );
	}
	
	/**
	 * Returns a list of all suported units.
	 * 
	 * @return array
	 */
	public static function getUnits() {
		return array_keys( self::$mUnits );
	}
	
}