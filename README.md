# Laravel Smart Facades :

[![Code Quality](https://scrutinizer-ci.com/g/imanghafoori1/laravel-smart-facades/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/imanghafoori1/laravel-smart-facades/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/imanghafoori1/laravel-smart-facades/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/imanghafoori1/laravel-smart-facades/?branch=master)
[![Build Status](https://travis-ci.org/imanghafoori1/laravel-smart-facades.svg?branch=master)](https://travis-ci.org/imanghafoori1/laravel-smart-facades)

## Installation:

```
composer require imanghafoori/laravel-smart-facades
```

This package tries to add some features on the top of the current laravel's facade feature so you can get more advantages out of them.

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

### No need to have getFacadeAccessor()


#### Before
```php
MyFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return SomeClass1::class;
    }
}
```

#### After
```php
MyFacade extends Facade
{
    //
}
```

#### shouldProxyTo():

You can choose the low level implementation class which is behind your facade like this :

```php
MyFacade::shouldProxyTo( SomeClass1::class ); // Within a service provider.
```

Note : if you invoke it again, it will be overriden:
```php
MyFacade::shouldProxyTo( SomeClass1::class );
MyFacade::shouldProxyTo( SomeClass2::class ); // This wins !!
```

#### Method Hooks:
You can introduce some code (like event listeners on eloquent models) before and after a method on a facade is called :

![image](https://user-images.githubusercontent.com/6961695/71644014-c9e3e280-2cd6-11ea-8ebb-38009f6e45cf.png)
