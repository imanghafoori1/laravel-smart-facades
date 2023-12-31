<h1 align="center">
🍄 Laravel Smart Facades 🍄
</h1>
   <h2 align="center"> 
Strategy pattern in laravel, made easy
   </h2> 
   <h3 align="center"> 
by adding some features on top of laravel facades.
</h3>

<p align="center">
Built with :heart: for every smart laravel developer

</br>

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;
<a href="https://scrutinizer-ci.com/g/imanghafoori1/laravel-smart-facades"><img src="https://img.shields.io/scrutinizer/g/imanghafoori1/laravel-smart-facades.svg?style=flat-square" alt="Quality Score"></img></a>
[![Code Quality](https://scrutinizer-ci.com/g/imanghafoori1/laravel-smart-facades/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/imanghafoori1/laravel-smart-facades/?branch=master)
[![Build Status](https://travis-ci.org/imanghafoori1/laravel-smart-facades.svg?branch=master)](https://travis-ci.org/imanghafoori1/laravel-smart-facades)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=round-square)](LICENSE.md)
[![Check Imports](https://github.com/imanghafoori1/laravel-smart-facades/actions/workflows/check_import.yml/badge.svg?branch=master)](https://github.com/imanghafoori1/laravel-smart-facades/actions/workflows/check_import.yml)
[![StyleCI](https://github.styleci.io/repos/166631643/shield?branch=master)](https://github.styleci.io/repos/166631643)
</p>





## :flashlight: Installation:

```
composer require imanghafoori/laravel-smart-facades
```


### ⚡️ No need to have getFacadeAccessor()


#### Before:
```php
use Illuminate\Support\Facades\Facade;

class MyFacade extends Facade
{
    protected static function getFacadeAccessor() // <--- normal facade
    {
        return 'some_key'; 
    }
}
```

#### After:
```php
use Imanghafoori\SmartFacades\Facade;

class MyFacade extends Facade
{
    //                                          <--- smart facade
}
```


### ⚡️ Setting the default driver by `shouldProxyTo($class)`:

Instead of bind a string to a concrete class with IOC container, you can choose the low level implementation class like this:
```php

public function register()    // <-- in a service provider
{             
    if ($someCondition) {
        MyFacade::shouldProxyTo( SomeDriver::class );
    } else {
        MyFacade::shouldProxyTo( SomeOtherDriver::class );
    }
}
```

You can proxyTo any abstract string (or closure) bound on the IoC container.

Note: If you invoke it twice, it will override:
```php
MyFacade::shouldProxyTo( DriverClass1::class );
MyFacade::shouldProxyTo( DriverClass2::class ); // <--- This wins!
```

### ⚡️ Using Non-default Driver:

If you want to change the driver at call site:
```php
MyFacade::withDriver(nonDefaultDriver::class)::myMethod();
```

### ⚡️ Method Hooks:

You can introduce some code "Before" and "after" a method call, remotely: (like event listeners on eloquent models) 

![image](https://user-images.githubusercontent.com/6961695/71646327-f100db00-2cfb-11ea-9277-1271395efca0.png)

Here we have told the system evenever the `MyFacade::findUser($id)` method was called in the system, to perform a log.

### ⚡️ Choosing the driver, based on parameters value:

For example, lets say you want your facade to use an SMS based driver by default, but if the text is very long (more than 200 chars) it  should use an email driver.

You can do it like this:

![image](https://user-images.githubusercontent.com/6961695/77253047-98dfd200-6c75-11ea-8ab8-b9bf4146dd9f.png)

#### :wrench: Automatic method injection when calling a method through a facade.

This the adds ability to enjoy automatic method injection when calling methods on POPOs (Plain Old Php Objects) WITHOUT any performance hit when you do not need it.


#### 🐙 Example:
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

Calling `Bar` through a Facade:

Before:
```php
MyFacade::m1(resolve(Foo::class), resolve(LoggerInterface::class), 'hey there !'); 
```

After:
```php
 // This will work and $foo, $logger would be auto-injected for us.

MyFacade::m1('hey there !');          // normal facade

// or you may want to provide some dependencies yourself:
\Facades\Bar::m1(new Foo('hey man!'), 'hey there !');   //Now only the Logger is injected
```

--------------------

### :raising_hand: Contributing:
If you find an issue or have a better way to do something, feel free to open an issue or a pull request.

### :star: Your Stars Make Us Do More :star:
As always if you found this package useful and you want to encourage us to maintain and work on it. Just press the star button to declare your willingness.



## More from the author:


###  Laravel Microscope

:gem: Automatically find bugs before they bite.

- https://github.com/imanghafoori1/laravel-microscope

-----------------

### Laravel Widgetize

 :gem: A minimal yet powerful package to give a better structure and caching opportunity for your laravel apps.

- https://github.com/imanghafoori1/laravel-widgetize

----------------

<p align="center">
  
    It's not I am smarter or something, I just stay with the problems longer.
    
    "Albert Einstein"
    
</p>
