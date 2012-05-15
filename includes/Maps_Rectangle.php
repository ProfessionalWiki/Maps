<?php
/**
 * Class that holds metadata on rectangles made up by locations on map.
 *
 * @since 1.1
 *
 * @file Maps_Rectangle.php
 * @ingroup Maps
 *
 * @licence GNU GPL v3
 * @author Kim Eik < kim@heldig.org >
 */
class MapsRectangle extends MapsBaseFillableElement {


	/**
	 * @var
	 */
	protected $rectangleNorthEast;

	/**
	 * @var
	 */
	protected $rectangleSouthWest;

	/**
	 *
	 */
	function __construct( $rectangleNorthEast , $rectangleSouthWest ) {
		$this->setRectangleNorthEast( $rectangleNorthEast );
		$this->setRectangleSouthWest( $rectangleSouthWest );
	}

	/**
	 * @return
	 */
	public function getRectangleNorthEast() {
		return $this->rectangleNorthEast;
	}

	/**
	 * @param  $rectangleNorthEast
	 */
	public function setRectangleNorthEast( $rectangleNorthEast ) {
		$this->rectangleNorthEast = new MapsLocation( $rectangleNorthEast );
	}

	/**
	 * @return
	 */
	public function getRectangleSouthWest() {
		return $this->rectangleSouthWest;
	}

	/**
	 * @param  $rectangleSouthWest
	 */
	public function setRectangleSouthWest( $rectangleSouthWest ) {
		$this->rectangleSouthWest = new MapsLocation( $rectangleSouthWest );
	}

	public function getJSONObject( $defText = '' , $defTitle = '' ) {

		$parentArray = parent::getJSONObject( $defText , $defTitle );
		$array = array(
			'ne' => array(
				'lon' => $this->getRectangleNorthEast()->getLongitude() ,
				'lat' => $this->getRectangleNorthEast()->getLatitude()
			) ,
			'sw' => array(
				'lon' => $this->getRectangleSouthWest()->getLongitude() ,
				'lat' => $this->getRectangleSouthWest()->getLatitude()
			) ,
		);

		return array_merge( $parentArray , $array );
	}
}
