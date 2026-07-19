<?php

declare( strict_types = 1 );

namespace Maps\Presentation;

use Maps\MapsFactory;
use MediaWiki\MediaWikiServices;

/**
 * Static class for distance validation and parsing. Internal representations are in meters.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapsDistanceParser {

	private static $unitRegex = false;

	public static function parseAndFormat( string $distance, ?string $unit = null, int $decimals = 2 ): string {
		return self::formatDistance( self::parseDistance( $distance ), $unit, $decimals );
	}

	/**
	 * Formats a given distance in meters to a distance in an optionally specified notation.
	 */
	public static function formatDistance( float $meters, ?string $unit = null, int $decimals = 2 ): string {
		$meters = MediaWikiServices::getInstance()->getContentLanguage()->formatNum( round( $meters / self::getUnitRatio( $unit ), $decimals ) );
		return "$meters $unit";
	}

	/**
	 * Returns the unit to meter ratio in a safe way, by first resolving the unit.
	 */
	public static function getUnitRatio( ?string $unit = null ): float {
		return (float)self::getUnitDefinitions()[self::getValidUnit( $unit )];
	}

	/**
	 * Returns a valid unit. If the provided one is invalid, the default will be used.
	 */
	public static function getValidUnit( ?string $unit = null ): string {
		$units = self::getUnitDefinitions();

		if ( $unit === null || !array_key_exists( $unit, $units ) ) {
			return self::getDefaultUnit( $units );
		}

		return $unit;
	}

	/**
	 * The configured default unit, or the first available unit when the configured one is unknown.
	 *
	 * @param array<string, int|float> $units
	 */
	private static function getDefaultUnit( array $units ): string {
		$default = MapsFactory::globalInstance()->getEffectiveSettings()->get( 'egMapsDistanceUnit' );

		return array_key_exists( $default, $units ) ? $default : (string)array_key_first( $units );
	}

	/**
	 * @return array<string, int|float> Unit name to meter ratio.
	 */
	private static function getUnitDefinitions(): array {
		return MapsFactory::globalInstance()->getEffectiveSettings()->get( 'egMapsDistanceUnits' );
	}

	/**
	 * Parses a distance optionally containing a unit to a float value in meters.
	 *
	 * @param string $distance
	 *
	 * @return float|false The distance in meters or false on failure
	 */
	public static function parseDistance( string $distance ) {
		if ( !self::isDistance( $distance ) ) {
			return false;
		}

		$distance = self::normalizeDistance( $distance );

		self::initUnitRegex();

		$matches = [];
		preg_match( '/^\d+(\.\d+)?\s?(' . self::$unitRegex . ')?$/', $distance, $matches );

		$value = (float)( $matches[0] . $matches[1] );
		$value *= self::getUnitRatio( $matches[2] );

		return $value;
	}

	public static function isDistance( string $distance ): bool {
		$distance = self::normalizeDistance( $distance );

		self::initUnitRegex();

		return (bool)preg_match( '/^\d+(\.\d+)?\s?(' . self::$unitRegex . ')?$/', $distance );
	}

	/**
	 * Normalizes a potential distance by removing spaces and turning comma's into dots.
	 */
	protected static function normalizeDistance( string $distance ): string {
		$distance = trim( (string)$distance );
		$strlen = strlen( $distance );

		for ( $i = 0; $i < $strlen; $i++ ) {
			if ( !ctype_digit( $distance[$i] ) && !in_array( $distance[$i], [ ',', '.' ] ) ) {
				$value = substr( $distance, 0, $i );
				$unit = substr( $distance, $i );
				break;
			}
		}

		$value = str_replace( ',', '.', isset( $value ) ? $value : $distance );

		if ( isset( $unit ) ) {
			$value .= ' ' . str_replace( [ ' ', "\t" ], '', $unit );
		}

		return $value;
	}

	private static function initUnitRegex() {
		if ( self::$unitRegex === false ) {
			self::$unitRegex = implode( '|', self::getUnits() ) . '|';
		}
	}

	/**
	 * Returns a list of all supported units.
	 */
	public static function getUnits(): array {
		return array_keys( self::getUnitDefinitions() );
	}

}
