<?php

require_once 'PHPUnit\Framework\TestCase.php';

// Trick MW into thinking this is a command line script.
// This is obviously not a good approach, as it will not work on other setups then my own.
unset( $_SERVER['REQUEST_METHOD'] );
$argv = array( 'over9000failz' );
require_once '../../../smw/maintenance/commandLine.inc';

/**
 * MapsCoordinateParser test case.
 * 
 * @ingroup Maps
 * @since 0.6.5
 * @author Jeroen De Dauw
 */
class MapsCoordinateParserTest extends PHPUnit_Framework_TestCase {
	
	public static $coordinates = array(
		'float' => array(
			'55.7557860 N, 37.6176330 W',
			'55.7557860, -37.6176330',
			'55 S, 37.6176330 W',
			'-55, -37.6176330',
			'5.5S,37W ',
			'-5.5,-37 '
		),
		'dd' => array(
			'55.7557860° N, 37.6176330° W',
			'55.7557860°, -37.6176330°',
			'55° S, 37.6176330 ° W',
			'-55°, -37.6176330 °',
			'5.5°S,37°W ',
			'-5.5°,-37° '
		),
		'dm' => array(
			"55° 45.34716' N, 37° 37.05798' W",
			"55° 45.34716', -37° 37.05798'",
			"55° S, 37° 37.05798'W",
			"-55°, 37° -37.05798'",
			"55°S, 37°37.05798'W ",
			"-55°, 37°-37.05798' "
		),
		'dms' => array(
			"55° 45' 21\" N, 37° 37' 3\" W",
			"55° 45' 21\" N, -37° 37' 3\"",
			"55° 45' S, 37° 37' 3\"W",
			"-55°, -37° 37' 3\"",
			"55°45'S,37°37'3\"W ",
			"-55°,-37°37'3\" "
		),
	);
	
	public static $fakeCoordinates = array(
		'55.7557860 E, 37.6176330 W',
		'55.7557860 N, 37.6176330 N',
		'55.7557860 S, 37.6176330 N',
		'55.7557860 N, 37.6176330 S',
	);
	
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
		foreach( self::$coordinates['float'] as $coord ) {
			$this->assertEquals( Maps_COORDS_FLOAT, MapsCoordinateParser::getCoordinatesType( $coord ), "$coord not recognized as float." );
		}

		foreach( self::$coordinates['dd'] as $coord ) {
			$this->assertEquals( Maps_COORDS_DD, MapsCoordinateParser::getCoordinatesType( $coord ), "$coord not recognized as dd." );
		}
		
		foreach( self::$coordinates['dm'] as $coord ) {
			$this->assertEquals( Maps_COORDS_DM, MapsCoordinateParser::getCoordinatesType( $coord ), "$coord not recognized as dm." );
		}

		foreach( self::$coordinates['dms'] as $coord ) {
			$this->assertEquals( Maps_COORDS_DMS, MapsCoordinateParser::getCoordinatesType( $coord ), "$coord not recognized as dms." );
		}		
	}
	
	public function provider() {
		die(__METHOD__);
		$coords = array();
		
		foreach( self::$coordinates as $coordsOfType ) {
			foreach( $coordsOfType as $coord ) {
				$coords[] = array( $coord );
			}			
		}		
		return $coords;
	}	
	
	/**
	 * @dataProvider provider
	 */
	public function testAreCoordinates( $coord ) {
		$this->assertTrue( MapsCoordinateParser::areCoordinates( $coord ), "$coord not recognized as coordinate." );

		
		/*
		 * Tests MapsCoordinateParser::areCoordinates()
		 * 
		foreach ( self::$fakeCoordinates as $coord ) {
			$this->assertFalse( MapsCoordinateParser::areCoordinates( $coord ), "$coord was recognized as coordinate." );
		}
		*/
		
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

