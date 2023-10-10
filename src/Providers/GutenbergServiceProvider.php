<?php

namespace Samurai\Providers;

use Illuminate\Support\ServiceProvider;

class GutenbergServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            \Samurai\Acf\Gutenberg\GutenbergConfigurator::class,
            fn ($app) => new \Samurai\Acf\Gutenberg\GutenbergConfigurator($app)
        );
    }

    public function boot(): void
    {
        $this->app->make(\Samurai\Acf\Gutenberg\GutenbergConfigurator::class)->boot();
    }
}
