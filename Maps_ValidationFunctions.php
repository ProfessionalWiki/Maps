<?php

/** 
 * File holding the MapsValidationFunctions class.
 *
 * @file Maps_ValidationFunctions.php
 * @ingroup Maps
 *
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/** 
 * Class holding variouse static methods for the validation of parameters that have to comply to cetrain criteria.
 * 
 * @ingroup Maps
 * 
 * @author Jeroen De Dauw
 */
final class MapsValidationFunctions {
	
	/**
	 * Returns whether the provided value, which must be a number, is within a certain range.
	 * 
	 * @param string $value
	 * @param array $limits
	 * 
	 * @return boolean
	 */
	public static function in_range($value, array $limits) {
		if (! is_numeric($value)) return false;
		$value = (int)$value;
		return ($value >= $limits[0] && $value <= $limits[1]) || ($value <= $limits[0] And $value >= $limits[1]);
	}
	
	/**
	 * Returns whether the string value is not empty. Not empty is defined as having at least one character after trimming.
	 * 
	 * @param string $value
	 * 
	 * @return boolean
	 */
	public static function not_empty($value) {
		return strlen(trim($value)) > 0;
	}	
	
	/**
	 * Returns if all items of the first array are present in the second one.
	 * 
	 * @param array $needles
	 * @param array $haystack
	 * 
	 * @return boolean
	 */
	public static function all_in_array(array $needles, array $haystack) {
		$true = true;
		foreach($needles as $needle) {
			if (! in_array($needle, $haystack)) {
				$true = false;
				break;
			}
		}
		return $true;
	}	
	
	/**
	 * Returns if any items of the first array are present in the second one.
	 * 
	 * @param array $needles
	 * @param array $haystack
	 * 
	 * @return boolean
	 */
	public static function any_in_array(array $needles, array $haystack) {
		$true = false;
		foreach($needles as $needle) {
			if (in_array($needle, $haystack)) {
				$true = true;
				break;
			}
		}
		return $true;
	}
	
	/**
	 * Returns if all items in the string are present in the array.
	 * The first element of the $args array should be the delimieter, 
	 * the second one an array holding the haystack.
	 * 
	 * @param string $needles
	 * @param array $args
	 * 
	 * @return boolean
	 */
	public static function all_str_in_array($needles, array $args) {
		return self::all_in_array(explode($args[0], $needles), $args[1]);
	}	

	/**
	 * Returns if any items in the string are present in the array.
	 * The first element of the $args array should be the delimieter, 
	 * the second one an array holding the haystack.
	 * 
	 * @param string $needles
	 * @param array $args
	 * 
	 * @return boolean
	 */
	public static function any_str_in_array($needles, array $args) {
		return self::any_in_array(explode($args[0], $needles), $args[1]);
	}	
	
}