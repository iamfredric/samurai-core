<?php

namespace Samurai\Http;

use Illuminate\Contracts\Http\Kernel as KernelContract;
use Samurai\Acf\Bootstrap\BootProviders;
use Samurai\Acf\Bootstrap\HandleExceptions;
use Samurai\Acf\Bootstrap\LoadConfiguration;
use Samurai\Acf\Bootstrap\LoadEnvironmentVariables;
use Samurai\Acf\Bootstrap\RegisterFacades;
use Samurai\Acf\Bootstrap\RegisterProviders;
use Samurai\Application;
use Samurai\Routing\Router;

class Kernel implements KernelContract
{
    /** @var class-string[] */
    protected array $bootstrappers = [
        LoadEnvironmentVariables::class,
        LoadConfiguration::class,
        // HandleExceptions::class,
        RegisterFacades::class,
        RegisterProviders::class,
        BootProviders::class,
    ];

    public function __construct(protected Application $app) {}

    public function bootstrap(): void
    {
        if (! $this->app->hasBeenBootstrapped()) {
            $this->app->bootstrapWith($this->bootstrappers());
        }
    }

    /** @return class-string[] */
    protected function bootstrappers(): array
    {
        return $this->bootstrappers;
    }

    public function handle($request)
    {
        $this->bootstrap();

        $router = $this->app->make(Router::class);

        $router->capture();

        return $router;
    }

    public function terminate($request, $response): void
    {
        $this->app->terminate();
    }

    public function getApplication(): \Illuminate\Contracts\Foundation\Application
    {
        return $this->app;
    }
}
