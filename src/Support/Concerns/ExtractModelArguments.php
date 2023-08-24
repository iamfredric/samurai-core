<?php

namespace Boil\Support\Concerns;

use Boil\Database\Model;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use Reflector;

class ExtractModelArguments
{
    public static function fromCallable(callable $callable, $arguments = [])
    {
        return static::extractArgumentsFromReflector(
            new ReflectionFunction($callable),
            $arguments
        );
    }

    public static function fromConstructor(string $className, array $arguments = [])
    {
        $reflection = new ReflectionClass($className);

        if ($constructor = $reflection->getConstructor()) {
            return static::extractArgumentsFromReflector($constructor, $arguments);
        }

        return $arguments;
    }

    public static function fromMethod(object $class, string $method, array $arguments = [])
    {
        return static::extractArgumentsFromReflector(
            new ReflectionMethod($class, $method),
            $arguments
        );
    }

    protected static function extractArgumentsFromReflector(Reflector $reflection, mixed $arguments)
    {
        foreach ($reflection->getParameters() as $parameter) {
            if (! $parameter->isOptional()) {
                if ($parameter->getType()->isBuiltin()) {
                    if (isset($arguments[$parameter->getName()])) {
                        continue;
                    }

                    continue;
                }

                $reflector = new ReflectionClass($parameter->getType()->getName());

                if ($reflector?->getParentClass()?->getName() === Model::class) {
                    $arguments[$parameter->getName()] = $reflector->getName()::current();
                }
            }
        }

        return $arguments;
    }
}
