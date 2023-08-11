<?php

namespace Boil\Acf;

use Closure;
use Illuminate\Support\Str;

class AcfConfiguratorOptionsPage
{
    public bool|Closure $share = false;

    public function __construct(
        public string $id,
        protected string $title,
        protected ?string $menuTitle = null,
        protected ?string $parentSlug = null,
        protected ?int $position = null,
        protected string $capability = 'edit_posts',
        protected string $iconUrl = '',
        protected bool $redirect = false,
        protected bool $autoload = false,
        protected ?string $updateButtonLabel = null,
        protected ?string $updateMessage = null,
        protected ?string $slug = null,
    ) {
        // Todo...
    }

    public function toArray(): array
    {
        return [
            'page_title' => $this->title,
            'menu_title' => $this->getMenuTitle(),
            'menu_slug' => $this->getMenuSlug(),
            'capability' => $this->capability,
            'position' => $this->position,
            'parent_slug' => $this->parentSlug,
            'icon_url' => $this->iconUrl,
            'redirect' => $this->redirect,
            'post_id' => $this->id,
            'autoload' => $this->autoload,
            'update_button' => $this->updateButtonLabel,
            'updated_message' => $this->updateMessage,
        ];
    }

    public function share(?callable $callback = null): static
    {
        $this->autoload = true;

        if (is_callable($callback)) {
            $this->share = Closure::fromCallable($callback);

            return $this;
        }

        $this->share = true;

        return $this;
    }

    protected function getMenuTitle(): string
    {
        return $this->menuTitle ?: $this->title;
    }

    protected function getMenuSlug(): string
    {
        return $this->slug ?: Str::slug($this->getMenuTitle());
    }
}
