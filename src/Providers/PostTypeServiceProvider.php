<?php

namespace Boil\Providers;

use Boil\PostTypes\PostTypeConfigurator;
use Boil\Support\Concerns\ConfigPath;
use Illuminate\Support\ServiceProvider;

class PostTypeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            PostTypeConfigurator::class,
            fn ($app) => new PostTypeConfigurator(
                new ConfigPath($app['config']->get('features.menus.routes'))
            )
        );
    }

    public function boot(): void
    {
        $this->app->make(PostTypeConfigurator::class)->boot();
    }
}
