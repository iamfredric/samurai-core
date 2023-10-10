<?php

namespace Samurai\Support\Transformers;

class Casts
{
    public function __construct(
        protected mixed $value,
        protected ?string $cast = null
    ) {
    }

    public function transform(): mixed
    {
        if ($this->cast === 'stdClass' || $this->cast === 'object') {
            return (object) $this->value;
        }

        if ($this->cast === 'array') {
            return (array) $this->value;
        }

        return $this->cast
            ? new $this->cast($this->value)
            : $this->value;
    }
}
