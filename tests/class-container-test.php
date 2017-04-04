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
		require_once __DIR__ . '/fixtures/class-pack.php';
		$this->pack = new \Pack\Pack;
	}

	public function tearDown() {
		parent::tearDown();
		unset( $this->container, $this->pack );
	}

	public function test_make_not_defined() {
		$this->setExpectedException( 'InvalidArgumentException', 'Identifier `fredrik` is not defined' );
		$this->container->make( 'fredrik' );
	}

	public function test_bind() {
		$this->container->bind( 'name', 'Fredrik' );
		$this->assertSame( 'Fredrik', $this->container->make( 'name' ) );
	}

	public function test_bind_if() {
		$this->container->bind_if( 'name', 'Fredrik' );
		$this->assertSame( 'Fredrik', $this->container->make( 'name' ) );
		$this->container->bind_if( 'name', 'Elli' );
		$this->assertSame( 'Fredrik', $this->container->make( 'name' ) );
	}

	public function test_bound() {
		$this->container->bind( 'name', 'Fredrik' );
		$this->assertTrue( $this->container->bound( 'name' ) );
	}

	public function test_closure() {
		$this->container->bind( 'num', 123 );
		$this->container->bind( 'num2', function ( $c ) {
			return $c->make( 'num' );
		} );
		$this->container->bind( 'num3', function ( $c ) {
			return $c->make( 'num2' );
		} );
		$this->assertSame( 123, $this->container->make( 'num2' ) );
		$this->assertSame( 123, $this->container->make( 'num3' ) );
	}

	public function test_closure_injection() {
		$this->container->bind( 'num', 123 );
		$this->container->bind( 'num2', function ( Container $c ) {
			return $c->make( 'num' );
		} );
		$this->container->bind( 'num3', function ( Container $c, $num ) {
			return $c->make( 'num2' ) + $num;
		} );
		$this->assertSame( 123, $this->container->make( 'num2' ) );
		$this->assertSame( 124, $this->container->make( 'num3', [1] ) );
		$this->container->bind( new Test );
		$this->container->bind( 'test-class', function ( Test $test ) {
			return $test->value();
		} );
		$this->assertSame( 'Test class', $this->container->make( 'test-class' ) );
	}

	public function test_flush() {
		$this->container->bind( 'name', 'Fredrik' );
		$this->assertTrue( $this->container->bound( 'name' ) );
		$this->container->flush();
		$this->assertFalse( $this->container->bound( 'name' ) );
	}

	public function test_instance() {
		$this->assertNull( Container::get_instance() );
		Container::set_instance( $this->container );
		$this->assertSame( $this->container, Container::get_instance() );
	}

	public function test_get_bindings() {
		$this->assertEmpty( $this->container->get_bindings() );
		$this->container->bind( 'name', 'Fredrik' );
		$bindings = $this->container->get_bindings();
		$this->assertSame( 'Fredrik', $bindings['name']['closure']() );
	}

	public function test_remove() {
		$this->container['plugin'] = 'Papi';
		$this->container->remove( 'plugin' );
		$this->assertFalse( isset( $this->container['plugin'] ) );
	}

	public function test_set_instance() {
		$container = new Container;
		$this->assertTrue( $container->get_instance() instanceof Container );
		$pack = new \Pack\Pack;
		$pack->bind( 'name', 'Fredrik' );
		$container->set_instance( $pack );
		$this->assertTrue( $container->get_instance() instanceof $pack );
	}

	public function test_singleton() {
		$this->container->singleton( 'Singleton', 'App' );
		$this->assertSame( 'App', $this->container->make( 'Singleton' ) );

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

		$this->assertSame( 'App', $this->container->make( 'Singleton' ) );
		$this->assertTrue( $this->container->is_singleton( 'Singleton' ) );

		try {
			$this->container->is_singleton( true );
		} catch ( \Exception $e ) {
			$this->assertSame( 'Invalid argument. Must be string.', $e->getMessage() );
		}
	}

	public function test_bind_class_string() {
		$container = new Container;
		$container->bind( 'pack', '\\Pack\\Pack' );
		$this->assertInstanceOf( '\\Pack\\Pack', $container->make( 'pack' ) );
	}

	public function test_offset_exists() {
		$this->container->bind( 'name', 'Fredrik' );
		$this->assertTrue( isset( $this->container['name'] ) );
	}

	public function test_offset_get() {
		$this->container->bind( 'name', 'Fredrik' );
		$this->assertSame( 'Fredrik', $this->container['name'] );
	}

	public function test_offset_set() {
		$this->container['plugin'] = 'Papi';
		$this->assertSame( 'Papi', $this->container['plugin'] );
	}

	public function test_offset_unset() {
		$this->container['plugin'] = 'Papi';
		unset( $this->container['plugin'] );
		$this->assertFalse( isset( $this->container['plugin'] ) );
	}
}
