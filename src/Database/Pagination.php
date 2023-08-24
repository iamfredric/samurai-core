<?php

namespace Boil\Database;

use ArrayIterator;
use Boil\Support\Wordpress\WpHelper;
use IteratorAggregate;

class Pagination implements IteratorAggregate
{
    /**
     * @param  Model[]  $items
     * @param  \WP_Query  $query
     */
    public function __construct(protected array $items, protected $query)
    {
    }

    /**
     * @param  array<string, mixed>  $args
     * @return string|string[]|void
     */
    public function paginate(array $args = [])
    {
        return WpHelper::paginate_links(array_merge([
            'total' => $this->query->max_num_pages,
            'current' => WpHelper::get_query_var('paged') ?: 1,
        ], $args));
    }

    public function prev(string $label = 'Next'): ?string
    {
        return WpHelper::get_previous_posts_link($label);
    }

    public function prevUrl(): ?string
    {
        return $this->currentPage() > 1 ? WpHelper::get_previous_posts_page_link() : null;
    }

    public function next(string $label = 'Next'): ?string
    {
        return WpHelper::get_next_posts_link($label);
    }

    public function nextUrl(): ?string
    {
        return $this->currentPage() < $this->maxPage()
            ? WpHelper::get_next_posts_page_link()
            : null;
    }

    public function currentPage(): int
    {
        return WpHelper::get_query_var('paged') ?: 1;
    }

    public function maxPage(): int
    {
        return $this->query->max_num_pages;
    }

    /**
     * @return Model[]
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * @return Model[]
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet(mixed $offset): Model
    {
        return $this->items[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->items[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }
}
