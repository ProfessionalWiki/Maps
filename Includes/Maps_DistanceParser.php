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
	
	private static $validatedDistanceUnit = false;
	
	/**
	 * Parses a distance optionaly containing a unit to a float value in meters.
	 * 
	 * @param string $distance
	 * 
	 * @return float The distance in meters.
	 */
	public static function parseDistance( $distance ) {
		global $egMapsDistanceUnits;
		
		if ( !self::isDistance( $distance ) ) {
			return false;
		}
		
		$matches = array();
		preg_match( '/^(\d+)((\.|,)(\d+))?\s*(.*)?$/', $distance, $matches );
		
		$value = (float)( $matches[1] . $matches[2] );
		$unit = $matches[5];
		
		// Check for the precence of a supported unit, and if found, factor it in.
		if ( $unit != '' && array_key_exists( $unit, $egMapsDistanceUnits ) ) {
			$value *= $egMapsDistanceUnits[$unit];
		}
		
		return $value;
	}
	
	/**
	 * Formats a given distance in meters to a distance in an optionaly specified notation.
	 * 
	 * @param float $meters
	 * @param string $unit
	 * @param integer $decimals
	 * 
	 * @return string
	 */
	public static function formatDistance( $meters, $unit = null, $decimals = 2 ) {
		$meters = round( $meters / self::getUnitRatio( $unit ), $decimals );
		return "$meters $unit";
	}
	
	public static function parseAndFormat( $distance, $unit ) {
		return self::formatDistance( self::parseDistance( $distance ), $unit );
	}
	
	public static function isDistance( $distance ) {
		return preg_match( '/^(\d+)((\.|,)(\d+))?\s*(.*)?$/', $distance );
	}
	
	public static function getUnitRatio( $unit = null ) {
		global $egMapsDistanceUnits;
		return $egMapsDistanceUnits[self::getValidUnit( $unit )];
	}
	
	/**
	 * Returns a valid unit. If the provided one is invalid, the default will be used.
	 * 
	 * @param string $unit
	 */
	public static function getValidUnit( $unit = null ) {
		global $egMapsDistanceUnit, $egMapsDistanceUnits;
		
		// This ensures the value for $egMapsDistanceUnit is correct, and caches the result.
		if ( !self::$validatedDistanceUnit ) {
			if ( !array_key_exists( $egMapsDistanceUnit, $egMapsDistanceUnits ) ) {
				$egMapsDistanceUnit = $egMapsDistanceUnits[0];
			}
			
			self::$validatedDistanceUnit = true;
		}		
		
		if ( $unit == null || !array_key_exists( $unit, $egMapsDistanceUnits ) ) {
			$unit = $egMapsDistanceUnit;
		}
		
		return $unit;
	}
	
	/**
	 * Returns a list of all suported units.
	 * 
	 * @return array
	 */
	public static function getUnits() {
		global $egMapsDistanceUnits;
		return array_keys( $egMapsDistanceUnits );
	}
	
}