# Laravel Smart Facades :

### This package tries to add some features on the top of the current laravel's facade

[![Code Quality](https://scrutinizer-ci.com/g/imanghafoori1/laravel-smart-facades/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/imanghafoori1/laravel-smart-facades/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/imanghafoori1/laravel-smart-facades/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/imanghafoori1/laravel-smart-facades/?branch=master)
[![Build Status](https://travis-ci.org/imanghafoori1/laravel-smart-facades.svg?branch=master)](https://travis-ci.org/imanghafoori1/laravel-smart-facades)

## Installation:

```
composer require imanghafoori/laravel-smart-facades
```


### No need to have getFacadeAccessor()


#### Before
```php
use Illuminate\Support\Facades\Facade;

MyFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'some_key';
    }
}
```

#### After
```php
use Imanghafoori\SmartFacades\Facade;

MyFacade extends Facade
{
    //
}
```


### shouldProxyTo():

You can choose the low level implementation class, (so that the facades proxies the method calls to it)

like this:
```php
   // Normally, within a service provider.
if ($someCondition) {
    MyFacade::shouldProxyTo( SomeClass::class );
} else {
    MyFacade::shouldProxyTo( SomeOtherClass::class );
}
```

Note : if you invoke it twice, it will be override:
```php
MyFacade::shouldProxyTo( SomeClass1::class );
MyFacade::shouldProxyTo( SomeClass2::class ); // This wins !!
```


#### Method Hooks:
You can introduce some code (like event listeners on eloquent models) "before" and "after" a method call, remotely:

![image](https://user-images.githubusercontent.com/6961695/71646327-f100db00-2cfb-11ea-9277-1271395efca0.png)

Here we have told the system even ever the 'findUser' method was called in the system, perform a log.

### Automatic method injection when calling a method through a facade.

This adds ability to enjoy automatic method injection when calling methods on POPOs (Plain Old Php Objects) WITHOUT any performance hit when you do not need it.

Example :
```php
class Foo { ... }

class Bar
{
    // This has dependencies: "Foo", "LoggerInterface"
    public function m1 (Foo $foo, LoggerInterface $logger, string $msg)
    {
       
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

