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

	function initializeWithEditor(map) {
		let editor = maps.leaflet.LeafletEditor(
			map,
			new maps.MapSaver(mw.config.get('wgPageName'))
		);

		editor.onSaved(function() {
			alert(mw.msg('maps-json-editor-changes-saved'));
		});

		editor.initialize(window.GeoJson);

		fitContent(map, editor.getLayer());
	}

	function initializePlainMap(map) {
		fitContent(
			map,
			maps.leaflet.GeoJson.newGeoJsonLayer(L, window.GeoJson).addTo(map)
		);
	}

	function addEditButton(map) {
		maps.api.canEditPage(mw.config.get('wgPageName')).done(
			function(canEdit) {
				if (canEdit) {
					let editButton = L.easyButton(
						'<span style="font-size: 15px;">&#9998;</span>',
						function() {
							editButton.remove();
							map.eachLayer(function(layer) {
								if (layer instanceof L.GeoJSON) {
									map.removeLayer(layer);
								}
							});
							initializeWithEditor(map);
						},
						mw.msg('maps-json-editor-toolbar-button-edit')
					);
					editButton.addTo(map);
				}
			}
		);
	}

	function initializeGeoJsonAndEditorUi(map) {
		initializePlainMap(map);

		if (mw.config.get('wgCurRevisionId') === mw.config.get('wgRevisionId')) {
			addEditButton(map);
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
		initializeGeoJsonAndEditorUi(map);
	} );

})( window.jQuery, window.mediaWiki, window.maps );
