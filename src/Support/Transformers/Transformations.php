<?php

namespace Boil\Support\Transformers;

class Transformations
{
    public function __construct(protected mixed $attributes)
    {
    }

    /** @param  class-string  $classname */
    public function through(string $classname, mixed ...$args): static
    {
        $resolvedClass = new $classname($this->attributes, ...$args);

        if (! method_exists($resolvedClass, 'transform')) {
            // Todo: Throw exception
            return $this;
        }

        $this->attributes = $resolvedClass->transform();

        return $this;
    }

    public function output(): mixed
    {
        return $this->attributes;
    }
}
