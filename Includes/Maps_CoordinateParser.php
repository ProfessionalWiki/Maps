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

/**
 * Static class for coordinate validation and parsing.
 * Supports floats, DMS, decimal degrees, and decimal minutes notations, both directional and non-directional.
 * Internal representatations are arrays with lat and lon key with float values.
 * 
 * TODO: it migt be a lot nicer to return the releveant segments from the regexes instead of manually parsing them out.
 * 
 * @ingroup Maps
 * @since 0.6
 * 
 * @author Jeroen De Dauw
 */
class MapsCoordinateParser {
	
	protected static $separators = array( ',', ';' );
	protected static $separatorsRegex = false;
	
	protected static $i18nDirections = false; // Cache for localised direction labels
	protected static $directions; // Cache for English direction labels

	/**
	 * Takes in a set of coordinates and checks if they are a supported format.
	 * If they are, they will be parsed to a set of non-directional floats, that
	 * will be stored in an array with keys 'lat' and 'lon'. 
	 * 
	 * @since 0.6
	 * 
	 * @param string $coordinates The coordinates to be parsed.
	 * 
	 * @return array or false
	 */
	public static function parseCoordinates( $coordinates ) {
		// Handle i18n notations.
		$coordinates = self::handleI18nLabels( $coordinates );
		
		// Determine what notation the coordinates are in.
		$coordsType = self::getCoordinatesType( $coordinates );

		// If getCoordinatesType returned false, the provided value is invalid or in an unsuported notation.
		if ( $coordsType === false ) {
			return false;
		}
		
		// Split the coodrinates string into a lat and lon part.
		foreach ( self::$separators as $separator ) {
			$split = explode( $separator, $coordinates );
			if ( count( $split ) == 2 ) break;
		}
		
		// This should not happen, as the validity of the coordinate set is already ensured by the regexes,
		// but do the check anyway, and return false if it fails.
		if ( count( $split ) != 2 ) {
			return false;
		}

		$coordinates = array(
			'lat' => trim( $split[0] ),
			'lon' => trim( $split[1] ),
		);
		
		// Ensure the coordinates are in non-directional notation.
		$coordinates = self::resolveAngles( $coordinates );
		
		// Parse both latitude and longitude to float notation, and return the result.
		return array(
			'lat' => self::parseCoordinate( $coordinates['lat'], $coordsType ),
			'lon' => self::parseCoordinate( $coordinates['lon'], $coordsType ),
		);
	}
	
