<?php

namespace Frozzare\Tests\Tank;

use Frozzare\Tank\Container;
use Frozzare\Tank\Service_Provider;

class Service_Provider_Test extends \PHPUnit_Framework_TestCase {

	public function test_service_provider() {
        $container = new Container;
        $provider  = new Service_Provider_Stub( $container );

        $provider->register();

        $this->assertEquals( 'Fredrik', $container->make( 'name' ) );
        $this->assertEquals( 'Fredrik', $container['name'] );
    }

    public function test_service_provider_2() {
        $container = new Container_Stub;
        $provider  = new Service_Provider_Stub( $container );

        $provider->register();

        $this->assertEquals( 'Fredrik', $container->make( 'name' ) );
        $this->assertEquals( 'Fredrik', $container['name'] );
    }

    public function test_service_provider_3() {
        $container = new Container_Stub;
        $provider  = new Service_Provider_Stub( $container );

        $provider->register();

        $this->assertEquals( 'Fredrik', $container->make( 'name' ) );
        $this->assertEquals( 'Fredrik', $container['name'] );

        try {
            $provider->test();
        } catch ( \BadMethodCallException $e ) {
            $this->assertEquals( 'Call to undefined method `test`', $e->getMessage() );
        }
    }

}

class Service_Provider_Stub extends Service_Provider {

    public function register() {
        $this->container->bind( 'name', 'Fredrik' );
    }

}

class Container_Stub extends Container {

    public function __construct() {
      $this->bind( 'number', 12345 );
    }

}
