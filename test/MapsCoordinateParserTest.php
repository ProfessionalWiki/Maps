<?php

require_once 'PHPUnit\Framework\TestCase.php';

/**
 * MapsCoordinateParser test case.
 * 
 * @ingroup Maps
 * @since 0.6.5
 * @author Jeroen De Dauw
 */
class MapsCoordinateParserTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var MapsCoordinateParser
	 */
	private $MapsCoordinateParser;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated MapsCoordinateParserTest::setUp()
		

		$this->MapsCoordinateParser = new MapsCoordinateParser(/* parameters */);
	
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated MapsCoordinateParserTest::tearDown()
		

		$this->MapsCoordinateParser = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests MapsCoordinateParser::parseCoordinates()
	 */
	public function testParseCoordinates() {
		// TODO Auto-generated MapsCoordinateParserTest::testParseCoordinates()
		$this->markTestIncomplete ( "parseCoordinates test not implemented" );
		
		MapsCoordinateParser::parseCoordinates(/* parameters */);
	
	}
	
	/**
	 * Tests MapsCoordinateParser::getCoordinatesType()
	 */
	public function testGetCoordinatesType() {
		// Floats
		$this->assertEquals( MapsCoordinateParser::getCoordinatesType( '55.7557860 N, 37.6176330 W' ), Maps_COORDS_FLOAT );
		$this->assertEquals( MapsCoordinateParser::getCoordinatesType( '55.7557860, -37.6176330' ), Maps_COORDS_FLOAT );
		$this->assertEquals( MapsCoordinateParser::getCoordinatesType( '55 S, 37.6176330 W' ), Maps_COORDS_FLOAT );
		$this->assertEquals( MapsCoordinateParser::getCoordinatesType( '-55, -37.6176330' ), Maps_COORDS_FLOAT );	
		$this->assertEquals( MapsCoordinateParser::getCoordinatesType( '5.5S,37W ' ), Maps_COORDS_FLOAT );
		$this->assertEquals( MapsCoordinateParser::getCoordinatesType( '-5.5,-37 ' ), Maps_COORDS_FLOAT );

		// Decimal Degrees 
		$this->assertEquals( MapsCoordinateParser::getCoordinatesType( '55.7557860° N, 37.6176330° W' ), Maps_COORDS_DD );
		$this->assertEquals( MapsCoordinateParser::getCoordinatesType( '55.7557860°, -37.6176330°' ), Maps_COORDS_DD );
		$this->assertEquals( MapsCoordinateParser::getCoordinatesType( '55° S, 37.6176330 ° W' ), Maps_COORDS_DD );
		$this->assertEquals( MapsCoordinateParser::getCoordinatesType( '-55°, -37.6176330 °' ), Maps_COORDS_DD );	
		$this->assertEquals( MapsCoordinateParser::getCoordinatesType( '5.5°S,37°W ' ), Maps_COORDS_DD );
		$this->assertEquals( MapsCoordinateParser::getCoordinatesType( '-5.5°,-37° ' ), Maps_COORDS_DD );
		
		// Decimal Minutes 
		$this->assertEquals( MapsCoordinateParser::getCoordinatesType( "55° 45.34716' N, 37° 37.05798' W" ), Maps_COORDS_DM );
		$this->assertEquals( MapsCoordinateParser::getCoordinatesType( "55° 45.34716', -37° 37.05798'" ), Maps_COORDS_DM );
		$this->assertEquals( MapsCoordinateParser::getCoordinatesType( "55° S, 37° 37.05798'W" ), Maps_COORDS_DM );
		$this->assertEquals( MapsCoordinateParser::getCoordinatesType( "-55°, 37° -37.05798'" ), Maps_COORDS_DM );	
		$this->assertEquals( MapsCoordinateParser::getCoordinatesType( "55°S, 37°37.05798'W " ), Maps_COORDS_DM );
		$this->assertEquals( MapsCoordinateParser::getCoordinatesType( "-55°, 37°-37.05798' " ), Maps_COORDS_DM );
		
		// Degrees Minutes Seconds 
		$this->assertEquals( MapsCoordinateParser::getCoordinatesType( "55° 45' 21\" N, 37° 37' 3\" W" ), Maps_COORDS_DMS );
		$this->assertEquals( MapsCoordinateParser::getCoordinatesType( "55° 45' 21\" N, -37° 37' 3\"" ), Maps_COORDS_DMS );
		$this->assertEquals( MapsCoordinateParser::getCoordinatesType( "55° 45' S, 37° 37' 3\"W" ), Maps_COORDS_DMS );
		$this->assertEquals( MapsCoordinateParser::getCoordinatesType( "-55°, -37° 37' 3\"" ), Maps_COORDS_DMS );	
		$this->assertEquals( MapsCoordinateParser::getCoordinatesType( "55°45'S,37°37'3\"W " ), Maps_COORDS_DMS );
		$this->assertEquals( MapsCoordinateParser::getCoordinatesType( "-55°,-37°37'3\" " ), Maps_COORDS_DMS );		
	}
	
	/**
	 * Tests MapsCoordinateParser::areCoordinates()
	 */
	public function testAreCoordinates() {
		// TODO Auto-generated MapsCoordinateParserTest::testAreCoordinates()
		$this->markTestIncomplete ( "areCoordinates test not implemented" );
		
		MapsCoordinateParser::areCoordinates(/* parameters */);
	
	}
	
	/**
	 * Tests MapsCoordinateParser::formatCoordinates()
	 */
	public function testFormatCoordinates() {
		// TODO Auto-generated MapsCoordinateParserTest::testFormatCoordinates()
		$this->markTestIncomplete ( "formatCoordinates test not implemented" );
		
		MapsCoordinateParser::formatCoordinates(/* parameters */);
	
	}
	
	/**
	 * Tests MapsCoordinateParser::formatToArray()
	 */
	public function testFormatToArray() {
		// TODO Auto-generated MapsCoordinateParserTest::testFormatToArray()
		$this->markTestIncomplete ( "formatToArray test not implemented" );
		
		MapsCoordinateParser::formatToArray(/* parameters */);
	
	}
	
	/**
	 * Tests MapsCoordinateParser::areFloatCoordinates()
	 */
	public function testAreFloatCoordinates() {
		// TODO Auto-generated MapsCoordinateParserTest::testAreFloatCoordinates()
		$this->markTestIncomplete ( "areFloatCoordinates test not implemented" );
		
		MapsCoordinateParser::areFloatCoordinates(/* parameters */);
	
	}
	
	/**
	 * Tests MapsCoordinateParser::areDMSCoordinates()
	 */
	public function testAreDMSCoordinates() {
		// TODO Auto-generated MapsCoordinateParserTest::testAreDMSCoordinates()
		$this->markTestIncomplete ( "areDMSCoordinates test not implemented" );
		
		MapsCoordinateParser::areDMSCoordinates(/* parameters */);
	
	}
	
	/**
	 * Tests MapsCoordinateParser::areDDCoordinates()
	 */
	public function testAreDDCoordinates() {
		// TODO Auto-generated MapsCoordinateParserTest::testAreDDCoordinates()
		$this->markTestIncomplete ( "areDDCoordinates test not implemented" );
		
		MapsCoordinateParser::areDDCoordinates(/* parameters */);
	
	}
	
	/**
	 * Tests MapsCoordinateParser::areDMCoordinates()
	 */
	public function testAreDMCoordinates() {
		// TODO Auto-generated MapsCoordinateParserTest::testAreDMCoordinates()
		$this->markTestIncomplete ( "areDMCoordinates test not implemented" );
		
		MapsCoordinateParser::areDMCoordinates(/* parameters */);
	
	}
	
	/**
	 * Tests MapsCoordinateParser::parseAndFormat()
	 */
	public function testParseAndFormat() {
		// TODO Auto-generated MapsCoordinateParserTest::testParseAndFormat()
		$this->markTestIncomplete ( "parseAndFormat test not implemented" );
		
		MapsCoordinateParser::parseAndFormat(/* parameters */);
	
	}

}

