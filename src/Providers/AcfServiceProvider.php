<?php

namespace Samurai\Providers;

use Samurai\Support\Concerns\ConfigPath;

class AcfServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\Samurai\Acf\AcfConfigurator::class, fn ($app) => new \Samurai\Acf\AcfConfigurator(
            new ConfigPath($app['config']->get('features.acf.routes')),
            $app['config']->get('features.acf.groups', []),
            $app['config']->get('features.acf.options_pages', []),
        ));
    }

    public function boot(): void
    {
        $this->app->make(\Samurai\Acf\AcfConfigurator::class)->boot();
    }
}
