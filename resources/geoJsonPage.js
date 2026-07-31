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

	// Pages rendered before the GeoJSON moved into the data attribute are still served from
	// the parser cache, with the JSON in an inline script that assigns window.GeoJson.
	function getGeoJson($mapElement) {
		return $mapElement.data('mw-maps-geojson') || window.GeoJson;
	}

	function initializePage($content) {
		let $mapElement = $content.find('#GeoJsonMap');

		if ($mapElement.length === 0) {
			return;
		}

		let map = L.map(
			$mapElement.get(0),
			{
				fullscreenControl: true,
				fullscreenControlOptions: {position: 'topright'},
				zoomControl: false
			}
		);

		hideLoadingMessage(map, $content);
		addZoomControl(map);
		addTitleLayer(map);
		initializeGeoJsonAndEditorUi(map, getGeoJson($mapElement));
	}

	mw.hook( 'wikipage.content' ).add( initializePage );

	maps.geoJsonPage = {
		getGeoJson: getGeoJson,
		initializePage: initializePage
	};

})( window.jQuery, window.mediaWiki, window.maps );
