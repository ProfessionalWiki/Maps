<?php

require_once 'PHPUnit\Framework\TestCase.php';

// Trick MW into thinking this is a command line script.
// This is obviously not a good approach, as it will not work on other setups then my own.
unset( $_SERVER['REQUEST_METHOD'] );
$argv = array( 'over9000failz' );
require_once '../../../smw/maintenance/commandLine.inc';

/**
 * MapsDistanceParser test case.
 * 
 * @ingroup Maps
 * @since 0.6.5
 * @author Jeroen De Dauw
 */
class MapsDistanceParserTest extends PHPUnit_Framework_TestCase {
	
	public static $distances = array(
		//'1' => 1,
		//'1m' => 1,
		//'1 m' => 1,
		//'   1   	  m ' => 1,
		'1.1' => 1.1,
		'1,1' => 1.1,
		'1 km' => 1000,
		'42 km' => 42000,
		'4.2 km' => 4200,
		'4,20km' => 4200,
		'1 mile' => 1609.344,
		'10 nauticalmiles' => 18520,
		'1.0nautical mile' => 1852,
	);
	
	/**
	 * Invalid distances.
	 * 
	 * @var array
	 */	
	public static $fakeDistances = array(	
		'IN YOUR CODE, BEING TOTALLY REDICULOUSE',
		'0x20 km',
		'km 42',
		'42 42 km',
		'42 km km',
		'42 foo',
		'3.4.2 km'
	);
	
	/**
	 * @var MapsDistanceParser
	 */
	private $MapsDistanceParser;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		$this->MapsDistanceParser = new MapsDistanceParser(/* parameters */);
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->MapsDistanceParser = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		
	}
	
	/**
	 * Tests MapsDistanceParser::parseDistance()
	 */
	public function testParseDistance() {
		foreach ( self::$distances as $rawValue => $parsedValue ) {
			$this->assertEquals( $parsedValue, MapsDistanceParser::parseDistance( $rawValue ), "'$rawValue' was not parsed to '$parsedValue':" );
		}
		
		foreach ( self::$fakeDistances as $fakeDistance ) {
			$this->assertFalse( MapsDistanceParser::parseDistance( $fakeDistance ), "'$fakeDistance' should not be recognized:" );
		}
	}
	
	/**
	 * Tests MapsDistanceParser::formatDistance()
	 */
	public function testFormatDistance() {
		// TODO Auto-generated MapsDistanceParserTest::testFormatDistance()
		$this->markTestIncomplete ( "formatDistance test not implemented" );
		
		MapsDistanceParser::formatDistance(/* parameters */);
	
	}
	
	/**
	 * Tests MapsDistanceParser::parseAndFormat()
	 */
	public function testParseAndFormat() {
		// TODO Auto-generated MapsDistanceParserTest::testParseAndFormat()
		$this->markTestIncomplete ( "parseAndFormat test not implemented" );
		
		MapsDistanceParser::parseAndFormat(/* parameters */);
	
	}
	
	/**
	 * Tests MapsDistanceParser::isDistance()
	 */
	public function testIsDistance() {
		// TODO Auto-generated MapsDistanceParserTest::testIsDistance()
		$this->markTestIncomplete ( "isDistance test not implemented" );
		
		MapsDistanceParser::isDistance(/* parameters */);
	
	}
	
	/**
	 * Tests MapsDistanceParser::getUnitRatio()
	 */
	public function testGetUnitRatio() {
		// TODO Auto-generated MapsDistanceParserTest::testGetUnitRatio()
		$this->markTestIncomplete ( "getUnitRatio test not implemented" );
		
		MapsDistanceParser::getUnitRatio(/* parameters */);
	
	}
	
	/**
	 * Tests MapsDistanceParser::getValidUnit()
	 */
	public function testGetValidUnit() {
		// TODO Auto-generated MapsDistanceParserTest::testGetValidUnit()
		$this->markTestIncomplete ( "getValidUnit test not implemented" );
		
		MapsDistanceParser::getValidUnit(/* parameters */);
	
	}
	
	/**
	 * Tests MapsDistanceParser::getUnits()
	 */
	public function testGetUnits() {
		// TODO Auto-generated MapsDistanceParserTest::testGetUnits()
		$this->markTestIncomplete ( "getUnits test not implemented" );
		
		MapsDistanceParser::getUnits(/* parameters */);
	
	}

}