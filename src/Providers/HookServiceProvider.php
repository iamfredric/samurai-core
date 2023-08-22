<?php

namespace Boil\Providers;

use Boil\Hooks\HookConfigurator;
use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(HookConfigurator::class, fn ($app) => new HookConfigurator($app));
    }

    public function boot(): void
    {
        $this->app->make(HookConfigurator::class)->boot();
    }
}
