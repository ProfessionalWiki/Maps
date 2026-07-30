( function ( mw ) {
	'use strict';

	var originalGoogle = window.google;
	var infoWindowContents = [];

	// geoxml3 only renders info windows via a Google Maps InfoWindow, so the content it composes
	// is captured here as it is handed over.
	function InfoWindow( options ) {
		infoWindowContents.push( options ? options.content : null );
	}

	function MVCObject() {}

	MVCObject.prototype.get = function ( key ) {
		return this.values_ ? this.values_[ key ] : undefined;
	};

	MVCObject.prototype.set = function ( key, value ) {
		this.values_ = this.values_ || {};
		this.values_[ key ] = value;
	};

	function OverlayView() {}

	OverlayView.prototype = new MVCObject();

	OverlayView.prototype.setMap = function () {};

	OverlayView.prototype.getPanes = function () {
		return { overlayLayer: document.createElement( 'div' ) };
	};

	function LatLng( lat, lng ) {
		this.lat_ = lat;
		this.lng_ = lng;
	}

	LatLng.prototype.toUrlValue = function () {
		return this.lat_ + ',' + this.lng_;
	};

	function LatLngBounds() {}

	LatLngBounds.prototype.extend = function () {};

	LatLngBounds.prototype.union = function () {};

	LatLngBounds.prototype.isEmpty = function () {
		return true;
	};

	LatLngBounds.prototype.getSouthWest = function () {
		return new LatLng( 52, 5 );
	};

	LatLngBounds.prototype.getNorthEast = function () {
		return new LatLng( 53, 6 );
	};

	function Point( x, y ) {
		this.x = x;
		this.y = y;
	}

	function Size( width, height ) {
		this.width = width;
		this.height = height;
	}

	function Marker() {}

	// Stand-in for the parts of the Google Maps API that geoxml3 uses while rendering a KML
	// document. geoxml3 touches google.maps while it is being loaded, so it cannot be a static
	// test dependency: the stub is installed first and the module is loaded on demand, the way
	// jquery.googlemap.js loads it in production.
	var googleStub = {
		maps: {
			MVCObject: MVCObject,
			OverlayView: OverlayView,
			LatLng: LatLng,
			LatLngBounds: LatLngBounds,
			Point: Point,
			Size: Size,
			Marker: Marker,
			InfoWindow: InfoWindow,
			event: {
				addListener: function () {},
				addListenerOnce: function () {},
				trigger: function () {}
			}
		}
	};

	var mapStub = {
		getZoom: function () {
			return 5;
		}
	};

	var projectionStub = {
		fromLatLngToDivPixel: function () {
			return { x: 0, y: 0 };
		}
	};

	QUnit.module( 'Maps.KmlRendering', {
		beforeEach: function () {
			infoWindowContents = [];
			window.google = googleStub;

			return mw.loader.using( 'ext.maps.gm3.geoxml' );
		},
		afterEach: function () {
			window.google = originalGoogle;
		}
	} );

	function kmlDocument( documentContent ) {
		return '<?xml version="1.0" encoding="UTF-8"?>' +
			'<kml xmlns="http://www.opengis.net/kml/2.2"><Document>' +
			documentContent +
			'</Document></kml>';
	}

	function kmlWithPlacemark( placemarkContent ) {
		return kmlDocument(
			'<Placemark><Point><coordinates>5,52,0</coordinates></Point>' +
			placemarkContent +
			'</Placemark>'
		);
	}

	function kmlWithNetworkLink( href ) {
		return kmlDocument(
			'<NetworkLink><Link><href>' + href + '</href>' +
			'<refreshMode>onInterval</refreshMode><refreshInterval>60</refreshInterval>' +
			'</Link></NetworkLink>'
		);
	}

	function kmlWithGroundOverlay( iconHref ) {
		return kmlDocument(
			'<GroundOverlay><Icon><href>' + iconHref + '</href></Icon>' +
			'<LatLonBox><north>53</north><south>52</south><east>6</east><west>5</west></LatLonBox>' +
			'</GroundOverlay>'
		);
	}

	// Directions are suppressed so that the info window holds only what the KML document
	// contributed, rather than also the two Google Maps links geoxml3 adds for a point.
	function parseKml( kml ) {
		var parsedDocuments = [];

		new geoXML3.parser( {
			map: mapStub,
			zoom: false,
			suppressDirections: true
		} ).parseKmlString( kml, parsedDocuments );

		return parsedDocuments[ 0 ];
	}

	// The info window content of the document's first placemark, as an inert document: parsing it
	// this way inspects the real markup without loading or running anything it contains.
	function renderInfoWindow( kml ) {
		parseKml( kml );

		return new DOMParser().parseFromString( infoWindowContents[ 0 ], 'text/html' );
	}

	// The image that ProjectedOverlay renders for the document's first ground overlay.
	function renderGroundOverlayImage( kml ) {
		var overlay = parseKml( kml ).ggroundoverlays[ 0 ];

		overlay.set( 'projection', projectionStub );
		overlay.draw();

		return overlay.div_.querySelector( 'img' );
	}

	// What geoxml3 schedules to refresh a NetworkLink. The browser evaluates a string handler as
	// code, so the handler is captured rather than allowed to run.
	function networkLinkRefreshHandler( kml ) {
		var scheduled = null;
		var originalSetInterval = window.setInterval;

		window.setInterval = function ( handler ) {
			scheduled = handler;
			return 0;
		};

		try {
			parseKml( kml );
		} finally {
			window.setInterval = originalSetInterval;
		}

		return scheduled;
	}

	// Everything in rendered markup that could execute: event handler attributes, javascript: URLs,
	// and elements that load or run code. Asserting on the whole set catches payloads that a check
	// for one particular attribute name would let through.
	function executableMarkup( root ) {
		var found = [];

		Array.prototype.forEach.call( root.querySelectorAll( '*' ), function ( element ) {
			Array.prototype.forEach.call( element.attributes, function ( attribute ) {
				var executes = attribute.name.indexOf( 'on' ) === 0 ||
					/^\s*javascript:/i.test( attribute.value );

				if ( executes ) {
					found.push( element.tagName.toLowerCase() + '[' + attribute.name + ']' );
				}
			} );
		} );

		Array.prototype.forEach.call(
			root.querySelectorAll( 'script, iframe, object, embed' ),
			function ( element ) {
				found.push( element.tagName.toLowerCase() );
			}
		);

		return found;
	}

	QUnit.test( 'Placemark description cannot inject executable markup', function ( assert ) {
		var content = renderInfoWindow( kmlWithPlacemark(
			'<description><![CDATA[<img src=x onerror="alert(1)">]]></description>'
		) );

		assert.deepEqual(
			executableMarkup( content ),
			[],
			'Nothing executable survives in the info window'
		);
	} );

	QUnit.test( 'Placemark name cannot inject executable markup', function ( assert ) {
		var content = renderInfoWindow( kmlWithPlacemark(
			'<name><![CDATA[<script>alert(1)</script><a href="javascript:alert(2)">go</a>]]></name>'
		) );

		assert.deepEqual(
			executableMarkup( content ),
			[],
			'Nothing executable survives in the info window'
		);
	} );

	QUnit.test( 'BalloonStyle template cannot inject executable markup', function ( assert ) {
		var content = renderInfoWindow( kmlWithPlacemark(
			'<Style><BalloonStyle><text><![CDATA[<img src="x" onerror="alert(1)">]]></text></BalloonStyle></Style>'
		) );

		assert.deepEqual(
			executableMarkup( content ),
			[],
			'Nothing executable survives in the info window'
		);
	} );

	QUnit.test( 'styleUrl cannot break out of the info window class attribute', function ( assert ) {
		var content = renderInfoWindow( kmlWithPlacemark(
			'<styleUrl>#pin" onmouseover="alert(1)</styleUrl>'
		) );

		assert.deepEqual(
			executableMarkup( content ),
			[],
			'Nothing executable survives in the info window'
		);
	} );

	QUnit.test( 'GroundOverlay icon href cannot break out of the image source attribute', function ( assert ) {
		var image = renderGroundOverlayImage( kmlWithGroundOverlay(
			'overlay.png" onerror="void 0'
		) );

		assert.deepEqual(
			executableMarkup( image.parentNode ),
			[],
			'Nothing executable survives on the overlay image'
		);
	} );

	QUnit.test( 'NetworkLink href is never evaluated as code', function ( assert ) {
		var handler = networkLinkRefreshHandler( kmlWithNetworkLink(
			'refresh.kml");alert(1);//'
		) );

		assert.strictEqual(
			typeof handler,
			'function',
			'The refresh is scheduled as a call rather than as a string the browser would evaluate'
		);
	} );

	QUnit.test( 'Placemark description keeps a link target', function ( assert ) {
		var content = renderInfoWindow( kmlWithPlacemark(
			'<description><![CDATA[<a href="https://example.com/" target="_blank">Example</a>]]></description>'
		) );

		assert.strictEqual(
			content.querySelector( 'a' ).getAttribute( 'target' ),
			'_blank',
			'The link still opens in a new tab'
		);
	} );

	QUnit.test( 'Placemark description keeps links and formatting', function ( assert ) {
		var content = renderInfoWindow( kmlWithPlacemark(
			'<description><![CDATA[See <a href="https://example.com/">Example</a> and <b>read</b> it]]></description>'
		) );

		assert.strictEqual(
			content.querySelector( 'a' ).getAttribute( 'href' ),
			'https://example.com/',
			'The link is rendered as a link'
		);

		assert.strictEqual(
			content.querySelector( 'b' ).textContent,
			'read',
			'The formatting is rendered as formatting'
		);
	} );

}( window.mediaWiki ) );
