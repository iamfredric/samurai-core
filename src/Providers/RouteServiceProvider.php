<?php

namespace Boil\Providers;

use Boil\Routing\Api\ApiRouter;
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

        $this->app->singleton(ApiRouter::class, fn ($app) => new ApiRouter(
            $app,
            $app['config']->get('features.api.namespace', 'internal')
        ));
    }

    public function boot(): void
    {
        $this->app->make(ApiRouter::class)->boot();
    }
}
