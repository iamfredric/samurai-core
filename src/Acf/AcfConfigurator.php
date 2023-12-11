<?php

namespace Samurai\Acf;

use Illuminate\Support\Collection;
use Samurai\Support\Concerns\ConfigPath;
use Samurai\Support\Facades\View;
use Samurai\Support\Wordpress\WpHelper;

class AcfConfigurator
{
    /**
     * @param  string[]  $groups
     * @param  string[]|AcfConfiguratorOptionsPage[]  $optionsPages
     */
    public function __construct(
        protected ConfigPath $configPath,
        protected array $groups = [],
        protected array $optionsPages = [],
        protected array $gutenbergBlocks = [],
    ) {
        //        $this->groups = $this->app['config']->get('features.acf.groups', []);
        //        $this->optionsPages = (new Collection($this->app['config']->get('features.acf.options_pages', [])))
        //            ->map(fn (string $className) => new $className)
        //            ->map(fn (AcfOptionsPage $option) => new AcfConfiguratorOptionsPage(
        //                $option->id(),
        //                $option->title(),
        //                $option->menuTitle(),
        //                $option->parentSlug(),
        //                $option->position(),
        //                $option->capability(),
        //                $option->iconUrl(),
        //                $option->redirect(),
        //                $option->autoload(),
        //                $option->updateButtonLabel(),
        //                $option->updateMessage(),
        //                $option->slug(),
        //                $option->share(),
        //            ))
        //            ->toArray();
    }

    public function addOptionsPage(
        string $id,
        string $title,
        string $menuTitle = null,
        string $parentSlug = null,
        int $position = null,
        string $capability = 'edit_posts',
        string $iconUrl = '',
        bool $redirect = false,
        bool $autoload = false,
        string $updateButtonLabel = null,
        string $updateMessage = null,
        string $slug = null,
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

    /** @param  string[]  $groups */
    public function addGroups(array $groups): static
    {
        $this->groups = array_merge($this->groups, $groups);

        return $this;
    }

    /** @param class-string $block */
    public function addGutenbergBlock(string $block): static
    {
        $this->gutenbergBlocks[] = $block;

        return $this;
    }

    /** @param class-string[] $blocks */
    public function addGutenbergBlocks(array $blocks): static
    {
        $this->gutenbergBlocks = array_merge($this->gutenbergBlocks, $blocks);

        return $this;
    }


    public function boot(): void
    {
        $this->configPath->include();

        $this->optionsPages = (new Collection($this->optionsPages))
            ->map(fn (string|AcfConfiguratorOptionsPage $optionsPage) => is_string($optionsPage) ? $this->initializeOptionsPageFromString($optionsPage) : $optionsPage)
            ->toArray();

        WpHelper::add_action('acf/init', function () {
            foreach ($this->optionsPages as $optionsPage) {
                WpHelper::acf_add_options_page($optionsPage->toArray()); // @phpstan-ignore-line
            }

            foreach ($this->groups as $group) {
                WpHelper::register_extended_field_group((new $group())->toArray()); // @phpstan-ignore-line
            }

            foreach ($this->gutenbergBlocks as $block) {
                $callable = new $block();

                WpHelper::acf_register_block_type($callable->getBlockRegistrationsAttributes());

                WpHelper::register_extended_field_group($callable->getFieldGroupRegistrationAttributes());
            }
        });

        foreach ($this->optionsPages as $optionsPage) {
            if ($optionsPage->share) {
                $value = WpHelper::get_fields($optionsPage->id);
                $sharer = $optionsPage->share;

                if (is_callable($optionsPage->share)) {
                    $value = $sharer($value);
                }

                View::share($optionsPage->id, $value);
            }
        }
    }

    protected function initializeOptionsPageFromString(string $optionsPage): AcfConfiguratorOptionsPage
    {
        /** @var AcfOptionsPage $option */
        $option = new $optionsPage();

        return new AcfConfiguratorOptionsPage(
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
        );
    }
}
