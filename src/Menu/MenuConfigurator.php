<?php

namespace Boil\Menu;

use Boil\Application;
use Boil\Support\Concerns\ConfigPath;

class MenuConfigurator
{
    public function __construct(protected Application $app)
    {
    }

    public function register(string $slug, string $label)
    {
        register_nav_menu($slug, $label);
    }

    public function render(string $slug, array $args = [])
    {
        return wp_nav_menu(array_merge([
            'theme_location' => $slug,
            'container' => null,
            'items_wrap' => '%3$s'
        ], $args));
    }

    public function boot(): void
    {
        $config = new ConfigPath($this->app['config']->get('features.menus.routes'));

        $config->include();
    }
}
