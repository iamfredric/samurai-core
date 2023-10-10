<?php

namespace Samurai\Acf\Bootstrap;

use Samurai\Application;

class RegisterProviders
{
    public function bootstrap(Application $app): void
    {
        $app->registerConfiguredProviders();
    }
}
