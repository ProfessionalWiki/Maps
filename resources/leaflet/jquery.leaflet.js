/**
 * JavaScript for Leaflet in the Maps extension.
 * @see https://www.mediawiki.org/wiki/Extension:Maps
 *
 * @author Pavel Astakhov < pastakhov@yandex.ru >
 * @author Peter Grassberger < petertheone@gmail.com >
 * @author Jeroen De Dauw
 */
(function ($, mw, L, maps, sm) {
	/**
	 * Creates a new marker with the provided data and returns it.
	 * @param {Object} properties Contains the fields lat, lon, title, text and icon
	 * @param {Object} options Map options
	 * @return {L.Marker}
	 */
	function createMarker(properties, options) {
		let markerOptions = {
			title:properties.title
		};

		let marker = L.marker( [properties.lat, properties.lon], markerOptions );

		if (properties.hasOwnProperty('icon') && properties.icon !== '') {
			marker.setOpacity(0);

			let img = new Image();
			img.onload = function() {
				let icon = new L.Icon({
					iconUrl: properties.icon,
					iconSize: [ img.width, img.height ],
					iconAnchor: [ img.width / 2, img.height ],
					popupAnchor: [ -img.width % 2, -img.height*2/3 ]
				});

				marker.setIcon(icon);
				marker.setOpacity(1);
			};
			img.src = properties.icon;
		}

		if( properties.hasOwnProperty('text') && properties.text.length > 0 ) {
			marker.bindPopup( properties.text );
		}

		if ( options.copycoords ) {
			marker.on(
				'contextmenu',
				function( e ) {
					prompt(mw.msg('maps-copycoords-prompt'), e.latlng.lat + ',' + e.latlng.lng);
				}
			);
		}

		return marker;
	}

	function getMapOptions(options) {
		let mapOptions = {};
		if (options.minzoom !== false) mapOptions.minZoom = options.minzoom;
		if (options.maxzoom !== false) mapOptions.maxZoom = options.maxzoom;

		if (options.fullscreen) {
			mapOptions.fullscreenControl = true;
			mapOptions.fullscreenControlOptions= {
				position: 'topleft'
			};
		}

		mapOptions.scrollWheelZoom = options.scrollwheelzoom;

		if (options.static) {
			mapOptions.scrollWheelZoom = false;
			mapOptions.doubleClickZoom = false;
			mapOptions.touchZoom = false;
			mapOptions.boxZoom = false;
			mapOptions.tap = false;
			mapOptions.keyboard = false;
			mapOptions.zoomControl = false;
			mapOptions.dragging = false;
		}

		return mapOptions;
	}

	function newLineFromProperties(properties) {
		var latlngs = [];

		for (var x = 0; x < properties.pos.length; x++) {
			latlngs.push([properties.pos[x].lat, properties.pos[x].lon]);
		}

		let line = L.polyline(
			latlngs,
			{
				color: properties.strokeColor,
				weight: properties.strokeWeight,
				opacity: properties.strokeOpacity
			}
		);

		// TODO: maybe bind via feature group
		if ( properties.hasOwnProperty('text') && properties.text.trim().length > 0 ) {
			line.bindPopup( properties.text );
		}

		return line;
	}

	function newPolygonFromProperties(properties) {
		let polygon = L.polygon(
			properties.pos.map(function(position) {
				return [position.lat, position.lon];
			}),
			{
				color: properties.strokeColor,
				weight:properties.strokeWeight,
				opacity:properties.strokeOpacity,
				fillColor:properties.fillColor,
				fillOpacity:properties.fillOpacity
			}
		);

		if( properties.hasOwnProperty('text') && properties.text.trim().length > 0 ) {
			polygon.bindPopup( properties.text );
		}

		return polygon;
	}

	function newCircleFromProperties(properties) {
		let circle = L.circle(
			[properties.centre.lat, properties.centre.lon],
			{
				radius: properties.radius,
				color: properties.strokeColor,
				weight:properties.strokeWeight,
				opacity:properties.strokeOpacity,
				fillColor:properties.fillColor,
				fillOpacity:properties.fillOpacity,
			}
		);

		if( properties.hasOwnProperty('text') && properties.text.trim().length > 0 ) {
			circle.bindPopup( properties.text );
		}

		return circle;
	}

	function newRectangleFromProperties(properties) {
		let rectangle = L.rectangle(
			[
				[properties.sw.lat, properties.sw.lon],
				[properties.ne.lat, properties.ne.lon]
			],
			{
				color: properties.strokeColor,
				weight: properties.strokeWeight,
				opacity: properties.strokeOpacity,
				fillColor: properties.fillColor,
				fillOpacity: properties.fillOpacity
			}
		);

		if( properties.hasOwnProperty('text') && properties.text.trim().length > 0 ) {
			rectangle.bindPopup( properties.text );
		}

		return rectangle;
	}

	/**
	 * Caution: mutates markerLayer
	 * @param {Object} options
	 * @param {L.LayerGroup} markerLayer
	 * @return {L.GeoJSON}
	 */
	function newGeoJsonLayer(options, markerLayer) {
		return L.geoJSON(
			options.geojson,
			{
				style: function (feature) {
					return maps.leaflet.GeoJson.simpleStyleToLeafletPathOptions(feature.properties);
				},
				pointToLayer: function(feature, latlng) {
					markerLayer.addLayer(
						createMarker(
							{
								lat: latlng.lat,
								lon: latlng.lng,
								title: feature.properties.title || '',
								text: maps.leaflet.GeoJson.popupContentFromProperties(feature.properties),
								icon: ''
							},
							options
						)
					);
				},
				onEachFeature: function (feature, layer) {
					if (feature.geometry.type !== 'Point') {
						let popupContent = maps.leaflet.GeoJson.popupContentFromProperties(feature.properties);
						if (popupContent !== '') {
							layer.bindPopup(popupContent);
						}
					}
				}
			}
		);
	}

	function getMarkersAndShapes(options) {
		let features = L.featureGroup();

		$.each(options.lines, function(index, properties) {
			features.addLayer(newLineFromProperties(properties));
		});

		$.each(options.polygons, function(index, properties) {
			features.addLayer(newPolygonFromProperties(properties));
		});

		$.each(options.circles, function(index, properties) {
			features.addLayer(newCircleFromProperties(properties));
		});

		$.each(options.rectangles, function(index, properties) {
			features.addLayer(newRectangleFromProperties(properties));
		});

		let markers = options.cluster ? maps.leaflet.LeafletCluster.newLayer(options) : L.featureGroup();

		features.addLayer(markers);
		features.markerLayer = markers;

		$.each(options.locations, function(index, properties) {
			markers.addLayer(createMarker(properties, options));
		});

		if (options.geojson !== '') {
			features.addLayer(newGeoJsonLayer(options, markers));
		}

		return features
	}






	$.fn.leafletmaps = function ( options ) {
		let _this = this;
		_this.options = options; // needed for LeafletAjax.js

		this.setup = function() {
			this.map = L.map( this.get(0), getMapOptions(options) );
			this.mapContent = getMarkersAndShapes(options).addTo(this.map);

			this.hideLoadingMessage();
			this.addLayersAndOverlays(this.map);
			this.centerAndZoomMap();
			this.bindClickTarget();
			this.applyResizable();

			this.maybeAddEditButton();

			let ajaxRequest = null;

			if ( options.ajaxquery && options.ajaxcoordproperty ) {
				this.map.on( 'dragend zoomend', function() {
					let bounds = _this.map.getBounds();

					let query = sm.buildQueryString(
						decodeURIComponent( options.ajaxquery.replace( /\+/g, ' ' ) ),
						options.ajaxcoordproperty,
						bounds.getNorthEast().lat,
						bounds.getNorthEast().lng,
						bounds.getSouthWest().lat,
						bounds.getSouthWest().lng
					);

					if( ajaxRequest !== null ) {
						ajaxRequest.abort();
					}

					ajaxRequest = sm.ajaxUpdateMarker( _this, query, options.icon ).done( function() {
						ajaxRequest = null;
					} );
				} );
			}
		};

		this.maybeAddEditButton = function() {
			if ( options.geojson === '' || options.GeoJsonSource === null ) {
				return;
			}

			if (mw.config.get('wgCurRevisionId') !== mw.config.get('wgRevisionId')) {
				return;
			}

			mw.user.getRights(
				function(rights) {
					if (rights.includes('edit')) {
						_this.addEditButton();
					}
				}
			);
		};

		this.addEditButton = function() {
			// TODO: page creation right checks
			// TODO: specific page edit right check

			this.editButton = L.easyButton(
				'<img src="' + mw.config.get('egMapsScriptPath') + 'resources/leaflet/images/edit-solid.svg">',
				function() {
					_this.removeEditButton();
					_this.mapContent.remove();

					let editor = _this.getEditor();
					editor.initialize(options.geojson);

					// TODO: edit conflict / old revision detection

					editor.onSaved(function() {
						_this.purgePage();

						editor.remove();
						options.geojson = editor.getLayer().toGeoJSON();
						_this.mapContent = getMarkersAndShapes(options).addTo(_this.map);

						alert(mw.msg('maps-json-editor-changes-saved'));
						_this.addEditButton();
					});
				},
				mw.msg('maps-editor-edit-geojson')
			).addTo(this.map);
		};

		this.getEditor = function() {
			if (!this.editor) {
				this.editor = maps.leaflet.LeafletEditor(
					_this.map,
					new maps.MapSaver('GeoJson:' + options.GeoJsonSource)
				);
			}

			return this.editor;
		};

		this.purgePage = function() {
			new mw.Api().post({
				action: 'purge',
				titles: mw.config.get( 'wgPageName' )
			}).then(function(response) {

			});
		};

		this.removeEditButton = function() {
			if (this.editButton) {
				this.editButton.remove();
				this.editButton = null;
			}
		};

		this.hideLoadingMessage = function() {
			this.map.on(
				'load',
				function() {
					$(_this).find('div.maps-loading-message').hide();
				}
			);
		};

		this.applyResizable = function() {
			if (options.resizable) {
				_this.resizable();
			}
		};

		// Caution: used by ajaxUpdateMarker
		this.addMarker = function (properties) {
			this.mapContent.markerLayer.addLayer(createMarker(properties, options));
		};

		// Caution: used by ajaxUpdateMarker
		this.removeMarkers = function () {
			this.mapContent.markerLayer.clearLayers();
		};

		this.bindClickTarget = function() {
			function newClickTargetUrl(latlng) {
				return options.clicktarget
					.replace( /%lat%/g, latlng.lat )
					.replace( /%long%/g, latlng.lng );
			}

			if (options.clicktarget !== '') {
				this.map.on(
					'click',
					function(e) {
						window.location.href = newClickTargetUrl(e.latlng);
					}
				);
			}
		};

		this.isUserUsesDarkMode = function () {
			return window.matchMedia( '(prefers-color-scheme: dark)' ).matches;
		};

		this.getLayerNames = function () {
			if ( this.isUserUsesDarkMode() ) {
				return mw.config.get('egMapsLeafletLayersDark');
			}

			return options.layers;
		};

		this.addLayers = function() {
			let apiKeys = mw.config.get('egMapsLeafletLayersApiKeys');
			let layers = {};

			$.each( this.getLayerNames().reverse(), function(index, layerName) {
				var options = {} ;
				var providerName = layerName.split('.')[0] ;
				if (apiKeys.hasOwnProperty(providerName) && apiKeys[providerName] !== '') {
					options.apikey = apiKeys[providerName] ;
				}
				if (layerName === 'MapQuestOpen') {
					layers[layerName] = new window.MQ.TileLayer().addTo(_this.map);
				} else {
					layers[layerName] = new L.tileLayer.provider(layerName,options).addTo(_this.map);
				}
			});

			return layers;
		};

		this.addOverlays = function() {
			let overlays = {};

			$.each(options.overlays, function(index, overlayName) {
				overlays[overlayName] = new L.tileLayer.provider(overlayName).addTo(_this.map);
			});

			return overlays;
		};

		this.addLayersAndOverlays = function() {
			let layers = this.addLayers();
			let overlays = this.addOverlays();

			if (options.layers.length > 1 || options.overlays.length > 0) {
				L.control.layers(layers, overlays).addTo(this.map);
			}
		};

		this.centerAndZoomMap = function() {
			this.map.fitWorld();
			this.fitContent();

			if (options.zoom !== false) {
				this.map.setZoom(options.zoom);
			}

			if (options.centre !== false) {
				this.map.setView(
					new L.LatLng(options.centre.lat, options.centre.lon),
					this.map.getZoom()
				);
			}
		};

		this.fitContent = function() {
			let bounds = this.mapContent.getBounds();

			if (bounds.isValid()) {
				if (bounds.getNorthEast().equals(bounds.getSouthWest())) {
					this.map.setView(
						bounds.getCenter(),
						options.defzoom
					);
				}
				else {
					this.map.fitBounds(bounds);
				}
			}
		};

		this.getDependencies = function ( options ) {
			var dependencies = [];

			if (true) { // TODO
				dependencies.push( 'ext.maps.leaflet.editor' );
			}

			if (options.fullscreen) {
				dependencies.push( 'ext.maps.leaflet.fullscreen' );
			}

			if (options.resizable) {
				dependencies.push( 'ext.maps.resizable' );
			}

			if (true) { // TODO: options.cluster
				dependencies.push( 'ext.maps.leaflet.markercluster' );
			}

			return dependencies;
		};

		mw.loader.using( this.getDependencies( options ) ).then( function() {
			_this.setup();
		} );

		return this;

	};
})(window.jQuery, window.mediaWiki, window.L, window.maps,  window.sm);
