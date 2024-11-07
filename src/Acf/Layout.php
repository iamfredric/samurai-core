<?php

namespace Samurai\Acf;

abstract class Layout extends \Extended\ACF\Fields\Layout
{
    public function __construct(string $label, ?string $name = null)
    {
        parent::__construct($label, $name);

        $this->fields($this->items());
    }

    abstract public function items(): array;
}
