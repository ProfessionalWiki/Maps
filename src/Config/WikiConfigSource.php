<?php

declare( strict_types = 1 );

namespace Maps\Config;

/**
 * A source of the raw (undhardened, unvalidated) configuration set on-wiki, such as the
 * MediaWiki:Maps config page.
 */
interface WikiConfigSource {

	/**
	 * The decoded config page as a map of group name to group data, or null when there is none.
	 */
	public function getConfig(): ?array;

}
