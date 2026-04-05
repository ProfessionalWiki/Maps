( function () {
	'use strict';

	QUnit.module( 'Maps.MapSaver' );

	QUnit.test( 'MapSaver constructor returns an object with a save method', function ( assert ) {
		var saver = window.maps.MapSaver( 'TestPage' );

		assert.strictEqual( typeof saver, 'object', 'MapSaver returns an object' );
		assert.strictEqual( typeof saver.save, 'function', 'Object has a save method' );
	} );

}() );
