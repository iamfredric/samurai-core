<?php

namespace Samurai\Providers;

use Illuminate\Support\ServiceProvider;
use Samurai\Menu\MenuConfigurator;

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
