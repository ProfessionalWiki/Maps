<?php

/**
 * Initialization file for parser function functionality in the Maps extension
 *
 * @file Maps_ParserFunctions.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$wgAutoloadClasses['MapsParserFunctions'] = __FILE__;

$wgHooks['MappingFeatureLoad'][] = 'MapsParserFunctions::initialize';

/**
 * A class that holds handlers for the mapping parser functions.
 * 
 * @author Jeroen De Dauw
 */
final class MapsParserFunctions {
	
	public static $parameters = array();
	
	/**
	 * Initialize the parser functions feature. This function handles the parser function hook,
	 * and will load the required classes.
	 */
	public static function initialize() {
		global $egMapsDir, $IP, $wgAutoloadClasses, $egMapsFeatures, $egMapsServices;
		
		include_once $egMapsDir . 'ParserFunctions/Maps_iDisplayFunction.php';
		
		self::initializeParams();
		
		// This runs a small hook that enables parser functions to run initialization code.
		foreach ( $egMapsFeatures['pf'] as $hook ) {
			call_user_func( $hook );
		}
		
		return true;
	}
	
	private static function initializeParams() {
		global $egMapsAvailableServices, $egMapsDefaultServices, $egMapsAvailableGeoServices, $egMapsDefaultGeoService;
		
		self::$parameters = array(
			'service' => array(
				'criteria' => array(
					'in_array' => $egMapsAvailableServices
				),		
			),
			'coordinates' => array(
				'aliases' => array( 'coords', 'location', 'locations' ),
			),
			'geoservice' => array(
				'criteria' => array(
					'in_array' => $egMapsAvailableGeoServices
				),
				'default' => $egMapsDefaultGeoService
			),
		);
	}
	
	/**
	 * Returns the output for the call to the specified parser function.
	 * 
	 * @param $parser
	 * @param array $params
	 * @param string $parserFunction
	 * 
	 * @return array
	 */
	public static function getMapHtml( &$parser, array $params, $parserFunction ) {
        global $wgLang, $egValidatorErrorLevel;
        
        array_shift( $params ); // We already know the $parser.

        $map = array();
        $coordFails = array();
        
        $paramInfo = array_merge( MapsMapper::getMainParams(), self::$parameters );

        $geoFails = self::changeAddressesToCoords( $params, $paramInfo, $parserFunction );
        
        // Go through all parameters, split their names and values, and put them in the $map array.
        foreach ( $params as $param ) {
            $split = explode( '=', $param );
            if ( count( $split ) > 1 ) {           	
                $paramName = strtolower( trim( array_shift( $split ) ) );
                $paramValue = trim( implode( $split ) );
                if ( $paramName != '' && $paramValue != '' ) {
                	$map[$paramName] = $paramValue;
                	if ( self::inParamAliases( $paramName, 'coordinates', $paramInfo ) ) $coordFails = self::filterInvalidCoords( $map[$paramName] );
                }
            }
            else { // Default parameter (without name).
            	$split[0] = trim( $split[0] );
                if ( $split[0] != '' ) $map['coordinates'] = $split[0];
            }
        }

        $coords = self::getParamValue( 'coordinates', $map, $paramInfo );
        
        if ( $coords ) {
            $mapClass = self::getParserClassInstance( $map['service'], $parserFunction );
    
            // Call the function according to the map service to get the HTML output.
            $output = $mapClass->displayMap( $parser, $map );

            if ( $egValidatorErrorLevel >= Validator_ERRORS_WARN ) {
	            if ( count( $coordFails ) > 0 ) {
	                $output .= '<i>' . wfMsgExt( 'maps_unrecognized_coords_for', array( 'parsemag' ), $wgLang->listToText( $coordFails ), count( $coordFails ) ) . '</i>';
	            }

	            if ( count( $geoFails ) > 0 ) {
	                $output .= '<i>' . wfMsgExt( 'maps_geocoding_failed_for', array( 'parsemag' ), $wgLang->listToText( $geoFails ), count( $geoFails ) ) . '</i>';
	            }
            }
        }
        elseif ( $egValidatorErrorLevel >= Validator_ERRORS_MINIMAL ) {
	        if ( $coords == '' && ( count( $geoFails ) > 0 || count( $coordFails ) > 0 ) ) {
	        	if ( count( $coordFails ) > 0 ) $output = '<i>' . wfMsgExt( 'maps_unrecognized_coords', array( 'parsemag' ), $wgLang->listToText( $coordFails ), count( $coordFails ) ) . '</i>';
	            if ( count( $geoFails ) > 0 ) $output = '<i>' . wfMsgExt( 'maps_geocoding_failed', array( 'parsemag' ), $wgLang->listToText( $geoFails ), count( $geoFails ) ) . '</i>';
	            $output .= '<i>' . wfMsg( 'maps_map_cannot_be_displayed' ) . '</i>';
	        }
	        else {
	            $output = '<i>' . wfMsg( 'maps_coordinates_missing' ) . '</i>';
	        }
        }
        
        // Return the result.
        return array( $output, 'noparse' => true, 'isHTML' => true );
	}
	
