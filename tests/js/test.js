( function () {
	QUnit.module( 'Maps' );

	QUnit.test( 'GeoJSON.simpleStyleToLeafletPathOptions', function ( assert ) {
		assert.deepEqual(
			maps.GeoJSON.simpleStyleToLeafletPathOptions( {} ),
			{},
			'Empty properties results in empty path'
		);

		let pathOptions = maps.GeoJSON.simpleStyleToLeafletPathOptions( {
			"stroke": "#a92c2c",
			"stroke-width": 5.1,
			"stroke-opacity": 1,
			"fill": "#ffff00",
			"fill-opacity": 0.5,
			"title": "hi"
		} );

		assert.equal(
			pathOptions.color,
			'#a92c2c',
			'color is set (based on stroke)'
		);

		assert.equal(
			pathOptions.weight,
			5.1,
			'weight is set (based on stroke-width)'
		);

		assert.equal(
			pathOptions.opacity,
			1,
			'opacity is set (based on stroke-opacity)'
		);

		assert.equal(
			pathOptions.fillColor,
			"#ffff00",
			'fillColor is set'
		);

		assert.equal(
			pathOptions.fillOpacity,
			0.5,
			'fillOpacity is set'
		);

	} );


}() );
