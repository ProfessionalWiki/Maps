<?php

declare( strict_types = 1 );

namespace Maps;

/**
 * @licence GNU GPL v2+
 */
interface LeafletConfigLookup {

	public function getConfig(): LeafletConfig;

}
