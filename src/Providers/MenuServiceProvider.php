<?php

namespace Boil\Providers;

use Boil\Menu\MenuConfigurator;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MenuConfigurator::class, fn ($app) => new MenuConfigurator($app));
    }

    public function boot(): void
    {
        $this->app->make(MenuConfigurator::class)->boot();
    }
}
