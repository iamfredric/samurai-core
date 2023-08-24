<?php

namespace Boil\Support\Transformers;

class Transformations
{
    public function __construct(protected mixed $attributes)
    {
    }

    public function through(string $classname, mixed ...$args): static
    {
        $this->attributes = (new $classname($this->attributes, ...$args))
            ->transform();

        return $this;
    }

    public function output(): mixed
    {
        return $this->attributes;
    }
}
