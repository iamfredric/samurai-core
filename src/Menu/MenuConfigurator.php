<?php

namespace Samurai\Menu;

use Samurai\Application;
use Samurai\Support\Concerns\ConfigPath;
use Samurai\Support\Wordpress\WpHelper;

class MenuConfigurator
{
    public function __construct(protected Application $app) {}

    public function register(string $slug, string $label): void
    {
        WpHelper::register_nav_menu($slug, $label);
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function render(string $slug, array $args = []): ?string
    {
        return WpHelper::wp_nav_menu(array_merge([
            'theme_location' => $slug,
            'container' => null,
            'items_wrap' => '%3$s',
        ], $args)) ?: null;
    }

    public function boot(): void
    {
        $config = new ConfigPath($this->app['config']->get('features.menus.routes'));

        $config->include();
    }
}
