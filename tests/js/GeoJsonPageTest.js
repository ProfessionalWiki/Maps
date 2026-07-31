( function ( $ ) {
	'use strict';

	function mapElementWith( geoJson ) {
		return $( '<div>' ).attr( 'data-mw-maps-geojson', JSON.stringify( geoJson ) );
	}

	function pointNamed( name ) {
		return {
			type: 'FeatureCollection',
			features: [ {
				type: 'Feature',
				properties: { title: name },
				geometry: { type: 'Point', coordinates: [ 4.35, 50.85 ] }
			} ]
		};
	}

	QUnit.module( 'Maps.geoJsonPage' );

	QUnit.test( 'GeoJSON is read from the map element', function ( assert ) {
		var geoJson = pointNamed( 'Brussels' );

		assert.deepEqual( maps.geoJsonPage.getGeoJson( mapElementWith( geoJson ) ), geoJson );
	} );

	QUnit.test( 'ampersands and angle brackets in values are read unchanged', function ( assert ) {
		var geoJson = pointNamed( 'Tea & Coffee <b>Ltd</b>' );

		assert.deepEqual( maps.geoJsonPage.getGeoJson( mapElementWith( geoJson ) ), geoJson );
	} );

	QUnit.module( 'Maps.geoJsonPage page initialization' );

	QUnit.test( 'content without a GeoJson map is left untouched', function ( assert ) {
		var $content = $( '<div><p>No map here</p></div>' );

		maps.geoJsonPage.initializePage( $content );

		assert.strictEqual( $content.find( '.leaflet-container' ).length, 0 );
	} );

	QUnit.module( 'Maps.geoJsonPage with cached inline script markup', {
		beforeEach: function () {
			this.originalGeoJson = window.GeoJson;
		},
		afterEach: function () {
			window.GeoJson = this.originalGeoJson;
		}
	} );

	QUnit.test( 'GeoJSON falls back to the global when the map element has no data attribute', function ( assert ) {
		window.GeoJson = pointNamed( 'Brussels' );

		assert.deepEqual( maps.geoJsonPage.getGeoJson( $( '<div>' ) ), window.GeoJson );
	} );

}( window.jQuery ) );
