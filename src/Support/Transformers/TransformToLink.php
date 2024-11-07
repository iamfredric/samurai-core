<?php

namespace Samurai\Support\Transformers;

use Samurai\Support\Wordpress\Link;

class TransformToLink
{
    public function __construct(protected mixed $value) {}

    public function transform(): mixed
    {
        return is_array($this->value) && isset($this->value['url']) && isset($this->value['title']) && isset($this->value['target'])
            ? new Link($this->value)
            : $this->value;
    }
}
