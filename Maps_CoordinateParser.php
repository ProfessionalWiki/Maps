<?php

/**
 * File holding class MapsCoordinateParser.
 *
 * @file Maps_CoordinateParser.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

// Unicode symbols for coordinate minutes and seconds.
// TODO: use?
define( 'Maps_GEO_MIN', '′' );
define( 'Maps_GEO_SEC', '″' );

/**
 * Static class for coordinate validation and parsing.
 * Supports floats, DMS, decimal degrees, and decimal minutes notations, both directional and non-directional.
 * 
 * @ingroup Maps
 * 
 * @author Jeroen De Dauw
 * 
 * TODO: hold into account all valid spacing styles
 * TODO: support different coordinate seperators, currently only comma's are supported
 * TODO: normalize the input before doing anything with it 
 */
class MapsCoordinateParser {
	
	protected static $mI18nDirections = false; // Cache for localised direction labels
	protected static $mDirections; // Cache for English direction labels
	
	/**
	 * Takes in a set of coordinates and checks if they are a supported format.
	 * If they are, they will be parsed to a set of non-directional floats, that
	 * will be stored in an array with keys 'lat' and 'lon'. 
	 * 
	 * @param string $coordinates The coordinates to be parsed.
	 * 
	 * @return array or false
	 */
	public static function parseCoordinates( $coordinates ) {
		$coordinates = trim( $coordinates );
		
		/*
		
		$value = str_replace( array( '&#176;', '&deg;' ), '°', $value );
		$value = str_replace( array( '&acute;', '&#180;' ), '´', $value );
		$value = str_replace( array( '&#8243;', '&Prime;', "''", '"', '´´', SM_GEO_MIN . SM_GEO_MIN ), SM_GEO_SEC, $value );
		$value = str_replace( array( '&#8242;', '&prime;', "'", '´' ), SM_GEO_MIN, $value );
		
		 */

		$coordinates = self::handleI18nLabels( $coordinates );
		
		$coordsType = self::getCoordinatesType( $coordinates );
		
		// If getCoordinatesType returned false, the provided value is invalid.
		if ( $coordsType === false ) {
			return false;
		}
		
		// Split the coodrinates string into a lat and lon part.
		$coordinates = explode( ',', $coordinates );
		$coordinates = array(
			'lat' => trim ( $coordinates[0] ),
			'lon' => trim ( $coordinates[1] ),
		);
		
		$coordinates = self::resolveAngles( $coordinates );
		
		return array(
			'lat' => self::parseCoordinate( $coordinates['lat'], $coordsType ),
			'lon' => self::parseCoordinate( $coordinates['lon'], $coordsType ),
		);
	}
	
	/**
	 * Returns the type of the provided coordinates, or flase if they are invalid.
	 * You can use this as validation function, but be sure to use ===, since 0 can be returned.
	 * 
	 * @param string $coordinates
	 * 
	 * @return Integer or false
	 */
	public static function getCoordinatesType( $coordinates ) {
		switch ( true ) {
			case self::areFloatCoordinates( $coordinates ):
				return Maps_COORDS_FLOAT;
				break;
			case self::areDMSCoordinates( $coordinates ):
				return Maps_COORDS_DMS;
				break;
			case self::areDDCoordinates( $coordinates ):
				return Maps_COORDS_DD;
				break;
			case self::areDMCoordinates( $coordinates ):
				return Maps_COORDS_DM;
				break;
			default:
				return false;
		}
	}
	
	/**
	 * Returns a boolean indicating if the provided value is a valid set of coordinate.
	 * 
	 * @param string $coordsOrAddress
	 * 
	 * @return boolean
	 */	
	public static function areCoordinates( $coordsOrAddress ) {
		return self::getCoordinatesType( $coordsOrAddress ) !== false;
	}
	
	/**
	 * Turns a given coordinate set into a single string that gets formatted
	 * depending on the $targetType and $directional parameters. 
	 * 
	 * they will be parsed to the given notation, which defaults to
	 * non-directional floats
	 * 
	 * @param array $coordinates The set of coordinates that needs to be formatted. Either an associative
	 *        array with lat and lon keys, or a numbered aray with lat on index 0, and lon on index 1.
	 * @param coordinate type $targetFormat The notation to which they should be formatted. Defaults to floats.
	 * @param boolean $directional Indicates if the target notation should be directional. Defaults to false.
	 * 
	 * @return string
	 */
	public static function formatCoordinates( array $coordinates, $targetFormat = Maps_COORDS_FLOAT, $directional = false, $separator = ', ' ) {
		if ( !array_key_exists( 'lat', $coordinates ) || !array_key_exists( 'lon', $coordinates ) ) {
			list( $coordinates['lat'], $coordinates['lon'] ) = $coordinates;
		}
		
		$coordinates = array(
			'lat' => self::formatCoordinate( $coordinates['lat'], $targetFormat ),
			'lon' => self::formatCoordinate( $coordinates['lon'], $targetFormat ),
		);		
		
		$coordinates = self::setAngles( $coordinates, $directional );
		
		return implode( $separator, $coordinates );
	}
	
