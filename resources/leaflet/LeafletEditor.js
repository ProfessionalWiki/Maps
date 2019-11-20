(function( $, mw ) {

	function initializeMessages() {
		let buttons = L.drawLocal.draw.toolbar.buttons;

		buttons.marker = mw.msg('maps-json-editor-button-marker');
		buttons.polyline = mw.msg('maps-json-editor-button-line');
		buttons.polygon = mw.msg('maps-json-editor-button-polygon');
		buttons.rectangle = mw.msg('maps-json-editor-button-rectangle');
		buttons.circle = mw.msg('maps-json-editor-button-circle');

		let handlers = L.drawLocal.draw.handlers;

		handlers.marker.tooltip.start = mw.msg('maps-json-editor-tooltip-marker');
		handlers.polyline.tooltip.start = mw.msg('maps-json-editor-tooltip-line');
		handlers.polygon.tooltip.start = mw.msg('maps-json-editor-tooltip-polygon');
		handlers.rectangle.tooltip.start = mw.msg('maps-json-editor-tooltip-rectangle');
		handlers.circle.tooltip.start = mw.msg('maps-json-editor-tooltip-circle');

		let toolbar = L.drawLocal.edit.toolbar;

		toolbar.actions.save.title = mw.msg('maps-json-editor-toolbar-save-title');
		toolbar.actions.save.text = mw.msg('maps-json-editor-toolbar-save-text');
		toolbar.actions.cancel.title = mw.msg('maps-json-editor-toolbar-cancel-title');
		toolbar.actions.cancel.text = mw.msg('maps-json-editor-toolbar-cancel-text');
		toolbar.actions.clearAll.title = mw.msg('maps-json-editor-toolbar-clear-title');
		toolbar.actions.clearAll.text = mw.msg('maps-json-editor-toolbar-clear-text');

		toolbar.buttons.edit = mw.msg('maps-json-editor-toolbar-button-edit');
		toolbar.buttons.editDisabled = mw.msg('maps-json-editor-toolbar-button-edit-disabled');
		toolbar.buttons.remove = mw.msg('maps-json-editor-toolbar-button-remove');
		toolbar.buttons.removeDisabled = mw.msg('maps-json-editor-toolbar-button-remove-disabled');
	}

	function getUserHasPermission(permission, callback) {
		mw.user.getRights(
			function(rights) {
				callback(rights.includes(permission))
			}
		);
	}

	function ifUserHasPermission(permission, callback) {
		getUserHasPermission(
			permission,
			function(hasPermission) {
				if (hasPermission) {
					callback();
				}
			}
		);
	}

	let MapSaver = function() {
		let self = {};

		self.save = function(newContent, summary) {
			new mw.Api().edit(
				mw.config.get('wgPageName'),
				function(revision) {
					let editApiParameters = {
						text: newContent,
						summary: summary,
						minor: false
					};

					ifUserHasPermission(
						"applychangetags",
						function() {
							editApiParameters.tags = ['maps-visual-edit'];
						}
					);

					return editApiParameters;
				}
			).then(
				function(response) {
					if (response.result !== 'Success') {
						console.log(response);
						alert(mw.msg('maps-json-editor-edit-failed'));
					}
				}
			);
		};

		return self;
	};

	let MapEditor = function(mapId, json) {
		let self = {};

		self.initialize = function() {
			self.map = L.map(
				mapId,
				{
					fullscreenControl: true,
					fullscreenControlOptions: {position: 'topright'},
					zoomControl: false
				}
			);

			self.hideLoadingMessage();

			self.map.addControl(new L.Control.Zoom());

			self.geoJsonLayer = L.geoJSON(
				json,
				{
					style: function (feature) {
						let pathOptions = {};

						// https://github.com/Leaflet/Leaflet/blob/f8e09f993292579a1af88261c9b461730f22e4e6/src/layer/GeoJSON.js#L49-L57
						// https://github.com/mapbox/simplestyle-spec/tree/master/1.1.0
						if (feature.properties.fill) {
							pathOptions.fillColor = feature.properties.fill;
						}

						return pathOptions;
					},
					onEachFeature: function (feature, layer) {
						if (feature.properties.title) {
							layer.bindPopup(feature.properties.title);
						}
					}
				}
			);

			self.geoJsonLayer.addTo(self.map);

			self.addTitleLayer();

			self.fitBounds();

			self.addEditUi();

			self.map.on(
				L.Draw.Event.EDITED,
				self.saveJson
			);

			self.map.on(
				L.Draw.Event.DELETED,
				self.saveJson
			);

			// window.geoJsonLayer = self.geoJsonLayer;
			// window.map = self.map;
		};

		self.hideLoadingMessage = function() {
			self.map.on(
				'load',
				function() {
					$('#' + mapId).find('div.maps-loading-message').hide();
				}
			);
		};

		self.saveJson = function(event) {
			new MapSaver().save(
				JSON.stringify(self.geoJsonLayer.toGeoJSON()),
				self.summaryFromEvent(event)
			)
		};

		self.summaryFromEvent = function(event) {
			if (event.type === L.Draw.Event.CREATED) {
				return mw.msg('maps-json-editor-added-' + self.getLayerTypeName(event.layerType));
			}

			if (event.type === L.Draw.Event.DELETED) {
				return mw.message(
					'maps-json-editor-edit-removed-shapes',
					event.layers.getLayers().length
				).text();
			}

			if (event.type === L.Draw.Event.EDITED) {
				return mw.msg('maps-json-editor-edit-modified');
			}

			return mw.msg('maps-json-editor-edit-other');
		};

		self.getLayerTypeName = function(layerType) {
			return {
				'marker': 'marker',
				'polyline': 'line',
				'polygon': 'polygon',
				'rectangle': 'rectangle',
				'circle': 'circle',
			}[layerType];
		};

		self.addTitleLayer = function() {
			L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
			}).addTo(self.map);
		};

		self.fitBounds = function() {
			if (json.features.length === 0) {
				self.map.setView([0, 0], 1);
			}
			else {
				self.map.fitBounds(self.geoJsonLayer.getBounds());
			}
		};

		self.addEditUi = function() {
			if (mw.config.get('wgCurRevisionId') !== mw.config.get('wgRevisionId')) {
				return;
			}

			ifUserHasPermission(
				"edit",
				function() {
					self.addDrawControls();
					self.addNewLayersToJsonLayer();
				}
			);
		};

		self.addDrawControls = function() {
			self.map.addControl(L.control.styleEditor({
				position: "topleft",
				useGrouping: false,
				openOnLeafletDraw: true,
			}));

			self.map.addControl(new L.Control.Draw({
				edit: {
					featureGroup: self.geoJsonLayer,
					poly: {
						allowIntersection: true
					}
				},
				draw: {
					polygon: {
						allowIntersection: true,
						showArea: true
					},
					circlemarker: false, // Do not want this one
					circle: false // Is not showing properly after save
				}
			}));
		};

		self.addNewLayersToJsonLayer = function() {
			self.map.on(L.Draw.Event.CREATED, function (event) {
				self.geoJsonLayer.addLayer(event.layer);
				self.saveJson(event);
			});
		};

		return self;
	};

	$( document ).ready( function() {
		let editor = MapEditor('GeoJsonMap', window.GeoJson);
		editor.initialize();

		initializeMessages();
	} );

})( window.jQuery, mediaWiki );
