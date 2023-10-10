<?php

namespace Samurai\Acf\Bootstrap;

use Illuminate\Support\Facades\Facade;
use Samurai\Application;

class RegisterFacades
{
    public function bootstrap(Application $app): void
    {
        Facade::clearResolvedInstances();

        Facade::setFacadeApplication($app);

        foreach ($app->make('config')->get('app.aliases', []) as $key => $value) {
            class_alias($value, $key);
        }
    }
}
