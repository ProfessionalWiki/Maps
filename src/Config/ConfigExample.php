<?php

declare( strict_types = 1 );

namespace Maps\Config;

/**
 * The worked example preloaded into the MediaWiki:Maps config page when it is first created. It is a
 * small, valid subset of the schema showing the nested shape: a couple of general settings, a
 * coordinate setting and one custom Leaflet layer definition. A test pins that it passes the
 * ConfigValidator, so a schema change can never leave the preload invalid.
 */
class ConfigExample {

	public const JSON = <<<'JSON'
{
	"general": {
		"mapWidth": "100%",
		"mapHeight": "500px"
	},
	"coordinates": {
		"notation": "float"
	},
	"leaflet": {
		"layerDefinitions": {
			"Historic 1904": {
				"url": "https://tiles.example.org/historic1904/{z}/{x}/{y}.png",
				"options": {
					"attribution": "Historic map tiles",
					"maxZoom": 18
				}
			}
		}
	}
}
JSON;

}
