<?php

namespace Boil\Support\Transformers;

use Illuminate\Support\Str;

class MapKeysToCamel
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(protected array $attributes)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function transform(): array
    {
        $attributes = [];

        foreach ($this->attributes as $key => $attribute) {
            $attributes[Str::camel($key)] = $attribute;
        }

        return $attributes;
    }
}
