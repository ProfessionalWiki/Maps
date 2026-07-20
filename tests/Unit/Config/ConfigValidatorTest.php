<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\Config;

use Maps\Config\ConfigSchema;
use Maps\Config\ConfigValidator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\Config\ConfigValidator
 */
class ConfigValidatorTest extends TestCase {

	private function validate( string $json ): array {
		return ( new ConfigValidator( ConfigSchema::newDefault() ) )->validate( $json );
	}

	public function testEmptyObjectIsValid(): void {
		$this->assertSame( [], $this->validate( '{}' ) );
	}

	public function testEmptyGroupIsValid(): void {
		$this->assertSame( [], $this->validate( '{"leaflet":{}}' ) );
	}

	public function testFullyPopulatedConfigIsValid(): void {
		$json = '{
			"general": { "mapWidth": "auto", "mapHeight": 400, "distanceUnits": { "m": 1, "km": 1000 }, "distanceDecimals": 2 },
			"coordinates": { "availableNotations": [ "float", "dms" ], "notation": "dms", "directional": true },
			"geocoding": { "service": "google" },
			"semanticMediaWiki": { "showTitle": false, "template": "Map popup" },
			"leaflet": { "defaultLayers": [ "OpenStreetMap" ], "availableLayers": { "OpenStreetMap": true }, "defaultZoom": 10 },
			"googleMaps": { "zoom": 8, "type": "satellite", "types": [ "roadmap", "satellite" ], "language": "en-GB", "showPoi": false }
		}';

		$this->assertSame( [], $this->validate( $json ) );
	}

	public function testSyntaxErrorIsLeftToCore(): void {
		$this->assertSame( [], $this->validate( '{ not valid json' ) );
	}

	public function testLiteralNullIsRejected(): void {
		$this->assertSame( [ [ ConfigValidator::ERROR_INVALID_JSON ] ], $this->validate( 'null' ) );
	}

	public function testNonObjectJsonIsRejected(): void {
		$this->assertSame( [ [ ConfigValidator::ERROR_INVALID_JSON ] ], $this->validate( '123' ) );
	}

	public function testJsonArrayIsRejected(): void {
		$this->assertSame( [ [ ConfigValidator::ERROR_INVALID_JSON ] ], $this->validate( '[ 1, 2 ]' ) );
	}

	public function testUnknownGroupIsRejected(): void {
		$this->assertSame(
			[ [ ConfigValidator::ERROR_UNKNOWN_KEY, 'somethingElse' ] ],
			$this->validate( '{ "somethingElse": {} }' )
		);
	}

	public function testGroupMustBeAnObject(): void {
		$this->assertSame(
			[ [ ConfigValidator::ERROR_NOT_OBJECT, 'general' ] ],
			$this->validate( '{ "general": [ 1, 2 ] }' )
		);
	}

	public function testUnknownKeyInGroupIsRejected(): void {
		$this->assertSame(
			[ [ ConfigValidator::ERROR_UNKNOWN_KEY, 'general.somethingElse' ] ],
			$this->validate( '{ "general": { "somethingElse": 1 } }' )
		);
	}

	public function testInvalidBooleanSurfacesTheTypeError(): void {
		$this->assertSame(
			[ [ 'maps-config-error-invalid-boolean', 'general.resizableByDefault' ] ],
			$this->validate( '{ "general": { "resizableByDefault": "yes" } }' )
		);
	}

	public function testInvalidDimensionSurfacesTheTypeError(): void {
		$this->assertSame(
			[ [ 'maps-config-error-invalid-dimension', 'general.mapWidth' ] ],
			$this->validate( '{ "general": { "mapWidth": "1 px" } }' )
		);
	}

	public function testInvalidNotationEnumSurfacesTheTypeError(): void {
		$this->assertSame(
			[ [ 'maps-config-error-invalid-enum', 'coordinates.notation', 'float, dms, dm, dd' ] ],
			$this->validate( '{ "coordinates": { "notation": "utm" } }' )
		);
	}

	public function testInvalidLanguagePatternSurfacesTheTypeError(): void {
		$this->assertSame(
			[ [ 'maps-config-error-invalid-language', 'googleMaps.language' ] ],
			$this->validate( '{ "googleMaps": { "language": "not a code!" } }' )
		);
	}

	public function testLeafletLayerDefinitionErrorSurfaces(): void {
		$this->assertSame(
			[ [ 'maps-config-error-invalid-url', 'Historic' ] ],
			$this->validate( '{ "leaflet": { "layerDefinitions": { "Historic": { "url": "ftp://x" } } } }' )
		);
	}

	public function testErrorsFromMultipleGroupsAreAllReported(): void {
		$json = '{ "general": { "distanceDecimals": "lots" }, "coordinates": { "notation": "utm" } }';

		$this->assertEqualsCanonicalizing(
			[
				[ 'maps-config-error-invalid-integer', 'general.distanceDecimals' ],
				[ 'maps-config-error-invalid-enum', 'coordinates.notation', 'float, dms, dm, dd' ],
			],
			$this->validate( $json )
		);
	}

}
