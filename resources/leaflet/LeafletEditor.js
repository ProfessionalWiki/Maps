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

	let MapEditor = function(map, json, mapSaver) {
		let self = {};

		self.initialize = function() {
			self.map = map;

			if (mw.config.get('wgCurRevisionId') === mw.config.get('wgRevisionId')) {
				getUserHasPermission(
					"edit",
					function(hasPermission) {
						if (hasPermission) {
							self._setupWithEditor();
						}
						else {
							self._setupWithPlainMap();
						}
					}
				);
			}
			else {
				self._setupWithPlainMap();
			}

			initializeMessages();
		};

		self.fitBounds = function () {
			if (json.features.length === 0) {
				self.map.setView([0, 0], 1);
			}
			else {
				self.map.fitBounds(self.geoJsonLayer.getBounds());
			}
		};

		self._setupWithPlainMap = function() {
			self.geoJsonLayer = maps.leaflet.GeoJson.newGeoJsonLayer(L, json);
			self._finishSetup();
		};

		self._setupWithEditor = function() {
			self.geoJsonLayer = L.geoJSON(
				json,
				{
					style: function (feature) {
						return  maps.leaflet.GeoJson.simpleStyleToLeafletPathOptions(feature.properties);
					},
					onEachFeature: self._onEditableFeature
				}
			);

			self._addDrawControls();

			self.map.on(
				L.Draw.Event.CREATED,
				function (event) {
					self.geoJsonLayer.addLayer(event.layer);
					self._showSaveButton();
				}
			);

			self.map.on(
				L.Draw.Event.EDITED,
				self._showSaveButton
			);

			self.map.on(
				L.Draw.Event.DELETED,
				self._showSaveButton
			);

			self._finishSetup();
		};

		self._showSaveButton = function() {
			if (!self.saveButton) {
				self.saveButton = L.easyButton(
					'<img src="' + mw.config.get('egMapsScriptPath') + 'resources/leaflet/images/save-solid.svg">',
					function() {
						let editSummary = prompt(
							'Enter an edit summary for your changes to the map',
							'Visual map edit'
						); // TODO: i18n

						if (editSummary!== null) {
							self.saveButton.remove();
							self.saveButton = null;

							mapSaver.save(
								{
									newContent: JSON.stringify(self.geoJsonLayer.toGeoJSON()),
									summary: editSummary,
									done: function(response) {
										if (response.result === 'Success') {
											alert(mw.msg('maps-json-editor-changes-saved'));
										}
										else {
											console.log(response);
											self._showSaveButton();
											alert(mw.msg('maps-json-editor-edit-failed'));
										}
									}
								}
							);
						}
					},
					mw.msg('maps-json-editor-toolbar-button-save')
				).addTo(self.map);
			}
		};

		self._finishSetup = function() {
			self.geoJsonLayer.addTo(self.map);
			self.fitBounds();

			$(window).bind('beforeunload', function() {
				if (self.saveButton) {
					return 'The map has unsaved changes. Are you sure you want to leave the page?';
				}
			});
		};

		function onSizeChange(element, callback) {
			let currentWidth = element[0].clientWidth;
			let currentHeight = element[0].clientHeight;

			element.bind('mouseup', function() {
				if (element[0].clientWidth !== currentWidth || element[0].clientHeight !== currentHeight) {
					currentWidth = element[0].clientWidth;
					currentHeight = element[0].clientHeight;
					callback(element);
				}
			});
		}

		self._onEditableFeature = function( feature, layer) {
			let titleInput = $('<textarea cols="50" rows="1" />').text(feature.properties.title);
			let descriptionInput = $('<textarea cols="50" rows="2" />').text(feature.properties.description);
			let button = $('<button style="width: 100%">').text(mw.msg('maps-json-editor-toolbar-save-text'));

			layer.on("popupopen", function () {
				let v = titleInput.val();
				titleInput.focus().val('').val(v);
			});

			let popup = L.popup({
				minWidth: 250,
				maxWidth: 9000,
				keepInView: true,
				closeButton: false,
				autoClose: false,
				closeOnEscapeKey: false,
				closeOnClick: false
			});

			let onSizeChangedHandler = function(element) {
				popup.update(); element.focus();
			};

			onSizeChange(titleInput, onSizeChangedHandler);
			onSizeChange(descriptionInput, onSizeChangedHandler);

			button.click(function() {
				popup.remove();

				if (titleInput.val() !== titleInput.text() || descriptionInput.val() !== descriptionInput.text()) {
					feature.properties["title"] = titleInput.val();
					feature.properties["description"] = descriptionInput.val();
					self._showSaveButton();
				}
			});

			popup.setContent($('<div />').append(titleInput, descriptionInput, button)[0]);
			layer.bindPopup(popup);
		};

		self._addDrawControls = function() {
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

		return self;
	};

	if (!maps.leaflet) {maps.leaflet = {};}

	maps.leaflet.LeafletEditor = MapEditor;

})( window.jQuery, window.mediaWiki, window.maps );
