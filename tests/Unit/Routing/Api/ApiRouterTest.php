<?php

namespace Tests\Unit\Routing\Api\ApiRouterTest;

use Boil\Routing\Api\ApiRoute;
use Boil\Routing\Api\ApiRouter;
use Boil\Support\Wordpress\WpHelper;

it('can register a get route', function () {
    $router = new ApiRouter($this->app, 'test');

    $route = $router->get('me-get-uri/{name}', function () {
        return 'test';
    });

    expect($route)->toBeInstanceOf(ApiRoute::class)
        ->namespace->toBe('test')
        ->method->toBe('get')
        ->uri->toBe('me-get-uri/{name}')
        ->callback->toBeInstanceOf(\Closure::class);

    expect($route->getUri())->toBe('me-get-uri/(?P<name>\w+)');
});

it('can register a post route', function () {
    $router = new ApiRouter($this->app, 'test');

    $route = $router->post('me-post-uri/{name}', function () {
        return 'test';
    });

    expect($route)->toBeInstanceOf(ApiRoute::class)
        ->namespace->toBe('test')
        ->method->toBe('post')
        ->uri->toBe('me-post-uri/{name}')
        ->callback->toBeInstanceOf(\Closure::class);

    expect($route->getUri())->toBe('me-post-uri/(?P<name>\w+)');
});

it('can register a delete route', function () {
    $helper = WpHelper::fake([
        'add_action' => fn ($action, $callback) => $action === 'rest_api_init' && $callback(),
        'register_rest_route' => fn () => null
    ]);

    $router = new ApiRouter($this->app, 'test');

    $route = $router->delete('me-delete-uri/{name}', function () {
        return 'test';
    });

    expect($route)->toBeInstanceOf(ApiRoute::class)
        ->namespace->toBe('test')
        ->method->toBe('delete')
        ->uri->toBe('me-delete-uri/{name}')
        ->callback->toBeInstanceOf(\Closure::class);

    expect($route->getUri())->toBe('me-delete-uri/(?P<name>\w+)');

    $router->boot();

    $helper->assertCalled('add_action');

    $helper->assertCalled('register_rest_route', function ($namespace, $route, $args = []) {
        return $namespace === 'test'
            && $route === 'me-delete-uri/(?P<name>\w+)'
            && $args['methods'] = 'delete'
            && $args['permission_callback'] = '__return_true';
    });
});
