<?php

namespace Boil\Providers;

use Illuminate\Support\ServiceProvider;

class GutenbergServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(
            \Boil\Acf\Gutenberg\GutenbergConfigurator::class,
            fn () => new \Boil\Acf\Gutenberg\GutenbergConfigurator($this->app)
        );
    }

    public function boot()
    {
        $this->app->make(\Boil\Acf\Gutenberg\GutenbergConfigurator::class)->boot();
    }
}
