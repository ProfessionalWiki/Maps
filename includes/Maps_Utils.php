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

}