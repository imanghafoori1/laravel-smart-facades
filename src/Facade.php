<?php

namespace Imanghafoori\SmartFacades;

use TypeError;
use ReflectionMethod;
use RuntimeException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Facade as LaravelFacade;

class Facade extends LaravelFacade
{
    protected static function getFacadeAccessor()
    {
        return static::class;
    }

    static function shouldProxyTo($class)
    {
        static::$app->singleton(self::getFacadeAccessor(), $class);
    }

    public static function preCall(string $method, $listener)
    {
        $listener = self::makeListener($method, $listener);

        Event::listen('calling: '. static::class.'@'. $method, $listener);
    }

    public static function postCall(string $method, $listener)
    {
        $listener = self::makeListener($method, $listener);

        Event::listen('called: '. static::class.'@'. $method, $listener);
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
            event('calling: '. static::class.'@'. $method, [$method, $args]);
            $result =  $instance->$method(...$args);
            event('called: '. static::class.'@'. $method, [$method, $args, $result]);

            return $result;
        } catch (TypeError $error) {
            $params = (new ReflectionMethod($instance, $method))->getParameters();
            self::addMissingDependencies($params, $args);
            $result = $instance->$method(...$args);
            event('called: '. static::class.'@'. $method, [$method, $args, $result]);

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
            $listener = function ($_eventName, $methodAndArguments) use ($listener) {
                static::$app->call($listener, $methodAndArguments);
            };
        } else {
            $listener = function ($methodName, $args, $result = null) use ($listener) {
                static::$app->call($listener, [
                    $methodName,
                    $args,
                    $result
                ]);
            };
        }

        return $listener;
    }
}
