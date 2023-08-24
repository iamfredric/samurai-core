<?php

namespace Tests;

use Boil\Acf\Bootstrap\RegisterFacades;
use Boil\Providers\TranslationServiceProvider;
use Illuminate\Config\Repository;
use Illuminate\Support\Collection;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use PHPMock;

    public $app;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->app = new \Boil\Application(dirname(__DIR__) );

        $this->app->instance('config', $config = new Repository($this->config()));

        (new Collection([
            RegisterFacades::class
        ]))->each(fn($bootstrapper) => $this->app->make($bootstrapper)->bootstrap($this->app));

        $this->app->registerConfiguredProviders();
    }

    protected function config(): array
    {
        return [
            'app' => [
                'providers' => [
                    TranslationServiceProvider::class,
                ]
            ]
        ];
    }
}
