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

}( window.jQuery, window.mediaWiki ) );
