<?php

namespace Maps;

use DataValues\LatLongValue;
use MWException;

/**
 * Class representing a collection of LatLongValue objects forming a line.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @since 3.0
 *
 * @ingroup Maps
 *
 * @licence GNU GPL v2+
 * @author Kim Eik < kim@heldig.org >
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Line extends \MapsBaseStrokableElement {

	/**
	 * @since 3.0
	 *
	 * @var LatLongValue[]
	 */
	protected $coordinates;

	/**
	 * @since 3.0
	 *
	 * @param LatLongValue[] $coordinates
	 *
	 * @throws MWException
	 */
	public function __construct( array $coordinates = array() ) {
		foreach ( $coordinates as $coordinate ) {
			if ( !( $coordinate instanceof LatLongValue ) ) {
				throw new MWException( 'Can only construct Maps\Line with DataValues\LatLongValue objects' );
			}
		}

		$this->coordinates = $coordinates;

		parent::__construct();
	}

	/**
	 * @since 3.0
	 *
	 * @return LatLongValue[]
	 */
	public function getLineCoordinates() {
		return $this->coordinates;
	}

	/**
	 * @since 3.0
	 *
	 * @param string $defText
	 * @param string $defTitle
	 *
	 * @return array
	 */
	public function getJSONObject( $defText = '' , $defTitle = '' ) {
		$parentArray = parent::getJSONObject( $defText , $defTitle );
		$posArray = array();

		foreach ( $this->coordinates as $mapLocation ) {
			$posArray[] = array(
				'lat' => $mapLocation->getLatitude() ,
				'lon' => $mapLocation->getLongitude()
			);
		}

		$posArray = array( 'pos' => $posArray );

		return array_merge( $parentArray , $posArray );
	}

}
