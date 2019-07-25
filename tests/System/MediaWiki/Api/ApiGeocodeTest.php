<?php

declare( strict_types = 1 );

namespace Maps\Tests\System\MediaWiki\Api;

use MediaWiki\MediaWikiServices;
use MediaWiki\Permissions\PermissionManager;

/**
 * @covers \Maps\MediaWiki\Api\ApiGeocode
 *
 * @group API
 * @group Database
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

	public function testUseCannotGeocodeWithoutGeocodePermission() {
		$this->revokeGeocodePermission();

		$this->expectException( \ApiUsageException::class );
		$this->expectExceptionMessage( 'is limited to users' );

		$this->doApiRequest( [
			'action' => 'geocode',
			'locations' => ''
		] );
	}

	private function revokeGeocodePermission() {
		$this->setMwGlobals( 'wgGroupPermissions', [
			'*' => [ 'read' => true, 'geocode' => false ],
		] );

		if ( class_exists( PermissionManager::class ) ) {
			MediaWikiServices::getInstance()->resetServiceForTesting( 'PermissionManager' );
		}
	}

}
