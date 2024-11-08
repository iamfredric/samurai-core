<?php

namespace Samurai\Database;

class MetaBuilder
{
    /**
     * @var array<MetaBuilder>
     */
    protected array $groups = [];

    /** @var array<mixed, mixed> */
    protected array $arguments = [];

    protected string $relation = 'AND';

    public function where(string|callable $key, mixed $compare = null, mixed $value = null): MetaBuilder
    {
        if (is_callable($key)) {
            return $this->buildGroup($key);
        }

        return $this->setArgument($key, $compare, $value);
    }

    public function whereNotNull(string $key): MetaBuilder
    {
        $this->arguments[] = [
            'key' => $key,
            'compare' => '!=',
            'value' => '',
        ];

        return $this;
    }

    public function orWhere(mixed ...$args): MetaBuilder
    {
        $this->relation = 'OR';

        return $this->where(...$args);
    }

    /**
     * @return array<mixed, mixed>
     */
    public function toArray(): array
    {
        $arguments = $this->arguments;

        foreach ($this->groups as $group) {
            $arguments = array_merge([$group->toArray()], $arguments);
        }

        if (count($arguments) > 1) {
            $arguments['relation'] = $this->relation;
        }

        return $arguments;
    }

    protected function buildGroup(callable $callable): MetaBuilder
    {
        $callable($group = new MetaBuilder);

        $this->groups[] = $group;

        return $this;
    }

    public function setArgument(string $key, ?string $compare, ?string $value = null): MetaBuilder
    {
        $this->arguments[] = [
            'key' => $key,
            'compare' => is_null($value) ? '=' : $compare,
            'value' => is_null($value) ? $compare : $value,
        ];

        return $this;
    }
}
