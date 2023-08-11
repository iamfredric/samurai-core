<?php

namespace Boil\Providers;

class AcfServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\Boil\Acf\AcfConfigurator::class, fn () => new \Boil\Acf\AcfConfigurator($this->app));
    }

    public function boot()
    {
        $this->app->make(\Boil\Acf\AcfConfigurator::class)->boot();
    }
}
