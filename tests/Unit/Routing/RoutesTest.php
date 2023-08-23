<?php

namespace Tests\Unit\Routing\RoutesTest;

use Boil\Routing\Router;
use Boil\Routing\Routes;
use Illuminate\Http\Response;

it('registers a route', function () {
    $routes = new Routes();

    $route = $routes->register('index', function () {
        return 'test';
    });

    expect($route)->toBeInstanceOf(\Boil\Routing\Template::class)
        ->name->toBe('index')
        ->getView()->toBeNull()
        ->options->toBe([])
        ->getCallable()->toBeInstanceOf(\Closure::class);

    $response = $route->call($this->app);

    expect($response)->toBeInstanceOf(Response::class);
    expect($response->content())->toBe('test');
});

it('registers a view', function () {
    $routes = new Routes();

    $route = $routes->view('index', 'index');

    expect($route)->toBeInstanceOf(\Boil\Routing\Template::class)
        ->name->toBe('index')
        ->getView()->toBe('index')
        ->options->toBe([])
        ->getCallable()->toBeInstanceOf(\Closure::class);
});

it('registers a page template', function () {
    $routes = new Routes();

    $template = $routes->template('index-template', 'Index template', 'TestController@test');

    expect($template)->toBeInstanceOf(\Boil\Routing\Template::class)
        ->name->toBe('Index template')
        ->getView()->toBeNull()
        ->options->toBe([])
        ->getCallable()->toBe(['TestController', 'test']);

    expect($routes->getTemplates())->toHaveCount(1);

    expect(isset($routes->getTemplates()['index-template']))->toBeTrue();

    expect($routes->isRegistered('index-template'))->toBeInstanceOf(\Boil\Routing\Template::class);
});

it('resolves a route', function () {
    $routes = new Routes();

    $route = $routes->register('index', function () {
        return 'test';
    });

    expect($routes->resolve('index'))->toBe($route);
});

it('gets search template', function () {
    $routes = new Routes();

    $route = $routes->register('search', function () {
        return 'test';
    });

    expect($routes->getSearchTemplate())->toBe($route);
});
