<?php

namespace Boil\Http;

use Boil\Acf\Bootstrap\BootProviders;
use Boil\Acf\Bootstrap\HandleExceptions;
use Boil\Acf\Bootstrap\LoadConfiguration;
use Boil\Acf\Bootstrap\LoadEnvironmentVariables;
use Boil\Acf\Bootstrap\RegisterFacades;
use Boil\Acf\Bootstrap\RegisterProviders;
use Boil\Application;
use Boil\Routing\Router;
use Illuminate\Contracts\Http\Kernel as KernelContract;

class Kernel implements KernelContract
{
    /** @var class-string[] */
    protected array $bootstrappers = [
        LoadEnvironmentVariables::class,
        LoadConfiguration::class,
        HandleExceptions::class,
        RegisterFacades::class,
        RegisterProviders::class,
        BootProviders::class,
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
