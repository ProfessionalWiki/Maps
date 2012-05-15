<?php
class MapsParamPolygon extends MapsParamLine {


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
		$polygonCoords = explode(':',array_shift($parts));

		$value = new MapsPolygon($polygonCoords);
		$linkOrTitle =  array_shift($parts);
		if($link = MapsUtils::isLinkParameter($linkOrTitle)){
			if(MapsUtils::isValidURL($link)){
				$value->setLink($link);
			}else{
				$title = Title::newFromText($link);
				$value->setLink($title->getFullURL());
			}
		}else{
			//create bubble data
			if($linkOrTitle){
				$value->setTitle($linkOrTitle);
			}
			if($text = array_shift($parts)){
				$value->setText($text);
			}
		}

		if($color = array_shift($parts)){
			$value->setStrokeColor($color);
		}

		if($opacity = array_shift($parts)){
			$value->setStrokeOpacity($opacity);
		}

		if($weight = array_shift($parts)){
			$value->setStrokeWeight($weight);
		}

		if($fillColor = array_shift($parts)){
			$value->setFillColor($fillColor);
		}

		if($fillOpacity = array_shift($parts)){
			$value->setFillOpacity($fillOpacity);
		}

		if($visibleOnHover = array_shift($parts)){
			$value->setOnlyVisibleOnHover(filter_var($parts[8], FILTER_VALIDATE_BOOLEAN));
		}

		$value = $value->getJSONObject();
	}
}