<?php

namespace Imanghafoori\SmartFacades;

use Illuminate\Support\Facades\Facade as LaravelFacade;
use ReflectionMethod;
use RuntimeException;
use TypeError;

class Facade extends LaravelFacade
{
    /**
     * Handle dynamic, static calls to the object.
     *
     * @param string $method
     * @param array  $args
     *
     * @throws \RuntimeException
     * @throws \ReflectionException
     *
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getFacadeRoot();

        if (!$instance) {
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
     * @param array                 $inputData
     *
     * @return array
     */
    private static function addMissingDependencies($parameters, array $inputData)
    {
        foreach ($parameters as $i => $parameter) {
            $class = $parameter->getClass()->name ?? '';
            if ($class && !is_a($inputData[$i] ?? '', $class)) {
                array_splice($inputData, $i, 0, [self::$app[$class]]);
            }
        }

        return $inputData;
    }
}
