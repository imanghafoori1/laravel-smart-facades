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
    protected static function getFacadeAccessor()
    {
        return static::class;
    }

    public static function shouldProxyTo($class)
    {
        static::$app->singleton(self::getFacadeAccessor(), $class);
    }

    public static function preCall($methodName, $listener)
    {
        $listener = self::makeListener($methodName, $listener);

        Event::listen('calling: '.static::class.'@'.$methodName, $listener);
    }

    public static function postCall($methodName, $listener)
    {
        $listener = self::makeListener($methodName, $listener);

        Event::listen('called: '.static::class.'@'.$methodName, $listener);
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param string $method
     * @param array $args
     * @return mixed
     *
     * @throws \RuntimeException
     * @throws \ReflectionException
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getFacadeRoot();

        if (! $instance) {
            throw new RuntimeException('A facade root has not been set.');
        }

        try {
            Event::dispatch('calling: '.static::class.'@'.$method, [$method, $args]);
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
     * @param ReflectionParameter[] $parameters
     * @param array $inputData
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
            $listener = function ($_eventName, $methodAndArguments) use ($listener) {
                static::$app->call($listener, $methodAndArguments);
            };
        } else {
            $listener = function ($methodName, $args, $result = null) use ($listener) {
                static::$app->call($listener, [
                    $methodName,
                    $args,
                    $result,
                ]);
            };
        }

        return $listener;
    }
}
