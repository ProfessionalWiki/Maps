( function () {
	QUnit.module( 'Maps' );

	let GeoJSON = window.maps.leaflet.GeoJson;

	QUnit.test( 'GeoJSON.simpleStyleToLeafletPathOptions', function ( assert ) {
		assert.deepEqual(
			GeoJSON.simpleStyleToLeafletPathOptions( {} ),
			{},
			'Empty properties results in empty path'
		);

		let pathOptions = GeoJSON.simpleStyleToLeafletPathOptions( {
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

	QUnit.test( 'GeoJSON.popupContentFromProperties', function ( assert ) {
		assert.equal(
			GeoJSON.popupContentFromProperties({
				title: 'Hello World',
				description: 'pew pew'
			}),
			'<strong>Hello World</strong><br>pew pew',
			'Title and description: title is made bold and description is on a new line'
		);

		assert.equal(
			GeoJSON.popupContentFromProperties({
				title: 'Hello World'
			}),
			'Hello World',
			'Only title'
		);

		assert.equal(
			GeoJSON.popupContentFromProperties({
				description: 'pew pew'
			}),
			'pew pew',
			'Only description'
		);

		assert.equal(
			GeoJSON.popupContentFromProperties({
			}),
			'',
			'No content'
		);

		assert.equal(
			GeoJSON.popupContentFromProperties({
				title: 'Hello <a href="#">link</a>'
			}),
			'Hello &lt;a href=\"#\"&gt;link&lt;/a&gt;',
			'Title escaping'
		);

		assert.equal(
			GeoJSON.popupContentFromProperties({
				description: 'Hello <a href="#">link</a>'
			}),
			'Hello &lt;a href=\"#\"&gt;link&lt;/a&gt;',
			'Description escaping'
		);

		assert.equal(
			GeoJSON.popupContentFromProperties({
				title: 'Hello <a href="#">link</a>',
				description: "<script>alert('evil')</script>"
			}),
			'<strong>Hello &lt;a href=\"#\"&gt;link&lt;/a&gt;</strong><br>&lt;script&gt;alert(\'evil\')&lt;/script&gt;',
			'Title and description escaping'
		);
	} );

}() );
