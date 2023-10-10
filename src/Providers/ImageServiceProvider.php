<?php

namespace Samurai\Providers;

use Illuminate\Support\ServiceProvider;
use Samurai\Support\Concerns\ConfigPath;
use Samurai\Support\Media\ImageConfigurator;

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
