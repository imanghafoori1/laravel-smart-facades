# Laravel Smart Facades :

[![Code Coverage](https://scrutinizer-ci.com/g/imanghafoori1/laravel-smart-facades/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/imanghafoori1/laravel-smart-facades/?branch=master)

## Installation:

```
composer require imanghafoori/laravel-smart-facades
```

This package tries to add some features on the top of the current laravel's facade feature so you can get more advantages out of them.

### Automatic method injection when calling a method through a facade.

This adds ability to enjoy automatic method injection when calling methods on POPOs (Plain Old Php Objects) WITHOUT any performance hit when you do not need it.

In fact, it only comes into play if there is any `TypeError` thrown from a Facade call, trying to handle it gracefully by providing injected dependencies right within the method input,
and retry the method call once again. (in the catch block)
And would be happy to know if something is missing...

Example :
```php
class Foo { ... }

class Bar {
    public function m1 (Foo $foo, LoggerInterface $logger, string $msg) {
        //...
    }
}
```

Calling `Bar` through a Facade :

Before : 
```php
MyFacade::m1(resolve(Foo::class), resolve(LoggerInterface::class), 'hey there !'); 
```

After :
```php
 // This will work and $foo, $logger would be auto-injected for us.

MyFacade::m1('hey there !');          // normal facade

// or you may want to provide some dependecies your self :
\Facades\Bar::m1(new Foo('hey man!'), 'hey there !');   //Now only the Logger is injected
```

and as always, 
we may define the facade class like this:

```php

use Imanghafoori\SmartFacades\Facade;

MyFacade extends Facade {
    protected static function getFacadeAccessor () {
        return Bar::class;
    }
}
```
