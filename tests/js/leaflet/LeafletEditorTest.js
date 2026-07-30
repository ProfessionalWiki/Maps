( function () {
	'use strict';

	QUnit.module( 'Maps.LeafletEditor', {
		beforeEach: function () {
			this.$container = $( '<div>' ).css( { width: '400px', height: '300px' } ).appendTo( '#qunit-fixture' );
			this.map = L.map( this.$container[ 0 ], { center: [ 52, 5 ], zoom: 10 } );
			this.mapSaver = new window.maps.MapSaver( 'TestPage' );
		},
		afterEach: function () {
			this.map.remove();
		}
	} );

	QUnit.test( 'remove() does not crash when no edits were made', function ( assert ) {
		var editor = window.maps.leaflet.LeafletEditor(
			this.map,
			this.mapSaver
		);

		editor.initialize( { type: 'FeatureCollection', features: [] } );

		var layerCountBefore = 0;
		this.map.eachLayer( function () { layerCountBefore++; } );

		editor.remove();

		var layerCountAfter = 0;
		this.map.eachLayer( function () { layerCountAfter++; } );

		assert.true( layerCountAfter < layerCountBefore, 'GeoJSON layer was removed from the map' );
	} );

	QUnit.test( 'remove() cleans up after edits were made', function ( assert ) {
		var editor = window.maps.leaflet.LeafletEditor(
			this.map,
			this.mapSaver
		);

		editor.initialize( { type: 'FeatureCollection', features: [] } );

		// Simulate a created feature to trigger _showSaveButton
		this.map.fire( L.Draw.Event.CREATED, {
			layer: L.marker( [ 52, 5 ] )
		} );

		var layerCountBefore = 0;
		this.map.eachLayer( function () { layerCountBefore++; } );

		editor.remove();

		var layerCountAfter = 0;
		this.map.eachLayer( function () { layerCountAfter++; } );

		assert.true( layerCountAfter < layerCountBefore, 'Layers were removed from the map after edits' );
	} );

}() );
