<?php

namespace Samurai\Acf\Bootstrap;

use Samurai\Application;
use Spatie\Ignition\Ignition;

class HandleExceptions
{
    public function bootstrap(Application $app): void
    {
        Ignition::make()
            ->applicationPath($app->basePath())
            ->shouldDisplayException($app->hasDebugModeEnabled())
            ->register();
    }
}
