<?php

namespace Boil\Providers;

use Boil\Database\Model;
use Boil\Support\Facades\Blade;
use Boil\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ModelServiceProvider extends ServiceProvider
{
    public function register()
    {
        // $this->app->bindMethod(Model::class.'::current', fn ($model) => $model::current());
        // $this->app->bind(Model::class, fn ($model) => $model::current());
    }

    public function boot()
    {

    }
}
