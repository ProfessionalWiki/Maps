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
			"-55°, -37° 37.05798'",
			"55°S, 37°37.05798'W ",
			"-55°, 37°37.05798' "
		),
		'dms' => array(
			"55° 45' 21\" N, 37° 37' 3\" W",
			"55° 45' 21\", -37° 37' 3\"",
			"55° 45' S, 37° 37' 3\"W",
			"-55°, -37° 37' 3\"",
			"55°45'S,37°37'3\"W ",
			"-55°,-37°37'3\" "
		),
	);
	
	public static $coordinateMappings = array(
		'float-dms' => array(
			'42° 30\' 0", -42° 30\' 0"' => array( '42.5, -42.5', '42.5 N, 42.5 W' )
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
		$this->MapsCoordinateParser = new MapsCoordinateParser(/* parameters */);
	
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->MapsCoordinateParser = null;
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
	}
	
	/**
	 * Tests MapsCoordinateParser::parseCoordinates()
	 */
	public function testParseCoordinates() {
		// TODO Auto-generated MapsCoordinateParserTest::testParseCoordinates()
		foreach ( self::$fakeCoordinates as $coord ) {
			$this->assertFalse( MapsCoordinateParser::parseCoordinates( $coord ), "parseCoordinates did not return false for $coord." );
		}
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
	
	/*
	public function coordinatesProvider() {
		die(__METHOD__);
		$coords = array();
		
		foreach( self::$coordinates as $coordsOfType ) {
			foreach( $coordsOfType as $coord ) {
				$coords[] = array( $coord );
			}			
		}		
		return $coords;
	}
	*/
	
	/**
	 * @dataProvider coordinatesProvider
	 */
	public function testAreCoordinates( $coord ) {
		foreach( self::$coordinates as $coordsOfType ) {
			foreach( $coordsOfType as $coord ) {	
				$this->assertTrue( MapsCoordinateParser::areCoordinates( $coord ), "$coord not recognized as coordinate." );
			}	
		}
		
		foreach ( self::$fakeCoordinates as $coord ) {
			$this->assertFalse( MapsCoordinateParser::areCoordinates( $coord ), "$coord was recognized as coordinate." );
		}
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
		foreach ( self::$fakeCoordinates as $coord ) {
			$this->assertFalse( MapsCoordinateParser::areFloatCoordinates( $coord ), "$coord was recognized as float." );
		}
		foreach( self::$coordinates['float'] as $coord ) {
			$this->assertEquals( Maps_COORDS_FLOAT, MapsCoordinateParser::getCoordinatesType( $coord ), "$coord not recognized as float." );
		}		
	}
	
	/**
	 * Tests MapsCoordinateParser::areDMSCoordinates()
	 */
	public function testAreDMSCoordinates() {
		foreach ( self::$fakeCoordinates as $coord ) {
			$this->assertFalse( MapsCoordinateParser::areFloatCoordinates( $coord ), "$coord was recognized as dms." );
		}
		foreach( self::$coordinates['dms'] as $coord ) {
			$this->assertEquals( Maps_COORDS_DMS, MapsCoordinateParser::getCoordinatesType( $coord ), "$coord not recognized as dms." );
		}		
	}
	
	/**
	 * Tests MapsCoordinateParser::areDDCoordinates()
	 */
	public function testAreDDCoordinates() {
		foreach ( self::$fakeCoordinates as $coord ) {
			$this->assertFalse( MapsCoordinateParser::areFloatCoordinates( $coord ), "$coord was recognized as dd." );
		}
		foreach( self::$coordinates['dd'] as $coord ) {
			$this->assertEquals( Maps_COORDS_DD, MapsCoordinateParser::getCoordinatesType( $coord ), "$coord not recognized as dd." );
		}	
	}
	
	/**
	 * Tests MapsCoordinateParser::areDMCoordinates()
	 */
	public function testAreDMCoordinates() {
		foreach ( self::$fakeCoordinates as $coord ) {
			$this->assertFalse( MapsCoordinateParser::areFloatCoordinates( $coord ), "$coord was recognized as dm." );
		}
		foreach( self::$coordinates['dm'] as $coord ) {
			$this->assertEquals( Maps_COORDS_DM, MapsCoordinateParser::getCoordinatesType( $coord ), "$coord not recognized as dm." );
		}		
	}
	
	/**
	 * Tests MapsCoordinateParser::parseAndFormat()
	 */
	public function testParseAndFormat() {
		// TODO Auto-generated MapsCoordinateParserTest::testParseAndFormat()
		foreach ( self::$fakeCoordinates as $coord ) {
			$this->assertFalse( MapsCoordinateParser::parseAndFormat( $coord ), "parseAndFormat did not return false for $coord." );
		}
		
		foreach ( self::$coordinateMappings['float-dms'] as $destination => $sources ) {
			foreach ( $sources as $source ) {
				$result = MapsCoordinateParser::parseAndFormat( $source, Maps_COORDS_DMS, false );
				$this->assertEquals( 
					$destination,
					$result,
					"$source parsed to $result, not $destination"
				);
			}
		}
	}

}

