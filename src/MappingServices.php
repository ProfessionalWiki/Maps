<?php

namespace Maps;

use MWException;

/**
 * Class for interaction with MappingService objects.
 *
 * @since 0.6.6
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
final class MappingServices {

	/**
	 * Associative array containing service identifiers as keys and the names
	 * of service classes as values.
	 *
	 * @var string[]
	 */
	private static $registeredServices = [];

	/**
	 * Associative with service identifiers as keys containing instances of
	 * the mapping service classes.
	 *
	 * Note: This list only contains the instances, so is not to be used for
	 * looping over all available services, as not all of them are guaranteed
	 * to have an instance already, use $registeredServices for this purpose.
	 *
	 * @var MappingService[]
	 */
	private static $services = [];

	/**
	 * Registers a service class linked to an identifier.
	 */
	public static function registerService( string $serviceIdentifier, string $serviceClassName ) {
		self::$registeredServices[$serviceIdentifier] = $serviceClassName;
	}

	/**
	 * Returns the instance of a service class. This method takes
	 * care of creating the instance if this is not done yet.
	 *
	 * @throws MWException
	 */
	public static function getServiceInstance( string $serviceIdentifier ): MappingService {
		if ( !array_key_exists( $serviceIdentifier, self::$services ) ) {
			if ( array_key_exists( $serviceIdentifier, self::$registeredServices ) ) {
				$service = new self::$registeredServices[$serviceIdentifier]( $serviceIdentifier );

				if ( $service instanceof MappingService ) {
					self::$services[$serviceIdentifier] = $service;
				} else {
					throw new MWException(
						'The service object linked to service identifier ' . $serviceIdentifier . ' does not implement iMappingService.'
					);
				}
			} else {
				throw new MWException(
					'There is no service object linked to service identifier ' . $serviceIdentifier . '.'
				);
			}
		}

		return self::$services[$serviceIdentifier];
	}

	public static function getMainServiceName( string $serviceName ): string {
		if ( !array_key_exists( $serviceName, self::$services ) ) {
			foreach ( array_keys( self::$registeredServices ) as $serviceIdentifier ) {
				$service = self::getServiceInstance( $serviceIdentifier );

				if ( $service->hasAlias( $serviceName ) ) {
					return $service->getName();
				}
			}
		}

		return $serviceName;
	}

}
