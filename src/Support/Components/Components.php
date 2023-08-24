<?php

namespace Boil\Support\Components;

use ArrayIterator;
use Countable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use IteratorAggregate;

class Components implements Arrayable, Countable, IteratorAggregate, Jsonable
{
    /**
     * @var Component[]
     */
    protected array $components = [];

    /**
     * @param array<string, mixed> $components
     * @param string|null $prefix
     */
    public function __construct(array $components = [], protected ?string $prefix = null)
    {
        if (! is_null($prefix)) {
            $this->prefix = ucfirst(strtolower($prefix));
        }

        $this->resolveComponents($components);
    }

    /** @return Component[] */
    public function all(): array
    {
        return $this->components;
    }

    public function exists(): bool
    {
        return count($this->components) > 0;
    }

    /** @param  array<string, mixed> $components */
    protected function resolveComponents(array $components): void
    {
        if (! $components) {
            return;
        }

        foreach ($components as $component) {
            $this->components[] = $this->initializeComponent(
                $component,
                $this->resolveClassname($component['acf_fc_layout'])
            );
        }

        foreach ($this->components as $key => $component) {
            if (isset($this->components[$key - 1])) {
                $component->setPreviousComponent($this->components[$key - 1]->hash());
            }

            if (isset($this->components[$key + 1])) {
                $component->setNextComponent($this->components[$key + 1]->hash());
            }
        }
    }

    protected function resolveClassname(string $name): string
    {
        $name = collect(explode('-', $name))->map(function ($name) {
            return ucfirst($name);
        })->implode('');

        if (! $this->prefix) {
            return (string) Str::of($name)->camel()->ucfirst()->prepend('\\App\\Components\\')->append('Component');
        }

        return (string) Str::of($name)->camel()->ucfirst()->prepend("\\App\\Components\\{$this->prefix}\\")->append('Component');
    }

    /**
     * @param array<string, mixed> $attributes
     * @param string $classname
     * @return Component
     */
    protected function initializeComponent(array $attributes, string $classname)
    {
        if (class_exists($classname)) {
            if (is_subclass_of($class = new $classname($attributes, $this->prefix), Component::class)) {
                return $class;
            }
        }

        return new Component($attributes, $this->prefix);
    }

    /**
     * @return array<string, mixed>[]
     */
    public function toArray(): array
    {
        return (new Collection($this->components))
            ->toArray();
    }

    public function toJson($options = 0): bool|string
    {
        return json_encode($this->toArray(), $options);
    }

    public function count(): int
    {
        return count($this->components);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->components);
    }
}
