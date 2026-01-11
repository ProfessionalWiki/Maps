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

	function initializeWithEditor(map, geoJson) {
		let editor = maps.leaflet.LeafletEditor(
			map,
			new maps.MapSaver(mw.config.get('wgPageName'))
		);

		editor.onSaved(function() {
			alert(mw.msg('maps-json-editor-changes-saved'));
		});

		editor.initialize(geoJson);

		fitContent(map, editor.getLayer());
	}

	function initializePlainMap(map, geoJson) {
		fitContent(
			map,
			maps.leaflet.GeoJson.newGeoJsonLayer(L, geoJson).addTo(map)
		);
	}

	function initializeGeoJsonAndEditorUi(map, geoJson) {
		if (mw.config.get('wgCurRevisionId') === mw.config.get('wgRevisionId')) {

			maps.api.canEditPage(mw.config.get('wgPageName')).done(
				function(canEdit) {
					if (canEdit) {
						initializeWithEditor(map, geoJson);
					}
					else {
						initializePlainMap(map, geoJson);
					}
				}
			);
		}
		else {
			initializePlainMap(map, geoJson);
		}
	}

	mw.hook( 'wikipage.content' ).add( function ( $content ) {
		let $mapElement = $content.find('#GeoJsonMap');

		if ($mapElement.length === 0) {
			return;
		}

		let geoJsonData = $mapElement.attr('data-geo-json');

		if (!geoJsonData) {
			return;
		}

		let geoJson;
		try {
			geoJson = JSON.parse(geoJsonData);
		} catch (e) {
			console.error('Failed to parse GeoJSON data:', e);
			return;
		}

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
		initializeGeoJsonAndEditorUi(map, geoJson);
	} );

})( window.jQuery, window.mediaWiki, window.maps );
