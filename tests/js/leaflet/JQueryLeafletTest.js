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

}( window.jQuery, window.mediaWiki ) );
