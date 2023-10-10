<?php

namespace Samurai\Acf;

abstract class Group
{
    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->config();
    }

    /** @return array<string, mixed> */
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

    /** @return string[] */
    abstract public function fields(): array;

    /** @return array<string, mixed>[] */
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
