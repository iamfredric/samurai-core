<?php

namespace Samurai\Providers;

use Illuminate\Support\ServiceProvider;
use Samurai\PostTypes\PostTypeConfigurator;
use Samurai\Support\Concerns\ConfigPath;

class PostTypeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            PostTypeConfigurator::class,
            fn ($app) => new PostTypeConfigurator(
                new ConfigPath($app['config']->get('features.post_types.routes'))
            )
        );
    }

    public function boot(): void
    {
        $this->app->make(PostTypeConfigurator::class)->boot();
    }
}
