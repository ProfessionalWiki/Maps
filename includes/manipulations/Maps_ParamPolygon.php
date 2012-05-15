<?php
class MapsParamPolygon extends ItemParameterManipulation {

	protected $metaDataSeparator;

	public function __construct( $metaDataSeparator ) {
		parent::__construct();

		$this->metaDataSeparator = $metaDataSeparator;
	}

	/**
	 * Manipulate an actual value.
	 *
	 * @param string $value
	 * @param Parameter $parameter
	 * @param array $parameters
	 *
	 * @since 0.4
	 *
	 * @return mixed
	 */
	public function doManipulation(&$value, Parameter $parameter, array &$parameters)
	{
		$parts = explode($this->metaDataSeparator,$value);
		$polygonCoords = explode(':',$parts[0]);

		$value = new MapsPolygon($polygonCoords);
		$value->setTitle( isset($parts[1]) ? $parts[1] : '' );
		$value->setText( isset($parts[2]) ? $parts[2] : '' );
		$value->setStrokeColor( isset($parts[3]) ? $parts[3] : '' );
		$value->setStrokeOpacity( isset($parts[4]) ? $parts[4] : '' );
		$value->setStrokeWeight( isset($parts[5]) ? $parts[5] : '' );
		$value->setFillColor(isset($parts[6]) ? $parts[6] : '');
		$value->setFillOpacity(isset($parts[7]) ? $parts[7] : '');

		if(isset($parts[8])){
			$value->setOnlyVisibleOnHover(filter_var($parts[8], FILTER_VALIDATE_BOOLEAN));
		}

		$value = $value->getJSONObject();
	}
}