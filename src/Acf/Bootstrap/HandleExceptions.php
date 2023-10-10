<?php

namespace Samurai\Acf\Bootstrap;

use Samurai\Application;

class HandleExceptions
{
    public function bootstrap(Application $app): void
    {
        \Spatie\Ignition\Ignition::make()
            ->applicationPath($app->basePath())
            ->shouldDisplayException($app->hasDebugModeEnabled())
            ->register();
    }
}
