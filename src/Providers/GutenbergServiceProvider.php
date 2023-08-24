<?php

namespace Boil\Providers;

use Illuminate\Support\ServiceProvider;

class GutenbergServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            \Boil\Acf\Gutenberg\GutenbergConfigurator::class,
            fn ($app) => new \Boil\Acf\Gutenberg\GutenbergConfigurator($app)
        );
    }

    public function boot(): void
    {
        $this->app->make(\Boil\Acf\Gutenberg\GutenbergConfigurator::class)->boot();
    }
}
