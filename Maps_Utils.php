<?php

/**
 * A class that holds static helper functions for Semantic Maps
 *
 * @file Maps_Utils.php
 * @ingroup Maps
 *
 * @author Robert Buzink
 * @author Yaron Koren
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

class MapsUtils {

	public static function getLatLon($param2) {
		$coordinates = preg_split("/,/", $param2);
		if (count($coordinates) == 2) {
			$lat = MapsUtils::convertCoord($coordinates[0]);
			$lon = MapsUtils::convertCoord($coordinates[1]);
			return array($lat, $lon);
		}
		return array(null, null);
	}

	private static function degree2Decimal($deg_coord="") {
		$dpos=strpos($deg_coord,'°');
		$mpos=strpos($deg_coord,'.');
		$spos=strpos($deg_coord,'"');
		$mlen=(($mpos-$dpos)-1);
		$slen=(($spos-$mpos)-1);
		$direction=substr(strrev($deg_coord),0,1);
		$degrees=substr($deg_coord,0,$dpos);
		$minutes=substr($deg_coord,$dpos+1,$mlen);
		$seconds=substr($deg_coord,$mpos+1,$slen);
		$seconds=($seconds/60);
		$minutes=($minutes+$seconds);
		$minutes=($minutes/60);
		$decimal=($degrees+$minutes);
		//South latitudes and West longitudes need to return a negative result
		if (($direction=="S") or ($direction=="W")) {
			$decimal *= -1;
		}
		return $decimal;
	}

	private static function decDegree2Decimal($deg_coord = "") {
		$direction = substr(strrev($deg_coord), 0, 1);
		$decimal = floatval($deg_coord);
		if (($direction == "S") or ($direction == "W")) {
			$decimal *= -1;
		}
		return $decimal;
	}

	private static function convertCoord($deg_coord = "") {
		if (preg_match('/°/', $deg_coord)) {
			if (preg_match('/"/', $deg_coord)) {
				return MapsUtils::degree2Decimal($deg_coord);
			} else {
				return MapsUtils::decDegree2Decimal($deg_coord);
			}
		}
		return $deg_coord;
	}

	public static function latDecimal2Degree($decimal) {
		if ($decimal < 0) {
			return abs($decimal) . "° S";
		} else {
			return $decimal . "° N";
		}
	}

	public static function lonDecimal2Degree($decimal) {
		if ($decimal < 0) {
			return abs($decimal) . "° W";
		} else {
			return $decimal . "° E";
		}
	}
	
	/**
	 * Add 'px' to a provided width/heigt value
	 *
	 * @param unknown_type $value
	 */
	public static function makePxValue(&$value) {
		if (substr($value, strlen($value) - 2) != 'px') $value .= 'px';
	}

}
