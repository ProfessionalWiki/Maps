<?php

namespace SM\Test;

use SMMapPrinter;
use SMW\Test\QueryPrinterRegistryTestCase;

/**
 * @covers SMMapPrinter
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MapPrinterTest extends QueryPrinterRegistryTestCase {

	/**
	 * @see ResultPrinterTestCase::getFormats
	 *
	 * @since 1.8
	 *
	 * @return array
	 */
	public function getFormats() {
		return [ 'map' ];
	}

	/**
	 * @see ResultPrinterTestCase::getClass
	 *
	 * @since 1.8
	 *
	 * @return string
	 */
	public function getClass() {
		return SMMapPrinter::class;
	}

}
