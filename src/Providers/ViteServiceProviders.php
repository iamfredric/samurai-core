<?php

namespace Boil\Providers;

use Boil\Support\Vite;
use Illuminate\Support\ServiceProvider;

class ViteServiceProviders extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('Illuminate\\Foundation\\Vite', fn ($app) => new Vite());
    }
}
