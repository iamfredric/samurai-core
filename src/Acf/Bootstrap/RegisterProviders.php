<?php

namespace Boil\Acf\Bootstrap;

use Boil\Application;

class RegisterProviders
{
    public function bootstrap(Application $app): void
    {
        $app->registerConfiguredProviders();
    }
}
