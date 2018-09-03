<?php

/**
 * This group contains all Leaflet related files of the Maps extension.
 *
 * @defgroup Leaflet
 */

/**
 * This file holds the hook and initialization for the Leaflet service.
 *
 * @licence GNU GPL v2+
 * @author Pavel Astakhov < pastakhov@yandex.ru >
 */

// Check to see if we are being called as an extension or directly
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is an extension to MediaWiki and thus not a valid entry point.' );
}

call_user_func(
	function () {
		global $wgResourceModules;

		$pathParts = explode( '/', str_replace( DIRECTORY_SEPARATOR, '/', __DIR__ ) );
		$remoteExtPath = implode( DIRECTORY_SEPARATOR, array_slice( $pathParts, -4 ) );

		$wgResourceModules['ext.maps.leaflet.base'] = [
			'localBasePath' => __DIR__ . '/leaflet',
			'remoteExtPath' => $remoteExtPath . '/leaflet',
			'group' => 'ext.maps',
			'targets' => [
				'mobile',
				'desktop'
			],
			'scripts' => [
				'leaflet.js',
			],
			'styles' => [
				'leaflet.css',
			],
		];

		$wgResourceModules['ext.maps.leaflet'] = [
			'dependencies' => [ 'ext.maps.common', 'ext.maps.leaflet.base' ],
			'localBasePath' => __DIR__,
			'remoteExtPath' => $remoteExtPath,
			'group' => 'ext.maps',
			'targets' => [
				'mobile',
				'desktop'
			],
			'scripts' => [
				'jquery.leaflet.js',
				'ext.maps.leaflet.js',
			],
			'messages' => [
				'maps-markers',
				'maps-copycoords-prompt',
				'maps-searchmarkers-text',
			],
		];

		$wgResourceModules['ext.maps.leaflet.fullscreen'] = [
			'dependencies' => [ 'ext.maps.leaflet' ],
			'localBasePath' => __DIR__ . '/leaflet.fullscreen',
			'remoteExtPath' => $remoteExtPath . '/leaflet.fullscreen',
			'group' => 'ext.maps',
			'targets' => [
				'mobile',
				'desktop'
			],
			'scripts' => [
				'Control.FullScreen.js',
			],
			'styles' => [
				'Control.FullScreen.css',
			],
		];

		$wgResourceModules['ext.maps.leaflet.markercluster'] = [
			'dependencies' => [ 'ext.maps.leaflet' ],
			'localBasePath' => __DIR__ . '/leaflet.markercluster',
			'remoteExtPath' => $remoteExtPath . '/leaflet.markercluster',
			'group' => 'ext.maps',
			'targets' => [
				'mobile',
				'desktop'
			],
			'scripts' => [
				'leaflet.markercluster.js',
			],
			'styles' => [
				'MarkerCluster.css',
			],
		];

		$wgResourceModules['ext.maps.leaflet.providers'] = [
			'dependencies' => [ 'ext.maps.leaflet' ],
			'localBasePath' => __DIR__ . '/leaflet-providers',
			'remoteExtPath' => $remoteExtPath . '/leaflet-providers',
			'group' => 'ext.maps',
			'targets' => [
				'mobile',
				'desktop'
			],
			'scripts' => [
				'leaflet-providers.js',
			],
		];

		$wgResourceModules['ext.maps.leaflet.editable'] = [
			'dependencies' => [ 'ext.maps.leaflet.base' ],
			'localBasePath' => __DIR__ . '/leaflet.editable',
			'remoteExtPath' => $remoteExtPath . '/leaflet.editable',
			'group' => 'ext.maps',
			'targets' => [
				'mobile',
				'desktop'
			],
			'scripts' => [
				'Leaflet.Editable.js',
			],
		];

		$wgResourceModules['ext.maps.leaflet.editor'] = [
			'dependencies' => [
				'ext.maps.leaflet.base',
				//'ext.maps.leaflet.editable'
			],
			'localBasePath' => __DIR__,
			'remoteExtPath' => $remoteExtPath,
			'group' => 'ext.maps',
			'targets' => [
				'mobile',
				'desktop'
			],
			'scripts' => [
				'leaflet.editor.js',
			],
		];
	}
);
