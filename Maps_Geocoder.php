<?php

/**
 * File containing the MapsGeocoder class which handles the non specific geocoding tasks
 *
 * {{#geocode:<Address>|<param1>=<value1>|<param2>=<value2>}}
 * {{#geocodelat:<Address>|<param1>=<value1>|<param2>=<value2>}}
 * {{#geocodelng:<Address>|<param1>=<value1>|<param2>=<value2>}}
 *
 * @file Maps_Geocoder.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 * @author Sergey Chernyshev
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

class MapsGeocoder {

	private static $GeocoderCache = array();

	public static function renderGeocoder(&$parser, $address) {
		$params = func_get_args();
		array_shift($params); # first one is parser - we don't need it
		array_shift($params); # second one is address

		$params_hash = array();
	
		foreach ($params as $param) {
	                $pair = explode('=', $param, 2);
	
	                if (count($pair) == 2)
	                {
				$key = trim($pair[0]);
				$val = trim($pair[1]);
	
				$params_hash[$key] = $val;
	                }
			else
			{
				$params_hash[$param] = true;
			}
	        }
	
		$geovalues = MapsGeocoder::callGeocoder($address);
	
		if (!$geovalues)
		{
			return '';
		}
	
		return $geovalues[2].', '.$geovalues[3];
	}



	public static function renderGeocoderLat(&$parser, $address) {
		$params = func_get_args();
		array_shift($params); # first one is parser - we don't need it
		array_shift($params); # second one is address

		$params_hash = array();
	
	        foreach ($params as $param)
	        {
	                $pair = explode('=', $param, 2);
	
	                if (count($pair) == 2)
	                {
				$key = trim($pair[0]);
				$val = trim($pair[1]);
	
				$params_hash[$key] = $val;
	                }
			else
			{
				$params_hash[$param] = true;
			}
	        }
	
		$geovalues = MapsGeocoder::callGeocoder($address);
	
		if (!$geovalues)
		{
			return '';
		}
	
		return $geovalues[2];
	}



	public static function renderGeocoderLng(&$parser, $address) {
		$params = func_get_args();
		array_shift($params); # first one is parser - we don't need it
		array_shift($params); # second one is address
	
		$params_hash = array();
	
		foreach ($params as $param)
	       {
	               $pair = explode('=', $param, 2);
	
	               if (count($pair) == 2)
	               {
				$key = trim($pair[0]);
				$val = trim($pair[1]);
	
				$params_hash[$key] = $val;
			}
			else
			{
				$params_hash[$param] = true;
			}
		}
	
		$geovalues = MapsGeocoder::callGeocoder($address);
	
		if (!$geovalues)
		{
			return '';
		}
	
		return $geovalues[3];
	}

	private static function callGeocoder($address, $service = 'google') {
		MapsGeocoder::$GeocoderCache;
	
		// If the adress is already in the cache, return the coordinates
		if (isset(MapsGeocoder::$GeocoderCache[$address])) return MapsGeocoder::$GeocoderCache[$address];

		// If not, use the selected geocoding service to geocode the provided adress
		switch($service) {
			case '':
				MapsGeocoder::addAutoloadClassIfNeeded('', '');
				$coordinates = '';
				break;
			default:
				MapsGeocoder::addAutoloadClassIfNeeded('MapsGoogleGeocoder', 'Maps_GoogleGeocoder.php');
				$coordinates = MapsGoogleGeocoder::callGeocoder($address);
				break;
		}

		if ($coordinates) {
			// Add the obtained coordinates to the cache when there is a result
			MapsGeocoder::$GeocoderCache[$address] = $coordinates;
		}

		return $coordinates;
	}

	private static function addAutoloadClassIfNeeded($className, $fileName) {
		global $wgAutoloadClasses, $egMapsIP;
		if (!array_key_exists($className, $wgAutoloadClasses)) $wgAutoloadClasses[$className] = $egMapsIP . '/Geocoders/' . $fileName;
	}
}



