<?php
/**
 * Class that holds metadata on lines made up by locations on map.
 *
 * @since 0.7.2
 *
 * @file Maps_Line.php
 * @ingroup Maps
 *
 * @licence GNU GPL v3
 * @author Kim Eik < kim@heldig.org >
 */
class MapsLine{


    /**
     * @var
     */
    protected $lineCoords;

    /**
     * @var
     */
    protected $title;

    /**
     * @var
     */
    protected $text;

    /**
     * @var
     */
    protected $strokeColor;
    /**
     * @var
     */
    protected $strokeOpacity;
    /**
     * @var
     */
    protected $strokeWeight;

    /**
     *
     */
    function __construct($coords)
    {
        $this->setLineCoords($coords);
    }

    /**
     * @param \text $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return \text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param \title $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return \title
     */
    public function getTitle()
    {
        return $this->title;
    }

    protected function setLineCoords($lineCoords)
    {
        foreach($lineCoords as $lineCoord){
            $this->lineCoords[] = new MapsLocation($lineCoord);
        }
    }

    protected function getLineCoords()
    {
        return $this->lineCoords;
    }

    public function getJSONObject( $defText = '', $defTitle = '') {
        $posArray = array();
        foreach ($this->lineCoords as $mapLocation){
            $posArray[] = array(
                'lat' => $mapLocation->getLatitude(),
                'lon' => $mapLocation->getLongitude()
            );
        }

        return array(
            'pos' => $posArray,
            'text' => $this->hasText() ? $this->getText() : $defText,
            'title' => $this->hasTitle() ? $this->getTitle() : $defTitle,
            'strokeColor' => $this->hasStrokeColor() ? $this->getStrokeColor() : '#FF0000',
            'strokeOpacity' => $this->hasStrokeOpacity() ? $this->getStrokeOpacity() : '1',
            'strokeWeight' => $this->hasStrokeWeight() ? $this->getStrokeWeight() : '2'
        );
    }


    /**
     * @param  $strokeColor
     */
    public function setStrokeColor($strokeColor)
    {
        $this->strokeColor = $strokeColor;
    }

    /**
     * @return
     */
    public function getStrokeColor()
    {
        return $this->strokeColor;
    }

    /**
     * @param  $strokeOpacity
     */
    public function setStrokeOpacity($strokeOpacity)
    {
        $this->strokeOpacity = $strokeOpacity;
    }

    /**
     * @return
     */
    public function getStrokeOpacity()
    {
        return $this->strokeOpacity;
    }

    /**
     * @param  $strokeWeight
     */
    public function setStrokeWeight($strokeWeight)
    {
        $this->strokeWeight = $strokeWeight;
    }

    /**
     * @return
     */
    public function getStrokeWeight()
    {
        return $this->strokeWeight;
    }


    public function hasText(){
        return $this->text !== '';
    }

    public function hasTitle(){
        return $this->title !== '';
    }

    public function hasStrokeColor(){
        return $this->strokeColor !== '';
    }

    public function hasStrokeOpacity(){
        return $this->strokeOpacity !== '';
    }


    public function hasStrokeWeight(){
        return $this->strokeWeight !== '';
    }
}