	/**
	 * Formats a single non-directional float coordinate in the given notation.
	 * 
	 * @param string $coordinate The coordinate to be formatted.
	 * @param coordinate type $targetFormat The notation to which they should be formatted.
	 * 
	 * @return string
	 */
	private static function formatCoordinate( $coordinate, $targetFormat ) {
		switch ( $targetFormat ) {
			case Maps_COORDS_FLOAT:
				return $coordinate;
			case Maps_COORDS_DMS:
				$coordStr = round( $coordinate ) . '° ';
				$minutes = ( $coordinate % 1 ) / 60;
				$coordStr .= round( $minutes ) . "' ";
				$coordStr .= ( $minutes % 1 ) / 60 . '"';
				return $coordStr;
			case Maps_COORDS_DD:
				return $coordinate . '°';
			case Maps_COORDS_DM:
				return round( $coordinate ) . '° ' . ( $coordinate % 1 ) / 60 . "'";
			default:
				throw new Exception( __METHOD__ . " does not support formatting of coordinates to the $targetFormat notation." );
		}
	}
	
	/**
	 * Parses a coordinate that's in the provided notation to float representatation.
	 * 
	 * @param string $coordinate The coordinate to be parsed.
	 * @param coordinate type $coordType The notation the coordinate is currently in.
	 * 
	 * @return string
	 */
	private static function parseCoordinate( $coordinate, $coordType ) {
		switch ( $coordType ) {
			case Maps_COORDS_FLOAT:
				return $coordinate;
			case Maps_COORDS_DMS:
				return self::parseDMSCoordinate( $coordinate );
			case Maps_COORDS_DD:
				return self::parseDDCoordinate( $coordinate );
			case Maps_COORDS_DM:
				return self::parseDMCoordinate( $coordinate );
			default:
				throw new Exception( __METHOD__ . " does not support parsing of the $coordType coordinate type." );
		}		
	}	
	
	/**
	 * returns whether the coordinates are in float representataion.
	 * 
	 * @param string $coordinates
	 * 
	 * @return boolean
	 */
	public static function areFloatCoordinates( $coordinates ) {
		return preg_match( '/^(-)?\d{1,3}(\.\d{1,20})?,(\s)?(-)?\d{1,3}(\.\d{1,20})?$/', $coordinates ) // Non-directional
			|| preg_match( '/^\d{1,3}(\.\d{1,20})?(\s)?(N|S),(\s)?\d{1,3}(\.\d{1,20})?(\s)?(E|W)$/', $coordinates ); // Directional
	}
	
	/**
	 * returns whether the coordinates are in DMS representataion.
	 * 
	 * @param string $coordinates
	 * 
	 * @return boolean
	 */	
	public static function areDMSCoordinates( $coordinates ) {
		return preg_match( '/^(-)?(\d{1,3}°)(\d{1,2}(\′|\'))?((\d{1,2}(″|"))?|(\d{1,2}\.\d{1,2}(″|"))?),(\s)?(-)?(\d{1,3}°)(\d{1,2}(\′|\'))?((\d{1,2}(″|"))?|(\d{1,2}\.\d{1,2}(″|"))?)$/', $coordinates ) // Non-directional
			|| preg_match( '/^(\d{1,3}°)(\d{1,2}(\′|\'))?((\d{1,2}(″|"))?|(\d{1,2}\.\d{1,2}(″|"))?)(\s)?(N|S),(\s)?(\d{1,3}°)(\d{1,2}(\′|\'))?((\d{1,2}(″|"))?|(\d{1,2}\.\d{1,2}(″|"))?)(\s)?(E|W)$/', $coordinates ); // Directional
	}

	/**
	 * returns whether the coordinates are in Decimal Degree representataion.
	 * 
	 * @param string $coordinates
	 * 
	 * @return boolean
	 */	
	public static function areDDCoordinates( $coordinates ) {
		return preg_match( '/^(-)?\d{1,3}(|\.\d{1,20})°,(\s)?(-)?(\s)?\d{1,3}(|\.\d{1,20})°$/', $coordinates ) // Non-directional
			|| preg_match( '/^\d{1,3}(|\.\d{1,20})°(\s)?(N|S),(\s)?(\s)?\d{1,3}(|\.\d{1,20})°(\s)?(E|W)?$/', $coordinates ); // Directional
	}
	
	/**
	 * returns whether the coordinates are in Decimal Minute representataion.
	 * 
	 * @param string $coordinates
	 * 
	 * @return boolean
	 */	
	public static function areDMCoordinates( $coordinates ) {
		return preg_match( '/(-)?\d{1,3}°\d{1,3}(\.\d{1,20}\')?,(\s)?(-)?\d{1,3}°\d{1,3}(\.\d{1,20}\')?$/', $coordinates ) // Non-directional
			|| preg_match( '/\d{1,3}°\d{1,3}(\.\d{1,20}\')?(\s)?(N|S),(\s)?\d{1,3}°\d{1,3}(\.\d{1,20}\')?(\s)?(E|W)?$/', $coordinates ); // Directional
	}
	
