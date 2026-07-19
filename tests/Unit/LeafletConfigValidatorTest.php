<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit;

use Maps\LeafletConfigValidator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Maps\LeafletConfigValidator
 */
class LeafletConfigValidatorTest extends TestCase {

	private function validate( string $json ): array {
		return ( new LeafletConfigValidator() )->validate( $json );
	}

	private function configWithDefinition( string $definitionJson ): string {
		return '{"leaflet":{"layerDefinitions":{"Historic":' . $definitionJson . '}}}';
	}

	public function testEmptyObjectIsValid() {
		$this->assertSame( [], $this->validate( '{}' ) );
	}

	public function testEmptyLeafletSectionIsValid() {
		$this->assertSame( [], $this->validate( '{"leaflet":{}}' ) );
	}

	public function testFullyPopulatedConfigIsValid() {
		$json = '{
			"leaflet": {
				"layerDefinitions": {
					"Historic 1904": {
						"url": "https://tiles.example/{z}/{x}/{y}.png",
						"options": { "attribution": "Historic", "maxZoom": 18 }
					},
					"Weather": {
						"wms": true,
						"url": "https://example.org/wms",
						"options": { "layers": "precip", "format": "image/png", "transparent": true }
					}
				},
				"defaultLayers": [ "OpenStreetMap" ],
				"defaultOverlays": [],
				"availableLayers": { "OpenStreetMap": true, "Esri.WorldImagery": false },
				"availableOverlays": { "OpenSeaMap": true }
			}
		}';

