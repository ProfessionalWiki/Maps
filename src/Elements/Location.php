<?php

namespace Maps\Elements;

use DataValues\Geo\Values\LatLongValue;
use Maps\Geocoders;
use MWException;

/**
 * Class describing a single location (geographical point).
 *
 * TODO: rethink the design of this class after deciding on what actual role it has
 *
 * @since 3.0
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Daniel Werner
 */
class Location extends BaseElement {

	/**
	 * @var LatLongValue
	 */
	private $coordinates;

	/**
	 * @var string
	 */
	private $address;

	/**
	 * @var string
	 */
	private $icon = '';

	/**
	 * @var string
	 */
	private $group = '';

	/**
	 * @var string
	 */
	private $inlineLabel = '';

	/**
	 * @var string
	 */
	private $visitedIcon = '';

	/**
	 * Creates and returns a new instance of a Location from a latitude and longitude.
	 *
	 * @since 1.0
	 *
	 * @param float $lat
	 * @param float $lon
	 *
	 * @return Location
	 */
	public static function newFromLatLon( $lat, $lon ) {
		return new self( new LatLongValue( $lat, $lon ) );
	}


	/**
	 * Creates and returns a new instance of a Location with title from a latitude and longitude.
	 *
	 * @since 3.7
	 *
	 * @param float $lat
	 * @param float $lon
	 *
	 * @return Location
	 */
	public static function newTitledFromLatLon( $lat, $lon ) {
		$location = new self( new LatLongValue( $lat, $lon ) );
		$location->setTitle( $lat . ',' . $lon );
		return $location;
	}

	/**
	 * Creates and returns a new instance of a Location from an address.
	 *
	 * @since 1.0
	 *
	 * @param string $address
	 * @deprecated
	 *
	 * @return Location
	 * @throws MWException
	 */
	public static function newFromAddress( $address ) {
		$address = Geocoders::attemptToGeocode( $address );

		if ( $address === false ) {
			throw new MWException( 'Could not geocode address' );
		}

		return new static( $address );
	}

	public function __construct( LatLongValue $coordinates ) {
		parent::__construct();
		$this->coordinates = $coordinates;
	}

	/**
	 * Returns the locations coordinates.
	 *
	 * @since 3.0
	 *
	 * @return LatLongValue
	 */
	public function getCoordinates() {
		return $this->coordinates;
	}

	/**
	 * Returns the address corresponding to this location.
	 * If there is none, and empty sting is returned.
	 *
	 * @since 0.7.1
	 *
	 * @return string
	 */
	public function getAddress() {
		if ( is_null( $this->address ) ) {
			$this->address = '';
		}

		return $this->address;
	}


	/**
	 * Returns if there is any icon.
	 *
	 * @since 1.0
	 *
	 * @return boolean
	 */
	public function hasIcon() {
		return $this->icon !== '';
	}

	/**
	 * Sets the icon
	 *
	 * @since 0.7.2
	 *
	 * @param string $icon
	 */
	public function setIcon( $icon ) {
		$this->icon = trim( $icon );
	}

	/**
	 * Sets the group
	 *
	 * @since 2.0
	 *
	 * @param string $group
	 */
	public function setGroup( $group ) {
		$this->group = trim( $group );
	}

	/**
	 * Returns the icon.
	 *
	 * @since 0.7.2
	 *
	 * @return string
	 */
	public function getIcon() {
		return $this->icon;
	}

	/**
	 * Returns the group.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function getGroup() {
		return $this->group;
	}

	/**
	 * Returns whether Location is assigned to a group.
	 *
	 * @since 2.0
	 *
	 * @return string
	 */
	public function hasGroup() {
		return $this->group !== '';
	}

	/**
	 * @return string
	 * @since 2.0
	 */
	public function getInlineLabel(){
		return $this->inlineLabel;
	}

	/**
	 * @param $label
	 * @since 2.0
	 */
	public function setInlineLabel($label){
		$this->inlineLabel = $label;
	}

	/**
	 * @return bool
	 * @since 2.0
	 */
	public function hasInlineLabel(){
		return $this->inlineLabel !== '';
	}

	/**
	 * @return string
	 * @since 2.0
	 */
	public function getVisitedIcon() {
		return $this->visitedIcon;
	}

	/**
	 * @param $visitedIcon
	 * @since 2.0
	 */
	public function setVisitedIcon( $visitedIcon ) {
		$this->visitedIcon = trim($visitedIcon);
	}

	/**
	 * @return bool
	 * @since 2.0
	 */
	public function hasVisitedIcon(){
		return $this->visitedIcon !== '';
	}

	/**
	 * Returns an object that can directly be converted to JS using json_encode or similar.
	 *
	 * FIXME: complexity
	 *
	 * @since 1.0
	 *
	 * @param string $defText
	 * @param string $defTitle
	 * @param string $defIconUrl
	 * @param string $defGroup
	 * @param string $defInlineLabel
	 * @param string $defVisitedIcon
	 *
	 * @return array
	 */
	public function getJSONObject( $defText = '', $defTitle = '', $defIconUrl = '', $defGroup = '', $defInlineLabel = '', $defVisitedIcon = '' ) {
		$parentArray = parent::getJSONObject( $defText , $defTitle );

		$array = [
			'lat' => $this->coordinates->getLatitude(),
			'lon' => $this->coordinates->getLongitude(),
			'icon' => $this->hasIcon() ? \MapsMapper::getFileUrl( $this->getIcon() ) : $defIconUrl,
		];
		$val = $this->getAddress();
		if( $val !== '' ) {
			$array['address'] = $val;
		}
		$val = $this->hasGroup() ? $this->getGroup() : $defGroup;
		if( !empty( $val ) ) {
			$array['group'] = $val;
		}
		$val = $this->hasInlineLabel() ? $this->getInlineLabel() : $defInlineLabel;
		if( !empty( $val ) ) {
			$array['inlineLabel'] = $val;
		}
		$val = $this->hasVisitedIcon() ? $this->getVisitedIcon() : $defVisitedIcon;
		if( !empty( $val ) ) {
			$array['visitedicon'] = $val;
		}

		return array_merge( $parentArray , $array );
	}

}
