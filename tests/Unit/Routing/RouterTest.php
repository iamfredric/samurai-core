<?php

namespace Unit\Routing;

use Samurai\Routing\Router;

it('captures the routes', function () {
    $router = new Router($this->app);

    $router->capture();
})->todo();

it('sends route response', function () {})->todo();
