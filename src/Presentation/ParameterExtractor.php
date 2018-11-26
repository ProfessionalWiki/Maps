<?php

declare( strict_types = 1 );

namespace Maps\Presentation;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ParameterExtractor {

	/**
	 * Extracts the value of a parameter from a parameter list.
	 *
	 * @param string[] $parameterNames Name and aliases of the parameter. First match gets used
	 * @param string[] $rawParameters Parameters that did not get processed further than being put in a key-value map
	 *
	 * @return string|null
	 */
	public function extract( array $parameterNames, array $rawParameters ) {
		foreach( $parameterNames as $parameterName ) {
			foreach ( $rawParameters as $rawName => $rawValue ) {
				if ( trim( strtolower( $rawName ) ) === $parameterName ) {
					return trim( $rawValue );
				}
			}
		}

		return null;
	}

	public static function extractFromKeyValueStrings( array $keyValueStrings ) {
		$rawParameters = [];

		foreach ( $keyValueStrings as $keyValueString ) {
			$parts = explode( '=', $keyValueString, 2 );

			if ( count( $parts ) === 2 ) {
				$rawParameters[$parts[0]] = $parts[1];
			}
		}

		return $rawParameters;
	}

}
