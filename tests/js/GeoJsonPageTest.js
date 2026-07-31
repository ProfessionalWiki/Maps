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

	// The attribute as GeoJsonMapPageUi emits it: quotes and newlines entity-encoded,
	// U+0338 as &#x338;, and & < > as JSON \uXXXX escapes.
	QUnit.test( 'values in page markup survive entity decoding unchanged', function ( assert ) {
		var $mapElement = $(
			'<div data-mw-maps-geojson="{&#10;' +
			'    &quot;type&quot;: &quot;FeatureCollection&quot;,&#10;' +
			'    &quot;features&quot;: [ {&#10;' +
			'        &quot;type&quot;: &quot;Feature&quot;,&#10;' +
			'        &quot;properties&quot;: { &quot;title&quot;: &quot;Tea \\u0026 Coffee \\u003Cb\\u003ELtd, open =&#x338; closed&quot; },&#10;' +
			'        &quot;geometry&quot;: { &quot;type&quot;: &quot;Point&quot;, &quot;coordinates&quot;: [ 4.35, 50.85 ] }&#10;' +
			'    } ]&#10;}"></div>'
		);

		assert.strictEqual(
			maps.geoJsonPage.getGeoJson( $mapElement ).features[ 0 ].properties.title,
			'Tea & Coffee <b>Ltd, open =\u0338 closed'
		);
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

	QUnit.test( 'the map element wins over the global when both are present', function ( assert ) {
		window.GeoJson = pointNamed( 'Cached global' );

		assert.deepEqual(
			maps.geoJsonPage.getGeoJson( mapElementWith( pointNamed( 'Brussels' ) ) ),
			pointNamed( 'Brussels' )
		);
	} );

}( window.jQuery ) );
