<?php

namespace Boil\PostTypes;

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
    ) {
    }

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

    public function toArray()
    {
        return [
            'labels'        => [
                'name'                  => $this->plural,
                'singular_name'         => $this->singular,
                'search_items'          => sprintf(__("Search %s"), $this->plural),
                'popular_items'         => sprintf(__("Popular %s"), $this->plural),
                'all_items'             => sprintf(__("All %s"), $this->plural),
                'parent_item'           => sprintf(__("Parent %s"), $this->singular),
                'parent_item_colon'     => sprintf(__("Parent: %s"), $this->singular),
                'edit_item'             => sprintf(__("Edit %s"), $this->singular),
                'view_item'             => sprintf(__("Show %s"), $this->singular),
                'update_item'           => sprintf(__("Update %s"), $this->singular),
                'add_new_item'          => sprintf(__("Add %s"), $this->singular),
                'new_item_name'         => sprintf(__("New %s name"), $this->singular),
                'add_or_remove_items'   => sprintf(__("Add/Remove %s"), $this->singular),
                'choose_from_most_used' => sprintf(__("Choose from most used %s"), $this->plural),
                'not_found'             => sprintf(__("No %s found"), $this->singular),
                'no_terms'              => sprintf(__("No %s"), $this->plural),
            ],
            'query_var'     => $this->queryVar,
            'description'   => $this->description,
            'public'        => $this->public,
            'hierarchical'  => $this->hierarchical,
            'show_ui'       => $this->showUi,
            'show_tagcloud' => $this->showTagCloud,
            'show_in_rest' => $this->showInRest,
            'rewrite' => [
                'slug' => $this->slug ?: $this->id
            ]
        ];
    }
}
