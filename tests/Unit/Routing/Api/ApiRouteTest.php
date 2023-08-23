<?php

namespace Tests\Unit\Routing\Api\ApiRouteTest;

use Boil\Routing\Api\ApiRoute;

it('creates a route and transforms the uri')
    ->expect(new ApiRoute('get', 'me-get-uri/{name}', fn () => 'test', 'test'))
    ->toBeInstanceOf(ApiRoute::class)
    ->method->toBe('get')
    ->uri->toBe('me-get-uri/{name}')
    ->callback->toBeInstanceOf(\Closure::class)
    ->namespace->toBe('test')
    ->getUri()->toBe('me-get-uri/(?P<name>\w+)');
