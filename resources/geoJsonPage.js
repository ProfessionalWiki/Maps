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

		let editor = maps.leaflet.LeafletEditor(
			map,
			window.GeoJson,
			new maps.MapSaver(mw.config.get('wgPageName'))
		);

		editor.initialize();
	} );

})( window.jQuery, window.mediaWiki, window.maps );
