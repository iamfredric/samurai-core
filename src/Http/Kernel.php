<?php

namespace Boil\Http;

use Boil\Application;
use Boil\Bootstrap\HandleExceptions;
use Boil\Bootstrap\LoadConfiguration;
use Boil\Bootstrap\LoadEnvironmentVariables;
use Boil\Bootstrap\RegisterFacades;
use Boil\Routing\Router;
use Illuminate\Contracts\Http\Kernel as KernelContract;

class Kernel implements KernelContract
{
    protected array $bootstrappers = [
        LoadEnvironmentVariables::class,
        LoadConfiguration::class,
        HandleExceptions::class,
        RegisterFacades::class,
        \Boil\Bootstrap\RegisterProviders::class,
        \Boil\Bootstrap\BootProviders::class
    ];

    public function __construct(protected Application $app)
    {
    }

    public function bootstrap(): void
    {
        if (! $this->app->hasBeenBootstrapped()) {
            $this->app->bootstrapWith($this->bootstrappers());
        }
    }

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
