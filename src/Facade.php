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
            $newArgs = self::addMissingDependencies($params, $args);

            return $instance->$method(...$newArgs);
        }
    }

    /**
     * Adds missing dependencies to the user-provided input.
     *
     * @param ReflectionParameter[] $parameters
     * @param array $inputData
     *
     * @return array
     */
    private static function addMissingDependencies($parameters, array $inputData)
    {
        $injectedInputData = $inputData;
        $c = 0;
        foreach ($parameters as $i => $parameter) {
            $class = $parameter->getClass();
            if ($class && ! is_a($inputData[$c] ?? null, $class->name)) {
                array_splice($injectedInputData, $i, 0, [self::$app[$class->name]]);
            } else {
                $c++;
            }
        }

        return $injectedInputData;
    }
}
