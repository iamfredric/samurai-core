<?php

namespace Samurai\PostTypes;

class Taxonomy
{
    public function __construct(
        public string $id,
        protected string $singular = '',
        protected string $plural = '',
        protected string $description = '',
        protected string|bool $queryVar = false,
        protected bool $public = true,
        protected bool $hierarchical = false,
        protected bool $showUi = true,
        protected bool $showTagCloud = false,
        protected ?string $slug = null,
        protected bool $showInRest = false,
    ) {}

    public function plural(string $plural): static
    {
        $this->plural = mb_strtolower($plural, 'utf-8');

        return $this;
    }

    public function singular(string $singular): static
    {
        $this->singular = mb_strtolower($singular, 'utf-8');

        return $this;
    }

    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function queryVar(string $queryVar): static
    {
        $this->queryVar = $queryVar;

        return $this;
    }

    public function isPublic(): static
    {
        $this->public = true;

        return $this;
    }

    public function isPrivate(): static
    {
        $this->public = false;

        return $this;
    }

    public function multilevel(): static
    {
        $this->hierarchical = true;

        return $this;
    }

    public function isHierarchical(): static
    {
        $this->hierarchical = true;

        return $this;
    }

    public function flat(): static
    {
        $this->hierarchical = false;

        return $this;
    }

    public function useGutenberg(): static
    {
        $this->showInRest = true;

        return $this;
    }

    public function isNotHierarchical(): static
    {
        $this->hierarchical = false;

        return $this;
    }

    public function showUi(): static
    {
        $this->showUi = true;

        return $this;
    }

    public function dontShowUi(): static
    {
        $this->showUi = false;

        return $this;
    }

    public function showTagCloud(): static
    {
        $this->showTagCloud = true;

        return $this;
    }

    public function dontShowTagCloud(): static
    {
        $this->showTagCloud = false;

        return $this;
    }

    public function slug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function toArray(): array
    {
        return [
            'labels' => [
                'name' => $this->plural,
                'singular_name' => $this->singular,
                'search_items' => string_translate('Search :attributes', ['attributes' => $this->plural]),
                'popular_items' => string_translate('Popular :attributes', ['attributes' => $this->plural]),
                'all_items' => string_translate('All :attributes', ['attributes' => $this->plural]),
                'parent_item' => string_translate('Parent :attribute', ['attribute' => $this->singular]),
                'parent_item_colon' => string_translate('Parent: :attribute', ['attribute' => $this->singular]),
                'edit_item' => string_translate('Edit :attribute', ['attribute' => $this->singular]),
                'view_item' => string_translate('View :attribute', ['attribute' => $this->singular]),
                'update_item' => string_translate('Update :attribute', ['attribute' => $this->singular]),
                'add_new_item' => string_translate('Add :attribute', ['attribute' => $this->singular]),
                'new_item_name' => string_translate('New :attribute name', ['attribute' => $this->singular]),
                'add_or_remove_items' => string_translate('Add or remove :attribute', ['attribute' => $this->singular]),
                'choose_from_most_used' => string_translate('Choose from most used :attribute', ['attribute' => $this->plural]),
                'not_found' => string_translate('No :attributes found', ['attribute' => $this->singular]),
                'no_terms' => string_translate('No :attribute', ['attribute' => $this->plural]),
            ],
            'query_var' => $this->queryVar,
            'description' => $this->description,
            'public' => $this->public,
            'hierarchical' => $this->hierarchical,
            'show_ui' => $this->showUi,
            'show_tagcloud' => $this->showTagCloud,
            'show_in_rest' => $this->showInRest,
            'rewrite' => [
                'slug' => $this->slug ?: $this->id,
            ],
        ];
    }
}
