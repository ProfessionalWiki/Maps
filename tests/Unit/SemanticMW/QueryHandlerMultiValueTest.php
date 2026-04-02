<?php

declare( strict_types = 1 );

namespace Maps\Tests\Unit\SemanticMW;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * @covers \Maps\SemanticMW\QueryHandler
 *
 * Tests for multi-valued property accumulation in QueryHandler.
 * Requires Semantic MediaWiki to be installed since QueryHandler depends on SMW classes.
 */
class QueryHandlerMultiValueTest extends TestCase {

	protected function setUp(): void {
		if ( !class_exists( 'SMW\Query\QueryResult' ) ) {
			$this->markTestSkipped( 'Semantic MediaWiki is not installed' );
		}
	}

	public function testMultiValuedPropertiesAreAccumulated(): void {
		$queryResult = $this->createMock( \SMW\Query\QueryResult::class );
		$queryResult->method( 'getNext' )->willReturn( false );

		$handler = new \Maps\SemanticMW\QueryHandler( $queryResult, SMW_OUTPUT_HTML );
		$handler->setHeaderStyle( 'hide' );

		$printRequest = $this->createMock( \SMW\Query\PrintRequest::class );
		$printRequest->method( 'getCanonicalLabel' )->willReturn( 'Category' );
		$printRequest->method( 'getHTMLText' )->willReturn( 'Category' );
		$printRequest->method( 'getText' )->willReturn( 'Category' );

		$dataValue1 = $this->createMock( \SMWDataValue::class );
		$dataValue1->method( 'getLongHTMLText' )->willReturn( 'Foo' );
		$dataValue1->method( 'getLongText' )->willReturn( 'Foo' );
		$dataValue1->method( 'getTypeID' )->willReturn( '_txt' );

		$dataValue2 = $this->createMock( \SMWDataValue::class );
		$dataValue2->method( 'getLongHTMLText' )->willReturn( 'Bar' );
		$dataValue2->method( 'getLongText' )->willReturn( 'Bar' );
		$dataValue2->method( 'getTypeID' )->willReturn( '_txt' );

		$resultArray = $this->createMock( \SMW\Query\Result\ResultArray::class );
		$resultArray->method( 'getPrintRequest' )->willReturn( $printRequest );
		$resultArray->method( 'getNextDataValue' )
			->willReturnOnConsecutiveCalls( $dataValue1, $dataValue2, false );

		// Use reflection to call the private method
		$method = new ReflectionMethod( \Maps\SemanticMW\QueryHandler::class, 'getLocationsAndProperties' );
		$method->setAccessible( true );

		// First element is skipped (index 0), so pass a dummy + our resultArray
		$dummyResultArray = $this->createMock( \SMW\Query\Result\ResultArray::class );
		$dummyResultArray->method( 'getNextDataValue' )->willReturn( false );

		[ $locations, $properties ] = $method->invoke( $handler, [ $dummyResultArray, $resultArray ] );

		$this->assertArrayHasKey( 'Category', $properties );
		$this->assertStringContainsString( 'Foo', $properties['Category'] );
		$this->assertStringContainsString( 'Bar', $properties['Category'] );
		$this->assertStringContainsString( ', ', $properties['Category'] );
	}

	public function testSingleValuedPropertyIsNotDuplicated(): void {
		$queryResult = $this->createMock( \SMW\Query\QueryResult::class );
		$queryResult->method( 'getNext' )->willReturn( false );

		$handler = new \Maps\SemanticMW\QueryHandler( $queryResult, SMW_OUTPUT_HTML );
		$handler->setHeaderStyle( 'hide' );

		$printRequest = $this->createMock( \SMW\Query\PrintRequest::class );
		$printRequest->method( 'getCanonicalLabel' )->willReturn( 'Name' );
		$printRequest->method( 'getHTMLText' )->willReturn( 'Name' );
		$printRequest->method( 'getText' )->willReturn( 'Name' );

		$dataValue = $this->createMock( \SMWDataValue::class );
		$dataValue->method( 'getLongHTMLText' )->willReturn( 'SingleValue' );
		$dataValue->method( 'getLongText' )->willReturn( 'SingleValue' );
		$dataValue->method( 'getTypeID' )->willReturn( '_txt' );

		$resultArray = $this->createMock( \SMW\Query\Result\ResultArray::class );
		$resultArray->method( 'getPrintRequest' )->willReturn( $printRequest );
		$resultArray->method( 'getNextDataValue' )
			->willReturnOnConsecutiveCalls( $dataValue, false );

		$method = new ReflectionMethod( \Maps\SemanticMW\QueryHandler::class, 'getLocationsAndProperties' );
		$method->setAccessible( true );

		$dummyResultArray = $this->createMock( \SMW\Query\Result\ResultArray::class );
		$dummyResultArray->method( 'getNextDataValue' )->willReturn( false );

		[ $locations, $properties ] = $method->invoke( $handler, [ $dummyResultArray, $resultArray ] );

		$this->assertSame( 'SingleValue', $properties['Name'] );
	}
}
