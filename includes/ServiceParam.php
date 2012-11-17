<?php

namespace Maps;
use MapsMappingServices;
use ParamProcessor\IParam;
use ParamProcessor\StringParam;

/**
 * Parameter definition for mapping service parameters.
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
 * @file
 * @since 2.0
 *
 * @ingroup Maps
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ServiceParam extends StringParam {

	/**
	 * The mapping feature. Needed to determine which services are available.
	 *
	 * @since 2.0
	 *
	 * @var string
	 */
	protected $feature;

	/**
	 * @see ParamDefinition::postConstruct()
	 *
	 * @since 2.0
	 */
	protected function postConstruct() {
		global $egMapsDefaultService, $egMapsDefaultServices;

		$this->setDefault( array_key_exists( $this->feature, $egMapsDefaultServices ) ? $egMapsDefaultServices[$this->feature] : $egMapsDefaultService );

		// FIXME
		$this->allowedValues = MapsMappingServices::getAllServiceValues();
	}

	/**
	 * @see ParamDefinition::formatValue()
	 *
	 * @since 2.0
	 *
	 * @param $value mixed
	 * @param $param IParam
	 * @param $definitions array of IParamDefinition
	 * @param $params array of IParam
	 *
	 * @return mixed
	 */
	protected function formatValue( $value, IParam $param, array &$definitions, array $params ) {
		// Make sure the service is valid.
		$value = MapsMappingServices::getValidServiceName( $value, $this->feature );

		// Get the service object so the service specific parameters can be retrieved.
		$serviceObject = MapsMappingServices::getServiceInstance( $value );

		// Add the service specific service parameters.
		$serviceObject->addParameterInfo( $definitions );

		$definitions = \ParamDefinition::getCleanDefinitions( $definitions );

		return $value;
	}

	/**
	 * @see ParamDefinition::setArrayValues()
	 *
	 * @since 2.0
	 *
	 * @param array $param
	 */
	public function setArrayValues( array $param ) {
		parent::setArrayValues( $param );

		if ( array_key_exists( 'feature', $param ) ) {
			$this->setFeature( $param['feature'] );
		}
	}

	/**
	 * Sets the mapping feature.
	 *
	 * @since 2.0
	 *
	 * @param string $feature
	 */
	public function setFeature( $feature ) {
		$this->feature = $feature;
	}

}
