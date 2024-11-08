<?php

namespace Samurai\PostTypes;

class PostType
{
    /**
     * @param  string[]  $supports
     * @param  Taxonomy[]  $taxonomies
     */
    public function __construct(
        public string $id,
        protected string $singular = '',
        protected string $plural = '',
        protected ?string $slug = null,
        protected bool $public = true,
        protected int $position = 25,
        protected string $icon = '',
        protected bool $showUi = true,
        protected bool $showInMenu = true,
        protected bool $showInAdminBar = true,
        protected bool $exportable = true,
        protected bool $deleteWithUser = false,
        protected bool $hierarchical = false,
        protected bool $hasArchives = false,
        protected bool|string $queryVar = false,
        protected string $capabilityType = 'post',
        protected bool $showInRest = false,
        protected array $supports = [
            'title',
            'editor',
            'excerpt',
            'author',
            'thumbnail',
            'comments',
            'trackbacks',
            'custom-fields',
            'revisions',
            'page-attributes',
            'post-formats',
        ],
        public array $taxonomies = [],
    ) {}

    public function taxonomy(Taxonomy $taxonomy): static
    {
        $this->taxonomies[] = $taxonomy;

        return $this;
    }

    public function supports(string ...$supports): static
    {
        $this->supports = $supports;

        return $this;
    }

    public function singular(string $singular): static
    {
        $this->singular = mb_strtolower($singular, 'utf-8');

        return $this;
    }

    public function plural(string $plural): static
    {
        $this->plural = $plural;

        return $this;
    }

    public function isPrivate(): static
    {
        $this->public = false;

        return $this;
    }

    public function position(int $positon): static
    {
        $this->position = $positon;

        return $this;
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function isExportable(): static
    {
        $this->exportable = true;

        return $this;
    }

    public function isNotExportable(): static
    {
        $this->exportable = false;

        return $this;
    }

    public function deleteWithUser(): static
    {
        $this->deleteWithUser = true;

        return $this;
    }

    public function hierarchical(): static
    {
        $this->hierarchical = true;

        return $this;
    }

    public function capability(string $capability): static
    {
        $this->capabilityType = $capability;

        return $this;
    }

    public function hasArchives(): static
    {
        $this->hasArchives = true;

        return $this;
    }

    public function hasIndexPage(): static
    {
        $this->hasArchives = true;

        return $this;
    }

    public function slug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function useGutenberg(bool $showInRest = true): self
    {
        $this->showInRest = $showInRest;

        return $this;
    }

    public function isMediaSupported(): bool
    {
        return in_array('thumbnail', $this->supports);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function toArray(): array
    {
        $attributes = [
            'public' => $this->public,
            'show_ui' => $this->showUi,
            'show_in_menu' => $this->showInMenu,
            'show_in_admin_bar' => $this->showInAdminBar,
            'menu_position' => $this->position,
            'menu_icon' => $this->icon,
            'can_export' => $this->exportable,
            'delete_with_user' => $this->deleteWithUser,
            'hierarchical' => $this->hierarchical,
            'has_archive' => $this->hasArchives,
            'capability_type' => $this->capabilityType,
            'show_in_rest' => $this->showInRest,
            'supports' => $this->supports,
            'rewrite' => [
                'slug' => $this->slug ?? $this->id,
            ],

            'labels' => [
                'name' => $this->plural,
                'singular_name' => $this->singular,
                'menu_name' => $this->plural,
                'name_admin_bar' => $this->plural,
                'add_new' => string_translate('Add :attribute', ['attribute' => $this->singular]),
                'add_new_item' => string_translate('Add new :attribute', ['attribute' => $this->singular]),
                'edit_item' => string_translate('Edit :attribute', ['attribute' => $this->singular]),
                'new_item' => string_translate('New :attribute', ['attribute' => $this->singular]),
                'view_item' => string_translate('View :attribute', ['attribute' => $this->singular]),
                'search_items' => string_translate('Search :attributes', ['attributes' => $this->plural]),
                'not_found' => string_translate('No :attributes found', ['attributes' => $this->singular]),
                'not_found_in_trash' => string_translate('No :attributes found in trash', ['attributes' => $this->plural]),
                'all_items' => string_translate('All :attributes', ['attributes' => $this->plural]),
                'parent_item' => string_translate('Parent :attribute', ['attribute' => $this->singular]),
                'parent_item_colon' => string_translate('Parent: :attribute', ['attribute' => $this->singular]),
                'archive_title' => string_translate('Archive: :attributes', ['attributes' => $this->plural]),
            ],
        ];

        if ($this->queryVar) {
            $attributes['query_var'] = $this->queryVar;
        }

        return $attributes;
    }
}
