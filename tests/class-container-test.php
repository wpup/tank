<?php

namespace Tank\Tests;

class Container_Test extends \WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->container = new \Tank\Container;
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->container );
	}

	public function test_make_not_defined() {
		$this->setExpectedException( 'InvalidArgumentException', 'Identifier `fredrik` is not defined' );
		$this->container->make( 'fredrik' );
	}

	public function test_bind() {
		$this->container->bind( 'name', 'Fredrik' );
		$this->assertEquals( 'Fredrik', $this->container->make( 'name' ) );
	}

	public function test_closure() {
		$this->container->bind( 'num', 123 );
		$this->container->bind( 'num2', function ( $c ) {
			return $c->make( 'num' );
		} );
		$this->container->bind( 'num3', function ( $c ) {
			return $c->make( 'num2' );
		} );
		$this->assertEquals( 123, $this->container->make( 'num2' ) );
		$this->assertEquals( 123, $this->container->make( 'num3' ) );
	}

	public function test_exists() {
		$this->container->bind( 'name', 'Fredrik' );
		$this->assertTrue( $this->container->exists( 'name' ) );
	}

	public function test_remove() {
		$this->container['plugin'] = 'Papi';
		$this->container->remove( 'plugin' );
		$this->assertFalse( isset( $this->container['plugin'] ) );
	}

	public function test_singleton() {
		$this->container->singleton( 'Singleton', 'App' );
		$this->assertEquals( 'App', $this->container->make( 'Singleton' ) );

		try {
			$this->container->bind( 'Singleton', 'App' );
		} catch ( \Exception $e ) {
			$this->assertNotEmpty( $e->getMessage() );
		}

		try {
			$this->container->singleton( 'Singleton', 'App' );
		} catch ( \Exception $e ) {
			$this->assertNotEmpty( $e->getMessage() );
		}

		$this->assertEquals( 'App', $this->container->make( 'Singleton' ) );
		$this->assertTrue( $this->container->is_singleton( 'Singleton' ) );

		try {
			$this->container->is_singleton( true );
		} catch ( \Exception $e ) {
			$this->assertEquals( 'Invalid argument. Must be string.', $e->getMessage() );
		}
	}

	public function test_offset_exists() {
		$this->container->bind( 'name', 'Fredrik' );
		$this->assertTrue( isset( $this->container['name'] ) );
	}

	public function test_offset_get() {
		$this->container->bind( 'name', 'Fredrik' );
		$this->assertEquals( 'Fredrik', $this->container['name'] );
	}

	public function test_offset_set() {
		$this->container['plugin'] = 'Papi';
		$this->assertEquals( 'Papi', $this->container['plugin'] );
	}

	public function test_offset_unset() {
		$this->container['plugin'] = 'Papi';
		unset( $this->container['plugin'] );
		$this->assertFalse( isset( $this->container['plugin'] ) );
	}

}
