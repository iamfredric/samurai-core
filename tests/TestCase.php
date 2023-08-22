<?php

namespace Tests;

use Illuminate\Config\Repository;
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

        $this->app->instance('config', $config = new Repository([]));
//        $app->singleton(
//            \Illuminate\Contracts\Http\Kernel::class,
//            \Boil\Http\Kernel::class
//        );

//        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

//        $response = $kernel->handle(
//            $request = \Illuminate\Http\Request::capture()
//        );
//
//        $response->send();
//
//        $kernel->terminate($request, $response);
    }
}
