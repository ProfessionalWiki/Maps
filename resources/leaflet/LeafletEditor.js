(function( $, mw, maps ) {

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

		function onEditableFeature(feature, layer) {
			let popup = L.popup({minWidth: 250, maxWidth: 9000, closeButton: false});

			let titleTextArea = $('<textarea cols="50" rows="1" />').text(feature.properties.title);
			let descriptionTextArea = $('<textarea cols="50" rows="2" />').text(feature.properties.description);

			// titleTextArea.mouseup(function() {
			// 	popup.update(); titleTextArea.focus();
			// });
			// descriptionTextArea.mouseup(function() {
			// 	popup.update(); descriptionTextArea.focus();
			// });

			titleTextArea.bind('keyup change', function() {
				feature.properties["title"] = titleTextArea.val();
			});

			descriptionTextArea.bind('keyup change', function() {
				feature.properties["description"] = descriptionTextArea.val();
			});

			layer.on("popupopen", function () {
				titleTextArea.focus();
			});

			let div = $('<div />');
			div.append(titleTextArea);
			div.append(descriptionTextArea);
			popup.setContent(div[0]);

			layer.bindPopup(popup);
		}

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

			if (mw.config.get('wgCurRevisionId') === mw.config.get('wgRevisionId')) {
				getUserHasPermission(
					"edit",
					function(hasPermission) {
						if (hasPermission) {
							self.setupWithEditor();
						}
						else {
							self.setupWithPlainMap();
						}
					}
				);
			}
			else {
				self.setupWithPlainMap();
			}
		};

		self.setupWithPlainMap = function() {
			self.geoJsonLayer = maps.GeoJSON.newGeoJsonLayer(L, json);
			self.finishSetup();
		};

		self.setupWithEditor = function() {
			self.geoJsonLayer = L.geoJSON(
				json,
				{
					style: function (feature) {
						return  maps.GeoJSON.simpleStyleToLeafletPathOptions(feature.properties);
					},
					onEachFeature: onEditableFeature
				}
			);

			self.addDrawControls();
			self.addNewLayersToJsonLayer();

			self.map.on(
				L.Draw.Event.EDITED,
				self.saveJson
			);

			self.map.on(
				L.Draw.Event.DELETED,
				self.saveJson
			);

			self.finishSetup();
		};

		self.finishSetup = function() {
			self.geoJsonLayer.addTo(self.map);
			self.addTitleLayer();
			self.fitBounds();
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

		self.addDrawControls = function() {
			 // self.map.addControl(L.control.styleEditor({
				//  position: "topleft",
				//  useGrouping: false,
				//  openOnLeafletDraw: false,
			 // }));

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

})( window.jQuery, window.mediaWiki, window.maps );
