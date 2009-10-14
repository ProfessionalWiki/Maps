<?php
 
/**
 * Initialization file for parser function functionality in the Maps extension
 *
 * @file Maps_ParserFunctions.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$wgHooks['LanguageGetMagic'][] = 'efMapsFunctionMagic';
$wgHooks['ParserFirstCallInit'][] = 'efMapsRegisterParserFunctions';

/**
 * Adds the magic words for the parser functions
 */
function efMapsFunctionMagic( &$magicWords, $langCode ) {
	$magicWords['display_point'] = array( 0, 'display_point', 'display_points' );
	$magicWords['display_address'] = array( 0, 'display_address', 'display_addresses' );

	$magicWords['display_map'] = array( 0,  'display_map');

	$magicWords['geocode'] = array( 0, 'geocode' );
	$magicWords['geocodelat']	= array ( 0, 'geocodelat' );
	$magicWords['geocodelng']	= array ( 0, 'geocodelng' );
	
	return true; // Unless we return true, other parser functions won't get loaded
}	

/**
 * Adds the parser function hooks
 */
function efMapsRegisterParserFunctions(&$wgParser) {
	// A hook to enable the '#display_map' parser function
	$wgParser->setFunctionHook( 'display_map', array('MapsParserFunctions', 'displayMapRender') );
	
	// Hooks to enable the '#display_point' and '#display_points' parser functions
	$wgParser->setFunctionHook( 'display_point', array('MapsParserFunctions', 'displayPointRender') );
	//$wgParser->setFunctionHook( 'display_points', array('MapsParserFunctions', 'displayPointRender') );

	// Hooks to enable the '#display_adress' and '#display_adresses' parser functions
	$wgParser->setFunctionHook( 'display_address', array('MapsParserFunctions', 'displayAddressRender') );
	//$wgParser->setFunctionHook( 'display_addresses', array('MapsParserFunctions', 'displayAddressRender') );

	// Hooks to enable the geocoding parser functions
	$wgParser->setFunctionHook( 'geocode', array('MapsGeocoder', 'renderGeocoder') );
	$wgParser->setFunctionHook( 'geocodelat', array('MapsGeocoder', 'renderGeocoderLat') );
	$wgParser->setFunctionHook( 'geocodelng', array('MapsGeocoder', 'renderGeocoderLng') );
	
	return true;
}	

/**
 * A class that holds handlers for the mapping parser functions.
 * Spesific functions are located in @see MapsUtils
 * 
 * @author Jeroen De Dauw
 *
 */
final class MapsParserFunctions {
	
	/**
	 * Initialize the parser functions feature. This function handles the parser function hook,
	 * and will load the required classes.
	 * 
	 */
	public static function initialize() {
		global $egMapsIP, $IP, $wgAutoloadClasses, $egMapsAvailableFeatures, $egMapsServices;
		
		//$wgAutoloadClasses['MapsBaseMap'] 			= $egMapsIP . '/ParserFunctions/Maps_BaseMap.php';
		//$wgAutoloadClasses['MapsBasePointMap'] 		= $egMapsIP . '/ParserFunctions/Maps_BasePointMap.php';	
		
		foreach($egMapsAvailableFeatures['pf']['functions'] as $parser_name => $parser_data) {
			$file = $parser_data['local'] ? $egMapsIP . '/' . $parser_data['file'] : $IP . '/extensions/' . $parser_data['file'];
			$wgAutoloadClasses[$parser_data['class']] = $file;
		}
		
		foreach($egMapsServices as $serviceName => $serviceData) {
			// Check if the service has parser function support
			$hasPFs = array_key_exists('pf', $serviceData);
			
			// If the service has no parser function support, skipt it and continue with the next one.
			if (!$hasPFs) continue;
			
			// Go through the parser functions supported by the mapping service, and load their classes.
			foreach($serviceData['pf'] as $parser_name => $parser_data) {
				$file = $parser_data['local'] ? $egMapsIP . '/' . $parser_data['file'] : $IP . '/extensions/' . $parser_data['file'];
				$wgAutoloadClasses[$parser_data['class']] = $file;
			}
		}				
	}
	
