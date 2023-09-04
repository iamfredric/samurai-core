<?php

namespace Boil\Support\Transformers;

class AutoCaster
{
    /**
     * @param  mixed[]  $attributes
     */
    public function __construct(protected array $attributes)
    {
    }

    /**
     * @return mixed[]
     */
    public function transform(): array
    {
        foreach ($this->attributes as $key => $value) {
            $this->attributes[$key] = (new Transformations($value))
                ->through(TransformToImage::class)
                ->through(TransformToLink::class)
                ->output();
        }

        return $this->attributes;
    }
}
