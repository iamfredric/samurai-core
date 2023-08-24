<?php

namespace Boil\Providers;

use Boil\Support\Concerns\ConfigPath;
use Boil\Support\Media\ImageConfigurator;
use Illuminate\Support\ServiceProvider;

class ImageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            ImageConfigurator::class,
            fn () => new ImageConfigurator(
                new ConfigPath($this->app['config']->get('features.images.routes')),
            )
        );
    }

    public function boot(): void
    {
        $this->app->make(ImageConfigurator::class)->boot();
    }
}
