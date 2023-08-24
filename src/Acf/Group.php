<?php

namespace Boil\Acf;

abstract class Group
{
    public function toArray(): array
    {
        return $this->config();
    }

    public function config(): array
    {
        return [
            'title' => $this->title(),
            'key' => $this->key(),
            'fields' => $this->fields(),
            'location' => $this->location(),
            'style' => $this->style(),
            'menu_order' => $this->order(),
        ];
    }

    abstract public function title(): string;

    abstract public function key(): string;

    abstract public function fields(): array;

    abstract public function location(): array;

    public function style(): string
    {
        return 'seamless';
    }

    public function order(): int
    {
        return 20;
    }
}
