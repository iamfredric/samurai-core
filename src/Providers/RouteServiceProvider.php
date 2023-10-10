<?php

namespace Samurai\Providers;

use Illuminate\Support\ServiceProvider;
use Samurai\Routing\Api\ApiRouter;
use Samurai\Routing\Router;
use Samurai\Routing\Routes;

class RouteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Routes::class, fn () => new Routes);
        $this->app->singleton(Router::class, fn ($app) => new Router($app));

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
