<?php

namespace Boil\Bootstrap;

use Boil\Application;

class RegisterProviders
{
    public function bootstrap(Application $app): void
    {
        $app->registerConfiguredProviders();
    }
}
