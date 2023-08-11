<?php

namespace Boil\Providers;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('events', fn () => new Dispatcher($this->app));
    }
}
