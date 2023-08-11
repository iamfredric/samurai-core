<?php

namespace Boil\Menu;

use Boil\Application;

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
        $routesPath = $this->app['config']->get('app.paths.routes.menus');

        if (! file_exists($routesPath)) {
            return;
        }

        include_once $routesPath;
    }
}
