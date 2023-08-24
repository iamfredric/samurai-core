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
            fn ($app) => new ImageConfigurator(
                new ConfigPath($app['config']->get('features.images.routes')),
            )
        );
    }

    public function boot(): void
    {
        $this->app->make(ImageConfigurator::class)->boot();
    }
}
