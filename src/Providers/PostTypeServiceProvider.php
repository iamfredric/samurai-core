<?php

namespace Boil\Providers;

use Boil\PostTypes\PostTypeConfigurator;
use Boil\Support\Concerns\ConfigPath;
use Illuminate\Support\ServiceProvider;

class PostTypeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(
            PostTypeConfigurator::class,
            fn () => new PostTypeConfigurator(
                new ConfigPath($this->app['config']->get('features.menus.routes'))
            )
        );
    }

    public function boot()
    {
        $this->app->make(PostTypeConfigurator::class)->boot();
    }
}
