<?php

declare( strict_types = 1 );

namespace Maps\Config;

/**
 * One or more literal values a user types verbatim, such as true, px or float. A value type returns
 * these from describe() to mark which parts of its description are literals; the configuration
 * reference renders them as code, so the value types stay free of any HTML.
 */
class LiteralValues {

	/**
	 * @var string[]
	 */
	public readonly array $values;

	public function __construct( string ...$values ) {
		$this->values = $values;
	}

}
