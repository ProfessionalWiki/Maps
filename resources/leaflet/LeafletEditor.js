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

		// parameters.pageName: required string
		// parameters.newContent: required string
		// parameters.summary: required string
		// parameters.done: required callback function
		self.save = function(paremeters) {
			new mw.Api().edit(
				paremeters.pageName,
				function(revision) {
					let editApiParameters = {
						text: paremeters.newContent,
						summary: paremeters.summary,
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
			).then(paremeters.done);
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
			self.geoJsonLayer = maps.leaflet.GeoJson.newGeoJsonLayer(L, json);
			self.finishSetup();
		};

		self.setupWithEditor = function() {
			self.geoJsonLayer = L.geoJSON(
				json,
				{
					style: function (feature) {
						return  maps.leaflet.GeoJson.simpleStyleToLeafletPathOptions(feature.properties);
					},
					onEachFeature: self.onEditableFeature
				}
			);

			self.addDrawControls();

			self.map.on(
				L.Draw.Event.CREATED,
				function (event) {
					self.geoJsonLayer.addLayer(event.layer);
					self.showSaveButton();
				}
			);

			self.map.on(
				L.Draw.Event.EDITED,
				self.showSaveButton
			);

			self.map.on(
				L.Draw.Event.DELETED,
				self.showSaveButton
			);

			self.finishSetup();
		};

		self.showSaveButton = function() {
			if (!self.saveButton) {
				self.saveButton = L.easyButton(
					'<img src="' + mw.config.get('egMapsScriptPath') + 'resources/leaflet/save-solid.svg">',
					function() {
						let editSummary = prompt(
							'Enter an edit summary for your changes to the map',
							'Visual map edit'
						); // TODO: i18n

						if (editSummary!== null) {
							self.saveButton.remove();
							self.saveButton = null;

							new MapSaver().save(
								{
									pageName: mw.config.get('wgPageName'),
									newContent: JSON.stringify(self.geoJsonLayer.toGeoJSON()),
									summary: editSummary,
									done: function(response) {
										if (response.result === 'Success') {
											alert(mw.msg('maps-json-editor-changes-saved'));
										}
										else {
											console.log(response);
											self.showSaveButton();
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

		self.finishSetup = function() {
			self.geoJsonLayer.addTo(self.map);
			self.addTitleLayer();
			self.fitBounds();

			$(window).bind('beforeunload', function() {
				if (self.saveButton) {
					return 'The map has unsaved changes. Are you sure you want to leave the page?';
				}
			});
		};

		self.hideLoadingMessage = function() {
			self.map.on(
				'load',
				function() {
					$('#' + mapId).find('div.maps-loading-message').hide();
				}
			);
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

		self.onEditableFeature = function(feature, layer) {
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
					self.showSaveButton();
				}
			});

			popup.setContent($('<div />').append(titleInput, descriptionInput, button)[0]);
			layer.bindPopup(popup);
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

		return self;
	};

	$( document ).ready( function() {
		let editor = MapEditor('GeoJsonMap', window.GeoJson);
		editor.initialize();

		initializeMessages();
	} );

})( window.jQuery, window.mediaWiki, window.maps );