	/**
	 * Filters all non coordinate valus from a coordinate string, 
	 * and returns an array containing all filtered out values.
	 * 
	 * @param string $coordList
	 * @param string $delimeter
	 * 
	 * @return array
	 */
	public static function filterInvalidCoords( &$coordList, $delimeter = ';' ) {
		$coordFails = array();
		$validCoordinates = array();
        $coordinateSets = explode( $delimeter, $coordList );
        
        // Loop through all the provided coordinates. If they are valid, format their parsed values
        // to non-directional floats, and add them to the valid array, else add them to the fails array.
        foreach ( $coordinateSets as $coordinates ) {
        	$parsedCoords = MapsCoordinateParser::parseCoordinates( $coordinates );
        	
        	if ( $parsedCoords ) { // Will be false when parsing failed.
        		$validCoordinates[] = MapsCoordinateParser::formatCoordinates( $parsedCoords, Maps_COORDS_FLOAT, false );
        	}
        	else {
        		$coordFails[] = $coordinates;
        	}
        }
        
        $coordList = implode( $delimeter, $validCoordinates );
        return $coordFails;
	}
	
	/**
	 * Changes the values of the address or addresses parameter into coordinates
	 * in the provided array. Returns an array containing the addresses that
	 * could not be geocoded.
	 * 
	 * Also ensures the service parameter is valid.
	 *
	 * @param array $params
	 * 
	 * @return array
	 */
	private static function changeAddressesToCoords( &$params, array $paramInfo, $parserFunction ) {
		global $egMapsDefaultService;

		$fails = array();
		
		// Get the service and geoservice from the parameters, since they are needed to geocode addresses.
		for ( $i = 0; $i < count( $params ); $i++ ) {
			$split = explode( '=', $params[$i] );
			if ( self::inParamAliases( strtolower( trim( $split[0] ) ), 'service', $paramInfo ) && count( $split ) > 1 ) {
				$service = trim( $split[1] );
			}
			else if ( strtolower( trim( $split[0] ) ) == 'geoservice' && count( $split ) > 1 ) {
				$geoservice = trim( $split[1] );
			}
		}

		// Make sure the service and geoservice are valid.
		if ( !isset( $service ) ) $service = '';
		$service = MapsMapper::getValidService( $service, $parserFunction );
		
		if ( ! isset( $geoservice ) ) $geoservice = '';
		
		$setService = false;
		
		// Go over all parameters.
		for ( $i = 0; $i < count( $params ); $i++ ) {
			$split = explode( '=', $params[$i] );
			$isAddress = ( strtolower( trim( $split[0] ) ) == 'address' || strtolower( trim( $split[0] ) ) == 'addresses' ) && count( $split ) > 1;
			$isDefault = count( $split ) == 1;
			
			// If a parameter is either the default (no name), or an addresses list, extract all locations.
			if ( $isAddress || $isDefault ) {
				
				$address_srting = $split[count( $split ) == 1 ? 0 : 1];
				$addresses = explode( ';', $address_srting );

				$coordinates = array();
				
				// Go over every location and attempt to geocode it.
				foreach ( $addresses as $address ) {
					$args = explode( '~', $address );
					$args[0] = trim( $args[0] );
					
					if ( strlen( $args[0] ) > 0 ) {
						$coords =  MapsGeocoder::attemptToGeocodeToString( $args[0], $geoservice, $service, $isDefault );
						
						if ( $coords ) {
							$args[0] = $coords;
							$coordinates[] = implode( '~', $args );
						}
						else {
							$fails[] = $args[0];
						}
					}
				}
				
				// Add the geocoded result back to the parameter list.
				$params[$i] = implode( ';', $coordinates );

			} else if ( self::inParamAliases( strtolower( trim( $split[0] ) ), 'service', $paramInfo ) && count( $split ) > 1 ) {
				$params[$i] = "service=$service";
				$setService = true;
			}
		}

		if ( !$setService ) {
			$params[] = "service=$service";
		}		
		
		return $fails;
	}
	
