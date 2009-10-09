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
	$magicWords['display_point'] = array( 0, 'display_point' );
	$magicWords['display_points'] = array( 0, 'display_points' );
	$magicWords['display_address'] = array( 0, 'display_address' );
	$magicWords['display_addresses'] = array( 0, 'display_addresses' );

	$magicWords['geocode'] = array( 0, 'geocode' );
	$magicWords['geocodelat']	= array ( 0, 'geocodelat' );
	$magicWords['geocodelng']	= array ( 0, 'geocodelng' );
	
	return true; // Unless we return true, other parser functions won't get loaded
}	

/**
 * Adds the parser function hooks
 */
function efMapsRegisterParserFunctions(&$wgParser) {
	// A hooks to enable the '#display_point' and '#display_points' parser functions
	$wgParser->setFunctionHook( 'display_point', array('MapsParserFunctions', 'displayPointRender') );
	$wgParser->setFunctionHook( 'display_points', array('MapsParserFunctions', 'displayPointsRender') );

	// A hooks to enable the '#display_adress' and '#display_adresses' parser functions
	$wgParser->setFunctionHook( 'display_address', array('MapsParserFunctions', 'displayAddressRender') );
	$wgParser->setFunctionHook( 'display_addresses', array('MapsParserFunctions', 'displayAddressesRender') );

	// A hook to enable the geocoder parser functions
	$wgParser->setFunctionHook( 'geocode', array('MapsGeocoder', 'renderGeocoder') );
	$wgParser->setFunctionHook( 'geocodelat' , array('MapsGeocoder', 'renderGeocoderLat') );
	$wgParser->setFunctionHook( 'geocodelng' , array('MapsGeocoder', 'renderGeocoderLng') );
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
	 * Initialize the parser functions feature
	 * 
	 */
	public static function initialize() {
		global $egMapsIP, $wgAutoloadClasses, $wgHooks, $wgParser;
		
		$wgAutoloadClasses['MapsBaseMap'] 			= $egMapsIP . '/ParserFunctions/Maps_BaseMap.php';	
	}
	
	public static function getMapHtml(&$parser, array $params, array $coordFails = array()) {
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
    
            $mapClass = self::getParserClassInstance($map['service']);
    
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
	
	private static function getParserClassInstance($service) {
		global $egMapsServices;
		return new $egMapsServices[$service]['pf']['class']();
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
				
		return self::getMapHtml($parser, $params);
	}
	
	/**
	 * Sets the default map properties, gets the map HTML depending 
	 * on the provided service, and then returns it.
	 *
	 * @param unknown_type $parser
	 */
	public static function displayPointsRender(&$parser) {	
		$params = func_get_args();
		array_shift( $params ); // We already know the $parser ...
		
		return self::getMapHtml($parser, $params);
	}
	
	/**
	 * Turns the address parameter into coordinates, then calls
	 * getMapHtml() and returns it's result. 
	 *
	 * @param unknown_type $parser
	 * @return array
	 */
	public static function displayAddressRender(&$parser) {		
		$params = func_get_args();
		array_shift( $params ); // We already know the $parser ...
		
		$fails = self::changeAddressToCoords($params);
		
		return self::getMapHtml($parser, $params, $fails);
	}
	
	/**
	 * Turns the address parameter into coordinates, then calls
	 * getMapHtml() and returns it's result. 
	 *
	 * @param unknown_type $parser
	 */
	public static function displayAddressesRender(&$parser) {
		$params = func_get_args();
		array_shift( $params ); // We already know the $parser ...
		
		$fails = self::changeAddressToCoords($params);
		
		return self::getMapHtml($parser, $params, $fails);
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