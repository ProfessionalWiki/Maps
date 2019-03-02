<?php

namespace Maps;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
final class MappingServices {

	/**
	 * @var MappingService[]
	 */
	private $nameToServiceMap = [];

	/**
	 * @var string Name of the default service, which is used as fallback
	 */
	private $defaultService;

	/**
	 * @param string[] $availableServices
	 * @param string $defaultService
	 * @param MappingService ...$services
	 * @throws \InvalidArgumentException
	 */
	public function __construct( array $availableServices, string $defaultService, MappingService ...$services ) {
		$this->defaultService = $defaultService;

		foreach ( $services as $service ) {
			if ( in_array( $service->getName(), $availableServices ) ) {
				$this->nameToServiceMap[$service->getName()] = $service;

				foreach ( $service->getAliases() as $alias ) {
					$this->nameToServiceMap[$alias] = $service;
				}
			}
		}

		if ( !$this->nameIsKnown( $defaultService ) ) {
			throw new \InvalidArgumentException( 'The default mapping service needs to be available' );
		}
	}

	/**
	 * @param string $name Name or alias of a service
	 * @return bool
	 */
	public function nameIsKnown( string $name ): bool {
		return array_key_exists( $name, $this->nameToServiceMap );
	}

	/**
	 * @param string $name Name or alias of a service
	 * @return MappingService
	 * @throws \OutOfBoundsException
	 */
	public function getService( string $name ): MappingService {
		if ( !$this->nameIsKnown( $name ) ) {
			throw new \OutOfBoundsException();
		}

		return $this->nameToServiceMap[$name];
	}

	/**
	 * @param string $name Name or alias of a service
	 * @return MappingService
	 */
	public function getServiceOrDefault( string $name ): MappingService {
		if ( $this->nameIsKnown( $name ) ) {
			return $this->nameToServiceMap[$name];
		}

		return $this->nameToServiceMap[$this->defaultService];
	}

	public function getAllNames(): array {
		return array_keys( $this->nameToServiceMap );
	}

}
