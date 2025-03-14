These are the release notes for the [Maps extension](README.md). For an overview of the
different releases and which versions of PHP and MediaWiki they support, see the
[platform compatibility tables](INSTALL.md#platform-compatibility-and-release-status).


## Maps 11.0.1

Release TDB

* Improved compatibility with future Semantic MediaWiki versions

## Maps 11.0.0

Released on March 5th, 2025.

* Improved encoding of the map data in the HTML. Parser cache rebuild is recommended after upgrading
* Dropped dependence on the Validator library

## Maps 10.3.0

Released on November 28th, 2024.

* Added compatibility with MediaWiki 1.43
* Added geolocation support for Google Maps with a new `mylocation` parameter
* Fixed compatibility with recent versions of Cargo

## Maps 10.2.0

Released on May 13th, 2024.

* Added additional Leaflet layer options by updating the Leaflet Providers library
* Fixed compatibility with VisualEditor on MediaWiki 1.39 and above
* Fixed display of GeoJSON pages on MediaWiki 1.39 and above

## Maps 10.1.2

Released on February 21st, 2024.

* Fixed PHP 7.4 compatibility issue in the GeoJSON content model's integration with Semantic MediaWiki

## Maps 10.1.1

Released on December 4th, 2023.

* Fixed errors occurring on GeoJSON pages on MediaWiki 1.39 and above

## Maps 10.1.0

Released on October 16th, 2023.

* Improved support for MW 1.39
* Fixed GeoJSON issue on MW 1.39+ by replacing usage of the deprecated method `Content::fillParserOutput()`
* Added config option for GoogleGeocoder API key: `egMapsGoogleGeocodingApiKey`, which defaults to `egMapsGMaps3ApiKey`
* Updated Basemap.at URLs for Leaflet and Google
* Fixed some Cargo integration issues
* Updated Composer support
* Removed usage of `wgHooks` global

## Maps 10.0.0

Released on October 26th, 2022.

* Added support for MediaWiki 1.38.x and 1.39.x
* Fixed issue that broke the wikis localization cache when Semantic MediaWiki and Maps where installed together
* Added `oepnvkarte` layer for Leaflet maps
* Marked the GeoJSON namespace as not content. So it will no longer appear in results via Special:Random
* Packages installed directly into `Maps/vendor/` by executing Composer in `Maps/` are no longer loaded
* Removed `Maps_VERSION` and `SM_VERSION` PHP constants

## Maps 9.0.7

Released on February 18th, 2022.

* Fixed error occurring in rare cases in SMW map queries (`Call to a member function getFullUrl() on null`)

## Maps 9.0.6

Released on February 11th, 2022.

* Fixed warning occurring when using an invalid map type in Google Maps `types` parameter
* Improved redirect handling for both Google Maps and Leaflet

## Maps 9.0.5

Released on February 9th, 2022.

* Fixed PHP 8.1+ compatibility issue

## Maps 9.0.4

Released on February 7th, 2022.

* Fixed `GeoJSON` content model registration, which broke in some situations
* Fixed 9.0.3 regression in `LineParser`

## Maps 9.0.3

Released on February 7th, 2022.

* Fixed issue in SMW queries when using an icon for a deleted page

## Maps 9.0.2

Released on January 31st, 2022.

* Fixed redirect issue when using marker links with Google Maps

## Maps 9.0.1

Released on December 14th, 2021.

* Fixed MediaWiki 1.33 deprecation notice when JS tests are turned on

## Maps 9.0.0

Released on July 30th, 2021.

* Raised minimum PHP version from PHP 7.3 to PHP 7.4
* Added support for MediaWiki 1.36 and MediaWiki 1.37
* Added `$egMapsGeoCacheType` setting (thanks [Pierre Rudloff](https://github.com/prudloff-insite))

## Maps 8.0.0

Released on December 9th, 2020.

* Dropped support for MediaWiki 1.31, 1.32, 1.33 and 1.34
* Dropped support for PHP 7.1 and 7.2
* Namespace are no longer shown by default in SMW query result titles (changeable via `$smgQPHideNamespace`)
* Removed the `geocode` permission, which has been unused for quite some time already
* Removed Google Earth support
* Special:MapEditor is no longer listed in a dedicated section on Special:SpecialPages

## Maps 7.20.1

Released on August 6th, 2020.

* Fixed intermittent map loading issue, happening especially when using live previews

## Maps 7.20.0

Released on May 31st, 2020.

* Added `fulltitle` parameter to templates in SMW queries
* Leaflet maps now use the default zoom when there are no markers, rather than showing the whole world

## Maps 7.19.0

Released on May 11th, 2020.

* Added dedicated `#leaflet` parser function
* Added dedicated `#google_maps` parser function
* Improved default zoom for Leaflet maps with multiple markers
* Added named version of the parameters passed to templates in SMW queries

## Maps 7.18.0

Released on April 19th, 2020.

* Added Cargo integration: the `map` display format will now use the Maps extension

## Maps 7.17.2

Released on April 10th, 2020.

* Added install-time compatibility check with MediaWiki

## Maps 7.17.1

Released on April 4th, 2020.

* Non-square images are now correctly scaled in the new `image layers` feature

## Maps 7.17.0

Released on April 2nd, 2020.

* Added experimental `image layers` parameter to Leaflet. Might see breaking changes in upcoming minor releases.
* Leaflet maps now show properly in the Visual Editor extension (no enhanced editing was added)
* Fixed issue causing some settings to be ignored in some situations

## Maps 7.16.0

Released on April 1st, 2020.

* Fixed display of Leaflet marker icons when using MediaWiki 1.34 or later
* Added support for images from remote repositories/wikis (by @edwardspec)
* Added experimental/unstable GeoJSON+SMW integration

## Maps 7.15.6

Released on February 9th, 2020.

* Translation updates

## Maps 7.15.5

Released on January 3rd, 2020.

* Fixed maps not loading properly on mobile
* Improved reliability of Leaflet resource loading

## Maps 7.15.4

Released on December 28th, 2019.

* Dark Leaflet layers are no longer shown (7.10.0 revert)
* Fixed recent Leaflet cluster regression

## Maps 7.15.3

Released on December 26th, 2019.

* Fixed Leaflet maps not loading when using the `circle` parameter
* Fixed default zoom on Leaflet maps when no  markers or shapes are present

## Maps 7.15.2

Released on December 24th, 2019.

* Improved reliability of Leaflet resource loading

## Maps 7.15.1

Released on December 23rd, 2019.

* Fixed `geojson` parameter in the Leaflet result format (7.12.0 regression)
* Fixed `center` parameter for Leaflet (7.12.0 regression)

## Maps 7.15.0

Released on December 22nd, 2019.

* The leaflet fullscreen control is now shown on the top right instead of the top left
* Improved internationalization of the visual GeoJSON editor

## Maps 7.14.0

Released on December 22nd, 2019.

* Improved Leaflet resource loading (some maps will now load slightly faster)

## Maps 7.13.0

Released on December 14th, 2019.

* The GeoJSON editor now shows in #display_maps and #ask for Leaflet maps using the geojson parameter.
* Removed the need to manually include `Maps_Settings.php` in `LocalSettings.php` when modifying maps settings.
* Improved compatibility with MediaWiki 1.35

## Maps 7.12.2

Released on December 9th, 2019.

* Invalid KML file names are no longer passed to Google Maps

## Maps 7.12.1

Released on December 9th, 2019.

* Map query output is no longer incorrectly handled by the MediaWiki parser
* Added logging of debug information when KML parsing fails
* Upgraded Google Maps GeoXML parsing library for KML

## Maps 7.12.0

Released on December 9th, 2019.

* Enhanced GeoJSON editor
    * Added editing of titles and descriptions (by clicking markers/shapes)
    * Added save button and removed auto-save
    * Added ability to specify an edit summary
    * Polygon intersections are now allowed
* Added [simplestyle](https://github.com/mapbox/simplestyle-spec/tree/master/1.1.0) support for GeoJSON
    * Popup text (property key `text`) (Only plaintext, HTML and wikitext are not supported)
    * Popup description (property key `description`) (Only plaintext, HTML and wikitext are not supported)
    * Fill color (property key `fill`)
    * Fill opacity (property key `fill-opacity`)
    * Border color (property key `stroke`)
    * Border width (property key `stroke-width`)
    * Border opacity (property key `stroke-opacity`)
    * `marker-size`, `marker-symbol` and `marker-color` are not yet supported and will be ignored
    * Display only, editing in the visual editor is not yet supported
* Marker clustering now also cluster markers from the GeoJSON layer
* Marker clustering now also cluster markers dynamically loaded via the `ajaxquery` feature
* The Leaflet layer control is now always shown when there are overlays
* Added `cluster` alias to the `markercluster` parameter for both Leaflet and Google Maps
* Added `overlays` alias to the `overlaylayers` parameter for Leaflet
* Leaflet maps with no markers or shapes are now zoomed out by default
* Upgraded Leaflet from 1.3.4 to 1.6.0
* Upgraded Leaflet marker cluster plugin from 1.3.0 to 1.4.1
* Added missing "KML parsing failed" message to Google Maps

## Maps 7.11.0

Released on November 7th, 2019.

* Fixed maps not loading without reloading the page after edit with Visual Editor
* Fixed Leaflet Ajax functionality

## Maps 7.10.0

Released on October 24th, 2019.

* Added dark mode support for Leaflet. Configurable via the new `egMapsLeafletLayersDark` setting (by @vedmaka)
* Fixed PHP notice on some MediaWiki versions when running maintenance scripts

## Maps 7.9.0

Released on October 4th, 2019.

* Added `clicktarget` parameter for Leaflet. `clicktarget=http://your.url?latitude=%lat%&longitude=%long%`
* The `#mapsdoc` parser function now shows all parameters, not just those specific to a mapping service
* The `visitedicon` parameter is no longer incorrectly shown as supported for Leaflet
* The `wmsoverlay` parameter is no longer incorrectly shown as supported for Leaflet

## Maps 7.8.3

Released on October 3rd, 2019.

* The "create page" button on GeoJson pages is now only shown to users with `createpage` permission

## Maps 7.8.2

Released on October 2nd, 2019.

* Fixed recent Google Maps regression

## Maps 7.8.1

Released on October 2nd, 2019.

* Fixed double display of marker icons in the GeoJson namespace

## Maps 7.8.0

Released on October 2nd, 2019.

* Loading messages for Leaflet maps are no longer visible when zooming out far or loading new tiles
* Added entirely visual creation flow for pages in the GeoJson namespace
* Enhanced validation of content in the GeoJson namespace
* Improved text on the creation and edit tabs in the GeoJson namespace
* Added "Visual map edit" tag to revisions created by the GeoJson visual editor

## Maps 7.7.0

Released on September 29th, 2019.

* Fixed GeoJson visual editor on MediaWiki 1.31.x (7.6.0 regression)
* Internationalized most of the GeoJson visual editor
* Added fullscreen control to the GeoJson visual editor
* Added `fullscreen` alias for the `enablefullscreen` parameter

## Maps 7.6.0

Released on September 27th, 2019.

* Fixed GeoJson map preview on MediaWiki 1.33+ (7.5.0 regression)
* Added `scrollzoom` alias for the `scrollwheelzoom` parameter

## Maps 7.5.0

Released on September 24th, 2019.

* Added visual editing UI to maps in the GeoJson namespace

## Maps 7.4.1

Released on August 31st, 2019.

* Fixed critical map loading bug that caused many maps to not load without a page refresh

## Maps 7.4.0

Released on August 9th, 2019.

* Fixed default map height bug occurring with recent versions of an used library
* Removed `egMapsSizeRestrictions` setting, unused since Maps 3.0.0

## Maps 7.3.3

Released on August 2nd, 2019.

* Fixed error in height parameter description

## Maps 7.3.2

Released on July 25th, 2019.

* Removed broken geocode API module

## Maps 7.3.1

Released on July 20th, 2019.

* Fixed compatibility issue with SMW 3.1+ (thanks @mwjames!)

## Maps 7.3.0

Released on May 27th, 2019.

* Fixed loading of certain Leaflet maps due to JavaScript error
* Fixed `ajaxquery` result format parameter
* Removed long broken `pagelinktext` option from KML result format

## Maps 7.2.0

Released on March 5th, 2019.

* Fixed Google Maps KML path issue

## Maps 7.1.0

Released on January 16th, 2019.

* Added `egMapsEnableCoordinateFunction` setting (for people using the GeoData extension)
* Fixed fatal error on Special:MapEditor (6.1.0 regression) (by @paladox)

## Maps 7.0.0

Released on December 16th, 2018.

* Breaking change: removed OpenLayers service
* Breaking change: removed `egMapsDefaultServices['qp']` setting (just use `egMapsDefaultService`)
* Breaking change: removed Google `fusiontables` parameter (Google is retiring this service)
* Added transit layer support for Google Maps (`layers=transit`) (by @acnetj)
* Added `egMapsGeoJsonCacheTtl` setting and optional caching for GeoJSON files
* Fixed display of Maps category on Special:SpecialPages
* Removed unused `tilt` parameter for Google Maps, including the `egMapsGMaps3DefaultTilt` setting

## Maps 6.3.0

Released on November 29th, 2018.

* The `copycoords` parameter (shows coordinates on right click of marker) now works for Leaflet

## Maps 6.2.2

Released on November 27th, 2018.

* Fixed image loading issues, most notably Leaflet markers (6.1.0 regression)

## Maps 6.2.1

Released on November 26th, 2018.

* Fixed Google Maps 'kml' parameter. It now again works with just the file name rather than the full path
* Fixed `lines` parameter for Ask queries (it is no longer ignored)
* Fixed `polygons` parameter for Ask queries (it is no longer ignored)
* Fixed `circles` parameter for Ask queries (it is no longer ignored)
* Fixed `rectangles` parameter for Ask queries (it is no longer ignored)
* Fixed optional list parameters (including `locations` for Ask queries) to ignore invalid values

## Maps 6.2.0

Released on November 23rd, 2018.

* Fixed markers with no text having empty popups (was likely only happening with recent MediaWiki versions)
* Fixed `link=all` in Ask queries: values are now linked where appropriate
* Fixed `link=none` and `link=subject` in Ask queries: properties are no longer linked
* Fixed `headers=hide` in Ask queries: the values now show
* Fixed KML result format (6.1.0 regression)

## Maps 6.1.0

Released on November 21st, 2018.

* The `rotate` control for Google Maps is now enabled by default (changeable via `$egMapsGMaps3Controls`) (by @acnetj)
* The `layers` parameter for Google Maps now works again (5.2.0 regression)
* Files can again be referenced without capitalizing the first letter (3.0.0 regression)
* Removed broken SMW `_gpo` data type

## Maps 6.0.4

Released on October 20th, 2018.

* Fixed localizaion loading issue (by paladox)

## Maps 6.0.3

Released on October 4th, 2018.

* Fixed double display of markers when using Leaflet (6.0.0 regression)

## Maps 6.0.2

Released on October 1st, 2018.

* Coordinates formatted as Decimal Degrees or Float are now rounded sensibly
* The `smgQPCoodDirectional` setting is no longer ignored

## Maps 6.0.1

Released on September 5th, 2018.

* Fixed loading of default settings (6.0.0 regression)

## Maps 6.0.0

Released on September 4th, 2018.

* Dropped support for PHP older than 7.1
* Dropped support for MediaWiki older than 1.31
* Dropped support for Semantic MediaWiki older than 2.4
* Added API key support for Leaflet layers via the `egMapsLeafletLayersApiKeys` setting (by Gilles Cébélieu)
* Updated Leaflet library from version 1.3.1 to version 1.3.4

### GeoJson support

* Added GeoJson namespace. Pages in this namespace can only contain GeoJson
* The `#display_map` parameter `geojson` now accepts page names of pages in the GeoJson namespace

### Breaking changes

* Maps is no longer automatically loaded when it is installed with Composer. You now need to call `wfLoadExtension( 'Maps' )`
  (preferred) or include the entry point (`require_once __DIR__ . '/extensions/Maps/Maps.php';`) in `LocalSettings.php`.
  You will also need to include the default settings before your modify the Maps settings
  `require_once __DIR__ . '/extensions/Maps/Maps_Settings.php';`
* Removed unused `egMapsNamespaceIndex` setting

## Maps 5.6.0

Released on July 16th, 2018.

* Added `geojson` parameter to `#display_map`, available only for Leaflet
* Fixed `#display_map` parameter `imageoverlays`: title, text and link are no longer ignored

## Maps 5.5.5

Released on July 9th, 2018.

* Fixed `#display_map` parameter `polygons` when using Leaflet
* Fixed regression introduced in 5.5.3 that broke marker icons in some cases when using `visitedicon`

## Maps 5.5.4

Released on July 8th, 2018.

* Fixed popups for lines, polygons, circles and rectangles when using Leaflet
* Fixed display of popups with no content for Google Maps
* Fixed fatal error when geocoding fails for addresses in circles and rectangles

## Maps 5.5.3

Released on July 7th, 2018.

* Fixed `#display_map` parameters `icon` and `visitedicon` when using a namespace prefix (ie. File:FileName.png)
* Fixed `icon` and `visited icon` modifiers of `#display_map` parameter `locations` when using a namespace prefix
* Fixed `#display_map` parameter `circles` when using Leaflet

## Maps 5.5.2

Released on July 5th, 2018.

* Fixed the `visited icon` modifier of the `#display_map` parameter `locations` (it is no longer ignored)

## Maps 5.5.1

Released on July 4th, 2018.

* Fixed regression introduced in 5.5.0 causing the `#display_map` parameter `service` to often be ignored
* Fixed fatal error when using `#display_map` parameter `circles`
* Fixed fatal error when using `#display_map` parameter `rectangles`
* Fixed `#display_map` parameter `rectangles` fill color modifier (it is no longer ignored)
* Fixed `#display_map` parameter `rectangles` fill opacity modifier (it is no longer ignored)

## Maps 5.5.0

Released on July 3rd, 2018.

* Added Geoportail (France) layers for Leaflet (by Gilles Cébélieu)
* Updated Leaflet library from version 1.1.0 to version 1.3.1
* Updated Leaflet plugins to their latest versions
* Removed redundant `$egMapsDefaultServices['display_map']` setting

## Maps 5.4.0

Released on June 7th, 2018.

* Improved geocoding service fallback order (by Karsten Hoffmeyer)
* Improved compatibility with the latest MediaWiki development version (by Timo Tijhof)

## Maps 5.3.0

Released on April 28th, 2018.

* Added `scrollwheelzoom` parameter for GoogleMaps (by hidrarga)
* Fixed installation issue caused by using a development version of the geocoding library

## Maps 5.2.0

Released on January 30th, 2018.

* Added support for installation of Maps in non standard directories (using `wgExtensionDirectory`) (by Tobias Oetterer)
* Added `egMapsGeoCacheTtl` setting (by Máté Szabó)
* Added `scrollwheelzoom` parameter for Leaflet that allows disabling scroll wheel zoom (by hidrarga)

## Maps 5.1.0

Released on November 17th, 2017.

* Dropped support for PHP older than 7.0
* Improved layer support for Leaflet (by Peter Grassberger)
    * Leaflet maps now show a layer control when there are multiple layers
    * The `layer` parameter now accepts multiple values and has been aliased to `layers`
    * Added `egMapsLeafletLayers` setting
    * Deprecated `egMapsLeafletLayer` setting in favour of the new `egMapsLeafletLayers`
* Fixed Leaflet attribution link (by Bernhard Krabina)

## Maps 5.0.2

Released on October 19th, 2017.

* Custom marker icons on Leaflet maps are now scaled correctly (by hidrarga)

## Maps 5.0.1

Released on October 18th, 2017.

Special one-off PHP 7.x optimized release. (requires PHP 7.x)

## Maps 5.0.0

Released on October 16th, 2017.

* Added persistent geocoding cache (by hidrarga)
* Fixed rendering of wikitext in popups of the map result format when using the `template` parameter (by hidrarga)
* Fixed random loading failure of Leaflet maps (by Peter Grassberger)
* Geocoders now respect MediaWiki's network settings such as `wgHTTPProxy`
* Image overlays used in `#display_map` now support geocoding for their locations

### Breaking changes

* Removed `geoservice` parameters from the `#display_map` parser function
* Removed `geoservice` and `allowcoordinates` parameters from the `#geocode` parser function
* Removed `mappingservice` and `geoservice` parameters from the `#geodistance` parser function
* Removed `mappingservice`, `geoservice` and `allowcoordinates` params from the `#finddestination` parser function
* Removed `geoservice` parameter from the SMW result formats
* Removed `service` parameter from the `geocode` API module
* Removed `egMapsUserGeoOverrides` setting
* Removed `egMapsAvailableGeoServices` setting
* Removed `egMapsAllowCoordsGeocoding` setting
* Removed support for the GeocoderUS geocoding service as it appears to have shut down

## Maps 4.4.0

Released on September 14th, 2017.

* Added layer support for Leaflet (by Peter Grassberger)
* Added static map support for Leaflet (`static=true`) (by hidrarga)
* Fixed custom marker icon bug when using Leaflet (by hidrarga)

## Maps 4.3.0

Released on June 10th, 2017.

* Dropped support for MediaWiki older than 1.27
* Dropped support for PHP older than 5.6
* Fixed compatibility conflict with the GitHub MediaWiki extension

## Maps 4.2.1

Released on May 20th, 2017.

* Fixed issue occurring when using the `template` parameter in the Google Maps result format more than once on a page

## Maps 4.2.0

Released on May 15th, 2017.

* Fixed bug in Nomatim geocoder that caused page loading to fail when Nomatim is down
* Fixed bug in Nomatim geocoder that caused page loading to fail when Nomatim returned an invalid response
* Updated Leaflet from 1.0.0-rc to 1.0.3

## Maps 4.1.0

Released on April 14th, 2017.

* Fixed rendering of area query values (they now work properly in SMW "further result" links)
* Fixed type warning in `Maps\SemanticMW\ResultPrinters\SMMapPrinter::getMapHTML`
* Added missing geographical polygon type i18n messages

## Maps 4.0.5

Released on March 5th, 2017.

* Fixed i18n issue in the `mapsdoc` parser hook

## Maps 4.0.4

Released on January 9th, 2017.

* Fixed encoding of special characters in the Google geocoder (by somescout)
* Improved PHP 7 compatibility (by Andre Klapper)

## Maps 4.0.3

Released on December 6th, 2016.

* Fixed regression introduced in 4.0.2 that caused the JavaScript to not be loaded in some cases
* The `display_map` parser hook now correctly uses its `geoservice` parameter
* The `center` parameter for the map result format now takes into account the `geoservice` parameter

## Maps 4.0.2

Released on December 4th, 2016.

* Fixed fatal error caused by double loading of initialization code on some platforms

## Maps 4.0.1

Released on November 19th, 2016.

* The `geocode` parser hook now correctly uses its `geoservice` and `allowcoordinates` parameters

## Maps 4.0

Released on November 16th, 2016. Also see the [Maps 4.0 blog post](https://www.entropywins.wtf/blog/2016/11/09/maps-4-0-0-rc1-released/)

### Highlight: Integrated Semantic MediaWiki support

Merged in most of the features of the Semantic Maps extension. These are enabled automatically when SMW is installed.

* Added a [coordinate datatype](https://www.semantic-mediawiki.org/wiki/Help:Type_Geographic_coordinate)
* Added a [result format](https://www.semantic-mediawiki.org/wiki/Help:Result_formats) for each mapping service
* Added a KML result format
* Added [distance query](https://www.semantic-mediawiki.org/wiki/Semantic_Maps_examples/Distance_query) support

Semantic Maps is discontinued as the features will now be maintained in Maps. The Semantic Maps form input
has been moved into the [Page Forms](https://www.mediawiki.org/wiki/Extension:Page_Forms) extension.

### Breaking changes

* The default mapping service was changed from Google Maps to Leaflet (can be changed via the `egMapsDefaultService` setting)
* The Maps tracking category is now disabled by default (can be enabled using the `egMapsEnableCategory` setting)

### Other changes

* Added `egMapsDisableExtension` setting that allows disabling the extension even when it is installed
* The `egGoogleJsApiKey` setting from Maps 2.x will now be used as Google API key when `egMapsGMaps3ApiKey` is not set
* Various missing messages where added

## Maps 3.8.2

Released on September 22nd, 2016.

* Fixed incorrect centering of OpenLayers maps (by Peter Grassberger)

## Maps 3.8.1

Released on September 7th, 2016.

* Fixed bug that caused clustering to always be enabled for Leaflet (by Peter Grassberger)

## Maps 3.8

Released on August 24rd, 2016.

Due to changes to Google Maps, an API key now needs to be set. See the
[installation configuration instructions](https://github.com/JeroenDeDauw/Maps/blob/master/INSTALL.md#configuration).

* Added Google Maps API key `egMapsGMaps3ApiKey` setting (by Peter Grassberger)
* Added Google Maps API version number `egMapsGMaps3ApiVersion` setting (by Peter Grassberger)
* Added [Leaflet marker clustering](https://www.semantic-mediawiki.org/wiki/Maps_examples/Leaflet_marker_clustering) (by Peter Grassberger)
    * `markercluster`: Enables clustering, multiple markers are merged into one marker.
    * `clustermaxzoom`: The maximum zoom level where clusters may exist.
    * `clusterzoomonclick`: Whether clicking on a cluster zooms into it.
    * `clustermaxradius`: The maximum radius that a cluster will cover.
    * `clusterspiderfy`: At the lowest zoom level markers are separated so you can see them all.
* Added [Leaflet fullscreen control](https://www.semantic-mediawiki.org/wiki/Maps_examples/Leaflet_fullscreen_control) (by Peter Grassberger)
* Added [OSM Nominatim Geocoder](https://www.semantic-mediawiki.org/wiki/Maps_examples/Geocode) (by Peter Grassberger)
* Upgraded Leaflet library to its latest version (1.0.0-r3) (by Peter Grassberger)
* Made removal of marker clusters more robust (by Peter Grassberger)
* Unified system messages for several services (by Karsten Hoffmeyer)

## Maps 3.7

Released on June 21st, 2016.

* Added [rotate control support](https://www.semantic-mediawiki.org/wiki/Maps_examples/Google_Maps_with_rotate_control) for Google Maps (by Peter Grassberger)
* Changed coordinate display on OpenLayers maps from long-lat to lat-long (by Peter Grassberger)
* Upgraded google marker cluster library to its latest version (2.1.2) (by Peter Grassberger)
* Upgraded Leaflet library to its latest version (0.7.7) (by Peter Grassberger)
* Added missing system messages (by Karsten Hoffmeyer)
* Internal code enhancements (by Peter Grassberger)
* Removed broken custom map layer functionality. You no longer need to run update.php for full installation.

## Maps 3.6

Released on May 26th, 2016.

* Dropped support for MediaWiki older than 1.23
* Dropped support for PHP older than 5.5
* Added cluster properties for Google Maps (by Peter Grassberger)
    * `clustergridsize`: The grid size of a cluster in pixels
    * `clustermaxzoom`: The maximum zoom level that a marker can be part of a cluster
    * `clusterzoomonclick`: Whether the default behaviour of clicking on a cluster is to zoom into it.
    * `clusteraveragecenter`: Whether the center of each cluster should be the average of all markers in the cluster.
    * `clusterminsize`: The minimum number of markers required to form a cluster.
* Fixed missing marker cluster images for Google Maps (by Peter Grassberger)
* Fixed duplicate markers in OpenLayers maps (by Peter Grassberger)
* Fixed URL support in the icon parameter (by Peter Grassberger)
* Various minor MediaWiki compatibility enhancements (by Karsten Hoffmeyer, Siebrand Mazeland and FlorianSW)

## Maps 3.5

Released on April 2nd, 2016.

* Added `egMapsGMaps3Language` setting (by James Hong Kong and Karsten Hoffmeyer)
* Added `osm-mapquest` layer for OpenLayers (by Bernhard Krabina)
* Added license lable to display on "Special:Version" (by Karsten Hoffmeyer)
* Improved Mobile Frontend support (by James Hong Kong)
* Added missing Leaflet system messages (by Karsten Hoffmeyer)

## Maps 3.4.1

Released on January 30th, 2016.

* Fixed Open Street Map HTTPS support issues (by Karsten Hoffmeyer)
* Migrated remaining wfMsg* to wfMessage (by Florian Schmidt)
* Migrated wfRunHooks to Hooks::run (by Adam Shorland)

## Maps 3.4

Released on July 25th, 2015.

* Added KML support for OpenLayers via a new `kml` parameter (by akionux)
* Fixed Google Maps HTTPS support issues (by Karsten Hoffmeyer)

## Maps 3.3

Released on June 29th, 2015.

* Added `$egMapsEnableCategory` setting (by Bernhard Krabina)
* Fixed OpenLayers specific path issue (by Simon Heimler)

## Maps 3.2.4

Released on June 21st, 2015.

* Map reside is now triggered when going fullscreen (by Kjetil Volden)
* Improved styling of the fullscreen button (by Kjetil Volden)
* Removed no longer working osmarender layer (by Karsten Hoffmeyer)
* Fixed resource paths for some installation configurations

## Maps 3.2.3

Released on March 23rd, 2015.

* Protocol relative URLs are now used, avoiding HTTPS related problems
* Selecting OpenLayers markers now works on touch devices

## Maps 3.2.2

Released on January 19th, 2015.

* Fixed fatal error in the KML formatter

## Maps 3.2.1

Released on January 13th, 2015.

* Fixed `geocode` right
* Fixed coordinate precision issue after breaking changes in DataValues Geo

## Maps 3.2

Released on September 12th, 2014.

* Enhanced compatibility with MediaWiki 1.24
* Improved the translations
* Switched to using DataValue Geo 1.x

## Maps 3.1

Released on June 30th, 2014.

* Re added Google Earth support
* Removed support for the deprecated Google JavaScript API
* Updated the translations to use the new MediaWiki JSON format
* Re added support for fill color and fill opacity parameters for circles
* Re added image overlay support for Google Maps

## Maps 3.0.1

Released on March 27th, 2014.

* Fixed bug that prevented non-px units (%, ex, em) from being used in the width and height parameters.
* Translation updates

## Maps 3.0

Released on January 18th, 2014.

In this version a big part of the PHP codebase has been rewritten to decrease technical debt and
thus facilitate maintenance, new feature deployment and debugging. Many tests have been added and a
lot of bugs have been found and fixed. As an experimental feature, allowing the use of custom image
layers with OpenLayers has been reintroduced.

#### Compatibility changes

* The extension now needs to be installed with Composer.
* Changed minimum Validator version from 0.5 to 1.0.

#### New features

* Added leaflet service (by Pavel Astakhov)
* Added Geocoder.us geoservice support (Ike Hecht)
* Experimental: Usage of custom image layers defined in "Layer:" namespaced wiki pages.
  NOTE: This feature has been part of Maps in an old 0.7.x version but got broken shortly after.
        3.0 reintroduces the feature in a similar way but old layer definitions are probably not
        fully compatible for the sake of some advanced features of this rewrite.
  NOTE: Requires running MediaWiki's maintenance/update.php for database schema updates.

#### Bug fixes

* Fixed autoinfowindows functionality.
* Fixed various bugs in geocoordinate parsing and formatting.

#### Breaking internal changes

* Moved classes into Maps namespace
* Removed all Criteria classes
* Removed all Manipulation classes
* Removed MapsCoordinateParser
* Geocoding interfaces changed
* MapsLocation interface changed
* Custom image layers related classes (previously broken feature) changed

#### Infrastructure

* Maps is now hosted on GitHub at https://github.com/JeroenDeDauw/Maps
* Maps now has its tests run on TravisCI at https://travis-ci.org/JeroenDeDauw/Maps
* Maps code quality is now tracked by ScrutinizerCI at https://scrutinizer-ci.com/g/JeroenDeDauw/Maps/
* Maps is now available on Packagist at https://packagist.org/packages/mediawiki/maps

## Maps 2.0

Released on October 5, 2012.

#### Compatibility changes

* Changed minimum PHP version from 5.2 to 5.3.
* Changed minimum MediaWiki version from 1.17 to 1.18.
* Changed minimum Validator version from 0.4 to 0.5.
* Removed support for the deprecated Google Maps v2 API.
* Removed support for the now unsupported Yahoo! Maps API and associated geocoding service.
* Temporary disabled OSM service (you can still use OSM with the OpenLayers service).

#### New features

* Added support for defining an inline label to markers to GoogleMaps.
* Added support for marker clustering to Google Maps.
* Added support for grouping locations.
* Added support for defining image overlays (ground overlays) in Google Maps.
* Added support for defining lines, polygons, rectangles and circles using wikitext for Google Maps and OpenLayers.
* Added a graphical map editing tool that allows exporting to and importing from simple wikitext (Google Maps only).
* Added "copycoords" parameter to Google Maps and OpenLayers that allows copying coordinates after right clicking a location on a map.
* Added "minzoom" and "maxzoom" parameters to #display_map.
* Added support for using the Google JS API key with Google Maps (for increased map display and geocoding call limits).
* Added support for searching markers (searchmarkers=all/title) in Google Maps and OpenLayers
* Added support for creating static maps in OpenLayers and GoogleMaps (static=on)
* Added positional parameter to show polygons only on hover.
* Added an optional link parameter as an alternative to popup bubble with text and title
* Added an optional visitedicon parameter (both global and marker parameter), that will change the icon of a marker on click.

#### Other improvements

* Merged display_map and display_point(s) into a single parser function: display_map (display_points is now an alias).
* Updates parameter definitions from Validator 0.4.x to Validator 0.5.x.
* Improved script loading.
* Added various unit tests that caught some bugs and will now prevent regressions.

#### Bug fixes

* Fixed JavaScript error on some special pages due to incorrect order of map initialization.
* Fixed partially broken kml functionality.

## Maps 1.0.5

Released on Novebmer 30, 2011.

* Fixed display of attribution control for OpenLayers.
* Fixed to big precision of geographic coordinates in decimal minutes format (bug 32407).

## Maps 1.0.4

Released on October 15, 2011.

* Updated OpenLayers from 2.10 to 2.11.
* Fixed bug in adding additional markers for Google Maps v3 (mainly affecting the Semantic Maps form input).

## Maps 1.0.3

Released on September 14, 2011.

* Added API module for geocoding.
* Added 'geocoding' right.
* Added kmlrezoom parameter for Google Maps v3 and general $egMapsRezoomForKML setting.
* Fixed Google Maps v3 JavaScript issue occurring on MediaWiki 1.17.

## Maps 1.0.2

Released on August 24, 2011.

* Fixed Google Maps v3 JavaScript issue occurring when using Google Earth on unsupported systems.
* Fixed internationalization of distances (bug 30467).

## Maps 1.0.1

Released on August 17, 2011.

* Added language parameter to the mapsdoc hook.
* Use of Validator 0.4.10s setMessage method instead of setDescription for better i18n.
* Fixed zoom and types parameters for Google Maps v3.
* Minor improvement to script loading.
* Added support for Google Earth in Google Maps v3.
* Added tilt parameter for Google Earth in Google Maps v3.

## Maps 1.0

Released on July 19, 2011.

This version branched from Maps 0.7.x at version 0.7.3.

#### New features

* Added full Google Maps v3 support and set it as the default mapping service.
* Added new geocoder making use of the new GeoNames API.
* Added support for the auto-documentation features for parser hooks introduced in Validator 0.4.3.
* Added resizeable parameter to all mapping services except OSM.

#### Removed features

* Removed compatibility with pre MediaWiki 1.17.
* Removed overlays parameter for Google Maps v2.
* Removed the previously deprecated "display map", "display point" and "display points" parser hooks.
Use their underscored equivalents, ie "display_map".

#### Internal improvements

* Usage of the Resource Loader for all scripts and stylesheets.
* Rewrote all the map JavaScript to jQuery plugins.
* Rewrote the way parameters are translated to JavaScript. Now one big PHP object is json_encoded.
* Improved KML formatter.
* Use of Google Maps geocoding service v3 instead of v2.
* Completed coordinate and distance parser/formatter unit tests and made them compliant with the
MediaWiki unit testing support.

#### Bug fixes

* Fixed geocoding service overriding based on mapping service (merged in from Maps 0.7.5).
* Fixed fatal error occurring when running maintenance/refreshLinks.php.
* Fixed DMS coordinate parsing issue (bug 29419).
* Fixed coordinate normalization issue (bug 29421).

#### Other tweaks

* Improved default width of maps (merged in from Maps 0.7.5).

## Maps 0.7.3

Released on November 30, 2010.

* Some internal improvements and translation updates.
* Fixed issue occurring when Maps is the only extension adding custom namespaces.

## Maps 0.7.2

Released on October 28, 2010.

#### New features

* Added experimental support for KML layer definitions.

#### Internal improvements

* Extended the layer handling to support different types of layers, each of which can be supported by one or more mapping services.

#### Bug fixes

* Fixed incompatibility with MW 1.15.x.
* Fixed incorrect parsing of certain DM and DMS coordinates.
* Fixed small layout issue with pop-ups in Google Maps.
* Fixed incorrect error on non-existing pages in the Layer namespace.

## Maps 0.7.1

Released on October 20, 2010.

#### New features

* Image layers for OpenLayers maps, defined via pages in the Layer namespace.

#### Bug fixes

* Support for images without namespace prefix in the display points parser hook.
* Fixed layer oder for OpenLayers maps.

#### Internal improvements

* Rewrote OpenLayers layer handling.

## Maps 0.7

Released on October 15, 2010.

#### New features

* Tag support for these parser hooks (which previously only had parser function support):
    * Coordinates
    * Distance
    * Finddestination
    * Geocode
    * Geodistance
* Thumbs and photos parameters for the OSM service.

#### Bug fixes

* Fixed compatibility with the MW 1.17 resource loader.
* Fixed i18n issue with the overlays control for Google Maps v2 maps.
* Fixed default zoom level for Yahoo! Maps maps.
* Increased the maximum decimals for DMS coordinates from 2 to 20.

#### Removed features

* #geocodelong and #geocodelat parser functions - you can obtain their functionality using #geocode.

#### Internal improvements

* Rewrote the geocoding functionality. It's now an integral part of the extension that can not be just pulled out,
while the reverse is true for individual geocoders. Geocoder interaction now uses the same model as mapping
service interaction.
* Use of Validator 0.4, allowing for more robust and consistent error reporting.
* Rewrote the parser hooks to use the ParserHook class provided by Validator.
* Restructured the directory structure of the extension to better match it's architecture.
* Use of OpenLayers 2.10 instead of 2.9.

## Maps 0.6.6

Released on August 26, 2010.

#### New features

* Support for geocoding over proxies.
* Added $egMapsInternatDirectionLabels settings, allowing users to disable internationalization of direction labels.

#### Refactoring

* Added MapsMappingServices, which serves as factory for MapsPappingService objects and does away
with all the globals previously needed for this.
* Removed the http/curl request code from the geocoder classes - now using Http:get() instead.

#### Bug fixes

* Fixed issue that caused pop-up contents to render incorrectly when it contained wiki markup.
* Fixed coordinate parsing bug (direction labels did not get recognized) that was introduced in 0.6.4.
* Fixed spacing issues with several parser functions.

## Maps 0.6.5

Released on July 27, 2010.

#### Refactoring

* Added unit tests for the coordinates parser.
* Created iMappingFeature interface, from which iMapParserFunctions inherits.
* Moved map id creation to the mapping service class for all features.
* Moved marker JavaScript creation for display_points to the mapping service class for all features.
* Moved default zoom level access method to the mapping service class for all features.
* Improved the way marker data is turned into JavaScript variables.
* Improved coordinate recognition regexes.

#### Bug fixes

* Fixed several small coordinate parsing and formatting issues.
* Fixed a few small distance parsing issues.

## Maps 0.6.4

Released on July 8, 2010.

#### New features

* Added new OSM service based on iframe inclusion of toolserver page that renders OpenStreetMap tiles with Wikipedia overlays.
* Added internationalization to the OpenLayers service.
* Added support for including KML files for Google Maps v2.
* Added 'searchbar' control for Google Maps v2.

#### Refactoring

* Moved more functionality over from feature classes to service classes to prevent crazy code-flow and code duplication.

#### Bug fixes

* Fixed bug in the OpenLayers service causing it to display badly in Chrome.
* Fixed issue with with and height validation for % values, also causing backward compatibility problems with pre 0.6 setting definitions.
* Fixed several small bugs in the coordinate parser.

## Maps 0.6.3

Released on June 20, 2010.

#### Refactoring

* Mayor refactoring of the mapping service handling, to make the code flow less messy and be able to do mapping service related things in a more consistent fashion.
* Upgrade to OpenLayers 2.9.1.

#### Bug fixes

* Fixed severe bug in the coordinate parsing that removed the degree symbol from passes values, resulting in rendering most of them invalid. Presumably present since 0.6.2.

## Maps 0.6.2

Released on June 7, 2010.

#### New features

* Added #distance parser function parse distances using any of the supported units and outputting them in any of these.
* Made supported distance units configurable and added setting for the default distance unit.
* Added 'decimals' and 'unit' parameters to #geosiatnce.
* Default parameter handling improvements (via Validator 0.3.2).

#### Bug fixes

* Re-added parameter name and value insensitivity (via Validator 0.3.2).

## Maps 0.6.1

Released on June 4, 2010.

#### Bug fixes

* Fixed bug that caused geocoding requests to fail when using display_points
* Fixed bug that had broken the geoservice parameter for display_points and display_map.
* Fixed bug that made OSM layers in the OpenLayers service fail.
* Fixed issue that made custom markers on Google Maps not show up on initial page load and centred them wrongly.

## Maps 0.6

Released on May 31, 2010.

#### New features

* Added support for width and height in px, ex, em and %, instead of only px, allowing for maps that
adjust their size to the screen width and other content.
* Added full support for both directional and non-directional coordinate notations in DMS, DD, DM
and float notation.
* Added #coordinates parser function which allows rewformatting of coordinates to all supported notations.
* Rewrote the #geocode parser function to work with named parameters and added support for smart
geocoding. Now takes in all supported coordinate notations, and is able to output in all of them as well.
* Added #geodistance function (based on the one in MathFunctions) with smart geocoding support.
* Added #finddestination function with smart geocoding support.

#### Refactoring

* Rewrote the handling of the display_map and display_point(s) parser functions, esp the way the
service parameter is getting determined and acted upon.
* Removed the MapsMapFeature class to make the base classes for the features more independent and flexible.
* Restructured the directory structure to make what the services and features are more clear.
* Rewrote map divs and added loading message for each map.
* Rewrote individual map JS to be added to the page header.
* Mayor clean up of the coordinate handling, to allow for coordinate formatting and to facilitate
better integration by the GeoCoords data type in Semantic Maps. All this code is now located in MapsCoordinateParser.
* Use native MW hook system for mapping services and features if possible.
* Updated the magic words to mw >=1.16 style, and retained backward compatibility.
* Updated the OpenLayers version from 2.8 to 2.9.
* Rewrote the parameter definitions to work with Validator 0.3.
* Rewrote the resource inclusion html to make the code cleaner and more secure.

#### Bug fixes

* Changed parsing of parameters so that '=' signs in values don't cause themselves and
proceeding characters to be omitted.
* Add mapping to the language codes that are send to the Google Maps API to null the naming
differences between MW and the API.
* Added automatic icon image sizing for Google Maps and Yahoo! Maps markers.
* Fixed conflict with prototype library that caused compatibility problems with the Halo extension.

## Maps 0.5.5

Released on March 20, 2010.

#### Refactoring

* Stylized the code to conform to MediaWiki's spacing conventions.

#### Bug fixes

* Fixed issue with scrollbar in pop-ups on Google Maps maps.
* Fixed Google Maps key issue with OpenLayers that arose from the new OpenLayers layer definition system.
* Fixed JS issue with Google Maps default overlays.

## Maps 0.5.4

Released on March 1, 2010.

#### New features

* Added the ability to define the layers (and their dependencies) that can be added by users to an OpenLayers map.
* Added the ability to define "layer groups" for OpenLayers layers.

#### Refactoring

* Moved the OpenLayers layer definition validation and selection from JS to PHP.

#### Bug fixes

* Fixed bug causing the default zoom for each mapping service to be off.
* Fixed potential xss vectors.
* Fixed minor JS error that was present for all maps except OSM.

## Maps 0.5.3

Released on February 1, 2010.

#### New features

* Added Google Maps v3 support for display_map.

#### Refactoring

* Added service defaulting for features using a hook themselves.

#### Bug fixes

* Fixed JavaScript bug causing all OSM maps to fail.

## Maps 0.5.2

Released on January 20, 2010.

#### New features

* Added icon parameter to display_point(s), allowing you to set the icon for all markers that do not
have a specific icon assigned.

#### Refactoring

* Usage of Validator 0.2 features for the static map specific parameters.

#### Bug fixes

* Fixed escaping issue causing wikitext in the title and label parameters not to be displayed correctly.
* Fixed file path for location specific icons.

## Maps 0.5.1

Released on December 25, 2009.

#### New features

* Integrated further with Validator by holding into account the error level for coordinate validation
in the display_ parser functions.

* Added activatable= parameter to the static map support.

#### Refactoring

* Cleaned up the static map code for OSM display_map.
* Modified the parameter definitions to work with Validator 0.2
* Removed redundant (because of Validator 0.2) utility function calls from the mapping classes.
* Removed redundant (because of Validator 0.2) utility functions from the mapping service files.

#### Bug fixes

* Fixed issue with the hook system that caused code to get executed when it shouldn't.

## Maps 0.5

Released on December 17, 2009.

#### New features

* Added strict parameter validation.
* Added smart 'autopanzoom' like control for Google Maps and Yahoo! Maps.
* Added internationalization to the OSM service, and an extra parameter to define per-map languages.
* Static map support, similar and based upon SlippyMap.

#### Refactoring

* Rewrite the parameter handling to be more centralized and modular.
** Make it possible to override the info of parameters for mapping services, including
their aliases, default values and criteria.
** Make it possible to add and override parameters in each segment of Maps, instead of only
the mapping services.

* Cleaned up and centralized parser function code.
* Refactored the marker specific data handling code in every display point class up to
a central location.
* Removed backward compatibility (to 0.2.x and earlier) of the earth parameter.
* Removed support for Google Map API map type names for Google Maps.
* Added code to unload any services from the service hook that are not present in the list of
allowed services. This ensures they don't get initialized, and makes any check to see if the
service is one of the allowed ones further on unneeded.
* Added checks for extension dependencies that need to be present for Maps to be initialized.

#### Bug fixes

* Fixed bug causing markers not to show up when a specific description was provided.

#### Documenting

* Created screencast demonstrating display_map usage.
* Creates screencast demonstrating display_point usage.
* Updated the developer documentation about hooking into and extending Maps to be useful
for the current version.

## Maps 0.4.2

Released on November 15, 2009.

Changes in 0.4.2 discussed on the authors blog:

* [Maps and Semantic Maps 0.4.2 released](https://www.entropywins.wtf/blog/2009/11/16/maps-and-semantic-maps-0-4-2/)
* [New in Maps 0.4.2](https://www.entropywins.wtf/blog/2009/11/12/new-in-maps-0-4-2/)

#### New features

* Added overlays to Google Maps. This includes both an 'overlay' control, and a new parameter
to choose the available and default loaded overlays.
* Added specific handling for the coordinates= and addresses= parameters for both display_map
and display_point(s). You can now specify you do not want anything that's not a coordinate on
your map (so no geocoding) with the coordinates= parameter, or let Maps know everything is
an address with the addresses= parameter, causing everything to be geocoded. Also modified
the error messages for wrong addresses and coordinates to fit this new behavior.

#### Refactoring

* Added the version of Maps to the JS files call, to prevent issues when functions or calls
are changed in new versions.
* Changed the JavaScript map parameters for Google Maps from individual parameters to a group.

#### Bug fixes

* Fixed inclusion path to the OSM JS file. This bug prevented any OSM maps from showing up.
* Fixed display_map and the centre parameter of display_point(s). Both are unusable by a bug
introduced in 0.4.1.
* Fixed bug causing to many decimal digits in some coordinate notations, making them unrecognisable
for Maps.
* Fixed bug causing a form of DD notation not to get recognized.

## Maps 0.4.1

Released on November 10, 2009.

#### Bug fixes

* Fixed problems with the ° sign, caused by wrong file encodings, resulting into problems with
the DMS notation.
* Fixed flaw in DMS to float translation, resulting into a map being displayed when the values
where not separated by a comma.

## Maps 0.4

Released on November 3, 2009.

Changes in 0.4 discussed on the authors blog:

* [Finally! Maps and Semantic Maps 0.4!](https://www.entropywins.wtf/blog/2009/11/03/finally-maps-and-semantic-maps-0-4/)

#### New features

* Added display_map parser function, to display maps without any markers.
* Added parsing of marker-specific title and label values.
* Added geocoding support for the centre parameter. This is based on automatic detection of
non-coordinates to see if geocoding is required, similar to the modified behavior of display_point(s).
* Added minimum and maximum map size restrictions, like done in SlippyMap.
* Added OSM mapping service, which uses OL, but only allows OSM layers and is optimized for OSM.
* Added smart 'autopanzoom' control to OL and OSM services. It will determine for itself if a
panzoom, panzoombar, or no control should be displayed, depending on the maps height.
* Added support for DM and DD coordinate notations.

#### Refactoring

* Created a hook system for the parser functions, allowing the adding or removing of additional
parser function support.
* Removed redundant absolute script path variable. This absolute value caused problems for some installations.
* Changed the geocoding functionality into a true feature hook element, enabling easy removal.
* Created service hook for the geocoding feature, loose from the mapping services hook.
* Changed display_point(s) and display_address(es) to display_point(s), with auto detect
functionality to see if the provided value are coordinates or addresses. display_address and
display_addresses have been retained for backward compatibility, but will be removed from the docs.
Backward compatibility will be removed at some point, so the use of these functions is discouraged.

#### Bug fixes

* Fixed issue with the default parameter for the display_address(es) parser functions.
* Fixed major bug in the initialization method causing hook code to get executed at a probably
wrong moment. This bug can be the cause of some weird problems that surfaced since 0.3.3.
* Fixed issue with size of pop-ups in Google Maps. They did not stretch far enough vertically
for large contents.

## Maps 0.3.4

Released on September 12, 2009.

Changes in 0.3.4 discussed on the authors blog:

* [Maps and Semantic Maps 0.3.4 released](https://www.entropywins.wtf/blog/2009/09/12/maps-and-semantic-maps-0-3-4-released/)

#### New features

* Created hook system for features, which now also allows you to specify which features
should be enabled and which not.

#### Refactoring

* Added old style geocoding request again for people who do not have cURL enabled, plus a
more consistent fall-back mechanism.
* Added internationalization for the mapping service names.
* Added internationalized list notations.
* Restructured the parser function handling code to work with the new feature hook system.
* Improved structure of geocoding classes.
* Moved Semantic Maps JavaScript code from the Maps JS files to new SM JS files.
* Fixed tiny performance issues all over the code.

#### Bug fixes

* Fixed issue with empty parameters (par=value||par2=value2) that caused the default parameter
(coordinate(s)/address(es)) to be overridden if it occurred after the default one was set.
* Fixed wrong error message when you provide a coordinate(s)/address(es) parameter without
any value (ie |coordinates=|)

## Maps 0.3.3

Released on August 25, 2009.

Changes in 0.3.3 discussed on the authors blog:

* [Maps and Semantic Maps 0.3.3](https://www.entropywins.wtf/blog/2009/08/25/maps-and-semantic-maps-0-3-3/)

#### New features

*Added [Geonames](https://www.geonames.org) geocoding support. This is an open source geocoding
service, that does not require a licence. It has been made the default geocoding service.
* Added wiki-text rendering to the values of the title and label parameters, allowing users
to pass along links, images, and more.

#### Refactoring

* Refactored some common functionality of the geocoder classes up to MapsBaseGeocoder.
* Minor issue - the OpenLayers default zoom should be closer, when displaying one
point

#### Bug fixes

* Fixed small bug in MapsMapper::inParamAliases that caused the determination of the
geoservice to fail in some cases, and set it to the default.

## Maps 0.3.2

Released on August 18, 2009.

Release for consistency. Only changes to Semantic Maps where made in 0.3.2.

## Maps 0.3.1

Released on August 18, 2009.

#### New features

* Users can now define a default service for each feature - parser functions, query printers and form inputs.

#### Refactoring

* Added check to see if the classes array is present in a mapping service info array.
* Added check to see if a mapping service has handling for parser functions. In 0.3,
Maps assumed it had, preventing the adding of mapping services that only have a form input or/and query printer.
* The getValidService function now holds into account that not every service has support for
both parser functions, query printers and form inputs.

#### Bug fixes

* Added path to extension directory to non local class item in a service's info array,
since adding the path is impossible in the declaration.

## Maps 0.3

Released on August 14, 2009.

Changes in 0.3 discussed on the authors blog:

* [Final changes for Maps and SM 0.3](https://www.entropywins.wtf/blog/2009/08/13/final-changes-for-maps-and-sm-0-3/)
* [New features in Maps and SM 0.3](https://www.entropywins.wtf/blog/2009/08/07/new-features-in-maps-and-sm-0-3/)
* [Structural changes for Maps and SM 0.3](https://www.entropywins.wtf/blog/2009/08/05/structural-changes-for-maps-and-sm-0-3/)

#### New features

* Multi location parser functions. Two completely new parser functions have been added that
allow the displaying of multiple points on a map.
* Configurable map type controls. Users can now configure the map type controls of Google
maps and Yahoo! maps maps. They can set the available map types, and the order they want
them to be displayed in the map type control.
* Property names now have aliases. This means you can add several alternative ways to name
the same parameter, for instance, you can make so that ‘auto zoom’ and ‘auto-zoom’ will do
excellently the same as the main parameter ‘autozoom’. This is particularly handy for
parameters such as ‘centre’ (British spelling) and ‘center’ (American spelling).
* Added Google Maps moon, Mars and sky support.
* Controls on both Yahoo! Maps and Google Maps map can now be configured by the user with
the controls parameter. Yahoo! Maps maps already have this option for a limited set of
controls since version 0.2, but the amount of available controls has now been expanded
to what the Yahoo! Maps API offers. For Google Maps the change is significantly larger,
since a lot of new controls can now be added. These included an overview map, a scale
line, a drop down menu for map types, an automated reverse geocoding location determiner
and more.
* Added the ability to specify separate title, label and icon values for each marker
in the display_points and display_addresses parser functions.
* Added user friendly notices for when geocoding of an address fails.
* A whole list of OpenLayers base layers have been added. These include the satellite,
street and hybrid views for Yahoo! Maps and Bing Maps, but also finally the OpenStreetMap layers.

#### Refactoring

* Created hook system for the mapping services. All hard-coded references to mapping
services in the core code have been removed. A service is now added by one multi dimensional
array in Maps.php (note that this can also be done in the initialization file of another
extension!), which holds the name of the parser functions class and it’s location, the
aliases for the service name (feature added in 0.2), and their allowed specific parameters
and their aliases. This architecture allows other people to create their own mapping
extension using the Maps (and Semantic Maps) ‘API’.
* Created a class that bundles common functionality from MapsBaseMap and SMFormInput.
* Rewrote parts of the geocoder base class.
* Added separated handling for default parameter for each mapping service.
* Changed the requests in the geocoder classes to CURL requests to avoid security issues.
* Moved common, parser function specific, functions and variables from MapsMapper to a new MapsParserFunctions class.
* Moved common code within the mapping services out of the parser function class to a new utility classes.

#### Bug fixes

* Fixed issue preventing the extension description from showing up in 0.2.1 & 0.2.2.
* Fixed bug that caused Bing maps (for open layers) to not work.

## Maps 0.2

Released on July 29, 2009.

#### New features

* Added Backward compatibility by using the $wgGoogleMapsKey when this one is set and $egGoogleMapsKey isn't.
* Added hook for [[Extension:Admin_Links|Admin Links]].
* Added a true aliasing system for service names.
* Created a centre parameter, that will allow you to set a custom map centre (different from the
place where the marker will be put).
* Added pop-ups for the markers with title and label parameters to determine the pop-up contents.
* Changed the OpenLayers control handling. Make it accept all (36) OL controls by using eval()
instead of a switch statement in the JavaScript.
* Added the 'physical' button in the map type control of Google Maps maps when this map type is set.
* Added Yahoo! geocoder support (for parser functions).

#### Refactoring

* Refactored MapsBaseMap and all it's child classes. This will vastly increase code
centralization and decrease redundant logic and definitions.
* Did a major rewrite of the Google Maps and Yahoo! Maps code. The parser function
classes now only print a call to a JS function with all needed parameters, which then
does all the logic and creates the map.

#### Bug fixes

* Fixed issue causing aliases for service names getting turned into the default
service since they are not in the allowed services list.
* Removed redundant parts of the OpenLayers library.

## Maps 0.1

Released on July 20, 2009.

* Initial release, featuring Google Maps (+ Google Earth), Yahoo! Maps and OpenLayers mapping services.
