# tank

[![Build Status](https://travis-ci.org/wpup/tank.svg?branch=master)](https://travis-ci.org/wpup/tank)  [![codecov.io](http://codecov.io/github/wpup/tank/coverage.svg?branch=master)](http://codecov.io/github/wpup/tank?branch=master)
[![License](https://img.shields.io/packagist/l/frozzare/tank.svg)](https://packagist.org/packages/frozzare/tank)

> Requires PHP 5.6

WordPress Container.

## Install

```
$ composer require frozzare/tank
```

## Container example

```php
use Frozzare\Tank\Container;

class Plugin_Loader extends Container {

  public function __construct() {
    $this->bind( 'number', 12345 );
  }

}

$loader = new Plugin_Loader;

echo $loader->make( 'number' );
// 12345
```

Check the [container source code](https://github.com/wpup/tank/blob/master/src/class-container.php) for methods that can be used.

## Service provider example

```php
use Frozzare\Tank\Container;
use Frozzare\Tank\Service_Provider;

class Example_Provider extends Service_Provider {

  public function register() {
    $this->container->bind( 'say', 'Hello!' );
  }

}

$container = new Container;
$provider  = new Example_Provider( $container );
$provider->register();

echo $container->make( 'say' );
// Hello!
```

## License

MIT © [Fredrik Forsmo](https://github.com/frozzare)
