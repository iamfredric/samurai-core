<?php

namespace Boil\Providers;

use Boil\Routing\Router;
use Boil\Routing\Routes;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Routes::class, fn () => new Routes);
        $this->app->singleton(Router::class, fn () => new Router($this->app));

        $this->app->alias(Routes::class, 'router');
    }
}