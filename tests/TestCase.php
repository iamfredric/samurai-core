<?php

namespace Tests;

use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use PHPMock;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub


//        $app = new \Boil\Application(dirname(__DIR__) . '/tests/wp/wp-content/themes/testtheme');
//
//        $app->singleton(
//            \Illuminate\Contracts\Http\Kernel::class,
//            \Boil\Http\Kernel::class
//        );
//
//        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
//
//        $response = $kernel->handle(
//            $request = \Illuminate\Http\Request::capture()
//        );
//
//        $response->send();
//
//        $kernel->terminate($request, $response);
    }
}
