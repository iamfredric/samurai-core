<?php

namespace Boil\Acf\Bootstrap;

use Boil\Application;

class BootProviders
{
    public function bootstrap(Application $app): void
    {
        $app->boot();
    }
}