		$this->assertSame( [], $this->validate( $json ) );
	}

	public function testSyntaxErrorIsLeftToCore() {
		$this->assertSame( [], $this->validate( '{ not valid json' ) );
	}

	public function testLiteralNullIsRejected() {
		$this->assertSame( [ [ LeafletConfigValidator::ERROR_INVALID_JSON ] ], $this->validate( 'null' ) );
	}

	public function testNonObjectJsonIsRejected() {
		$this->assertSame( [ [ LeafletConfigValidator::ERROR_INVALID_JSON ] ], $this->validate( '123' ) );
	}

	public function testJsonArrayIsRejected() {
		$this->assertSame( [ [ LeafletConfigValidator::ERROR_INVALID_JSON ] ], $this->validate( '[ 1, 2 ]' ) );
	}

	public function testUnknownTopLevelKeyIsRejected() {
		$this->assertSame(
			[ [ LeafletConfigValidator::ERROR_UNKNOWN_KEY, 'somethingElse' ] ],
			$this->validate( '{ "somethingElse": 1 }' )
		);
	}

	public function testLeafletMustBeAnObject() {
		$this->assertSame(
			[ [ LeafletConfigValidator::ERROR_NOT_OBJECT, 'leaflet' ] ],
			$this->validate( '{ "leaflet": [ 1, 2 ] }' )
		);
	}

	public function testUnknownLeafletKeyIsRejected() {
		$this->assertSame(
			[ [ LeafletConfigValidator::ERROR_UNKNOWN_KEY, 'zoom' ] ],
			$this->validate( '{ "leaflet": { "zoom": 5 } }' )
		);
	}

	public function testLayerDefinitionsMustBeAnObject() {
		$this->assertSame(
			[ [ LeafletConfigValidator::ERROR_NOT_OBJECT, 'layerDefinitions' ] ],
			$this->validate( '{ "leaflet": { "layerDefinitions": [ 1 ] } }' )
		);
	}

	public function testReservedLayerNameIsRejected() {
		$json = '{"leaflet":{"layerDefinitions":{'
			. '"Good1":{"url":"https://tiles.example/1/{z}/{x}/{y}.png"},'
			. '"__proto__":{"url":"https://tiles.example/2/{z}/{x}/{y}.png"},'
			. '"Good2":{"url":"https://tiles.example/3/{z}/{x}/{y}.png"}'
			. '}}}';

		$this->assertSame(
			[ [ LeafletConfigValidator::ERROR_INVALID_LAYER_NAME, '__proto__' ] ],
			$this->validate( $json )
		);
	}

	public function testLayerDefinitionMustBeAnObject() {
		$this->assertSame(
			[ [ LeafletConfigValidator::ERROR_NOT_OBJECT, 'Historic' ] ],
			$this->validate( $this->configWithDefinition( '"not-an-object"' ) )
		);
	}

	public function testUnknownDefinitionKeyIsRejected() {
		$this->assertSame(
			[ [ LeafletConfigValidator::ERROR_UNKNOWN_LAYER_KEY, 'Historic', 'colour' ] ],
			$this->validate( $this->configWithDefinition(
				'{ "url": "https://tiles.example/{z}/{x}/{y}.png", "colour": "red" }'
			) )
		);
	}

	public function testMissingUrlIsRejected() {
		$this->assertSame(
			[ [ LeafletConfigValidator::ERROR_INVALID_URL, 'Historic' ] ],
			$this->validate( $this->configWithDefinition( '{ "options": {} }' ) )
		);
	}

	public function testNonHttpUrlIsRejected() {
		$this->assertSame(
			[ [ LeafletConfigValidator::ERROR_INVALID_URL, 'Historic' ] ],
			$this->validate( $this->configWithDefinition( '{ "url": "ftp://tiles.example/{z}/{x}/{y}.png" }' ) )
		);
	}

	public function testNonBooleanWmsIsRejected() {
		$this->assertSame(
			[ [ LeafletConfigValidator::ERROR_INVALID_WMS, 'Historic' ] ],
			$this->validate( $this->configWithDefinition(
				'{ "url": "https://tiles.example/{z}/{x}/{y}.png", "wms": "yes" }'
			) )
		);
	}

	public function testUnknownOptionIsRejected() {
		$this->assertSame(
			[ [ LeafletConfigValidator::ERROR_UNKNOWN_OPTION, 'Historic', 'evil' ] ],
			$this->validate( $this->configWithDefinition(
				'{ "url": "https://tiles.example/{z}/{x}/{y}.png", "options": { "maxZoom": 18, "evil": 1, "opacity": 0.5 } }'
			) )
		);
	}

	public function testWmsOnlyOptionIsRejectedForTileLayer() {
		$this->assertSame(
			[ [ LeafletConfigValidator::ERROR_UNKNOWN_OPTION, 'Historic', 'layers' ] ],
			$this->validate( $this->configWithDefinition(
				'{ "url": "https://tiles.example/{z}/{x}/{y}.png", "options": { "layers": "x" } }'
			) )
		);
	}

	public function testWmsOptionIsAcceptedForWmsLayer() {
		$this->assertSame(
			[],
			$this->validate( $this->configWithDefinition(
				'{ "url": "https://example.org/wms", "wms": true, "options": { "layers": "x" } }'
			) )
		);
	}

	public function testNonStringAttributionIsRejected() {
		$this->assertSame(
			[ [ LeafletConfigValidator::ERROR_INVALID_ATTRIBUTION, 'Historic' ] ],
			$this->validate( $this->configWithDefinition(
				'{ "url": "https://tiles.example/{z}/{x}/{y}.png", "options": { "attribution": 5 } }'
			) )
		);
	}

	public function testAttributionMarkupIsAcceptedAtSaveTime() {
		$this->assertSame(
			[],
			$this->validate( $this->configWithDefinition(
				'{ "url": "https://tiles.example/{z}/{x}/{y}.png", "options": { "attribution": "<script>x</script>" } }'
			) )
		);
	}

	public function testNonHttpErrorTileUrlOptionIsRejected() {
		$this->assertSame(
			[ [ LeafletConfigValidator::ERROR_INVALID_OPTION_URL, 'Historic', 'errorTileUrl' ] ],
			$this->validate( $this->configWithDefinition(
				'{ "url": "https://tiles.example/{z}/{x}/{y}.png", "options": { "errorTileUrl": "javascript:alert(1)" } }'
			) )
		);
	}

	public function testOptionsMustBeAnObject() {
		$this->assertSame(
			[ [ LeafletConfigValidator::ERROR_NOT_OBJECT, 'Historic.options' ] ],
			$this->validate( $this->configWithDefinition(
				'{ "url": "https://tiles.example/{z}/{x}/{y}.png", "options": [ 1 ] }'
			) )
		);
	}

	public function testInvalidDefaultLayersIsRejected() {
		$this->assertSame(
			[ [ LeafletConfigValidator::ERROR_INVALID_DEFAULT_LIST, 'defaultLayers' ] ],
			$this->validate( '{ "leaflet": { "defaultLayers": "OpenStreetMap" } }' )
		);
	}

	public function testDefaultLayersWithNonStringElementIsRejected() {
		$this->assertSame(
			[ [ LeafletConfigValidator::ERROR_INVALID_DEFAULT_LIST, 'defaultLayers' ] ],
			$this->validate( '{ "leaflet": { "defaultLayers": [ "OpenStreetMap", 5 ] } }' )
		);
	}

	public function testInvalidAvailableLayersIsRejected() {
		$this->assertSame(
			[ [ LeafletConfigValidator::ERROR_INVALID_AVAILABILITY, 'availableLayers' ] ],
			$this->validate( '{ "leaflet": { "availableLayers": { "OpenStreetMap": "yes" } } }' )
		);
	}

	public function testValidAvailabilityAndDefaultsAreAccepted() {
		$this->assertSame(
			[],
			$this->validate(
				'{ "leaflet": { "defaultLayers": [ "OpenStreetMap" ], "availableLayers": { "OpenStreetMap": true, "Esri": false } } }'
			)
		);
	}

	public function testAllErrorsAreReported() {
		$json = '{"leaflet":{"layerDefinitions":{"Historic":{"url":"ftp://x"}},"defaultLayers":"x"}}';

		$this->assertEqualsCanonicalizing(
			[
				[ LeafletConfigValidator::ERROR_INVALID_URL, 'Historic' ],
				[ LeafletConfigValidator::ERROR_INVALID_DEFAULT_LIST, 'defaultLayers' ],
			],
			$this->validate( $json )
		);
	}

}
