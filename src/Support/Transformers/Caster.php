<?php

namespace Boil\Support\Transformers;

class Caster
{
    /**
     * @param  array<int|string, mixed>  $values
     * @param  string[]  $casts
     */
    public function __construct(
        protected array $values,
        protected array $casts
    ) {
    }

    /**
     * @return mixed[]
     */
    public function transform(): array
    {
        foreach ($this->casts as $key => $cast) {
            $keys = explode('.', $key);
            $key = array_shift($keys);

            $this->values[$key] = $this->transformItem($key, $keys, $cast);
        }

        return $this->values;
    }

    /**
     * @param  mixed[]  $keys
     */
    protected function transformItem(string $key, array $keys, string $cast): mixed
    {
        $value = $this->values[$key] ?? null;
        $multiple = false;

        if (count($keys)) {
            while ($index = array_shift($keys)) {
                if ($index === '*' && count($keys) > 0) {
                    $multiple = true;

                    continue;
                }

                if ($multiple) {
                    $value = array_map(function ($value) use ($index, $keys, $cast) {
                        return $this->cast($value[$index], $index, $keys, $cast);
                    }, $value);

                    $multiple = false;
                } else {
                    $value = $this->cast($value, $index, $keys, $cast);
                }
            }
        } else {
            return (new Casts($value, $cast))->transform();
        }

        return $value;
    }

    /**
     * @param  mixed[]  $keys
     */
    protected function cast(mixed $value, mixed $key, array $keys, string $cast): mixed
    {
        if ($key === '*' && count($keys) === 0) {
            return array_map(function ($value) use ($cast) {
                return (new Casts($value, $cast))->transform();
            }, $value ?: []);
        } elseif ($key === '*' && count($keys) > 0) {
            $key = array_shift($keys);

            return array_map(function ($value) use ($cast, $key, $keys) {
                return $this->cast($value, $key, $keys, $cast);
            }, $value);
        }

        return (new Casts($value, $cast))->transform();
    }
}
