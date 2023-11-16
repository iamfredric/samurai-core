<?php

namespace Samurai\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Samurai\Routing\CustomRoute;

/**
 * @method static \Samurai\Routing\Template register(string $name, string|array|callable $callback)
 * @method static \Samurai\Routing\Template view(string $name, string $view, array $options = [])
 * @method static \Samurai\Routing\Template template(string $key, string $name, string|array|callable $callback, array $options = [])
 * @method static CustomRoute get(string $endpoint, string|array|callable $callback)
 */
class Route extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'router';
    }
}
