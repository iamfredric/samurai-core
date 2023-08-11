<?php

namespace Boil\Bootstrap;

use Boil\Application;

class BootProviders
{
    public function bootstrap(Application $app)
    {
        $app->boot();
    }
}
