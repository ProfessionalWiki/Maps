( function ( $, mw ) {
	'use strict';

	function makeDefaultOptions( overrides ) {
		return $.extend( true, {
			lines: [],
			polygons: [],
			circles: [],
			rectangles: [],
			locations: [],
			geojson: '',
			cluster: false,
			copycoords: false,
			layers: [ 'OpenStreetMap' ],
			imageLayers: [],
			overlays: [],
			zoom: false,
			defzoom: 14,
			centre: false,
			minzoom: false,
			maxzoom: false,
			fullscreen: false,
			scrollwheelzoom: true,
			static: false,
			resizable: false,
			clicktarget: '',
			ajaxquery: '',
			ajaxcoordproperty: '',
			GeoJsonSource: null,
			GeoJsonRevisionId: null,
			icon: ''
		}, overrides || {} );
	}

	QUnit.module( 'Maps.JQueryLeaflet', QUnit.newMwEnvironment( {
		config: {
			egMapsLeafletLayersApiKeys: {},
			egMapsScriptPath: mw.config.get( 'wgExtensionAssetsPath' ) + '/Maps/'
		}
	} ) );

	// Helper: creates a leafletmaps instance with a manual L.map, avoiding the
	// async setTimeout in setup() that causes global failures after test cleanup.
	function createTestMap( $div, options ) {
		var jqueryMap = $div.leafletmaps( options );
		jqueryMap.map = L.map( $div.get( 0 ), { zoomControl: false } );
		jqueryMap.mapContent = maps.leaflet.FeatureBuilder.contentLayerFromOptions( options );
		jqueryMap.mapContent.addTo( jqueryMap.map );
		jqueryMap.map.fitWorld();
		return jqueryMap;
	}

	// Helper: runs addLayersAndOverlays and returns the baseLayers/overlays objects
	// handed to L.control.layers. Stubs L.control.layers so the names are never
	// rendered (avoiding payload execution), and L.tileLayer.provider so arbitrary
	// names don't hit the provider catalog.
	function captureLayerControl( $div, options ) {
		var jqueryMap = createTestMap( $div, options );

		var stubLayer = { addTo: function () { return this; } };
		var originalProvider = L.tileLayer.provider;
		var originalControlLayers = L.control.layers;
		var captured = {};

		L.tileLayer.provider = function () { return stubLayer; };
		L.control.layers = function ( baseLayers, overlays ) {
			captured.baseLayers = baseLayers;
			captured.overlays = overlays;
			return stubLayer;
		};

		try {
			jqueryMap.addLayersAndOverlays();
		} finally {
			L.control.layers = originalControlLayers;
			L.tileLayer.provider = originalProvider;
			jqueryMap.map.remove();
		}

		return captured;
	}

	QUnit.test( 'leafletmaps creates a jquery object with map methods', function ( assert ) {
		var $div = $( '<div>' ).css( { width: '400px', height: '300px' } ).appendTo( '#qunit-fixture' );
		var options = makeDefaultOptions();

		var jqueryMap = createTestMap( $div, options );

		assert.true( jqueryMap.map instanceof L.Map, 'Creates an L.Map instance' );
		assert.strictEqual( typeof jqueryMap.getBaseLayers, 'function', 'Has getBaseLayers method' );
		assert.strictEqual( typeof jqueryMap.centerAndZoomMap, 'function', 'Has centerAndZoomMap method' );

		jqueryMap.map.remove();
	} );

	QUnit.test( 'getBaseLayers returns a Map with the requested layers', function ( assert ) {
		var $div = $( '<div>' ).css( { width: '400px', height: '300px' } ).appendTo( '#qunit-fixture' );
		var options = makeDefaultOptions( {
			layers: [ 'OpenStreetMap', 'OpenTopoMap' ]
		} );

		var jqueryMap = createTestMap( $div, options );

		var baseLayers = jqueryMap.getBaseLayers();

		assert.true( baseLayers instanceof Map, 'Returns a Map object' );
		assert.strictEqual( baseLayers.size, 2, 'Map has 2 entries' );
		assert.true( baseLayers.has( 'OpenStreetMap' ), 'Contains OpenStreetMap layer' );
		assert.true( baseLayers.has( 'OpenTopoMap' ), 'Contains OpenTopoMap layer' );

		jqueryMap.map.remove();
	} );

	QUnit.test( 'centerAndZoomMap applies explicit zoom', function ( assert ) {
		var $div = $( '<div>' ).css( { width: '400px', height: '300px' } ).appendTo( '#qunit-fixture' );
		var options = makeDefaultOptions( {
			zoom: 8,
			locations: [
				{ lat: 52, lon: 5, title: 'Test', text: '', icon: '' }
			]
		} );

		var jqueryMap = createTestMap( $div, options );
		jqueryMap.centerAndZoomMap();

		assert.strictEqual( jqueryMap.map.getZoom(), 8, 'Map zoom matches the explicit zoom value' );

		jqueryMap.map.remove();
	} );

	QUnit.test( 'getNormalBaseLayers does not mutate options.layers', function ( assert ) {
		var $div = $( '<div>' ).css( { width: '400px', height: '300px' } ).appendTo( '#qunit-fixture' );
		var options = makeDefaultOptions( {
			layers: [ 'OpenStreetMap', 'OpenTopoMap' ]
		} );

		var originalLayers = options.layers.slice();

		var jqueryMap = createTestMap( $div, options );
		jqueryMap.getNormalBaseLayers();

		assert.deepEqual( options.layers, originalLayers, 'options.layers unchanged after first getNormalBaseLayers call' );

		jqueryMap.getNormalBaseLayers();

		assert.deepEqual( options.layers, originalLayers, 'options.layers unchanged after second getNormalBaseLayers call' );

		jqueryMap.map.remove();
	} );

	QUnit.test( 'addLayersAndOverlays passes base layers object to L.control.layers', function ( assert ) {
		var $div = $( '<div>' ).css( { width: '400px', height: '300px' } ).appendTo( '#qunit-fixture' );
		var options = makeDefaultOptions( {
			layers: [ 'OpenStreetMap', 'OpenTopoMap' ],
			overlays: [ 'OpenRailwayMap' ]
		} );

		var jqueryMap = createTestMap( $div, options );

		var capturedBaseLayers = null;
		var capturedOverlays = null;
		var originalControlLayers = L.control.layers;
		L.control.layers = function ( baseLayers, overlays, opts ) {
			capturedBaseLayers = baseLayers;
			capturedOverlays = overlays;
			return originalControlLayers.call( L.control, baseLayers, overlays, opts );
		};

		jqueryMap.addLayersAndOverlays();

		L.control.layers = originalControlLayers;

		assert.notStrictEqual( capturedBaseLayers, null, 'L.control.layers was called' );
		assert.false( Array.isArray( capturedBaseLayers ), 'Base layers argument is not an array' );
		assert.strictEqual( Object.keys( capturedBaseLayers ).length, 2, 'Base layers object has 2 entries' );
		assert.strictEqual( Object.keys( capturedOverlays ).length, 1, 'Overlays object has 1 entry' );

		jqueryMap.map.remove();
	} );

	QUnit.test( 'addOverlays HTML-escapes overlay names used as layer-control labels', function ( assert ) {
		var $div = $( '<div>' ).css( { width: '400px', height: '300px' } ).appendTo( '#qunit-fixture' );
		var options = makeDefaultOptions( {
			overlays: [ '<img src=x onerror="alert(1)">' ]
		} );

		var captured = captureLayerControl( $div, options );

		assert.deepEqual(
			Object.keys( captured.overlays ),
			[ '&lt;img src=x onerror=&quot;alert(1)&quot;&gt;' ],
			'Overlay name is HTML-escaped before being used as a layer-control label'
		);
	} );

	QUnit.test( 'addLayersAndOverlays HTML-escapes base layer names used as layer-control labels', function ( assert ) {
		var $div = $( '<div>' ).css( { width: '400px', height: '300px' } ).appendTo( '#qunit-fixture' );
		var options = makeDefaultOptions( {
			layers: [ 'OpenStreetMap', '<img src=x onerror="alert(1)">' ]
		} );

		var captured = captureLayerControl( $div, options );

		assert.true(
			Object.prototype.hasOwnProperty.call( captured.baseLayers, '&lt;img src=x onerror=&quot;alert(1)&quot;&gt;' ),
			'Malicious base layer name is HTML-escaped'
		);
		assert.false(
			Object.prototype.hasOwnProperty.call( captured.baseLayers, '<img src=x onerror="alert(1)">' ),
			'Raw (unescaped) base layer name is not used as a key'
		);
		assert.true(
			Object.prototype.hasOwnProperty.call( captured.baseLayers, 'OpenStreetMap' ),
			'Legitimate base layer name is left unchanged'
		);
	} );

	QUnit.test( 'centerAndZoomMap skips fitContent when both centre and zoom are set', function ( assert ) {
		var $div = $( '<div>' ).css( { width: '400px', height: '300px' } ).appendTo( '#qunit-fixture' );
		var options = makeDefaultOptions( {
			centre: { lat: 40, lon: -74 },
			zoom: 5,
			locations: [
				{ lat: 52, lon: 5, title: 'Far away marker', text: '', icon: '' }
			]
		} );

		var jqueryMap = createTestMap( $div, options );

		var fitContentCalled = false;
		var originalFitContent = jqueryMap.fitContent;
		jqueryMap.fitContent = function () {
			fitContentCalled = true;
			originalFitContent.call( jqueryMap );
		};

		jqueryMap.centerAndZoomMap();

		var center = jqueryMap.map.getCenter();

		assert.false( fitContentCalled, 'fitContent is not called when both centre and zoom are explicitly set' );
		assert.strictEqual( jqueryMap.map.getZoom(), 5, 'Map zoom matches explicit value' );
		assert.strictEqual( center.lat, 40, 'Map center latitude matches explicit value' );
		assert.strictEqual( center.lng, -74, 'Map center longitude matches explicit value' );

		jqueryMap.map.remove();
	} );

	QUnit.test( 'getLayerDefinition returns null when options.layerDefinitions is absent', function ( assert ) {
		var $div = $( '<div>' ).css( { width: '400px', height: '300px' } ).appendTo( '#qunit-fixture' );
		var options = makeDefaultOptions();

		var jqueryMap = createTestMap( $div, options );

		assert.strictEqual(
			jqueryMap.getLayerDefinition( 'OpenStreetMap' ),
			null,
			'Absent layerDefinitions yields null without error'
		);

		jqueryMap.map.remove();
	} );

	QUnit.test( 'getLayerDefinition returns the definition only for a matching name', function ( assert ) {
		var $div = $( '<div>' ).css( { width: '400px', height: '300px' } ).appendTo( '#qunit-fixture' );
		var options = makeDefaultOptions( {
			layerDefinitions: {
				Historic: { url: 'https://tiles.example/{z}/{x}/{y}.png', options: {}, wms: false }
			}
		} );

		var jqueryMap = createTestMap( $div, options );

		assert.strictEqual( jqueryMap.getLayerDefinition( 'OpenStreetMap' ), null, 'Unknown name yields null' );
		assert.strictEqual(
			jqueryMap.getLayerDefinition( 'Historic' ).url,
			'https://tiles.example/{z}/{x}/{y}.png',
			'Known name yields its definition'
		);

		jqueryMap.map.remove();
	} );

	QUnit.test( 'newBaseLayerFromName builds a tile layer from a custom definition', function ( assert ) {
		var $div = $( '<div>' ).css( { width: '400px', height: '300px' } ).appendTo( '#qunit-fixture' );
		var options = makeDefaultOptions( {
			layers: [ 'Historic' ],
			layerDefinitions: {
				Historic: {
					url: 'https://tiles.example/{z}/{x}/{y}.png',
					options: { attribution: 'Example', maxZoom: 18 },
					wms: false
				}
			}
		} );

		var jqueryMap = createTestMap( $div, options );

		var capturedUrl = null;
		var capturedOptions = null;
		var sentinel = {};
		var originalTileLayer = L.tileLayer;
		L.tileLayer = function ( url, opts ) {
			capturedUrl = url;
			capturedOptions = opts;
			return sentinel;
		};
		L.tileLayer.provider = originalTileLayer.provider;
		L.tileLayer.wms = originalTileLayer.wms;

		var layer;
		try {
			layer = jqueryMap.newBaseLayerFromName( 'Historic' );
		} finally {
			L.tileLayer = originalTileLayer;
			jqueryMap.map.remove();
		}

		assert.strictEqual( layer, sentinel, 'Returns the layer built from the definition' );
		assert.strictEqual( capturedUrl, 'https://tiles.example/{z}/{x}/{y}.png', 'Uses the definition url' );
		assert.deepEqual(
			capturedOptions,
			{ attribution: 'Example', maxZoom: 18 },
			'Passes the definition options through to Leaflet'
		);
	} );

	QUnit.test( 'newBaseLayerFromName uses L.tileLayer.wms for a WMS definition', function ( assert ) {
		var $div = $( '<div>' ).css( { width: '400px', height: '300px' } ).appendTo( '#qunit-fixture' );
		var options = makeDefaultOptions( {
			layers: [ 'Weather' ],
			layerDefinitions: {
				Weather: {
					url: 'https://example/wms',
					options: { layers: 'hist1904', transparent: true },
					wms: true
				}
			}
		} );

		var jqueryMap = createTestMap( $div, options );

		var capturedUrl = null;
		var capturedOptions = null;
		var sentinel = {};
		var originalWms = L.tileLayer.wms;
		L.tileLayer.wms = function ( url, opts ) {
			capturedUrl = url;
			capturedOptions = opts;
			return sentinel;
		};

		var layer;
		try {
			layer = jqueryMap.newBaseLayerFromName( 'Weather' );
		} finally {
			L.tileLayer.wms = originalWms;
			jqueryMap.map.remove();
		}

		assert.strictEqual( layer, sentinel, 'Returns the WMS layer built from the definition' );
		assert.strictEqual( capturedUrl, 'https://example/wms', 'Uses the definition url' );
		assert.deepEqual(
			capturedOptions,
			{ layers: 'hist1904', transparent: true },
			'Passes the WMS options through to Leaflet'
		);
	} );

	QUnit.test( 'newBaseLayerFromName falls back to the provider catalog without a matching definition', function ( assert ) {
		var $div = $( '<div>' ).css( { width: '400px', height: '300px' } ).appendTo( '#qunit-fixture' );
		var options = makeDefaultOptions( {
			layers: [ 'OpenStreetMap' ],
			layerDefinitions: {
				Historic: { url: 'https://tiles.example/{z}/{x}/{y}.png', options: {}, wms: false }
			}
		} );

		var jqueryMap = createTestMap( $div, options );

		var capturedName = null;
		var sentinel = { addTo: function () { return this; } };
		var originalProvider = L.tileLayer.provider;
		L.tileLayer.provider = function ( name ) {
			capturedName = name;
			return sentinel;
		};

		try {
			jqueryMap.newBaseLayerFromName( 'OpenStreetMap' );
		} finally {
			L.tileLayer.provider = originalProvider;
			jqueryMap.map.remove();
		}

		assert.strictEqual( capturedName, 'OpenStreetMap', 'Names without a definition use the provider catalog' );
	} );

	QUnit.test( 'addOverlays builds a custom overlay from its definition', function ( assert ) {
		var $div = $( '<div>' ).css( { width: '400px', height: '300px' } ).appendTo( '#qunit-fixture' );
		var options = makeDefaultOptions( {
			overlays: [ 'HistoricOverlay' ],
			layerDefinitions: {
				HistoricOverlay: { url: 'https://tiles.example/{z}/{x}/{y}.png', options: {}, wms: false }
			}
		} );

		var jqueryMap = createTestMap( $div, options );

		var capturedUrl = null;
		var sentinel = { addTo: function () { return this; } };
		var originalTileLayer = L.tileLayer;
		L.tileLayer = function ( url ) {
			capturedUrl = url;
			return sentinel;
		};
		L.tileLayer.provider = originalTileLayer.provider;
		L.tileLayer.wms = originalTileLayer.wms;

		var overlays;
		try {
			overlays = jqueryMap.addOverlays();
		} finally {
			L.tileLayer = originalTileLayer;
			jqueryMap.map.remove();
		}

		assert.strictEqual(
			capturedUrl,
			'https://tiles.example/{z}/{x}/{y}.png',
			'Custom overlay is built from its definition url'
		);
		assert.true(
			Object.prototype.hasOwnProperty.call( overlays, 'HistoricOverlay' ),
			'Overlay is keyed by its name'
		);
	} );

}( window.jQuery, window.mediaWiki ) );
