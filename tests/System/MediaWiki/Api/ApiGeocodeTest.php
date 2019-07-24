<?php

declare( strict_types = 1 );

namespace Maps\Tests\System\MediaWiki\Api;

/**
 * @covers \Maps\MediaWiki\Api\ApiGeocode
 * @group medium
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ApiGeocodeTest extends \ApiTestCase {

	public function testResponseHasResultsArray() {
		$result = $this->doApiRequest( [
			'action' => 'geocode',
			'locations' => ''
		 ] );

		$this->assertArrayHasKey( 'results', $result[0] );
		$this->assertTrue( is_array( $result[0]['results'] ) );
	}

}
