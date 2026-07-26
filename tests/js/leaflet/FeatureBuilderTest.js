( function ( $ ) {
	'use strict';

	var FeatureBuilder = window.maps.leaflet.FeatureBuilder;

	QUnit.module( 'Maps.FeatureBuilder' );

	function newOptions( overrides ) {
		return $.extend( {
			lines: [],
			polygons: [],
			circles: [],
			rectangles: [],
			locations: [],
			geojson: [],
			cluster: false,
			copycoords: false
		}, overrides || {} );
	}

	function newPointFeature( title ) {
		return {
			type: 'Feature',
			geometry: { type: 'Point', coordinates: [ 5, 52 ] },
			properties: { title: title }
		};
	}

	function newFeatureCollection( features ) {
		return { type: 'FeatureCollection', features: features };
	}

	// The number of features rendered by the GeoJSON layer of a map content layer, 0 when there is none.
	function geoJsonFeatureCount( contentLayer ) {
		var count = 0;

		contentLayer.eachLayer( function ( layer ) {
			if ( layer instanceof L.GeoJSON ) {
				count = layer.getLayers().length;
			}
		} );

		return count;
	}

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
		var featureGroup = FeatureBuilder.contentLayerFromOptions( newOptions( {
			locations: [
				{ lat: 52, lon: 5, title: 'Amsterdam', text: '', icon: '' },
				{ lat: 51.9, lon: 4.5, title: 'Rotterdam', text: '', icon: '' }
			]
		} ) );

		assert.true( featureGroup instanceof L.FeatureGroup, 'Returns an L.FeatureGroup' );
		assert.true( featureGroup.markerLayer !== undefined, 'Feature group has a markerLayer property' );
		assert.strictEqual( featureGroup.markerLayer.getLayers().length, 2, 'Marker layer contains 2 markers' );
	} );

	QUnit.test( 'contentLayerFromOptions with GeoJSON Points includes points in GeoJSON layer', function ( assert ) {
		var featureGroup = FeatureBuilder.contentLayerFromOptions( newOptions( {
			geojson: [
				newFeatureCollection( [ newPointFeature( 'Amsterdam' ), newPointFeature( 'Rotterdam' ) ] )
			]
		} ) );

		assert.strictEqual( geoJsonFeatureCount( featureGroup ), 2, 'GeoJSON layer contains both Point features' );
	} );

	QUnit.test( 'contentLayerFromOptions includes the features of every GeoJSON source', function ( assert ) {
		var featureGroup = FeatureBuilder.contentLayerFromOptions( newOptions( {
			geojson: [
				newFeatureCollection( [ newPointFeature( 'Amsterdam' ), newPointFeature( 'Rotterdam' ) ] ),
				newFeatureCollection( [ newPointFeature( 'Ghent' ) ] )
			]
		} ) );

		assert.strictEqual( geoJsonFeatureCount( featureGroup ), 3, 'GeoJSON layer contains the features of both sources' );
	} );

	QUnit.test( 'contentLayerFromOptions adds no GeoJSON layer without sources', function ( assert ) {
		var featureGroup = FeatureBuilder.contentLayerFromOptions( newOptions() );

		var hasGeoJsonLayer = false;
		featureGroup.eachLayer( function ( layer ) {
			hasGeoJsonLayer = hasGeoJsonLayer || layer instanceof L.GeoJSON;
		} );

		assert.false( hasGeoJsonLayer, 'Feature group has no GeoJSON layer' );
	} );

}( window.jQuery ) );
