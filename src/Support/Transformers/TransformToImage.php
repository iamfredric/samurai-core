<?php

namespace Samurai\Support\Transformers;

use Samurai\Support\Wordpress\Image;

class TransformToImage
{
    public function __construct(protected mixed $value) {}

    public function transform(): mixed
    {
        return is_array($this->value) && isset($this->value['sizes']) && isset($this->value['width']) && isset($this->value['height'])
            ? new Image($this->value)
            : $this->value;
    }
}