	/**
	 * Returns the type of the provided coordinates, or flase if they are invalid.
	 * You can use this as validation function, but be sure to use ===, since 0 can be returned.
	 * 
	 * @since 0.6
	 * 
	 * @param string $coordinates
	 * 
	 * @return Integer or false
	 */
	public static function getCoordinatesType( $coordinates ) {
		// Normalize the coordinates string.
		$coordinates = self::normalizeCoordinates( $coordinates );		
		
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
	 * @since 0.6
	 * 
	 * @param string $coordsOrAddress
	 * 
	 * @return boolean
	 */
	public static function areCoordinates( $coordsOrAddress ) {
		// Handle i18n notations.
		$coordsOrAddress = self::handleI18nLabels( $coordsOrAddress );

		return self::getCoordinatesType( $coordsOrAddress ) !== false;
	}
	
	/**
	 * Turns a given coordinate set into a single string that gets formatted
	 * depending on the $targetType and $directional parameters. 
	 * 
	 * they will be parsed to the given notation, which defaults to
	 * non-directional floats
	 * 
	 * @since 0.6
	 * 
	 * @param array $coordinates The set of coordinates that needs to be formatted. Either an associative
	 *        array with lat and lon keys, or a numbered aray with lat on index 0, and lon on index 1.
	 * @param coordinate type $targetFormat The notation to which they should be formatted. Defaults to floats.
	 * @param boolean $directional Indicates if the target notation should be directional. Defaults to false.
	 * @param string $separator Delimiter to separate the latitude and longitude with.
	 * 
	 * @return string
	 */
	public static function formatCoordinates( array $coordinates, $targetFormat = Maps_COORDS_FLOAT, $directional = false, $separator = ', ' ) {
		return implode( $separator, self::formatToArray( $coordinates, $targetFormat, $directional ) );
	}

	/**
	 * Turns a given coordinate set into a single string that gets formatted
	 * depending on the $targetType and $directional parameters. 
	 * 
	 * they will be parsed to the given notation, which defaults to
	 * non-directional floats
	 * 
	 * @since 0.6.2
	 * 
	 * @param array $coordinates The set of coordinates that needs to be formatted. Either an associative
	 *        array with lat and lon keys, or a numbered aray with lat on index 0, and lon on index 1.
	 * @param coordinate type $targetFormat The notation to which they should be formatted. Defaults to floats.
	 * @param boolean $directional Indicates if the target notation should be directional. Defaults to false.
	 * 
	 * @return array
	 */
	public static function formatToArray( array $coordinates, $targetFormat = Maps_COORDS_FLOAT, $directional = false ) {
		if ( !array_key_exists( 'lat', $coordinates ) || !array_key_exists( 'lon', $coordinates ) ) {
			list( $coordinates['lat'], $coordinates['lon'] ) = $coordinates;
		}
		
		$coordinates = array(
			'lat' => self::formatCoordinate( $coordinates['lat'], $targetFormat ),
			'lon' => self::formatCoordinate( $coordinates['lon'], $targetFormat ),
		);
		
		return self::setAngles( $coordinates, $directional );
	}
	
	/**
	 * Returns a normalized version of the provided coordinates.
	 * 
	 * @since 0.6
	 * 
	 * @param string $coordinates
	 * 
	 * @return string The normalized version of the provided coordinates.
	 */
	protected static function normalizeCoordinates( $coordinates ) {
		$coordinates = str_replace( ' ', '', $coordinates );
		
		$coordinates = str_replace( array( '&#176;', '&deg;' ), Maps_GEO_DEG, $coordinates );
		$coordinates = str_replace( array( '&acute;', '&#180;' ), Maps_GEO_SEC, $coordinates );
		$coordinates = str_replace( array( '&#8243;', '&Prime;', Maps_GEO_SEC . Maps_GEO_SEC, '´´', '′′', '″' ), Maps_GEO_MIN, $coordinates );
		$coordinates = str_replace( array( '&#8242;', '&prime;', '´', '′' ), Maps_GEO_SEC, $coordinates );

		$coordinates = self::removeInvalidChars( $coordinates );

		return $coordinates;
	}
	
	/**
	 * Returns a string with control characters and characters with ascii values above 126 removed.
	 * 
	 * @since 0.6.3
	 * 
	 * @param string $string Yeah, it's a string, seriously!
	 * 
	 * @return string
	 */
	protected static function removeInvalidChars( $string ) {
		$filtered = array();

		foreach ( str_split( $string ) as $character ) {
			$asciiValue = ord( $character );
			
			if ( ( $asciiValue > 31 && $asciiValue < 127 ) || $asciiValue == 194 || $asciiValue == 176 ) {
				$filtered[] = $character;
			}
		}

		return implode( '', $filtered );
	}
	
	/**
	 * Formats a single non-directional float coordinate in the given notation.
	 * 
	 * @since 0.6
	 * 
	 * @param string $coordinate The coordinate to be formatted.
	 * @param coordinate type $targetFormat The notation to which they should be formatted.
	 * 
	 * @return string
	 */
	protected static function formatCoordinate( $coordinate, $targetFormat ) {
		$coordinate = (float)$coordinate;
		
		switch ( $targetFormat ) {
			case Maps_COORDS_FLOAT:
				return $coordinate;
			case Maps_COORDS_DMS:
				$isNegative = $coordinate < 0;
				$coordinate = abs( $coordinate );
				
				$degrees = floor( $coordinate );
				$minutes = ( $coordinate - $degrees ) * 60;
				$seconds = ( $minutes - floor( $minutes ) ) * 60;
				
				$result = $degrees . Maps_GEO_DEG . ' ' . floor( $minutes ) . Maps_GEO_MIN . ' ' . round( $seconds ) . Maps_GEO_SEC;
				if ( $isNegative ) $result = '-' . $result;
				
				return $result;
			case Maps_COORDS_DD:
				return $coordinate . Maps_GEO_DEG;
			case Maps_COORDS_DM:
				$isNegative = $coordinate < 0;
				$coordinate = abs( $coordinate );
				
				$result = floor( $coordinate ) . Maps_GEO_DEG . ' ' . ( $coordinate - floor( $coordinate ) ) * 60 . Maps_GEO_MIN;
				if ( $isNegative ) $result = '-' . $result;
				
				return $result;
			default:
				throw new Exception( __METHOD__ . " does not support formatting of coordinates to the $targetFormat notation." );
		}
	}
	
	/**
	 * Parses a coordinate that's in the provided notation to float representatation.
	 * 
	 * @since 0.6
	 * 
	 * @param string $coordinate The coordinate to be parsed.
	 * @param coordinate type $coordType The notation the coordinate is currently in.
	 * 
	 * @return string
	 */
	protected static function parseCoordinate( $coordinate, $coordType ) {
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
	 * @since 0.6
	 * 
	 * @param string $coordinates
	 * 
	 * @return boolean
	 */
	public static function areFloatCoordinates( $coordinates ) {
		$sep = self::getSeparatorsRegex();
		return preg_match( '/^(-)?\d{1,3}(\.\d{1,20})?' . $sep . '(\s)?(-)?\d{1,3}(\.\d{1,20})?$/i', $coordinates ) // Non-directional
			|| preg_match( '/^\d{1,3}(\.\d{1,20})?(\s)?(N|S)' . $sep . '(\s)?\d{1,3}(\.\d{1,20})?(\s)?(E|W)$/i', $coordinates ); // Directional
	}
	
	/**
	 * returns whether the coordinates are in DMS representataion.
	 * 
	 * @since 0.6
	 * 
	 * @param string $coordinates
	 * 
	 * @return boolean
	 */
	public static function areDMSCoordinates( $coordinates ) {
		$sep = self::getSeparatorsRegex();
		return preg_match( '/^(-)?(\d{1,3}°)((\s)?\d{1,2}(\′|\'))?(((\s)?\d{1,2}(″|"))?|((\s)?\d{1,2}\.\d{1,2}(″|"))?)'
			. $sep . '(\s)?(-)?(\d{1,3}°)((\s)?\d{1,2}(\′|\'))?(((\s)?\d{1,2}(″|"))?|((\s)?\d{1,2}\.\d{1,2}(″|"))?)$/i', $coordinates ) // Non-directional
			|| preg_match( '/^(\d{1,3}°)((\s)?\d{1,2}(\′|\'))?(((\s)?\d{1,2}(″|"))?|((\s)?\d{1,2}\.\d{1,2}(″|"))?)(\s)?(N|S)'
			. $sep . '(\s)?(\d{1,3}°)((\s)?\d{1,2}(\′|\'))?(((\s)?\d{1,2}(″|"))?|((\s)?\d{1,2}\.\d{1,2}(″|"))?)(\s)?(E|W)$/i', $coordinates ); // Directional
	}

	/**
	 * returns whether the coordinates are in Decimal Degree representataion.
	 * 
	 * @since 0.6
	 * 
	 * @param string $coordinates
	 * 
	 * @return boolean
	 */
	public static function areDDCoordinates( $coordinates ) {
		$sep = self::getSeparatorsRegex();
		return preg_match( '/^(-)?\d{1,3}(|\.\d{1,20})°' . $sep . '(\s)?(-)?(\s)?\d{1,3}(|\.\d{1,20})°$/i', $coordinates ) // Non-directional
			|| preg_match( '/^\d{1,3}(|\.\d{1,20})°(\s)?(N|S)' . $sep . '(\s)?(\s)?\d{1,3}(|\.\d{1,20})°(\s)?(E|W)?$/i', $coordinates ); // Directional
	}
	
	/**
	 * returns whether the coordinates are in Decimal Minute representataion.
	 * 
	 * @since 0.6
	 * 
	 * @param string $coordinates
	 * 
	 * @return boolean
	 */
	public static function areDMCoordinates( $coordinates ) {
		$sep = self::getSeparatorsRegex();
		return preg_match( '/(-)?\d{1,3}°(\s)?\d{1,2}(\.\d{1,20}\')?' . $sep . '(\s)?(-)?\d{1,3}°(\s)?\d{1,2}(\.\d{1,20}\')?$/i', $coordinates ) // Non-directional
			|| preg_match( '/\d{1,3}°(\s)?\d{1,2}(\.\d{1,20}\')?(\s)?(N|S)' . $sep . '(\s)?\d{1,3}°(\s)?\d{1,2}(\.\d{1,20}\')?(\s)?(E|W)?$/i', $coordinates ); // Directional
	}
	
	/**
	 * Turn i18n labels into English ones, for both validation and ease of handling.
	 * 
	 * @since 0.6
	 * 
	 * @param string $coordinates
	 * 
	 * @return string
	 */
	private static function handleI18nLabels( $coordinates ) {
		self::initializeDirectionLabels();
		return str_replace( self::$i18nDirections, self::$directions, $coordinates );
	}
	
	/**
	 * Initialize the cache for internationalized direction labels if not done yet. 
	 * 
	 * @since 0.6
	 */
	protected static function initializeDirectionLabels() {
		if ( !self::$i18nDirections ) {
			self::$i18nDirections = array(
				'N' => wfMsgForContent( 'maps-abb-north' ),
				'E' => wfMsgForContent( 'maps-abb-east' ),
				'S' => wfMsgForContent( 'maps-abb-south' ),
				'W' => wfMsgForContent( 'maps-abb-west' ),
			);
			self::$directions = array_keys( self::$i18nDirections );
		}
	}
	
	/**
	 * Turns directional notation (N/E/S/W) of a coordinate set into non-directional notation (+/-).
	 * 
	 * @since 0.6
	 * 
	 * @param array $coordinates
	 * 
	 * @return array
	 */
	protected static function resolveAngles( array $coordinates ) {
		return array(
			'lat' => self::resolveAngle( $coordinates['lat'] ),
			'lon' => self::resolveAngle( $coordinates['lon'] ),
		);
	}
	
	/**
	 * Turns directional notation (N/E/S/W) of a single coordinate into non-directional notation (+/-).
	 * 
	 * @since 0.6
	 * 
	 * @param string $coordinate
	 * 
	 * @return string
	 */
	protected static function resolveAngle( $coordinate ) {
		// Get the last char, which could be a direction indicator
		$lastChar = strtoupper( substr( $coordinate, -1 ) );
		
		// If there is a direction indicator, remove it, and prepend a minus sign for south and west directions.
		// If there is no direction indicator, the coordinate is already non-directional and no work is required.
		if ( in_array( $lastChar, self::$directions ) ) {
			$coordinate = substr( $coordinate, 0, -1 );
			
			if ( ( $lastChar == 'S' ) or ( $lastChar == 'W' ) ) {
				$coordinate = '-' . trim( $coordinate );
			}
		}
		
		return $coordinate;
	}
	
	/**
	 * Turns non-directional notation in directional notation when needed.
	 * 
	 * @since 0.6
	 * 
	 * @param array $coordinates The coordinates set to possibly make directional. Needs to be non-directional!
	 * 
	 * @return array
	 */
	protected static function setAngles( array $coordinates, $directional ) {
		if ( $directional ) {
			return array(
				'lat' => self::setDirectionalAngle( $coordinates['lat'], true ),
				'lon' => self::setDirectionalAngle( $coordinates['lon'], false ),
			);
		} else {
			return $coordinates;
		}
	}
	
	/**
	 * Turns non-directional notation in directional notation.
	 * 
	 * @since 0.6
	 * 
	 * @param string $coordinate The coordinate to make directional. Needs to be non-directional!
	 * @param boolean $isLat Should be true for latitudes and false for longitudes.
	 * 
	 * @return string
	 */
	protected static function setDirectionalAngle( $coordinate, $isLat ) {
		self::initializeDirectionLabels();
		
		$isNegative = $coordinate{0} == '-';
		if ( $isNegative ) $coordinate = substr( $coordinate, 1 );
		
		if ( $isLat ) {
			$directionChar = self::$i18nDirections[ $isNegative ? 'S' : 'N' ];
		} else {
			$directionChar = self::$i18nDirections[ $isNegative ? 'W' : 'E' ];
		}

		return $coordinate . ' ' . $directionChar;
	}
	
	/**
	 * Takes a set of coordinates in DMS representataion, and returns them in float representataion.
	 * 
	 * @since 0.6
	 * 
	 * @param string $coordinate
	 * 
	 * @return string
	 */
	protected static function parseDMSCoordinate( $coordinate ) {
		$isNegative = $coordinate{0} == '-';
		if ( $isNegative ) $coordinate = substr( $coordinate, 1 );
		
		$degreePosition = strpos( $coordinate, Maps_GEO_DEG );
		$minutePosition = strpos( $coordinate, Maps_GEO_MIN );
		$secondPosition = strpos( $coordinate, Maps_GEO_SEC );
		
		$degSignLength = strlen( Maps_GEO_DEG );
		
		$minuteLength = $minutePosition - $degreePosition - $degSignLength;
		$secondLength = $secondPosition - $minutePosition - 1;
		
		$degrees = substr ( $coordinate, 0, $degreePosition );
		$minutes = substr ( $coordinate, $degreePosition + $degSignLength, $minuteLength );
		$seconds = substr ( $coordinate, $minutePosition + 1, $secondLength );
		
		$coordinate = $degrees + ( $minutes + $seconds / 60 ) / 60;
		if ( $isNegative ) $coordinate *= -1;
		
		return $coordinate;
	}

	/**
	 * Takes a set of coordinates in Decimal Degree representataion, and returns them in float representataion.
	 * 
	 * @since 0.6
	 * 
	 * @param string $coordinate
	 * 
	 * @return string
	 */
	protected static function parseDDCoordinate( $coordinate ) {
		return (float)str_replace( Maps_GEO_DEG, '', $coordinate );
	}
	
	/**
	 * Takes a set of coordinates in Decimal Minute representataion, and returns them in float representataion.
	 * 
	 * @since 0.6
	 * 
	 * @param string $coordinate
	 * 
	 * @return string
	 */
	protected static function parseDMCoordinate( $coordinate ) {
		$isNegative = $coordinate{0} == '-';
		if ( $isNegative ) $coordinate = substr( $coordinate, 1 );
		
		list( $degrees, $minutes ) = explode( Maps_GEO_DEG, $coordinate );
		
		$minutes = substr( $minutes, 0, -1 );
		
		$coordinate = $degrees + $minutes / 60;
		if ( $isNegative ) $coordinate *= -1;
		
		return $coordinate;
	}
	
	/**
	 * Gets a regex group that allows only the supported seperators.
	 * 
	 * @since 0.6.2
	 * 
	 * @return string
	 */
	protected static function getSeparatorsRegex() {
		if ( !self::$separatorsRegex ) self::$separatorsRegex = '(' . implode( '|', self::$separators ) . ')';
		return self::$separatorsRegex;
	}
	
	/**
	 * Parse a string containing coordinates and return the same value in the specified notation.
	 * 
	 * @since 0.6
	 * 
	 * @param string $coordinates
	 * @param $targetFormat
	 * @param boolean $directional
	 * 
	 * return string
	 */
	public static function parseAndFormat( $coordinates, $targetFormat = Maps_COORDS_FLOAT, $directional = false ) {
		$parsedCoords = self::parseCoordinates( $coordinates );
		
		if ( $parsedCoords ) {
			return self::formatCoordinates( $parsedCoords );
		} else {
			return false;
		}
	}
	
}