<?php

declare( strict_types = 1 );

namespace Maps;

/**
 * A source of raw (undhardened, unvalidated) Leaflet configuration, such as the MediaWiki:Maps
 * config page.
 *
 * @licence GNU GPL v2+
 */
interface LeafletConfigSource {

	/**
	 * The decoded "leaflet" section of the config, or null when there is none.
	 */
	public function getLeafletConfig(): ?array;

}