	/**
	 * Returns an instance of the class supporting the spesified mapping service for
	 * the also spesified parser function.
	 * 
	 * @param string $service
	 * @param string $parserFunction
	 * 
	 * @return MapsMapFeature
	 */
	public static function getParserClassInstance( $service, $parserFunction ) {
		global $egMapsServices;
		return new $egMapsServices[$service]['features'][$parserFunction]();
	}
	
	/**
	 * Gets if a provided name is present in the aliases array of a parameter
	 * name in the $mainParams array.
	 *
	 * @param string $name The name you want to check for.
	 * @param string $mainParamName The main parameter name.
	 * @param array $paramInfo Contains meta data, including aliases, of the possible parameters.
	 * @param boolean $compareMainName Boolean indicating wether the main name should also be compared.
	 * 
	 * @return boolean
	 */
	public static function inParamAliases( $name, $mainParamName, array $paramInfo = array(), $compareMainName = true ) {
		$equals = $compareMainName && $mainParamName == $name;

		if ( array_key_exists( $mainParamName, $paramInfo ) ) {
			$equals = $equals || in_array( $name, $paramInfo[$mainParamName] );
		}

		return $equals;
	}
	
    /**
     * Gets if a parameter is present as key in the $stack. Also checks for
     * the presence of aliases in the $mainParams array unless specified not to.
     *
     * @param string $paramName
     * @param array $stack
	 * @param array $paramInfo Contains meta data, including aliases, of the possible parameters.
     * @param boolean $checkForAliases
     * 
     * @return boolean
     */
    public static function paramIsPresent( $paramName, array $stack, array $paramInfo = array(), $checkForAliases = true ) {
        $isPresent = array_key_exists( $paramName, $stack );
        
        if ( $checkForAliases && array_key_exists( 'aliases', $paramInfo[$paramName] ) ) {
            foreach ( $paramInfo[$paramName]['aliases'] as $alias ) {
                if ( array_key_exists( $alias, $stack ) ) {
                	$isPresent = true;
                	break;
                }
            }
        }

        return $isPresent;
    }
	
	/**
	 * Returns the value of a parameter represented as key in the $stack.
	 * Also checks for the presence of aliases in the $mainParams array
	 * and returns the value of the alies unless specified not to. When
	 * no array key name match is found, false will be returned.
	 *
	 * @param string $paramName
	 * @param array $stack The values to search through
	 * @param array $paramInfo Contains meta data, including aliases, of the possible parameters.
	 * @param boolean $checkForAliases
	 * 
	 * @return the parameter value or false
	 */
	private static function getParamValue( $paramName, array $stack, array $paramInfo = array(), $checkForAliases = true ) {
		$paramValue = false;
		
		if ( array_key_exists( $paramName, $stack ) ) $paramValue = $stack[$paramName];
		
		if ( $checkForAliases ) {
			foreach ( $paramInfo[$paramName]['aliases'] as $alias ) {
				if ( array_key_exists( $alias, $stack ) ) $paramValue = $stack[$alias];
				break;
			}
		}
		
		return $paramValue;
	}

}