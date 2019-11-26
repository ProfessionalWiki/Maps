(function( $, mw, maps ) {

	function hideLoadingMessage(map, $content) {
		map.on(
			'load',
			function() {
				$content.find('div.maps-loading-message').hide();
			}
		);
	}

	function addZoomControl(map) {
		map.addControl(new L.Control.Zoom());
	}

	function addTitleLayer(map) {
		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
		}).addTo(map);
	}

	function getUserHasPermission(permission, callback) {
		mw.user.getRights(
			function(rights) {
				callback(rights.includes(permission))
			}
		);
	}

	function fitContent(map, geoJsonLayer) {
		map.fitWorld();
		let bounds = geoJsonLayer.getBounds();

		if (bounds.isValid()) {
			if (bounds.getNorthEast().equals(bounds.getSouthWest())) {
				map.setView(
					bounds.getCenter(),
					14
				);
			}
			else {
				map.fitBounds(bounds);
			}
		}
	}

	mw.hook( 'wikipage.content' ).add( function ( $content ) {
		let map = L.map(
			'GeoJsonMap',
			{
				fullscreenControl: true,
				fullscreenControlOptions: {position: 'topright'},
				zoomControl: false
			}
		);

		hideLoadingMessage(map, $content);
		addZoomControl(map);
		addTitleLayer(map);

		if (mw.config.get('wgCurRevisionId') === mw.config.get('wgRevisionId')) {
			getUserHasPermission(
				"edit",
				function(hasPermission) {
					if (hasPermission) {
						let editor = maps.leaflet.LeafletEditor(
							map,
							window.GeoJson,
							new maps.MapSaver(mw.config.get('wgPageName'))
						);

						editor.initialize();
						console.log(editor);
						fitContent(map, editor.getLayer());
					}
					else {
						fitContent(
							map,
							maps.leaflet.GeoJson.newGeoJsonLayer(L, window.GeoJson).addTo(map)
						);
					}
				}
			);
		}
		else {
			fitContent(
				map,
				maps.leaflet.GeoJson.newGeoJsonLayer(L, window.GeoJson).addTo(map)
			);
		}
	} );

})( window.jQuery, window.mediaWiki, window.maps );
