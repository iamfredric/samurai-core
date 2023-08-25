<?php

namespace Boil\Database;

use ArrayAccess;
use Boil\Support\Wordpress\WpHelper;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;

/**
 * @property int $id
 * @property string $url
 * @property int $order
 * @property int $author
 * @property Carbon $date
 * @property string $date_gmt
 * @property string $content
 * @property string $title
 * @property string $excerpt
 * @property string $status
 * @property string $password
 * @property string $name
 * @property Carbon $modified
 * @property string $modified_gmt
 * @property string $content_filtered
 * @property int|null $parent
 * @property string $type
 * @property string $mime_type
 * @property string $comment_status
 * @property string $ping_status
 * @property string $to_ping
 * @property string $pinged
 * @property string $guid
 * @property int $comment_count
 * @property string $filter
 *
 * @method static \Boil\Database\Builder where($key, $value)
 * @method static \Boil\Database\Builder whereMeta($key, $compare, $value = null)
 * @method static \Boil\Database\Builder whereTaxonomyIn($taxonomy, $terms, $field = 'term_id')
 * @method static \Boil\Database\Builder orderBy($orderBy, $direction = 'asc')
 * @method static \Boil\Database\Builder limit($limit)
 * @method static \Boil\Database\Builder latest($orderBy = 'date')
 * @method static \Boil\Database\Builder oldest($orderBy = 'date')
 */
class Model implements Arrayable, Jsonable, ArrayAccess
{
    protected ?string $type = null;

    /** @var string[] */
    protected array $dates = [
        'date', 'modified',
    ];

    /** @var string[] */
    protected array $casts = [];

    /** @var string[] */
    protected array $hasCasted = [];

    /** @var string[] */
    protected array $hidden = [];

    protected int $excerptLength = 120;

    /** @var Collection */
    protected Collection $attributes;

    /** @var Term[] */
    protected array $terms = [];

    /** @param  \WP_Post|null  $post */
    final public function __construct($post = null)
    {
        if ($post) {
            $this->setAttributes($post);
        } else {
            $this->attributes = new Collection();
        }
    }

    /** @param  \WP_Post  $post */
    public static function make($post): static
    {
        return new static($post);
    }

    public static function current(): static
    {
        return static::make(WpHelper::callFunction('get_post'));
    }

    /**
     * @param int $id
     * @return Model|null
     */
    public static function find(int $id)
    {
        return Builder::find($id, new static());
    }

    public static function paginate(?int $limit = null): Pagination
    {
        return (new Builder(new static()))->paginate($limit);
    }

    public static function all(): Collection
    {
        return Builder::all(new static());
    }

    /**
     * @param array<string, mixed> $params
     * @return Model|null
     * */
    public static function create(array $params)
    {
        $instance = new static();

        $params['post_type'] = $instance->getType();

        $id = WpHelper::callFunction('wp_insert_post', $params);

        return static::find($id);
    }

    /** @param array<string, mixed> $args */
    public function update(array $args): static
    {
        $params = [];

        foreach ($args as $key => $value) {
            $params[$this->translateAttributeKeyToWordpress($key)] = $value;
        }

        $params['ID'] = $this->attributes->get('id');

        $model = static::create($params);

        $this->attributes = $model?->attributes ?? new Collection();

        return $this;
    }

    public function save(): void
    {
        WpHelper::callFunction('wp_update_post', $this->toWordpressArray());
    }

    public function get(string $key): mixed
    {
        if (in_array($key, $this->hasCasted)) {
            return $this->attributes->get($key);
        }

        if ($this->attributeShouldBeHidden($key)) {
            return null;
        }

        if ($this->isTermDefined($key)) {
            return $this->term($key);
        }

        $value = $this->attributes->get($key);

        if (! $this->attributes->has($key)) {
            if (method_exists($this, 'getFieldsAttribute')) {
                if ($this->getFieldsAttribute()->has($key)) {
                    $value = $this->getFieldsAttribute()->get($key);
                }
            }
        }

        $value = $this->castToDates($key, $value);
        $value = $this->cast($key, $value);

        if (method_exists($this, $method = $this->getAttributeMethodName($key))) {
            $value = $this->$method($value);
        }

        $this->hasCasted[] = $key;
        $this->attributes->put($key, $value);

        return $this->attributes->get($key);
    }

    public function getKey(): int
    {
        return $this->get('id');
    }

