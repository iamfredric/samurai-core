<?php

namespace Samurai\Providers;

use Illuminate\Support\ServiceProvider;
use Samurai\Support\Vite;

class ViteServiceProviders extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('Illuminate\\Foundation\\Vite', fn ($app) => new Vite());
    }
}
