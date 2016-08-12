<?php

namespace Frozzare\Tank;

use BadMethodCallException;

abstract class Service_Provider {

	/**
	 * The container.
	 *
	 * @var \Frozzare\Tank\Container
	 */
	protected $container;

	/**
	 * The constructor.
	 *
	 * @param \Frozzare\Tank\Container $container
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
	}

	/**
	 * Registers service provider.
	 */
	abstract public function register();

	/**
	 * Dynamically handle missing method calls.
	 *
	 * @param  string $method
	 * @param  array  $parameters
	 *
	 * @throws \BadMethodCallException
	 *
	 * @return mixed
	 */
	public function __call( $method, $parameters ) {
		throw new BadMethodCallException( "Call to undefined method `{$method}`" );
	}
}
