$( document ).ready( function() {
	function getErrorMessageForFailure(failureReason) {
		if (failureReason === 'assertuserfailed') {
			return 'Could not create page because your session expired. The page will be reloaded';
		}

		return 'Failed to create the page: ' + failureReason;
	}

	$('#maps-geojson-new').click(
		function() {
			$(this).prop('disabled', true);
			$(this).text(mw.msg('maps-geo-json-create-page-creating'));

			new mw.Api().create(
				mw.config.get('wgPageName'),
				{
					summary: mw.msg('maps-geo-json-create-page-summary')
				},
				'{"type": "FeatureCollection", "features": []}'
			).then(
				function(editData) {
					if (editData.result !== 'Success') {
						console.log(editData);
						alert('Failed to create the page');
					}

					location.reload();
				}
			).fail(
				function(reason) {
					alert(getErrorMessageForFailure(reason));
					location.reload();
				}
			);
		}
	);
} );
