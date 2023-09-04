<?php

declare(strict_types=1);

namespace Boil\Database;

use Boil\Exceptions\BuilderCallNotFoundException;
use Boil\Support\Wordpress\WpHelper;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;

// use Platon\Exceptions\BuilderCallNotFoundException;

/**
 * @method Builder where($key, $value)
 * @method Builder whereMeta($key, $compare = null, $value = null)
 * @method Builder orWhereMeta($key, $compare = null, $value = null)
 * @method Builder whereTaxonomyIn($taxonomy, $terms, $field = 'term_id')
 * @method Builder orderBy($orderBy, $direction = 'asc')
 * @method Builder limit($limit)
 * @method Builder latest($orderBy = 'date')
 * @method Builder oldest($orderBy = 'date')
 * @method Builder when($condition, $callback)
 */
class Builder
{
    use Macroable;

    protected ?MetaBuilder $metaBuilder = null;

    /** @var array<string, mixed> */
    protected $arguments = [
        'suppress_filters' => false,
    ];

    //    /** @var array<string, mixed> */
    //    protected $metaArguments = [];

    protected ?Model $model;

    final public function __construct(Model $model = null)
    {
        $this->model = $model;

        if ($model) {
            $this->setArgument('post_type', $model->getType());
        }
    }

    public function first(): ?Model
    {
        $this->setArgument('posts_per_page', 1);

        if ($posts = WpHelper::get_posts($this->getArguments())) {
            return $this->buildItem($posts[0]);
        }

        return null;
    }

    public static function find(int $id, Model $model = null): ?Model
    {
        $instance = new static($model);

        if ($post = WpHelper::callFunction('get_post', $id)) {
            return $instance->buildItem($post);
        }

        return null;
    }

    public function get(): Collection
    {
        $posts = new Collection();

        foreach ((array) WpHelper::get_posts($this->getArguments()) as $post) {
            $posts->push($this->buildItem($post));
        }

        return $posts;
    }

    public function paginate(int $limit = null): Pagination
    {
        $posts = [];

        $this->setArgument('posts_per_page', $limit);
        $this->setArgument('paged', WpHelper::get_query_var('paged') ?: 1);
        $query = WpQuery::make($this->getArguments());

        foreach ((array) $query->get_posts() as $post) {
            $posts[] = $this->buildItem($post);
        }

        return new Pagination($posts, $query);
    }

    /**
     * @param  mixed  $post
     * @return Model
     */
    protected function buildItem($post)
    {
        if ($this->model) {
            $class = get_class($this->model);

            return $class::make($post);
        }

        return Model::make($post);
    }

    /**
     * @param  mixed  $model
     * @return Collection
     */
    public static function all($model = null)
    {
        $instance = new static($model);

        $instance->setArgument('posts_per_page', -1);

        return $instance->get();
    }

    /**
     * Getter for arguments
     *
     * @return array<string, mixed>
     */
    public function getArguments(): array
    {
        $args = $this->arguments;

        if ($this->metaBuilder) {
            $args['meta_query'] = $this->metaBuilder->toArray();
        }

        return $args;
    }

    public function setArgument(string $key, mixed $value): void
    {
        $this->arguments[$key] = $value;
    }

    /** @param  array<string, mixed>  $query */
    public function setTaxQuery(array $query): void
    {
        if (! isset($this->arguments['tax_query'])) {
            $this->arguments['tax_query'] = [];
        }

        $this->arguments['tax_query'][] = $query;
    }

    //    public function setMetaArgument($key, $compare = null, $value = null)
    //    {
    //        if (! $this->metaBuilder) {
    //            $this->metaBuilder = new MetaBuilder();
    //        }
    //
    //        $this->metaBuilder->setArgument($key, $compare, $value);
    //    }

    protected function scopeWhereMeta(string $key, mixed $compare = null, mixed $value = null): void
    {
        if (! $this->metaBuilder) {
            $this->metaBuilder = new MetaBuilder();
        }

        $this->metaBuilder->where($key, $compare, $value);
    }

    protected function scopeOrWhereMeta(string $key, mixed $compare = null, mixed $value = null): void
    {
        if (! $this->metaBuilder) {
            $this->metaBuilder = new MetaBuilder();
        }

        $this->metaBuilder->orWhere($key, $compare, $value);
    }

    /**
     * @param  int[]  $terms
     */
    protected function scopeWhereTaxonomyIn(string $taxonomy, array $terms, string $field = 'term_id'): void
    {
        $this->setTaxQuery(compact('taxonomy', 'terms', 'field'));
    }

    protected function scopeWhen(mixed $value, callable $callback): void
    {
        if (! empty($value)) {
            $callback($this, $value);
        }
    }

    protected function scopeOrderBy(string $orderBy, string $direction = 'asc'): void
    {
        $this->setArgument('orderby', $orderBy);
        $this->setArgument('order', strtolower($direction) === 'asc' ? 'ASC' : 'DESC');
    }

    protected function scopeWhere(string $key, mixed $value): void
    {
        $this->setArgument($key, $value);
    }

    /**
     * @param  int  $limit
     */
    protected function scopeLimit($limit): void
    {
        $this->setArgument('posts_per_page', $limit);
    }

    protected function scopeLatest(string $orderBy = 'date'): void
    {
        $this->orderBy($orderBy, 'desc');
    }

    protected function scopeOldest(string $orderBy = 'date'): void
    {
        $this->orderBy($orderBy, 'asc');
    }

    protected function resolveMethodCall(string $method, mixed $args): static
    {
        if (static::hasMacro($method)) {
            static::$macros[$method]($this);

            return $this;
        } elseif (method_exists($this, $name = 'scope'.ucfirst($method))) {
            $this->{$name}(...$args);

            return $this;
        } elseif ($this->model instanceof Model && method_exists($this->model, 'scope'.ucfirst($method))) {
            $this->model->{$name}($this, ...$args);

            return $this;
        }

        throw BuilderCallNotFoundException::methodNotFound($method);
    }

    public function __call(string $method, mixed $parameters): static
    {
        return $this->resolveMethodCall($method, $parameters);
    }

    public static function __callStatic(string $method, mixed $parameters): static
    {
        $instance = new static();

        return $instance->__call($method, $parameters);
    }
}
