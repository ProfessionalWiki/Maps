<?php

/**
 * Form input hook that adds an OSM map format to Semantic Forms
 *
 * @file SM_OSMFormInput.php
 * @ingroup SMOSM
 * 
 * @author Jeroen De Dauw
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class SMOSMFormInput extends SMFormInput {
	
	public $serviceName = MapsOSM::SERVICE_NAME;	
	
	/**
	 * @see MapsMapFeature::setMapSettings()
	 *
	 */
	protected function setMapSettings() {
		global $egMapsOSMZoom, $egMapsOSMPrefix;
		
		$this->elementNamePrefix = $egMapsOSMPrefix;	

		$this->earthZoom = 1;

        $this->defaultZoom = $egMapsOSMZoom;	
	}	
	
	/**
	 * @see MapsMapFeature::addFormDependencies()
	 * 	  
	 */
	protected function addFormDependencies() {
		global $wgJsMimeType;
		global $smgScriptPath, $smgOSMFormsOnThisPage, $smgStyleVersion;
		
		MapsOSM::addOSMDependencies($this->output);
		
		if (empty($smgOSMFormsOnThisPage)) {
			$smgOSMFormsOnThisPage = 0;
			$this->output .= "<script type='$wgJsMimeType' src='$smgScriptPath/OpenStreetMap/SM_OSMFunctions.js?$smgStyleVersion'></script>";
		}
	}	
	
	/**
	 * @see MapsMapFeature::doMapServiceLoad()
	 *
	 */
	protected function doMapServiceLoad() {
		global $egOSMMapsOnThisPage, $smgOSMFormsOnThisPage;
		
		self::addFormDependencies();
		
		$egOSMMapsOnThisPage++;	
		$smgOSMFormsOnThisPage++;

		$this->elementNr = $egOSMMapsOnThisPage;
	}	
	
	/**
	 * @see MapsMapFeature::addSpecificMapHTML()
	 *
	 */
	protected function addSpecificMapHTML() {
		global $wgJsMimeType;		
		
		$controlItems = MapsMapper::createJSItemsString(explode(',', $this->controls));
		
		$this->output .= <<<EOT
			<script type='$wgJsMimeType'>
			makeOSMFormInput(
				'$this->mapName',
				'$this->coordsFieldName',
				{
				mode: 'osm-wm',
				layer: 'osm-like',
				locale: '$this->lang',				
				lat: $this->centre_lat,
				lon: $this->centre_lon,
				zoom: $this->zoom,
				width: $this->width,
				height: $this->height,
				controls: [$controlItems],
				}
				);
				
			
			</script>
		
				<div id='$this->mapName' class='map' style='width:{$this->width}px; height:{$this->height}px;'>
					<script type='$wgJsMimeType'>slippymaps['$this->mapName'].init();</script>
				</div>
EOT;
	}	
	
	/**
	 * @see SMFormInput::manageGeocoding()
	 *
	 */
	protected function manageGeocoding() {	
		$this->enableGeocoding = false;
	}
	
}
