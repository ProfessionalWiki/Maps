<?php

class MapsUtils {

	/**
	 * Checks if a given url is valid. (requires a schema present)
	 * @static
	 * @param $url
	 * @return mixed
	 */
	static function isValidURL($url){
		return filter_var($url, FILTER_VALIDATE_URL,FILTER_FLAG_SCHEME_REQUIRED);
	}


	/**
	 * Checks if a string is prefixed with link:
	 * @static
	 * @param $link
	 * @return bool|string
	 */
	static function isLinkParameter($link){
		if(self::strStartsWith($link,'link:')){
			return substr($link,5);
		}
		return false;
	}

	/**
	 * Helper function that returns true if a $haystack (string) starts with $needle
	 * @static
	 * @param $haystack
	 * @param $needle
	 * @return bool
	 */
	static function strStartsWith($haystack, $needle){
		return strpos($haystack, $needle) === 0;
	}
}