	public static function getMapHtml(&$parser, array $params, $parserFunction, array $coordFails = array()) {
        global $wgLang;
        
        $map = array();
        
        // Go through all parameters, split their names and values, and put them in the $map array.
        foreach($params as $param) {
            $split = split('=', $param);
            if (count($split) > 1) {
                $paramName = strtolower(trim($split[0]));
                $paramValue = trim($split[1]);
                if (strlen($paramName) > 0 && strlen($paramValue) > 0) {
                	$map[$paramName] = $paramValue;
                }
            }
            else if (count($split) == 1) { // Default parameter (without name)
            	$split[0] = trim($split[0]);
                if (strlen($split[0]) > 0) $map['coordinates'] = $split[0];
            }
        }
        
        $coords = MapsMapper::getParamValue('coordinates', $map);
        
        if ($coords) {
            if (! MapsMapper::paramIsPresent('service', $map)) $map['service'] = '';
            $map['service'] = MapsMapper::getValidService($map['service'], 'pf');                
    
            $mapClass = self::getParserClassInstance($map['service'], $parserFunction);
    
            // Call the function according to the map service to get the HTML output
            $output = $mapClass->displayMap($parser, $map);    
            
            if (count($coordFails) > 0) {
                $output .= '<i>' . wfMsgExt( 'maps_geocoding_failed_for', array( 'parsemag' ), $wgLang->listToText($coordFails ), count( $coordFails ) ) . '</i>';
            }
        }
        elseif (trim($coords) == "" && count($coordFails) > 0) {
            $output = '<i>' . wfMsgExt( 'maps_geocoding_failed', array( 'parsemag' ), $wgLang->listToText( $coordFails ), count( $coordFails ) ) . '</i>';
        }
        else {
            $output = '<i>'.wfMsg( 'maps_coordinates_missing' ).'</i>';
        }
        
        // Return the result
        return array( $output, 'noparse' => true, 'isHTML' => true ); 	
	}
	
	private static function getParserClassInstance($service, $parserFunction) {
		global $egMapsServices;
		// TODO: add check to see if the service actually supports this parser function, and return false for error handling if not.
		//die($egMapsServices[$service]['pf'][$parserFunction]['class']);
		return new $egMapsServices[$service]['pf'][$parserFunction]['class']();
	}

	/**
	 * Sets the default map properties, gets the map HTML depending 
	 * on the provided service, and then returns it.
	 *
	 * @param unknown_type $parser
	 * @return array
	 */
	public static function displayPointRender(&$parser) {	
		$params = func_get_args();
		array_shift( $params ); // We already know the $parser ...
				
		// TODO: auto geocode when required
		
		return self::getMapHtml($parser, $params, 'display_point');
	}
	
	/**
	 * Turns the address parameter into coordinates, then calls
	 * getMapHtml() and returns it's result. 
	 *
	 * @param unknown_type $parser
	 * @return array
	 */
	public static function displayAddressRender(&$parser) {	
		// TODO: remove	
		$params = func_get_args();
		array_shift( $params ); // We already know the $parser ...
		
		$fails = self::changeAddressToCoords($params);
		
		return self::getMapHtml($parser, $params, 'display_point', $fails);
	}
	
	/**
	 * If an address value is provided, turn it into coordinates,
	 * then calls getMapHtml() and returns it's result. 
	 *
	 * @param unknown_type $parser
	 * @return array
	 */
	public static function displayMapRender(&$parser) {		
		$params = func_get_args();
		array_shift( $params ); // We already know the $parser ...
		
		// TODO: auto geocode when required
		//$fails = self::changeAddressToCoords($params);
		//die('disp map');
		
		return self::getMapHtml($parser, $params, 'display_map');
	}	
	
	/**
	 * Changes the values of the address or addresses parameter into coordinates
	 * in the provided array. Returns an array containing the addresses that
	 * could not be geocoded.
	 *
	 * @param array $params
	 */
	private static function changeAddressToCoords(&$params) {
		global $egMapsDefaultService;

		$fails = array();
		
		for ($i = 0; $i < count($params); $i++) {
			$split = split('=', $params[$i]);
			if (MapsMapper::inParamAliases(strtolower(trim($split[0])), 'service') && count($split) > 1) {
				$service = trim($split[1]);
			}
			else if (strtolower(trim($split[0])) == 'geoservice' && count($split) > 1) {
				$geoservice = trim($split[1]);
			}			
		}

		$service = isset($service) ? MapsMapper::getValidService($service, 'pf') : $egMapsDefaultService;

		$geoservice = isset($geoservice) ? $geoservice : '';
		
		for ($i = 0; $i < count($params); $i++) {
			
			$split = split('=', $params[$i]);
			$isAddress = ((strtolower(trim($split[0])) == 'address' || strtolower(trim($split[0])) == 'addresses') && count($split) > 1);
			
			if ($isAddress || count($split) == 1) {
				$address_srting = count($split) == 1 ? $split[0] : $split[1];
				$addresses = explode(';', $address_srting);

				$coordinates = array();
				
				foreach($addresses as $address) {
					$args = explode('~', $address);
					$args[0] = trim($args[0]);
					
					if (strlen($args[0]) > 0) {
						$coords =  MapsGeocoder::geocodeToString($args[0], $geoservice, $service);
						
						if ($coords) {
							$args[0] = $coords;
							$coordinates[] = implode('~', $args);
						}
						else {
							$fails[] = $args[0];
						}
					}
				}				
				
				$params[$i] = 'coordinates=' . implode(';', $coordinates);

			}
		}

		return $fails;
	}

}