<?php

namespace Tank;

use ArrayAccess;
use InvalidArgumentException;

/**
 * Container class.
 *
 * @package Tank
 */
class Container implements ArrayAccess {

	/**
	 * The keys holder.
	 *
	 * @var array
	 */
	protected $keys = [];

	/**
	 * The values holder.
	 *
	 * @var array
	 */
	protected $values = [];

	/**
	 * Set a parameter or an object.
	 *
	 * @param string $id
	 * @param mixed $value
	 * @param bool $shared
	 */
	public function bind( $id, $value = null, $shared = false ) {
		if ( ! $this->is_shareable( $id ) ) {
			throw new \Exception( sprintf( 'Identifier `%s` is a singleton and cannot be rebind', $id ) );
		}

		if ( ! $value instanceof Closure ) {
			$value = $this->get_closure( $value, $shared );
		}

		$this->values[$id] = compact( 'value', 'shared' );
		$this->keys[$id] = true;
	}

	/**
	 * Check if identifier is set or not.
	 *
	 * @param string $id
	 *
	 * @return bool
	 */
	public function exists( $id ) {
		return isset( $this->keys[$id] );
	}

	/**
	 * Check if the container item is shareable or not.
	 *
	 * @param string $id
	 *
	 * @return bool
	 */
	protected function is_shareable( $id ) {
		if ( ! isset( $this->keys[$id] ) ) {
			return true;
		}

		return $this->values[$id]['shared'] === false;
	}

	/**
	 * Get closure function.
	 *
	 * @param mixed $value
	 * @param bool $shared
	 *
	 * @return mixed
	 */
	protected function get_closure( $value, $shared = false ) {
		return function () use( $value, $shared ) {
			return $value;
		};
	}

	/**
	 *
	 * @param string $id
	 *
	 * @return mixed
	 */
	public function make( $id ) {
		if ( ! isset( $this->keys[$id] ) ) {
			throw new \InvalidArgumentException( sprintf( 'Identifier `%s` is not defined', $id ) );
		}

		$value  = $this->values[$id];
		$shared = $value['shared'];
		$value  = $value['value'];

		return call_user_func( $value );
	}

	/**
	 * Set a parameter or an object.
	 *
	 * @param string $id
	 * @param mixed $value
	 */
	public function singleton( $id, $value ) {
		return $this->bind( $id, $value, true );
	}

	/**
	 * Unset value by identifier.
	 *
	 * @param string $id
	 */
	public function remove( $id ) {
		unset( $this->keys[$id], $this->values[$id] );
	}

	/**
	 * Check if identifier is set or not.
	 *
	 * @param string $id
	 *
	 * @return bool
	 */
	// @codingStandardsIgnoreStart
	public function offsetExists( $id ) {
	// @codingStandardsIgnoreEnd
		return $this->exists( $id );
	}

	/**
	 * Get value by identifier.
	 *
	 * @param string $id
	 *
	 * @return mixed
	 */
	// @codingStandardsIgnoreStart
	public function offsetGet( $id ) {
	// @codingStandardsIgnoreEnd
		return $this->make( $id );
	}

	/**
	 * Set a parameter or an object.
	 *
	 * @param string $id
	 * @param mixed $value
	 */
	// @codingStandardsIgnoreStart
	public function offsetSet( $id, $value ) {
	// @codingStandardsIgnoreEnd
		$this->bind( $id, $value );
	}

	/**
	 * Unset value by identifier.
	 *
	 * @param string $id
	 */
	// @codingStandardsIgnoreStart
	public function offsetUnset( $id ) {
	// @codingStandardsIgnoreEnd
		$this->remove( $id );
	}
}
