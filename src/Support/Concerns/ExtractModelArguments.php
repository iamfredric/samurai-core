<?php

namespace Boil\Support\Concerns;

use Boil\Database\Model;
use Illuminate\Http\Request;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use Reflector;

class ExtractModelArguments
{
    /**
     * @param array<string, mixed> $arguments
     * @param array<class-string, mixed> $bindings
     * @return mixed
     * @throws \ReflectionException
     */
    public static function fromCallable(\Closure|string $callable, array $arguments = [], array $bindings = []): mixed
    {
        return static::extractArgumentsFromReflector(
            new ReflectionFunction($callable),
            $arguments,
            $bindings
        );
    }

    /**
     * @param class-string $className
     * @param array<string, mixed> $arguments
     * @param array<class-string, mixed> $bindings
     * @return array<string, mixed>
     * @throws \ReflectionException
     */
    public static function fromConstructor(string $className, array $arguments = [], array $bindings = []): array
    {
        $reflection = new ReflectionClass($className);

        if ($constructor = $reflection->getConstructor()) {
            return static::extractArgumentsFromReflector($constructor, $arguments, $bindings);
        }

        return $arguments;
    }

    /**
     * @param object $class
     * @param string $method
     * @param array<string, mixed> $arguments
     * @param array<class-string, mixed> $bindings
     * @return array<string, mixed>
     * @throws \ReflectionException
     */
    public static function fromMethod(object $class, string $method, array $arguments = [], array $bindings = []): array
    {
        return static::extractArgumentsFromReflector(
            new ReflectionMethod($class, $method),
            $arguments,
            $bindings
        );
    }

    /**
     * @param ReflectionMethod|ReflectionFunction $reflection
     * @param array<string, mixed> $arguments
     * @param array<class-string, mixed> $bindings
     * @return array<string, mixed>
     * @throws \ReflectionException
     */
    protected static function extractArgumentsFromReflector(ReflectionMethod|ReflectionFunction $reflection, array $arguments, array $bindings = []): array
    {
        foreach ($reflection->getParameters() as $parameter) {
            if (! $parameter->isOptional()) {
                /** @var null|\ReflectionIntersectionType|\ReflectionNamedType|\ReflectionUnionType $type */
                $type = $parameter->getType();
                $isBuiltIn = method_exists($type, 'getType') && $type->isBuiltin(); // @phpstan-ignore-line

                /** @var class-string|null $name */
                $name = method_exists($parameter, 'getName') ? $parameter->getName() : null;

                if (isset($bindings[$type->getName()])) {
                    $arguments[$name] = $bindings[$type->getName()];
                    continue;
                }

                if ($isBuiltIn) {
                    continue;
                }

                if(empty($name)) {
                    continue;
                }

                if (isset($arguments[$name])) {
                    continue;
                }

                if ($type->getName() == Request::class) {
                    $arguments[$name] = Request::capture();
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
