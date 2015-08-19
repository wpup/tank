<?php

namespace Frozzare\Tests\Tank;

use Frozzare\Tank\Container;

class Test {
	public function value() {
		return 'Test class';
	}
}

class Container_Test extends \PHPUnit_Framework_TestCase {

	public function setUp() {
		parent::setUp();
		$this->container = new Container;
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

	public function test_closure_injection() {
		$this->container->bind( 'num', 123 );
		$this->container->bind( 'num2', function ( Container $c ) {
			return $c->make( 'num' );
		} );
		$this->container->bind( 'num3', function ( Container $c, $num ) {
			return $c->make( 'num2' ) + $num;
		} );
		$this->assertEquals( 123, $this->container->make( 'num2' ) );
		$this->assertEquals( 124, $this->container->make( 'num3', [1] ) );
		$this->container->bind( new Test );
		$this->container->bind( 'test-class', function ( Test $test ) {
			return $test->value();
		} );
		$this->assertEquals( 'Test class', $this->container->make( 'test-class' ) );
	}

	public function test_exists() {
		$this->container->bind( 'name', 'Fredrik' );
		$this->assertTrue( $this->container->exists( 'name' ) );
	}

	public function test_instance() {
		$this->assertNull( Container::get_instance() );
		Container::set_instance( $this->container );
		$this->assertEquals( $this->container, Container::get_instance() );
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
