<?php

namespace Samurai\Acf\Bootstrap;

use Samurai\Application;

class BootProviders
{
    public function bootstrap(Application $app): void
    {
        $app->boot();
    }
}
