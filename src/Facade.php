<?php

namespace Imanghafoori\SmartFacades;

use TypeError;
use ReflectionMethod;
use RuntimeException;
use Illuminate\Support\Facades\Facade as LaravelFacade;

class Facade extends LaravelFacade
{
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
            return $instance->$method(...$args);
        } catch (TypeError $error) {
            $params = (new ReflectionMethod($instance, $method))->getParameters();
            self::addMissingDependencies($params, $args);
            return $instance->$method(...$args);
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
}
