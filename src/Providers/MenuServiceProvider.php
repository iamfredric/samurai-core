<?php

namespace Boil\Providers;

use Boil\Menu\MenuConfigurator;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\Boil\Menu\MenuConfigurator::class, fn () => new \Boil\Menu\MenuConfigurator($this->app));
    }

    public function boot()
    {
        $this->app->make(MenuConfigurator::class)->boot();
    }
}
