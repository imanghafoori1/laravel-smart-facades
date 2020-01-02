<h1 align="center">
🍄 Laravel Smart Facades 🍄
</h1>
<h3 align="center">
This package tries to add some features on the top of the current laravel's facade
</h3>

<p align="center">
Built with :heart: for every smart laravel developer

</br>
    
<a href="https://scrutinizer-ci.com/g/imanghafoori1/laravel-smart-facades"><img src="https://img.shields.io/scrutinizer/g/imanghafoori1/laravel-smart-facades.svg?style=flat-square" alt="Quality Score"></img></a>
[![Code Quality](https://scrutinizer-ci.com/g/imanghafoori1/laravel-smart-facades/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/imanghafoori1/laravel-smart-facades/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/imanghafoori1/laravel-smart-facades/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/imanghafoori1/laravel-smart-facades/?branch=master)
[![Build Status](https://travis-ci.org/imanghafoori1/laravel-smart-facades.svg?branch=master)](https://travis-ci.org/imanghafoori1/laravel-smart-facades)

</p>





## :flashlight: Installation:

```
composer require imanghafoori/laravel-smart-facades
```


### :wrench: No need to have getFacadeAccessor()


#### Before:
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

#### After:
```php
use Imanghafoori\SmartFacades\Facade;

MyFacade extends Facade
{
    //
}
```


### :wrench: shouldProxyTo():

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

Note : If you invoke it twice, it will override:
```php
MyFacade::shouldProxyTo( SomeClass1::class );
MyFacade::shouldProxyTo( SomeClass2::class ); // This wins !!!
```


### :wrench: Method Hooks:

You can introduce some code "before" and "after" a method call, remotely: (like event listeners on eloquent models) 

![image](https://user-images.githubusercontent.com/6961695/71646327-f100db00-2cfb-11ea-9277-1271395efca0.png)

Here we have told the system evenever the 'MyFacade::findUser($id)' method was called in the system, perform a log.

#### :wrench: Automatic method injection when calling a method through a facade.

This adds ability to enjoy automatic method injection when calling methods on POPOs (Plain Old Php Objects) WITHOUT any performance hit when you do not need it.

#### Example:
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

Before:
```php
MyFacade::m1(resolve(Foo::class), resolve(LoggerInterface::class), 'hey there !'); 
```

After:
```php
 // This will work and $foo, $logger would be auto-injected for us.

MyFacade::m1('hey there !');          // normal facade

// or you may want to provide some dependecies your self :
\Facades\Bar::m1(new Foo('hey man!'), 'hey there !');   //Now only the Logger is injected
```

--------------------

### :raising_hand: Contributing 
If you find an issue, or have a better way to do something, feel free to open an issue or a pull request.

### :star: Your Stars Make Us Do More :star:
As always if you found this package useful and you want to encourage us to maintain and work on it. Just press the star button to declare your willing.



## More from the author:


###  Laravel middlewarize (new*)

:gem: You can put middleware on any method calls.

- https://github.com/imanghafoori1/laravel-middlewarize

-----------------

### Laravel Widgetize

 :gem: A minimal yet powerful package to give a better structure and caching opportunity for your laravel apps.

- https://github.com/imanghafoori1/laravel-widgetize


-----------------

### Laravel Terminator

 :gem: A minimal yet powerful package to give you opportunity to refactor your controllers.

- https://github.com/imanghafoori1/laravel-terminator


----------------

<p align="center">
  
    It's not I am smarter or something, I just stay with the problems longer.
    
    "Albert Einstein"
    
</p>
