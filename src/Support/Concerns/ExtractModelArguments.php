<?php

namespace Boil\Support\Concerns;

use Boil\Database\Model;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use Reflector;

class ExtractModelArguments
{
    /**
     * @param callable $callable
     * @param array<string, mixed> $arguments
     * @return mixed
     * @throws \ReflectionException
     */
    public static function fromCallable(callable $callable, array $arguments = []): mixed
    {
        return static::extractArgumentsFromReflector(
            new ReflectionFunction($callable),
            $arguments
        );
    }

    /**
     * @param string $className
     * @param array<string, mixed> $arguments
     * @return array<string, mixed>
     * @throws \ReflectionException
     */
    public static function fromConstructor(string $className, array $arguments = []): array
    {
        $reflection = new ReflectionClass($className);

        if ($constructor = $reflection->getConstructor()) {
            return static::extractArgumentsFromReflector($constructor, $arguments);
        }

        return $arguments;
    }

    /**
     * @param object $class
     * @param string $method
     * @param array<string, mixed> $arguments
     * @return array<string, mixed>
     * @throws \ReflectionException
     */
    public static function fromMethod(object $class, string $method, array $arguments = []): array
    {
        return static::extractArgumentsFromReflector(
            new ReflectionMethod($class, $method),
            $arguments
        );
    }

    /**
     * @param ReflectionMethod|ReflectionFunction $reflection
     * @param array<string, mixed> $arguments
     * @return array<string, mixed>
     * @throws \ReflectionException
     */
    protected static function extractArgumentsFromReflector(ReflectionMethod|ReflectionFunction $reflection, array $arguments): array
    {
        foreach ($reflection->getParameters() as $parameter) {
            if (! $parameter->isOptional()) {
                /** @var null|\ReflectionIntersectionType|\ReflectionNamedType|\ReflectionUnionType $type */
                $type = $parameter->getType();

                if (method_exists($type, 'getType') && $type->isBuiltin()) {
                    if (isset($arguments[$parameter->getName()])) {
                        continue;
                    }

                    continue;
                }

                $reflector = new ReflectionClass($type->getName());

                if ($reflector->getParentClass() && $reflector->getParentClass()->getName() === Model::class) {
                    $arguments[$parameter->getName()] = $reflector->getName()::current(); // @phpstan-ignore-line
                }
            }
        }

        return $arguments;
    }
}
