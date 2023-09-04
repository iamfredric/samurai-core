<?php

namespace Boil\Support\Concerns;

use Boil\Database\Model;
use Illuminate\Http\Request;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;

class ExtractModelArguments
{
    /**
     * @param  array<string, mixed>  $arguments
     * @param  array<class-string|string, mixed>  $bindings
     *
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
     * @param  class-string  $className
     * @param  array<string, mixed>  $arguments
     * @param  array<class-string|string, mixed>  $bindings
     * @return array<string, mixed>
     *
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
     * @param  array<string, mixed>  $arguments
     * @param  array<class-string|string, mixed>  $bindings
     * @return array<string, mixed>
     *
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
     * @param  array<string, mixed>  $arguments
     * @param  array<class-string|string, mixed>  $bindings
     * @return array<string, mixed>
     *
     * @throws \ReflectionException
     */
    protected static function extractArgumentsFromReflector(ReflectionMethod|ReflectionFunction $reflection, array $arguments, array $bindings = []): array
    {
        foreach ($reflection->getParameters() as $parameter) {
            if (! $parameter->isOptional()) {
                /** @var null|\ReflectionParameter $type */
                $type = $parameter->getType();
                $isBuiltIn = $type->isBuiltin(); // @phpstan-ignore-line
                if (empty($type)) {
                    continue;
                }
                /** @var class-string|null $name */
                $name = method_exists($parameter, 'getName') ? $parameter->getName() : null;

                if (isset($bindings[$type->getName()])) {
                    $arguments[$name] = $bindings[$type->getName()];

                    continue;
                }

                if ($isBuiltIn) {
                    continue;
                }

                if (empty($name)) {
                    continue;
                }

                if (isset($arguments[$name])) {
                    continue;
                }

                if ($type->getName() == Request::class) {
                    $arguments[$name] = Request::capture();

                    continue;
                }

                $reflector = new ReflectionClass($type->getName()); // @phpstan-ignore-line

                if ($reflector->getParentClass() && $reflector->getParentClass()->getName() === Model::class) {
                    $arguments[$parameter->getName()] = $reflector->getName()::current(); // @phpstan-ignore-line
                }
            }
        }

        return $arguments;
    }
}