	/**
	 * Turn i18n labels into English ones, for both validation and ease of handling.
	 * 
	 * @param string $coordinates
	 */
	private static function handleI18nLabels( $coordinates ) {
		self::initializeDirectionLabels();
		return str_replace( self::$mI18nDirections, self::$mDirections, $coordinates );
	}
	
	/**
	 * Initialize the cache for internationalized direction labels if not done yet. 
	 */
	private static function initializeDirectionLabels() {
		if ( !self::$mI18nDirections ) {
			self::$mI18nDirections = array(
				'N' => wfMsgForContent( 'maps-abb-north' ),
				'E' => wfMsgForContent( 'maps-abb-east' ),
				'S' => wfMsgForContent( 'maps-abb-south' ),
				'W' => wfMsgForContent( 'maps-abb-west' ),			
			);
			self::$mDirections = array_keys( self::$mI18nDirections );
		}		
	}	
	
	/**
	 * Turns directional notation (N/E/S/W) of a coordinate set into non-directional notation (+/-).
	 * 
	 * @param array $coordinates
	 * 
	 * @return array
	 */
	private static function resolveAngles( array $coordinates ) {
		return array(
			'lat' => self::resolveAngle( $coordinates['lat'] ),
			'lon' => self::resolveAngle( $coordinates['lon'] ),
		);
	}
	
	/**
	 * Turns directional notation (N/E/S/W) of a single coordinate into non-directional notation (+/-).
	 * 
	 * @param string $coordinate
	 * 
	 * @return string
	 */	
	private static function resolveAngle( $coordinate ) {
		// Get the last char, which could be a direction indicator
		$lastChar = substr( $coordinate, -1 );
		
		// If there is a direction indicator, remove it, and prepend a minus sign for south and west directions.
		// If there is no direction indicator, the coordinate is already non-directional and no work is required.
		if ( in_array( $lastChar, self::$mDirections ) ) {
			$coordinate = substr( $coordinate, 0, -1 );
			if ( ( $lastChar == "S" ) or ( $lastChar == "W" ) ) {
				$coordinate = '-' . trim( $coordinate );
			}
		}
		
		return $coordinate;
	}
	
	/**
	 * Turns non-directional notation in directional notation when needed.
	 * 
	 * @param array $coordinates The coordinates set to possibly make directional. Needs to be non-directional!
	 * 
	 * @return array
	 */	
	private static function setAngles( array $coordinates, $directional ) {
		if ( $directional ) {
			return array(
				'lat' => self::setDirectionalAngle( $coordinates['lat'] ),
				'lon' => self::setDirectionalAngle( $coordinates['lon'] ),
			);
		} else {
			return $coordinates;
		}
	}
	
	/**
	 * Turns non-directional notation in directional notation.
	 * 
	 * @param string $coordinate The coordinate to make directional. Needs to be non-directional!
	 * 
	 * @return string
	 */	
	private static function setDirectionalAngle( $coordinate ) {	
		// TODO: do reverse of resolveAngle
		return $coordinate;
	}
	
	/**
	 * Takes a set of coordinates in DMS representataion, and returns them in float representataion.
	 * 
	 * @param string $coordinate
	 * 
	 * @return string
	 */	
	private static function parseDMSCoordinate( $coordinate ) {
		$isNegative = substr( $coordinate, 0, 1 ) == '-';
		if ( $isNegative ) $coordinate = substr( $coordinate, 1 );
		
		$degreePosition = strpos( $coordinate, '°' );
		$minutePosition = strpos( $coordinate, "'" );
		$secondPosition = strpos( $coordinate, '"' );
		
		$minuteLength = $minutePosition - $degreePosition - 1;
		$secondLength = $secondPosition - $minutePosition - 1;
		
		$degrees = substr ( $coordinate, 0, $degreePosition );
		$minutes = substr ( $coordinate, $degreePosition + 1, $minuteLength );
		$seconds = substr ( $coordinate, $minutePosition + 1, $secondLength );
		
		$coordinate = $degrees + ( $minutes + $seconds / 60 ) / 60;		
		if ( $isNegative ) $coordinate *= -1;
		
		return $coordinate;
	}

	/**
	 * Takes a set of coordinates in Decimal Degree representataion, and returns them in float representataion.
	 * 
	 * @param string $coordinate
	 * 
	 * @return string
	 */	
	private static function parseDDCoordinate( $coordinate ) {
		return (int)str_replace( '°', '', $coordinate );
	}
	
	/**
	 * Takes a set of coordinates in Decimal Minute representataion, and returns them in float representataion.
	 * 
	 * @param string $coordinate
	 * 
	 * @return string
	 */	
	private static function parseDMCoordinate( $coordinate ) {
		$isNegative = substr( $coordinate, 0, 1 ) == '-';
		if ( $isNegative ) $coordinate = substr( $coordinate, 1 );
				
		list( $degrees, $minutes ) = explode( '°', $coordinate );
		$minutes = substr( $minutes, -1 );
		
		$coordinate = $degrees + $minutes / 60;
		if ( $isNegative ) $coordinate *= -1;
		
		return $coordinate;
	}
	
}