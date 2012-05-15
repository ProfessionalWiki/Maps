<?php

class MapsUtils {

	/**
	 * Checks if a given url is valid. (requires a schema present)
	 * @static
	 * @param $url
	 * @return mixed
	 * @since 1.1
	 */
	public static function isValidURL( $url ) {
		return filter_var( $url , FILTER_VALIDATE_URL , FILTER_FLAG_SCHEME_REQUIRED );
	}


	/**
	 * Helper function that returns true if a $haystack (string) starts with $needle
	 * @static
	 * @param $haystack
	 * @param $needle
	 * @return bool
	 * @since 1.1
	 */
	public static function strStartsWith( $haystack , $needle ) {
		return strpos( $haystack , $needle ) === 0;
	}
}