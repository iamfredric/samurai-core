<?php

namespace Boil\Menu;

use Boil\Application;
use Boil\Support\Concerns\ConfigPath;
use Boil\Support\Wordpress\WpHelper;

class MenuConfigurator
{
    public function __construct(protected Application $app)
    {
    }

    public function register(string $slug, string $label)
    {
        WpHelper::register_nav_menu($slug, $label);
    }

    public function render(string $slug, array $args = [])
    {
        return WpHelper::wp_nav_menu(array_merge([
            'theme_location' => $slug,
            'container' => null,
            'items_wrap' => '%3$s',
        ], $args));
    }

    public function boot(): void
    {
        $config = new ConfigPath($this->app['config']->get('features.menus.routes'));

        $config->include();
    }
}
