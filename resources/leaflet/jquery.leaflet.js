/**
 * JavaScript for Leaflet in the Maps extension.
 * @see https://www.mediawiki.org/wiki/Extension:Maps
 *
 * @author Pavel Astakhov < pastakhov@yandex.ru >
 * @author Peter Grassberger < petertheone@gmail.com >
 * @author Jeroen De Dauw
 */
(function ($, mw, L, maps, sm) {

	function getMapOptions(options) {
		let mapOptions = {};
		if (options.minzoom !== false) mapOptions.minZoom = options.minzoom;
		if (options.maxzoom !== false) mapOptions.maxZoom = options.maxzoom;

		if (options.fullscreen) {
			mapOptions.fullscreenControl = true;
			mapOptions.fullscreenControlOptions= {
				position: 'topright'
			};
		}

		// The control is removed and later re-added because else it ends up with the fullscreen one on the right
		mapOptions.zoomControl = false;

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

		if (options.imageLayers.length !== 0) {
			mapOptions.crs = L.CRS.Simple;
		}

		return mapOptions;
	}

	$.fn.leafletmaps = function ( options ) {
		let _this = this;
		_this.options = options; // needed by LeafletAjax.js

		this.setup = function() {
			this.map = L.map( this.get(0), getMapOptions(options) );
			this.mapContent = maps.leaflet.FeatureBuilder.contentLayerFromOptions(options);

			this.ranSetup = false;

			this.mapContent.on('add', function(e) {
				_this.doSetup();
			});

			this.mapContent.addTo(this.map);

			// For whatever reason the add event does not fire when using marker cluster, hence this fallback.
			setTimeout(function() {
				_this.doSetup();
			},250);
		};

		this.doSetup = function() {
			if (!this.ranSetup) {
				this.ranSetup = true;

				this.hideLoadingMessage();
				this.addZoomControl();
				this.addLayersAndOverlays();
				this.map.fitWorld();
				this.centerAndZoomMap();
				this.bindClickTarget();
				this.applyResizable();
				this.bindAjaxEvents();

				this.maybeAddEditButton();
			}
		};

		this.addZoomControl = function() {
			this.map.addControl(new L.Control.Zoom());
		};

		this.shouldShowEditButton = function() {
			if ( options.geojson === '' || options.GeoJsonSource === null ) {
				return false;
			}

			if (mw.config.get('wgCurRevisionId') !== mw.config.get('wgRevisionId')) {
				return false;
			}

			return true;
		};

		this.maybeAddEditButton = function() {
			if ( this.shouldShowEditButton() ) {
				mw.loader.using( [ 'ext.maps.leaflet.editor' ] ).then( function() {
					maps.api.canEditPage('GeoJson:' + options.GeoJsonSource).done(
						function(canEdit) {
							if (canEdit) {
								_this.addEditButton();
							}
						}
					);
				} );
			}
		};

		this.addEditButton = function() {
			this.editButton = L.easyButton(
				'<img src="' + mw.config.get('egMapsScriptPath') + 'resources/leaflet/images/edit-solid.svg">',
				this.startEditMode,
				mw.msg('maps-editor-edit-geojson')
			).addTo(this.map);
		};

		this.startEditMode = function() {
			_this.removeEditButton();
			_this.mapContent.remove();

			maps.api.getLatestRevision('GeoJson:' + options.GeoJsonSource).done(
				function(revision) {
					if (revision.revid === options.GeoJsonRevisionId) {
						_this.initializeEditor(options.geojson);
					}
					else {
						_this.purgePage();
						_this.initializeEditor(JSON.parse(revision['*']));
					}
				}
			);
		};

		this.initializeEditor = function(geoJson) {
			let editor = _this.getEditor();
			editor.initialize(geoJson);

			editor.onSaved(function() {
				_this.purgePage();

				editor.remove();
				options.geojson = editor.getLayer().toGeoJSON();
				_this.mapContent = maps.leaflet.FeatureBuilder.contentLayerFromOptions(options).addTo(_this.map);

				alert(mw.msg('maps-json-editor-changes-saved'));
				_this.addEditButton();
			});
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
			maps.api.purgePage(mw.config.get( 'wgPageName' ));
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
				mw.loader.using( [ 'ext.maps.resizable' ] ).then( function() {
					_this.resizable();
				} );
			}
		};

		// Caution: used by ajaxUpdateMarker
		this.addMarker = function (properties) {
			this.mapContent.markerLayer.addLayer(maps.leaflet.FeatureBuilder.createMarker(properties, options));
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
						_this.redirectToUrl( newClickTargetUrl( e.latlng ) );
					}
				);
			}
		};

		this.redirectToUrl = function( url ) {
			try {
				let urlObject = new URL( url );

				if ( urlObject.protocol === "http:" || urlObject.protocol === "https:" ) {
					window.location.href = url;
				}
			} catch (_) {
			}
		}

		this.getBaseLayers = function() {
			if ( options.imageLayers.length === 0 ) {
				return this.getNormalBaseLayers();
			}

			return this.getImageBaseLayers();
		};

		this.getNormalBaseLayers = function() {
			let layers = new Map();

			$.each( options.layers.reverse(), function(index, layerName) {
				layers.set(layerName, _this.newBaseLayerFromName(layerName));
			});

			return layers;
		};

		this.newBaseLayerFromName = function(layerName) {
			if (layerName === 'MapQuestOpen') {
				return new window.MQ.TileLayer();
			}

			let layerOptions = {} ;
			let apiKeys = mw.config.get('egMapsLeafletLayersApiKeys');
			let providerName = layerName.split('.')[0];

			if (apiKeys.hasOwnProperty(providerName) && apiKeys[providerName] !== '') {
				layerOptions.apikey = apiKeys[providerName];
			}

			return new L.tileLayer.provider(layerName, layerOptions);
		};

		this.getImageBaseLayers = function() {
			let layers = new Map();

			$.each( options.imageLayers.reverse(), function(index, layer) {
				layers.set(
					layer.name,
					L.imageOverlay(
						layer.url,
						[[0, 0], [layer.height, layer.width]]
					)
				);
			});

			return layers;
		};

		this.addBaseLayersToMap = function(layers) {
			layers.forEach(function(layerObject, layerName) {
				layerObject.addTo(_this.map);
			});
		};

		this.addOverlays = function() {
			let overlays = {};

			$.each(options.overlays, function(index, overlayName) {
				overlays[overlayName] = new L.tileLayer.provider(overlayName).addTo(_this.map);
			});

			return overlays;
		};

		this.addLayersAndOverlays = function() {
			let layers = this.getBaseLayers();
			this.addBaseLayersToMap(layers);

			let overlays = this.addOverlays();

			if (layers.size > 1 || options.overlays.length > 0) {
				let control = L.control.layers([], overlays).addTo(this.map);
				layers.forEach(function(layerObject, layerName) {
					control.addBaseLayer(layerObject, layerName);
				});
			}
		};

		this.bindAjaxEvents = function() {
			if ( !options.ajaxquery || !options.ajaxcoordproperty ) {
				return;
			}

			let ajaxRequest = null;

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
		};

		this.centerAndZoomMap = function() {
			this.fitContent();

			if (options.zoom !== false) {
				this.map.setZoom(options.zoom, {animate: false});
			}

			if (options.centre !== false) {
				this.map.setView(
					new L.LatLng(options.centre.lat, options.centre.lon),
					this.map.getZoom(),
					{animate: false}
				);
			}
		};

		this.fitContent = function() {
			let bounds = this.mapContent.getBounds();

			$.each( options.imageLayers.reverse(), function(index, layer) {
				bounds.extend(new L.LatLng(0, 0));
				bounds.extend(new L.LatLng(layer.height, layer.width));
			});

			if (bounds.isValid()) {
				this.map.setView(
					bounds.getCenter(),
					this.getZoomForValidBounds(bounds),
					{animate: false}
				);
			}
			else {
				this.map.setZoom(options.defzoom, {animate: false});
			}
		};

		this.getZoomForValidBounds = function(bounds) {
			if (bounds.getNorthEast().equals(bounds.getSouthWest())) {
				return options.defzoom;
			}

			return this.map.getBoundsZoom(bounds, false, [50, 75]);
		};

		return this;
	};
})(window.jQuery, window.mediaWiki, window.L, window.maps, window.sm);
