<?php
/**
 * Class that holds metadata on polygons made up by locations on map.
 *
 * @since 0.7.2
 *
 * @file Maps_Polygon.php
 * @ingroup Maps
 *
 * @licence GNU GPL v3
 * @author Kim Eik < kim@heldig.org >
 */
class MapsPolygon extends MapsLine{


	/**
	 * @var
	 */
	protected $fillColor;

	/**
	 * @var
	 */
	protected $fillOpacity;

	/**
	 * @var
	 */
	protected $onlyVisibleOnHover = false;


	public function getJSONObject( $defText = '', $defTitle = '') {
		$parentArray = parent::getJSONObject($defText,$defTitle);
		$array = array(
			'fillColor' => $this->hasFillColor() ? $this->getFillColor() : '#FF0000',
			'fillOpacity' => $this->hasFillOpacity() ? $this->getFillOpacity() : 0.5,
			'onlyVisibleOnHover' => $this->isOnlyVisibleOnHover(),
		);
		return array_merge($parentArray,$array);
	}

	private function hasFillColor(){
		return $this->fillColor !== '';
	}

	private function hasFillOpacity(){
		return $this->fillOpacity !== '';
	}

	/**
	 * @return
	 */
	public function getFillOpacity()
	{
		return $this->fillOpacity;
	}

	/**
	 * @param  $fillOpacity
	 */
	public function setFillOpacity($fillOpacity)
	{
		$this->fillOpacity = $fillOpacity;
	}

	/**
	 * @return
	 */
	public function getFillColor()
	{
		return $this->fillColor;
	}

	/**
	 * @param  $fillColor
	 */
	public function setFillColor($fillColor)
	{
		$this->fillColor = $fillColor;
	}

	/**
	 * @param $visible
	 */
	public function setOnlyVisibleOnHover($visible){
		$this->onlyVisibleOnHover = $visible;
	}

	/**
	 * @return mixed
	 */
	public function isOnlyVisibleOnHover(){
		return $this->onlyVisibleOnHover;
	}

}
