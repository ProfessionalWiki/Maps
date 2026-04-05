( function () {
	'use strict';

	var FeatureBuilder = window.maps.leaflet.FeatureBuilder;

	QUnit.module( 'Maps.FeatureBuilder' );

	QUnit.test( 'createMarker returns a marker at the correct position', function ( assert ) {
		var marker = FeatureBuilder.createMarker(
			{ lat: 52, lon: 5, title: 'Test', text: '', icon: '' },
			{ copycoords: false }
		);

		assert.true( marker instanceof L.Marker, 'Returns an L.Marker instance' );

		var latlng = marker.getLatLng();
		assert.strictEqual( latlng.lat, 52, 'Marker latitude is correct' );
		assert.strictEqual( latlng.lng, 5, 'Marker longitude is correct' );
	} );

	QUnit.test( 'createMarker binds a popup when text is provided', function ( assert ) {
		var marker = FeatureBuilder.createMarker(
			{ lat: 52, lon: 5, title: 'Test', text: 'Hello world', icon: '' },
			{ copycoords: false }
		);

		assert.true( marker.getPopup() !== undefined && marker.getPopup() !== null, 'Marker has a popup bound' );
	} );

	QUnit.test( 'createMarker does not bind a popup when text is empty', function ( assert ) {
		var marker = FeatureBuilder.createMarker(
			{ lat: 52, lon: 5, title: 'Test', text: '', icon: '' },
			{ copycoords: false }
		);

		assert.strictEqual( marker.getPopup(), undefined, 'Marker has no popup' );
	} );

	QUnit.test( 'contentLayerFromOptions returns feature group with markers', function ( assert ) {
		var featureGroup = FeatureBuilder.contentLayerFromOptions( {
			lines: [],
			polygons: [],
			circles: [],
			rectangles: [],
			locations: [
				{ lat: 52, lon: 5, title: 'Amsterdam', text: '', icon: '' },
				{ lat: 51.9, lon: 4.5, title: 'Rotterdam', text: '', icon: '' }
			],
			geojson: '',
			cluster: false,
			copycoords: false
		} );

		assert.true( featureGroup instanceof L.FeatureGroup, 'Returns an L.FeatureGroup' );
		assert.true( featureGroup.markerLayer !== undefined, 'Feature group has a markerLayer property' );
		assert.strictEqual( featureGroup.markerLayer.getLayers().length, 2, 'Marker layer contains 2 markers' );
	} );

}() );
