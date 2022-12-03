<?php

namespace Imanghafoori\SmartFacades;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Facade as LaravelFacade;
use Illuminate\Support\Str;
use ReflectionMethod;
use RuntimeException;
use TypeError;

class Facade extends LaravelFacade
{
    protected static $tmpDriver = null;

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        if ($tmp = static::$tmpDriver) {
            static::$tmpDriver = null;

            return $tmp;
        }

        return static::class;
    }

    /**
     * Temporarily changes the driver, only for the next call.
     *
     * @param  \Closure|string  $name
     * @return string
     */
    public static function changeProxyTo($name)
    {
        static::$tmpDriver = $name;

        return static::class;
    }

    /**
     * Temporarily changes the driver, only for the next call.
     *
     * @param  \Closure|string  $name
     * @return string
     */
    public static function withDriver($name)
    {
        return static::changeProxyTo($name);
    }

    /**
     * Changes the default driver of the facade.
     *
     * @param  \Closure|string  $name
     * @return string
     */
    public static function shouldProxyTo($class)
    {
        static::clearResolvedInstance(self::getFacadeAccessor());
        static::$app->singleton(self::getFacadeAccessor(), $class);

        return static::class;
    }

    /**
     * Sets up a listener to be invoked before the actual method call.
     *
     * @param  string  $methodName
     * @param  \Closure|string  $listener
     */
    public static function preCall($methodName, $listener)
    {
        $listener = self::makeListener($methodName, $listener);

        Event::listen('calling: '.static::class.'@'.$methodName, $listener);
    }

    /**
     * Sets up a listener to be invoked after the actual method.
     *
     * @param  string  $methodName
     * @param  \Closure|string  $listener
     */
    public static function postCall($methodName, $listener)
    {
        $listener = self::makeListener($methodName, $listener);

        Event::listen('called: '.static::class.'@'.$methodName, $listener);
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string  $method
     * @param  array  $args
     * @return mixed
     *
     * @throws \RuntimeException
     * @throws \ReflectionException
     */
    public static function __callStatic($method, $args)
    {
        Event::dispatch('calling: '.static::class.'@'.$method, [$method, $args]);
        $instance = static::getFacadeRoot();

        if (! $instance) {
            throw new RuntimeException('A facade root has not been set.');
        }

        try {
            $result = $instance->$method(...$args);
            Event::dispatch('called: '.static::class.'@'.$method, [$method, $args, $result]);

            return $result;
        } catch (TypeError $error) {
            $params = (new ReflectionMethod($instance, $method))->getParameters();
            self::addMissingDependencies($params, $args);
            $result = $instance->$method(...$args);
            Event::dispatch('called: '.static::class.'@'.$method, [$method, $args, $result]);

            return $result;
        }
    }

    /**
     * Adds missing dependencies to the user-provided input.
     *
     * @param  ReflectionParameter[]  $parameters
     * @param  array  $inputData
     */
    private static function addMissingDependencies($parameters, array &$inputData)
    {
        foreach ($parameters as $i => $parameter) {
            // Injects missing type hinted parameters within the array
            $class = $parameter->getClass()->name ?? false;
            if ($class && ! ($inputData[$i] ?? false) instanceof $class) {
                array_splice($inputData, $i, 0, [self::$app[$class]]);
            } elseif (! array_key_exists($i, $inputData) && $parameter->isDefaultValueAvailable()) {
                $inputData[] = $parameter->getDefaultValue();
            }
        }
    }

    private static function makeListener(string $method, $listener)
    {
        if (Str::contains($method, '*')) {
            // The $_eventName variable is passed to us by laravel
            // but we do not need it, because we already know it.
            return function ($_eventName, $methodAndArguments) use ($listener) {
                static::$app->call($listener, $methodAndArguments);
            };
        }

        return function ($methodName, $args, $result = null) use ($listener) {
            static::$app->call($listener, [
                'methodName' => $methodName,
                'args' => $args,
                'result' => $result,
            ]);
        };
    }

    public function __call($method, $args)
    {
        return static::__callStatic($method, $args);
    }
}
