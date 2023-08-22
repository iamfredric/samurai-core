<?php

namespace Boil\Acf;

use Boil\Application;
use Boil\Support\Concerns\ConfigPath;
use Boil\Support\Facades\View;
use Illuminate\Support\Collection;

class AcfConfigurator
{
    /** @var array<AcfConfiguratorOptionsPage> */
    protected array $optionsPages = [];

    protected array $groups = [];

    public function __construct(protected Application $app)
    {
        $this->groups = $this->app['config']->get('features.acf.groups', []);
        $this->optionsPages = (new Collection($this->app['config']->get('features.acf.options_pages', [])))
            ->map(fn (string $className) => new $className)
            ->map(fn (AcfOptionsPage $option) => new AcfConfiguratorOptionsPage(
                $option->id(),
                $option->title(),
                $option->menuTitle(),
                $option->parentSlug(),
                $option->position(),
                $option->capability(),
                $option->iconUrl(),
                $option->redirect(),
                $option->autoload(),
                $option->updateButtonLabel(),
                $option->updateMessage(),
                $option->slug(),
                $option->share(),
            ))
            ->toArray();
    }

    public function addOptionsPage(
        string $id,
        string $title,
        ?string $menuTitle = null,
        ?string $parentSlug = null,
        ?int $position = null,
        string $capability = 'edit_posts',
        string $iconUrl = '',
        bool $redirect = false,
        bool $autoload = false,
        ?string $updateButtonLabel = null,
        ?string $updateMessage = null,
        ?string $slug = null,
    ): AcfConfiguratorOptionsPage {
        $optionsPage = new AcfConfiguratorOptionsPage(
            $id,
            $title,
            $menuTitle,
            $parentSlug,
            $position,
            $capability,
            $iconUrl,
            $redirect,
            $autoload,
            $updateButtonLabel,
            $updateMessage,
            $slug,
        );

        $this->optionsPages[] = $optionsPage;

        return $optionsPage;
    }

    public function addGroup(string $group): static
    {
        $this->groups[] = $group;

        return $this;
    }

    public function addGroups(array $groups): static
    {
        $this->groups = array_merge($this->groups, $groups);

        return $this;
    }

    public function boot()
    {
        $config = new ConfigPath($this->app['config']->get('features.acf.routes'));

        $config->include();

        add_action('acf/init', function () {
            foreach ($this->optionsPages as $optionsPage) {
                acf_add_options_page($optionsPage->toArray());
            }

            foreach ($this->groups as $group) {
                register_extended_field_group((new $group())->toArray());
            }
        });

        foreach ($this->optionsPages as $optionsPage) {
            if ($optionsPage->share) {
                $value = get_fields($optionsPage->id);
                $sharer = $optionsPage->share;

                if (is_callable($optionsPage->share)) {
                    $value = $sharer($value);
                }

                View::share($optionsPage->id, $value);
            }
        }
    }
}
