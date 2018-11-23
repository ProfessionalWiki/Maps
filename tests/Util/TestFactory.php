<?php

declare( strict_types = 1 );

namespace Maps\Tests\Util;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class TestFactory {

	public static function newInstance(): self {
		return new self();
	}

	public function getPageCreator(): PageCreator {
		return new PageCreator();
	}

}
