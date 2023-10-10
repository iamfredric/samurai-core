<?php

namespace Samurai\Providers;

use Illuminate\Support\ServiceProvider;
use Samurai\Hooks\HookConfigurator;

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
