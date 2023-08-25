<?php

namespace Boil\Support\Components;

use Boil\Support\Transformers\AttributeGetters;
use Boil\Support\Transformers\AttributesWhenNull;
use Boil\Support\Transformers\AutoCaster;
use Boil\Support\Transformers\Caster;
use Boil\Support\Transformers\MapKeysToCamel;
use Boil\Support\Transformers\Transformations;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;

class Component implements Arrayable, Jsonable
{
    protected string $view = '';

    protected ?string $prefix = null;

    /**
     * @var array<string, mixed>
     */
    protected array $data = [];

    /**
     * @var array<string, string>
     */
    protected array $casts = [];

    protected ?string $nextComponent = null;

    protected ?string $prevComponent = null;

    /**
     * @param array<string, mixed> $data
     * @param string|null $prefix
     */
    public function __construct(array $data, ?string $prefix = null)
    {
        $this->view = $data['acf_fc_layout'];

        if ($prefix) {
            $this->prefix = strtolower($prefix);
        }

        unset($data['acf_fc_layout']);

        $this->data = $this->appendDataAttributes($data);

        $this->data = (new Transformations($this->data))
            ->through(Caster::class, $this->casts ?? [])
            ->through(AutoCaster::class)
            ->through(AttributeGetters::class, $this)
            ->through(AttributesWhenNull::class, $this)
            ->through(MapKeysToCamel::class)
            ->output();

    }

    public function render(): View
    {
        if ($path = config('features.acf.components_path')) {
            $view = str_replace('{name}', $this->prefix ? "{$this->prefix}.{$this->view}" : $this->view, $path);
        } else {
            $view = $this->prefix ? "components.{$this->prefix}.{$this->view}" : "components.{$this->view}";
        }

        return view($view, $this->attributes());
    }

    public function data(string $key = null): mixed
    {
        if ($key) {
            return $this->data[Str::camel($key)] ?? null;
        }

        return $this->data;
    }

    /**
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return array_merge($this->data, [
            'nextComponent' => $this->nextComponent,
            'prevComponent' => $this->prevComponent,
            'currentComponent' => $this->hash(),
        ]);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function appendDataAttributes(array $data): array
    {
        $reflection = new ReflectionClass($this);

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (str_starts_with($method->getName(), 'append') && str_ends_with($method->getName(), 'Attribute')) {
                $key = strtolower(str_replace(['append', 'Attribute'], '', $method->getName()));

                $data[$key] = $this->{$method->getName()}();
            }
        }

        return $data;
    }

    public function view(): string
    {
        return $this->view;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'view' => $this->view,
            'data' => (new Collection($this->data))
                ->mapWithKeys(function ($value, $key) {
                    if (is_array($value)) {
                        $value = (new Collection($value));
                    }

                    return [$key => $value instanceof Arrayable ? $value->toArray() : $value];
                })->toArray(),
        ];
    }

    public function setPreviousComponent(?string $hash): void
    {
        $this->prevComponent = $hash;
    }

    public function setNextComponent(?string $hash): void
    {
        $this->nextComponent = $hash;
    }

    public function hash(): string
    {
        return md5($this->view);
    }

    public function toJson($options = 0): bool|string
    {
        return json_encode($this->toArray(), $options);
    }
}