    public function getExcerptAttribute(string $excerpt): ?string
    {
        return (string) Str::of(strip_tags($excerpt ?: $this->get('content')))
            ->limit($this->excerptLength);
    }

    protected function getAttribute(string $key, mixed $value = null): mixed
    {
        if (! $this->attributes->has($key) && $value) {
            $this->attributes->put($key, $value);
        }

        return $this->attributes->get($key);
    }

    public function getUrlAttribute(?string $url = null): ?string
    {
        return $this->getAttribute('url', WpHelper::get_permalink($this->get('id'))) ?: null;
    }

    protected function getAttributeMethodName(string $key): string
    {
        $key = Str::camel($key);

        return "get{$key}Attribute";
    }

    public function getType(): ?string
    {
        if ($this->type) {
            return $this->type;
        }

        $reflection = new ReflectionClass($this);

        return Str::camel($reflection->getShortName());
    }

    protected function castToDates(string $key, mixed $value): mixed
    {
        if (! in_array($key, $this->dates)) {
            return $value;
        }

        return Carbon::parse($value);
    }

    protected function cast(string $key, mixed $value): mixed
    {
        if (isset($this->casts[$key])) {
            $this->hasCasted[] = $key;

            return new $this->casts[$key]($value);
        } elseif (isset($this->casts["{$key}.*"])) {
            /** @var class-string $castable */
            $castable = $this->casts["{$key}.*"];
            return (new Collection($value))
                ->mapInto($castable);
        }

        foreach (array_keys($this->casts) as $k) {
            if (preg_match("/$key\[(.*?)\]/", $k, $matches)) {
                return new $this->casts[$k]($this->get($matches[1]));
            }
        }

        return $value;
    }

    /** @param  \WP_Post|null  $attributes */
    public function setAttributes($attributes): void
    {
        if (empty($attributes)) {
            return;
        }

        if ($attributes instanceof \WP_Post) {
            $attributes = $attributes->to_array();
        }

        $collection = [];

        foreach ($attributes as $key => $value) {
            $key = $this->translateAttributeKey($key);

            if (preg_match('/_gmt/', $key)) {
                continue;
            }

            $collection[$key] = $value;
        }

        $this->attributes = new Collection($collection);
    }

    protected function attributeShouldBeHidden(string $key): bool
    {
        if (in_array($key, $this->hidden)) {
            return true;
        }

        return false;
    }

    protected function translateAttributeKey(string $key): string
    {
        return (string) Str::of($key)->lower()->replace(['post_', 'menu_'], '');
    }

    public function translateAttributeKeyToWordpress(string $key): string
    {
        if ($key == 'id') {
            return Str::upper($key);
        }

        if ($key == 'order') {
            return "menu_{$key}";
        }

        if (in_array($key, ['comment_status', 'ping_status', 'comment_count', 'menu_order', 'filter', 'guid', 'pinged', 'to_ping'])) {
            return $key;
        }

        return "post_{$key}";
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return $this->attributes->except($this->hidden)->toArray();
    }

    // should
    /** @return array<string, mixed> */
    public function toWordpressArray(): array
    {
        $items = [];

        foreach ($this->attributes as $key => $value) {
            $items[$this->translateAttributeKeyToWordpress($key)] = $value;
        }

        ksort($items);

        return $items;
    }

    public function toJson(mixed $options = 0): string
    {
        return $this->attributes->except($this->hidden)->toJson($options);
    }

    public function offsetExists(mixed $offset): bool
    {
        return ! is_null($this->get($offset));
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->attributes[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->attributes[$offset]);
    }

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function __set(string $method, mixed $value): void
    {
        $this->attributes[$method] = $value;
    }

    public function __get(string $variable): mixed
    {
        if ($variable == 'attributes') {
            return $this->attributes;
        }

        return $this->get($variable);
    }

    public static function query(): Builder
    {
        return new Builder(new static());
    }

    public static function __callStatic(string $method, mixed $args): Builder
    {
        $instance = new static();

        return (new Builder($instance))->__call($method, $args);
    }

    protected function isTermDefined(string $key): bool
    {
        return isset($this->terms[$key]);
    }

    /** @return Term|null */
    protected function term(string $key)
    {
        if (! $this->attributes->has("terms.{$key}")) {
            $this->attributes->put("terms.{$key}", $this->terms[$key]::forModel($this));
        }

        return $this->attributes->get("terms.{$key}");
    }
}
