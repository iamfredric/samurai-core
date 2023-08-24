<?php

namespace Boil\Providers;

use Boil\Support\Concerns\ConfigPath;

class AcfServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\Boil\Acf\AcfConfigurator::class, fn ($app) => new \Boil\Acf\AcfConfigurator(
            new ConfigPath($app['config']->get('features.acf.routes')),
            $app['config']->get('features.acf.groups', []),
            $app['config']->get('features.acf.options_pages', []),
        ));
    }

    public function boot(): void
    {
        $this->app->make(\Boil\Acf\AcfConfigurator::class)->boot();
    }
}
