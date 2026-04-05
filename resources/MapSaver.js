(function(mw) {
	'use strict';

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

	let MapSaver = function(pageName) {
		let self = {};

		// parameters.newContent: required string
		// parameters.summary: required string
		// parameters.done: required callback function
		self.save = function(paremeters) {
			getUserHasPermission(
				"applychangetags",
				function(canApplyTags) {
					new mw.Api().edit(
						pageName,
						function(revision) {
							let editApiParameters = {
								text: paremeters.newContent,
								summary: paremeters.summary,
								minor: false
							};

							if (canApplyTags) {
								editApiParameters.tags = ['maps-visual-edit'];
							}

							return editApiParameters;
						}
					).then(paremeters.done, function() {
						alert(mw.msg('maps-json-editor-edit-failed'));
						if (paremeters.onError) {
							paremeters.onError();
						}
					});
				}
			);
		};

		return self;
	};

	if (!window.maps) {window.maps = {};}

	window.maps.MapSaver = MapSaver;
})(window.mediaWiki);
