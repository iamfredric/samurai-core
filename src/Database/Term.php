<?php

namespace Samurai\Database;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Samurai\Support\Wordpress\WpHelper;

class Term
{
    protected Collection $attributes;

    /** @var string[] */
    protected array $hidden = [];

    /** @var string[] */
    protected array $dates = [];

    /**
     * @param  \WP_Term|array<string, mixed>|null  $attributes
     */
    final public function __construct($attributes = null)
    {
        $this->attributes = new Collection($attributes ?: []); // @phpstan-ignore-line
    }

    /**
     * @param  array<string, mixed>  $arguments
     * @return Collection<static>
     */
    public static function all(array $arguments = [])
    {
        $instance = new static;

        return (new Collection(
            WpHelper::get_terms(array_merge($arguments, [ // @phpstan-ignore-line
                'taxonomy' => $instance->type(),
            ]))
        ))->mapInto(static::class);
    }

    /**
     * @return null|static
     */
    public static function find(int $id)
    {
        if ($term = WpHelper::get_term($id)) {
            return new static($term); // @phpstan-ignore-line
        }

        return null;
    }

    /**
     * @return Collection<static>
     */
    public static function forModel(Model $model)
    {
        $instance = new static;

        return (new Collection(WpHelper::get_the_terms($model->id, $instance->type()))) // @phpstan-ignore-line
            ->filter()
            ->mapInto(static::class);
    }

    public function isActive(): bool
    {
        return WpHelper::is_category($this->get('term_id')) || WpHelper::is_tax($this->type(), $this->get('term_id'));
    }

    public function getUrlAttribute(): string
    {
        return WpHelper::get_term_link($this->get('term_id'));
    }

    public function type(): string
    {
        if (isset($this->type)) {
            return $this->type;
        }

        $parts = explode('\\', get_class($this));

        return Str::snake(end($parts));
    }

    protected function attributeShouldBeHidden(string $key): bool
    {
        return in_array($key, $this->hidden);
    }

    protected function cast(string $key, mixed $value): mixed
    {
        if (isset($this->casts[$key])) {
            return new $this->casts[$key]($value);
        }

        return $value;
    }

    protected function getAttributeMethodName(string $key): string
    {
        return Str::of($key)
            ->camel()
            ->prepend('get')
            ->append('Attribute')
            ->__toString();
    }

    protected function translateKey(string $key): string
    {
        return [
            'id' => 'term_id',
            'title' => 'name',
        ][$key] ?? $key;
    }

    public function get(string $key): mixed
    {
        if ($this->attributeShouldBeHidden($key)) {
            return null;
        }

        $key = $this->translateKey($key);

        $value = $this->attributes->get($key);

        $value = $this->cast($key, $value);

        if (method_exists($this, $method = $this->getAttributeMethodName($key))) {
            $value = $this->$method($value);
        }

        return $value;
    }

    public function __get(string $name): mixed
    {
        if ($name == 'attributes') {
            return $this->attributes;
        }

        return $this->get($name);
    }
}
