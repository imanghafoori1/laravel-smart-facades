# Laravel  Hyper Facades :

This package tries to add some features on the top of the current laravel's facade feature so you can get more advantages out of them.

### Automatic method injection when calling a method on a class through a facade.

``` // web.php
class A1 {}
// ...
class A2 {
     
     public function a2 (A1 $a1_obj) {
         var_dump(get_class($a1_obj));    // prints:  A1
     }
}

// Define a Hyper Facade
class myHyperFacade extends HyperFacade {

    public function getFacadeAccessor () {
        return A2::class;
    }
}


// now lets use Hyper Facade to call `a2` method on A2 class.

myHyperFacade::a2();     // prints A1


```

As you can see `$a1_obj` was injected for us. (which is not the case with normal laravel facades)
